# CNN_Article_Crawler

Create a PHP web application that accepts the URL of a cnn.com article page and spits out the following:
	1) Headline
	2) Photo (if one exists)
    3) Story
	
Make sure to strip out all HTML tags as the story should be text-only. Also, adhere to the following rules:
    1) must use class structures
    2) must have constructor
    3) it is ok to use native php stuff but no obvious 3rd party libraries please
	
* 'article_crawler' class is developed to retrieve these element of a CNN article.
* 'retrieve_article_element' is a php script which uses the 'article_crawler' class to get headline, story and images from an cnn article.