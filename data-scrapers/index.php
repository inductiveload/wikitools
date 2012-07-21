<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>Data Scrapers</title>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<h1>Data Scrapers</h1>
			These are tools used to collect data from the web and present it in a format suitable for WMF wikis.
			
			<ul>
				<li><a href="ia-citer.php">Internet Archive Citer</a>. This tool takes an Internet Archive identifier and makes it into a string suitable for a portal or author page at Wikisource.</li>
			</ul>
		
		<?php \toolserver_page_inclusions\Inclusions::footer(); ?>
		</div>
	</body>

</html> 
