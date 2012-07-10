<?php
//Collection of DB handling functions for use on the Toolserver
namespace toolserver_db_utils;

use PDO;

class Utils 
{
	function get_namespace_array($tsdb, $dbname){
	//Get an associative array of the preferred NS names for each ID in a
	//given wiki:
	//* $tsdb: PDO database connection for the toolserver metadatabase
	//* $dbname: the name of the database for the wiki concerned, eg. enwikisource_p
		
		$stmt = $tsdb->prepare("SELECT ns_id, ns_name from namespacename where dbname=:dbname AND ns_is_favorite=1");
		$stmt->bindParam(":dbname", $dbname);
		$stmt->execute();
		
		$namespaces = array();
		
		while ($row = $stmt->fetch()) {
			$namespaces[$row['ns_id']] = $row['ns_name'];
		}
		
		return $namespaces;
	}

	function get_toolserver_db(){
	//Return a PDO connection opject to the Toolserver project meta-database

		$dbname = "toolserver";
		$database_host = "sql-toolserver";
		require('/home/'.get_current_user().'/database.inc');
		
		try {
			$db = new PDO("mysql:host=$database_host;dbname=$dbname", $toolserver_username, $toolserver_password);
		} catch (Exception $e) {
			die("Error opening wiki list meta-database.");
		}	
		return $db;
	}

	function get_wiki_db($domain, $tsdb){
	//Get a database connection for a given domain
	//* $domain: the domain hosting the wiki of interest, eg. en.wikisource.org
	//* $tsdb: PDO database connection for the toolserver metadatabase

	//Output:
	//* $dbname: the name of the database, eg, enwikisource_p
	//* $db: the PDO database connection

		$stmt = $tsdb->prepare("SELECT dbname FROM wiki WHERE domain=:dmn;");
		$stmt->bindParam(":dmn", $domain);
		$stmt->execute();
		
		if ($stmt->rowCount() > 0){
			$row = $stmt->fetch();
			$dbname = $row['dbname'];
		} else {
			die("Couldn't indentify the database name for the domain $domain.");
		}
		
		require('/home/'.get_current_user().'/database.inc');
		$database_host = str_replace('_', '-', $dbname).".db.toolserver.org";
		try {
			$db = new PDO("mysql:host=$database_host;dbname=$dbname", $toolserver_username, $toolserver_password);
		} catch (Exception $e) {
			die("Error opening database: $dbname");
		}	
		return array($dbname, $db);
	}
}
?>
