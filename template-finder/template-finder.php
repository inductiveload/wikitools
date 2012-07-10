<?php
/*
 * Template Finder - a tool to display the usages of templates in a 
 * range of pages.
 *
 * This tool is distributed under the GPLv3
 * 
 * Written by: Inductiveload, July 2012
 * 
 */

$tsindl_root = '../';
set_include_path( $tsindl_root );

include_once('utils/inclusions.php');
include_once('utils/db_utils.php');
include_once('utils/logging.php');

function tsindl_getnsandtitle($fulltitle, $dbname, $tsdb){
//Break a full title down into a namespace ID and a title component.
//* $fulltitle: the input page title, with optional namespace component, eg. User:Foobar
//* $dbname: the name of the database for the wiki concerned, eg. enwikisource_p
//* $tsdb: PDO database connection for the toolserver metadatabase

//Output:
//* $ns: The namespace ID of the input (2)
//* $title: The title of the input: (Foobar)

	$is_namespace = False;
	$parts = explode(':', $fulltitle, 2);
	
	if (count($parts) > 1){
		$ns_candidate = $parts[0];
		
		$stmt = $tsdb->prepare("SELECT * from namespacename where dbname=:dbname AND ns_name=:candidate");
		$stmt->bindParam(":dbname", $dbname);
		$stmt->bindParam(":candidate", $ns_candidate);
		$stmt->execute();
		
		if ($stmt->rowCount() > 0){
			$row = $stmt->fetch();
			$ns = $row['ns_id'];
			$title = $parts[1];
			$is_namespace = True;
		} 
	}
	
	if (!$is_namespace) { #mainspace
		$ns = 0;
		$title = $fulltitle;
	}
	
	return array($ns, $title);
}

function tsindl_getprefixedpages($ns_id, $ns_name, $title, $db){
//Get a list of pages that begin with a certain prefix
//* $ns_id: The ID of the prefix namespace (eg. 2)
//* $ns_name: The name of the prefix namespace, used to construct a "real" title (eg User)
//* $title: The title component (eg. Foobar)
//* $db: PDO connection to that wiki's database

//Returns:
//* $pagelist: list of page with this prefix in the format { User:Foobar => {id=>314}, .... }

	#get a wiki's DB object from its domain
	$prefix = $title.'%';
	
	$stmt = $db->prepare("SELECT page_id, page_title FROM page WHERE page_title LIKE :prefix AND page_namespace=:ns; LIMIT 1000");
	$stmt->bindParam(":prefix", $prefix);
	$stmt->bindParam(":ns", $ns_id);
	$stmt->execute();
	
	$pagelist = array();
	
	while ($row = $stmt->fetch()) {
		$pagelist[($ns_name?$ns_name.':':'').$row['page_title']] = array('id'=>$row['page_id']);
	} 
	return $pagelist;
}

function tsindl_getcategorypages($category, $namespaces, $db){
//Get a list of pages that begin with a certain prefix
//* $category: the title of the category, without the NS prefix
//* $db: PDO connection to that wiki's database

//Returns:
//* $pagelist: list of page with this prefix in the format { User:Foobar => {id=>314}, .... }
	$stmt = $db->prepare("SELECT cl_from FROM categorylinks WHERE cl_to=:cat LIMIT 1000");
	$stmt->bindParam(":cat", $category);
	$stmt->execute();
	
	$pagelist = array();
	
	while ($row = $stmt->fetch()) {
		
		$innerStmt = $db->prepare("SELECT page_id, page_namespace, page_title FROM page WHERE page_id=:id");
		$innerStmt->bindParam(":id", $row['cl_from']);
		$innerStmt->execute();
		
		while ($page_record = $innerStmt->fetch()) {
			$pagelist[($page_record['page_namespace']?$namespaces[$page_record['page_namespace']].':':'').$page_record['page_title']] = array('id'=>$page_record['page_id']);
			break;
		}
	} 
	return $pagelist;
}

function tsindl_get_templateusages($pagelist, $wikidb, $namespaces){
//Get a list of templates used in a set of pages.
//* $pagelist: list of pages to check
//* $wikidb: PDP DB connection for the wiki in question
//* $namespaces: associative array of ID->names for that wiki

//Output:
//* $templates: a list of the templates used. Each template appears once only.
//* $pagelist: modified pagelist array which has a template field added to each
//    page holding a list of the templates on that page.
	
	$templates = array();
	
	foreach($pagelist as &$page){
		
		$stmt = $wikidb->prepare("SELECT tl_namespace, tl_title FROM templatelinks WHERE tl_from=:id;");
		$stmt->bindParam(":id", $page['id']);
		$stmt->execute();
		
		$page['templates'] = array();
	
		while ($row = $stmt->fetch()) {
			$template = ($row['tl_namespace']?$namespaces[$row['tl_namespace']].':':'').$row['tl_title'];
			
			if (!in_array($template, $templates)){
				$templates[] = $template;
			}
			
			if (!in_array($template, $page['templates'])){
				$page['templates'][] = $template;
			}
		} 
	}
	
	return array($templates, $pagelist);
	
}

//abort and divert to the manual if no parameters are given
if (count($_GET) === 0 ) {
	include('template-finder-manual.php');
	exit();
}

//SANITISE AND VALIDATE INPUTS
if (isset($_GET['wiki']) ) {
	//only allow WMF projects
	if (in_array($_GET['wiki'], array('wikipedia', 'wikisource', 'wikibooks', 'wikinews', 'wikiquote', 'wiktionary', 'wikiversity', 'wikimedia', 'mediawiki'))){
		$wiki = $_GET['wiki'];
	} else {
		die("Invalid wiki: {$_GET['wiki']}.");
	}
} else {
	die("You must provide a wiki, for example 'wikisource'.");
}

