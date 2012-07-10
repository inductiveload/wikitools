<?php
namespace toolserver_page_inclusions;

class Inclusions 
{

	function header(){
		
		global $tsindl_root;
	
		if (True) { ?>
			<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
			<link href="<?php echo $tsindl_root?>utils/style.css" type='text/css' rel='stylesheet'>
			<link rel="shortcut icon" href="<?php echo $tsindl_root?>/images/Toolserver%20logo%20modified.ico" />
		<?php }
	}
	
	function sidebar(){
		global $tsindl_root;
		
		if (True) { ?>
			<div id="tools-sidebar">
				<img src='<?php echo $tsindl_root?>images/Toolserver_IL-123.png'/>
				<h4>Inductiveload's tools</h4>
				<h5>Wikimedia</h5>
				<ul>
					<li><a href="<?php echo $tsindl_root?>wmf-qrcode">QR codes</a></li>
					<li><a href="<?php echo $tsindl_root?>template-finder">Template finder</a></li>
				</ul>
				
				<h5>Other</h5>
				<ul>
					<li><a href="<?php echo $tsindl_root?>regex-tester">Regex tester</a></li>
				</ul>
			</div>
		<?php }
	}
	
	function footer($extras=''){
		global $tsindl_root;
		
		if (True) { ?>
			<div id='tools-footer'>
			<a class='link-button' href='//toolserver.org'><img src='../images/wikimedia-toolserver-button.png' alt='About this server' title='About this server'/></a>
			<a class='link-button' href='//www.gnu.org/licenses/gpl-3.0.html'><img src='../images/gplv3-button.png' alt='GPLv3' title='GPLv3'/></a>
		<?php } 

		echo ($extras?$extras."<br>":'');
	
		if (True) { ?>
			This tool is maintained by <a href='//en.wikisource.org/wiki/User:Inductiveload'>Inductiveload</a>. View <a href='//github.com/inductiveload/wikitools'>source code of this tool</a> or <a href='//github.com/inductiveload/wikitools/issues'>report issues and make suggestions</a>.<br>
			This code is open-source, distributed under the <a href='//www.gnu.org/licenses/gpl-3.0.html'>GPLv3</a>.<br>
			</div>
		<?php } 
	}
	
}
?>
