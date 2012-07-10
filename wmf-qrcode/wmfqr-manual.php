<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>Wikimedia QR Code Generator - Inductiveload's tools</title>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<div class="navlink">← <a href="index.php">QR codes</a></div>
			<h1>Wikimedia QR Code Generator</h1>
			
			
			This is a tool to generate <a href="//en.wikipedia.org/wiki/QR%20code">QR codes</a> for a page on a Wikimedia Foundation project page. Supported domains are: Wikipedia, Wikisource, Wikinews, Wikiquote, Wiktionary, Wikibooks, Wikiversity, Wikispecies, Toolserver and any Wikimedia domain, including Commons.
			
			<h2>Parameters</h2>
			<ul>
				<li><span class="param">data</span>: This is the data to encode. It can be any URL for a supported domain.</li>
				<li><span class="param">level</span>: The error-correction level of the QR code. Must be one of 'L','M','Q', or 'H' (low, medium, quartile and high, which can correct 7, 15, 25 or 30% of the data). Optional, the default is 'L'.</li>
				<li><span class="param">size</span>: The size of each square of the QR code. Optional, the default is 4 pixels.</li>
			</ul>
			
			<h2>Examples</h2>
			<ul>
				<li>QR code for an article (<a href="//en.wikisource.org/wiki/QR_code">QR code</a>) at English Wikipedia:<br>
				<pre>
<a href="wmfqr.php?data=http://en.wikipedia.org/QR_code">wmfqr.php?data=http://en.wikipedia.org/QR_code</a></pre></li>
				
				<img src="ExampleQR.png"/>
			</ul>
			
			<h2>Generator</h2>
			You can use this form to generate a QR code image.</br></br>
			
			<form style="padding-left:1em;">
				
			URL:
			<input type="text" style="width:50em;" name="data" value="http://en.m.wikipedia.org/QR code" /><br>
			
			Error-correction level: 
			<select name="level">
				<option value="L">L (7% reconstruction)</option>
				<option value="M">M (15% reconstruction)</option>
				<option value="Q">Q (25% reconstruction)</option>
				<option value="H">H (30% reconstruction)</option>
			</select><br>
			
			Size of squares: 
			<select name="size">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4" selected="selected">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
			</select>px <br>
			
			<input type="submit" value="Submit" />
			</form>
		
		<?php \toolserver_page_inclusions\Inclusions::footer("This tool uses <a href='http://phpqrcode.sourceforge.net'>phpqrcode</a> (LGPL) to generate the QR code images."); ?>
		</div>
	</body>

</html> 
