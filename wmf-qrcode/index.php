<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>QR Generator</title>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<h1>QR Code Generator</h1>
			This is a tool to generate <a href="//en.wikipedia.org/wiki/QR%20code">QR codes</a> for Wikimedia Foundation sites.
			
			<ul>
				<li><a href="wmfqr.php">Main generator</a>. This page can generate a QR code for any URL on a Wikimedia domain, from Wikipedia to the Toolserver.</li>
				<li><a href="ws-qrcodes.php">Wikisource book links</a>. This tool generates links to a book at Wikisource in EPUB, as well as a direct link.</li>
			</ul>
		
		<?php \toolserver_page_inclusions\Inclusions::footer("This tool uses <a href='http://phpqrcode.sourceforge.net'>phpqrcode</a> (LGPL) to generate the QR code images."); ?>
		</div>
	</body>

</html> 
