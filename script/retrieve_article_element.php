<html>

	<head>
		<title>Information Gathered</title>
	</head>
	
	<body>
				
		<?php
			include('../lib/article_crawler.class.php');
			$url = $_POST['url'];
			
			echo "<p>Information Retrieved as follows: </p>";
			
			try
			{
				$finder = new ArticleCrawler($url);
				
				$headline = $finder->get_headline();
				$story = $finder->get_story();
				$images = $finder->get_images();

				echo '<b>Headline : </b>'.$headline.'</p>';
				echo '<b>Stroy : </b>'.$story.'</p>';
				
				echo '<b>Images : </b></p>';
				for($x=0;$x < count($images);$x++)
				{
					echo "<img src=\"".implode("",$images[$x])."\">"."</br>";
				}
			}
			catch(Exception $ex)
			{
				echo $ex->getMessage();
			}
		?>
		
	</body>
	
</html>