<?php
namespace toolserver_logging;

class Logging{

    function log($logname, $msg){
        
        $logdir = dirname(__DIR__).DIRECTORY_SEPARATOR.'logs';
        
        if (!is_dir($logdir)){
            mkdir($logdir);
        }
        
        $filename = $logdir.DIRECTORY_SEPARATOR.$logname.'.log';
        $mode = file_exists($filename)?"a":"w";
        $logfile = fopen($filename, $mode);
        $logmsg = "[" . date("d/m/Y h:i:s", mktime()) . "] $msg";
        fwrite($logfile, $logmsg . "\n");
        fclose($logfile);
    }
}
?>
