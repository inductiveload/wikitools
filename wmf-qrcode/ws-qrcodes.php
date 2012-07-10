<?php 
$tsindl_root = '../';
set_include_path( $tsindl_root ); 

include_once('utils/logging.php');
include_once('utils/inclusions.php');

/*
 * PHP QR Code encoder for Wikisource books. This is a static
 * alternative to Javascript.
 */
    //get the in/secure protocol
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {
        $protocol = "https://";
    } else {
        $protocol = "http://";
    }

    //set it to writable location, a place for temp generated PNG files
    $TEMP_NAME = 'temp'.DIRECTORY_SEPARATOR;
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.$TEMP_NAME;

    include "qrlib.php";

    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);

    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';

    $matrixPointSize = 4;

    if (!isset($_REQUEST['title'])) {
        include('ws-qrcodes-manual.php');
        exit();
    } else {
        #/^[!#$&-;=?-[]_a-z~]+$/
        $title = urlencode($_REQUEST['title']); //make sure everything is escaped
        
        $displayTitle = str_replace('_', ' ', htmlspecialchars(urldecode($title)));
    }
    
    if (isset($_REQUEST['lang'])) {
        $lang = $_REQUEST['lang'];
    } else {
        $lang = '';
    }
    
    $wsURL = ($lang?$lang.'.':'')."wikisource.org";
    
    $urls = array(
        "EPUB with images"=>$protocol."toolserver.org/~tpt/wsexport/book.php?lang=$lang&page=$title&format=epub",
        "EPUB without images"=>$protocol."toolserver.org/~tpt/wsexport/book.php?lang=$lang&page=$title&format=epub&images=false",
        "Book page"=>$protocol.$wsURL."/wiki/$title",
        );
    ?>

<!DOCTYPE html>
<html>
<head>
    <?php \toolserver_page_inclusions\Inclusions::header(); ?>
    <title><?php echo "$displayTitle @ ".($lang?$lang:'old').".wikisource - QR codes";?></title>
</head>
<body>
    <?php \toolserver_page_inclusions\Inclusions::sidebar(); ?>
    <div id="main-content">
        <div class="navlink">← <a href="index.php">QR codes</a></div>
    <?php
    
    echo "<h1>QR codes for \"<a href='".$urls['Book page']."'>$displayTitle</a>\" at ".($lang?$lang:'old').".wikisource</h1>";
    echo "Scan the relevant QR code below with your mobile device to get a direct link to the book.";
    echo "<table>";
        
    foreach ($urls as $desc => $url) {
        // user data
        $filename = $PNG_TEMP_DIR.'cache'.md5($url.'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';

        if (!file_exists ($filename )) {
            QRcode::png($url, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
        }

        //display generated file
        echo "<tr><td><a href='$url'>$desc</a></td><td><img title='$desc' src='".$TEMP_NAME.basename($filename)."'/></td></tr>";
    }
    
    \toolserver_logging\Logging::log('qrcodes-ws', ($lang?$lang.':':'').$displayTitle);
    ?>
    
    </table>

		<?php \toolserver_page_inclusions\Inclusions::footer("This page provides a static source for QR codes to Wikisource texts for those without Javascript. It uses <a href='http://phpqrcode.sourceforge.net'>phpqrcode</a> (LGPL) to generate the QR code images."); ?>
    </div>
    </div>
</body>
</html>




