<?php

$isToolserver = in_array(gethostname(), array('nightshade', 'willow'));

if ($isToolserver) {
	mysql_connect('enwikisource-p.db.toolserver.org', $toolserver_username, $toolserver_password);
	@mysql_select_db('enwikisource_p') or die(mysql_error());
} else {
	//mysql_connect('localhost', '', '');
	//@mysql_select_db('enwikisource') or die(mysql_error());
	$database_host = 'localhost';
	$database = 'enwikisource';
	$toolserver_username = '';
	$toolserver_password = '';
}

try {
	$db = new PDO("mysql:host=$database_host;dbname=$database", $toolserver_username, $toolserver_username);
} catch (Exception $e) {
    die("Error opening database: $database");
}
unset($toolserver_username, $toolserver_password, $database_host);

$templateName = 'Ts';
$wantedParameter = 'ac';
$foundInstances = array();

$stmt = $db->prepare("SELECT old_id, old_text FROM text WHERE old_id IN (SELECT tl_from FROM templatelinks WHERE tl_title=:template);");
$stmt->bindParam(":template", $templateName);
$stmt->execute();
 
while ($row = $stmt->fetch()) {
	$foundInstanceHere = False;
	    
	$m = preg_match_all("/\\{\\{ *$templateName *\|(.*?)\\}\\}/i", $row['old_text'], $matches, PREG_SET_ORDER);

	if (!$m) {
		continue;
	}
	
	foreach ($matches as $match) {
		
		if ($foundInstanceHere){
			break;
		}
		
		$parameters = explode('|', $match[1]);
		
		foreach ($parameters as $parameter) {
			if ($parameter === $wantedParameter){
				array_push($foundInstances, $row['old_id']);
				$foundInstanceHere = True;
				break;
			}
		}
	}
}

print_r($foundInstances);

?>
