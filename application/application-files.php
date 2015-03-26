<?php 
error_reporting(E_ALL);

function exception_error_handler($errno, $errstr, $errfile, $errline ) 
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

date_default_timezone_set('Europe/Berlin');



$baseDir = dirname(__FILE__);




require_once($baseDir.'/../my-frameworks/sugarloaf/lib/sugarloaf.php');
require_once($baseDir.'/../my-frameworks/tiro-php-profiler/src/tiro.php');
require_once($baseDir.'/../my-frameworks/pivole-und-pavoli/src/pivole-und-pavoli.php');
require_once($baseDir.'/../my-frameworks/cached-image-service/src/require.php');

require_once($baseDir.'/ZeitfadenExceptions.php');
require_once($baseDir.'/ZeitfadenApplication.php');

require_once($baseDir.'/AbstractZeitfadenController.php');

require_once($baseDir.'/controller/ImageController.php');
require_once($baseDir.'/controller/VideoController.php');