if (isset($_GET['lang']) ) {
	if (preg_match("/^[a-z]*$/", $_GET['lang'])){
		$lang = $_GET['lang'];
	} else {
		die("Invalid subdomain: {$_GET['lang']}.");
	}
} else {
	$lang = '';
}

if (!isset($_GET['prefix']) && !isset($_GET['category']) ) {
	die("You must provide one of 'prefix' or 'category'.");
}

if (isset($_GET['prefix']) && isset($_GET['category'])){
	die("Do not provide both prefix and category.");
}

if (isset($_GET['prefix'])){
	$prefix = str_replace(' ', '_', $_GET['prefix']);
} elseif (isset($_GET['category'])){
	$category = str_replace(' ', '_', $_GET['category']);
}

$display_style = 'matrix'; //default
if (isset($_GET['display'])){
	if (in_array($_GET['display'], array('table', 'matrix'))){
		$display_style = $_GET['display'];
	}
}
##FINISHED SANITISING AND VALIDATING!

#construct the domain
$base_url = $lang.($lang?'.':'').$wiki.'.org';

$tsdb = \toolserver_db_utils\Utils::get_toolserver_db();
list($dbname, $wikidb) = \toolserver_db_utils\Utils::get_wiki_db($base_url, $tsdb);

$namespaces = \toolserver_db_utils\Utils::get_namespace_array($tsdb, $dbname);

if (isset($prefix)){
	list($prefixns, $prefixtitle) = tsindl_getnsandtitle($prefix, $dbname, $tsdb);
	
	$pagelist = tsindl_getprefixedpages($prefixns, $namespaces[$prefixns], $prefixtitle, $wikidb);
} elseif (isset($category)){
	$pagelist = tsindl_getcategorypages($category, $namespaces, $wikidb);
}

list($templates, $pagelist) = tsindl_get_templateusages($pagelist, $wikidb, $namespaces);

#sort sensibly
uksort( $pagelist, 'strnatcmp');
natsort( $templates);

##WE HAVE ALL OUR DATA - DISPLAY IT! ##
?>


<!DOCTYPE html>
<html>
	<head>
		<?php \toolserver_page_inclusions\Inclusions::header(); ?>
		<title>Template Finder - Inductiveload's tools</title>
		
		<?php
		if ($display_style == 'matrix'){
			echo "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>";
		}?>
		
		<style type="text/css">
			.pagebox {
				float:left;
				text-align:center;
				margin:2px 2px 2px 2px;
				min-width:3em;
				background-color:#dddddd;
			}
			
			.pagebox > a {
				color:#111111 !important;
				text-decoration:none;
			}

			.templatepresent {
				background-color:#42ae82 !important;
			}
			
			select#template_select {
				min-width:20em;
			}
		</style>
		<script type="text/javascript">
			$(document).ready(function() {
				
				function update_template_display() {
					$('.templatepresent').removeClass('templatepresent');
					
					var template = $("#template_select option:selected").text();
					
					for (var page in window.template_usage_by_page){
						
						if ($.inArray( template, window.template_usage_by_page[page]['templates'] ) > -1 ){
							//console.log(window.template_usage_by_page[page]['id']);
							$('#page'+window.template_usage_by_page[page]['id']).addClass('templatepresent');
						}
					}
				}
				
				
				$('#template_select').change(function(){ 
					update_template_display();
				});

				update_template_display();
			});
		</script>
	</head>

	<body>
		<?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
		
		<div id="main-content">
			<div class="navlink">← <a href="template-finder.php">Template Finder</a></div>
			<h1>Wikimedia Template Finder</h1>

<?php

if (isset($prefix)){
	echo "This page displays the templates present on page at $base_url which begin with '<a href='http://$protocol$base_url/w/index.php?title=Special%3APrefixIndex&prefix=".urlencode($prefix)."'>$prefix</a>. ";
} elseif (isset($category)){
	echo "This page displays the templates present on page at $base_url which are in the category '<a href='http://$base_url/wiki/{$namespaces[14]}:".urlencode($category)."'>$category</a>'. ";
}

echo 'Pages which contain the selected template are shown with a <span class="templatepresent">green background</span>.<br><br>';

if ($display_style == 'matrix'){
	
	#template list
	echo "Template: <select id='template_select'>";
	
	
	foreach($templates as $template){
		echo "<option value='$text'>".$template."</option>";
	}
	echo "</select><br><br>";

	echo "<div>";
	foreach($pagelist as $page=>$info){
		echo "<div class='pagebox' id=page{$info['id']}>";
		
		
		$title = str_replace($prefix, '', $page);
		if ($title[0] !== '/') {
			$title = $page;
		}
		
		$title = str_replace('_', ' ', $title);
		
		echo "<a href='http://$base_url/wiki/$page' title='".htmlentities($page, ENT_QUOTES, 'UTF-8')."'>".$title."</a>   ";
		echo "</div>";
	}
	echo "</div>";
	
	#load info into the JS
	echo "<script type='text/javascript'>\nwindow.template_usage_by_page = ";
	echo json_encode($pagelist);
	echo "\n</script>";
}

?>

		<?php \toolserver_page_inclusions\Inclusions::footer(); ?>
		</div>
	</body>

</html> 

<?php 
	\toolserver_logging\Logging::log('template-finder', ($base_url."\t".(isset($prefix)?"Prefix: $prefix":"Category:$category")));
?>

