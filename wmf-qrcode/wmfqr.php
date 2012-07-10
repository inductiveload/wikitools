<?php
/*
 * PHP QR Code encoder for WikiMedia Foundation sites.
 * Other domains will be rejected.
 * Non-URL data will be rejected.
 *
 * This tool is distrubuted under the GPLv3
 * PHP QR Code is distributed under LGPL 3
 * 
 */
 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/logging.php');

    //domains that we will allow QR codes to point to
    $PERMITTED_HOSTS = array("wikisource.org", "toolserver.org", "wikipedia.org", "wikibooks.org", "wikispecies.org",
        "wikimedia.org", "wiktionary.org", "mediawiki.org", "wikiversity.org", "wikiquote.org", "wikinews.org");

    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

    include "qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);

    $errorCorrectionLevel = 'L';
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H'))) {
        $errorCorrectionLevel = $_REQUEST['level'];
    }

    $matrixPointSize = 4;
    if (isset($_REQUEST['size'])) {
        $matrixPointSize = filter_input(INPUT_GET, 'size', FILTER_SANITIZE_NUMBER_INT);
        $matrixPointSize = min(max((int)$matrixPointSize, 1), 10);
    }

    if (!isset($_REQUEST['data'])) {
        include('wmfqr-manual.php');
        exit();
    }

    $data = $_REQUEST['data'];
    $data = str_replace(' ', '_', $data);
    $data = filter_var($data, FILTER_VALIDATE_URL);
    
    if ($data !== false) {
        $parts = parse_url($data);
        
        $host_parts = explode(".", $parts['host']);
        //get the last two bits of the host domain (ignore subdomains)
        $host_domain = $host_parts[count($host_parts)-2] . "." . $host_parts[count($host_parts)-1];
        if (!in_array($host_domain, $PERMITTED_HOSTS)) {
            die("Illegal host domain: $host_domain. URL must be in a Wikimedia project domain.");
        }
    } else {
        die('Invalid URL provided: ' . $_REQUEST['data']);
    }

    // user data
    $filename = $PNG_TEMP_DIR.'cache'.md5($data.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';

    if (!file_exists ($filename )) {
        QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    \toolserver_logging\Logging::log('qrcodes', "$data");
    
    //display generated file
    header('content-type: image/png');
    echo file_get_contents($PNG_TEMP_DIR.basename($filename));




