<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>Wikisource QR Code Generator - Inductiveload's tools</title>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<div class="navlink">← <a href="index.php">QR codes</a></div>
			<h1>Wikisource QR Code Generator</h1>
			
			
			This is a tool to generate <a href="//en.wikipedia.org/wiki/QR%20code">QR codes</a> for pages at Wikisources. It provides a range of links, currently:
			<ul>
				<li>A direct link to the page at Wikisource.</li>
				<li>EPUB: A link to an EPUB, generated from the given page using the WS-export tool. EPUBs with and without images are provided.</li>
			</ul>
			
			<h2>Parameters</h2>
			<ul>
				<li><span class="param">title</span>: This is the title of a page on a Wikisource. It can be any page, in any namespace.</li>
				<li><span class="param">lang</span>: The language of the Wikisource, for example "en" for English. No lang parameter will result in a link to the multilingual Wikisource.</li>
			</ul>
			
			<h2>Examples</h2>
			<ul>
				<li>QR codes for a work (<a href="//en.wikisource.org/wiki/Bull-dog%20Drummond">Bull-dog Drummond</a>) at English Wikisource. This is an "uncurated" export, which means WS-export will try to ascertain the structure of the book automatically. This works almost always for simple books.<br>
				<pre>
<a href="ws-qrcodes.php?lang=en&amp;title=Bull-dog%20Drummond">ws-qrcodes.php?lang=en&amp;title=Bull-dog%20Drummond</a></pre></li>
				<li>QR codes for a curated book (<a href="//en.wikisource.org/wiki/User:Inductiveload/Books/Bull-dog%20Drummond">User:Inductiveload/Books/Bull-dog Drummond</a>) at English Wikisource:<br>
				<pre>
<a href="ws-qrcodes.php?lang=en&amp;title=User:Inductiveload/Books/Bull-dog%20Drummond">ws-qrcodes.php?lang=en&amp;title=User:Inductiveload/Books/Bull-dog%20Drummond</a></pre></li>
				<li>QR codes for a work (<a href="//en.wikisource.org/wiki/Bull-dog%20Drummond">Die liefde is lankmoedig</a>) at multilingual Wikisource:<br>
				<pre>
<a href="ws-qrcodes.php?title=Die%20liefde%20is%20lankmoedig">ws-qrcodes.php?title=Die liefde is lankmoedig</a></pre></li>
			</ul>
		
		<?php \toolserver_page_inclusions\Inclusions::footer("This tool uses <a href='http://phpqrcode.sourceforge.net'>phpqrcode</a> (LGPL) to generate the QR code images."); ?>
		</div>
	</body>

</html> 
