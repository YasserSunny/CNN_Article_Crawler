<?php

/**
 * Finds Headline, Story and Images from an CNN article URL. 
 *
 * @author   Yasser
 */
class ArticleCrawler
{
	private $document;
	private $url;
	private $base;
	const HEADLINE_CLASS = 'pg-headline';		//CNN article's headline tag class name.
	const STORY_CLASS = 'zn-body__paragraph';	//CNN article's story tag class name.


	/**
	 * Creates a new object.
	 * Throws an exception when given URL is not an article from CNN.
	 */
	public function __construct($url)
	{
		if (strpos($url, 'www.cnn.com') !== false) 
		{
			$this->url = $url;
		}
		else 
		{
			throw new Exception('Given URL is not an CNN article.');
		}
	}


	/**
	 * Loads the HTML from the url if not already done.
	 */
	public function load()
	{
		if($this->document)
			return;
			
		$this->document = self::get_document($this->url);
		
		$this->base = self::get_base($this->document);
		if( ! $this->base)
			$this->base = $this->url;
	}


	/**
	 * Returns an array with all the images found.
	 */
	public function get_images()
	{
		$this->load();

		$images = array();
		
		foreach($this->document->getElementsByTagName('img') as $img)
		{
			$image = array
			(
					'src' => self::make_absolute($img->getAttribute('src'), $this->base),
			);

			if( ! $image['src'])
					continue;

			$images[$image['src']] = $image;
		}

		return array_values($images);
	}
 
	/**
	 * Returns the headline as a string. If no headline found it returns an empty string.
	 */
	public function get_headline()
	{
		$this->load();
		
		$headline = "";
		$class_name = self::HEADLINE_CLASS;
		$finder = new DomXPath($this->document);
		$spaner = $finder->query("//*[contains(@class, '$class_name')]");
		
		if(is_object($spaner->item(0)))
			$headline = $spaner->item(0)->nodeValue;
		
		return  $headline;
	}
	
	/**
	 * Returns the story as a string. If no story found it returns an empty string.
	 */
	public function get_story()
	{
		$this->load();
		
		$story = "";
		$class_name = self::STORY_CLASS;		
		$finder = new DomXPath($this->document);
		
		foreach( $finder->query("//*[contains(@class, '$class_name')]") as $story_part)
		{
			$story .= $story_part->nodeValue;
		}

		return  $story;
	}


	/**
	 * Gets the html of a url and loads it up in a DOMDocument.
	 */
	private static function get_document($url)
	{
		$request = curl_init();
		curl_setopt_array($request, array
		(
			CURLOPT_URL => $url,
			
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HEADER => FALSE,
			
			CURLOPT_SSL_VERIFYPEER => TRUE,
			CURLOPT_CAINFO => 'cacert.pem',

			CURLOPT_FOLLOWLOCATION => TRUE,
			CURLOPT_MAXREDIRS => 10,
		));
		$response = curl_exec($request);
		curl_close($request);

		$document = new DOMDocument();

		if($response)
		{
			libxml_use_internal_errors(true);
			$document->loadHTML($response);
			libxml_clear_errors();
		}

		return $document;
	}



	/**
	 * Tries to get the base tag href from the given document.
	 */
	private static function get_base(DOMDocument $document)
	{
		$tags = $document->getElementsByTagName('base');

		foreach($tags as $tag)
				return $tag->getAttribute('href');

		return NULL;
	}


	/**
	 * Makes sure a url is absolute.
	 */
	private static function make_absolute($url, $base) 
	{
		if(! $url) 
			return $base;

		if(parse_url($url, PHP_URL_SCHEME) != '') 
			return $url;
			
		if($url[0] == '#' || $url[0] == '?') 
			return $base.$url;
			
		extract(parse_url($base));

		if( ! isset($path)) 
			$path = '/';
	 
		$path = preg_replace('#/[^/]*$#', '', $path);
	 
		if($url[0] == '/') 
			$path = '';
		   
		$abs = "$host$path/$url";
	 
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {}
			
		return $scheme.'://'.$abs;
	}
}
?>