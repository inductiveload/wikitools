<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>Template Finder - Inductiveload's tools</title>
		
		<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<h1>Wikimedia Template Finder</h1>
			
			This is a tool to show which templates are used in a range of pages on a wiki. This is useful if you want to see patterns in the use of templates in a set of pages, or when changing templates over.
			
			<h2>Parameters</h2>
			<ul>
				<li><span class="param">lang</span>: A language code, (e.g. "en"). Optional if not using a subdomain.</li>
				<li><span class="param">wiki</span>: A wiki project (e.g. "wikisource"). Cannot be blank.</li>
				<li>Generators (limited to 1000 results):
					<ul>
						<li><span class="param">prefix</span>: This is the prefix of the pages to check.</li>
						<li><span class="param">category</span>: This is the category of the pages to check.</li>
					</ul>
				</li>
				<li><span class="param">display</span>: How to display the results. Choose "matrix" or "table" (not yet implemented). Leaving it out will produce "matrix".</li>
			</ul>
			
			<h2>Examples</h2>
			<ul>
				<li>Matrix view for pages on English Wikisource beginning with "Page:Elementary_Chinese_-_San_Tzu_Ching_(1900).djvu":<br>
				<pre>
<a href="template-finder.php?lang=en&wiki=wikisource&prefix=Page:Elementary_Chinese_-_San_Tzu_Ching_(1900).djvu&amp;display=matrix">template-finder.php?lang=en&wiki=wikisource&prefix=Page:Elementary_Chinese_-_San_Tzu_Ching_(1900).djvu&amp;display=matrix</a></pre></li>

				<li>Matrix view for pages on French Wikipedia beginning in "Category:Montagne_d'Irlande":<br>
				<pre>
<a href="template-finder.php?lang=fr&wiki=wikipedia&category=Montagne_d'Irlande&amp;display=matrix">template-finder.php?lang=fr&wiki=wikipedia&category=Montagne_d'Irlande&amp;display=matrix</a></pre></li>

			</ul>
			
			<h2>Generator</h2>
			You can use this form to generate the link to a set of results.
			
			<form class="inputarea">
				
				<table class="form_table">
					<tr>
						<td>Wiki:</td>
						<td><input type="text" style="width:4em;" name="lang" value="en" />.
							<input type="text" style="width:10em;" name="wiki" value="wikisource" />.org</td>
					</tr><tr>
						<td>Page prefix: </td>
						<td><input type="radio" class="filterbutton" value="prefix" checked="checked"><input class="filterinput" type="text" style="width:50em;" name="prefix" value="Page:Elementary_Chinese_-_San_Tzu_Ching_(1900).djvu" /></td>
					</tr><tr>
						<td>Category:</td>
						<td><input type="radio" class="filterbutton" value="category"><input  class="filterinput" type="text" style="width:50em;" name="category" value="Works" disabled="disabled" /></td>
					</tr>
				</table>
			<input type="submit" value="Submit" />
			</form>
			
		<script type="text/javascript">
$(document).ready(function() {
	
	$('.filterbutton').change( function() {
		$('.filterbutton').prop('checked', false);
		$(this).prop('checked', true);
		
		$('.filterinput').attr('disabled', 'disabled');
		
		$('input:text[name='+$(this).val()+']').removeAttr('disabled');
		
	});

});
			
		</script>
		
		<?php \toolserver_page_inclusions\Inclusions::footer(); ?>
		</div>
	</body>

</html> 
