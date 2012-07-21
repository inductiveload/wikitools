<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/inclusions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<?php 
			\toolserver_page_inclusions\Inclusions::header(); 
			\toolserver_page_inclusions\Inclusions::jQuery();
			\toolserver_page_inclusions\Inclusions::JS('jsr_class.js');
		?>
		<script type="text/javascript" src="ia-citer.js"></script>
		
		<title>Internet Archive Citer</title>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<h1>Internet Archive Citer</h1>
		
		<div>
		Use this tool to create entries for an Author: or Portal: page. Enter the Internet Archive ID (<em>e.g.</em> "rembrandt00weir") in the box below and press "Execute".
		</div>
			
		<div class="inputarea">
			<table>
				<tr>
					<td class="setting">Internet Archive ID:</td>
					<td><input id="ia_id" name="ia_id" type="text" value="rembrandt00weir" /></td>
				</tr><tr>
					<td class="setting">Disambiguate:</td>
					<td><input type="checkbox" id="disambiguate" checked="false" /></td>
				</tr><tr>
					<td class="setting">Include author:</td>
					<td><input type="checkbox" id="includeauthor" checked="false" /></td>
				</tr>
			</table>
			
			<button type="button" onClick="ia_citer_execute()">Execute</button>
		</div>
		
		<div id="outputarea" style="font-family:monospace">
			
		</div>
		<?php \toolserver_page_inclusions\Inclusions::footer(); ?>
    </div>
    </div>
</body>
</html>
