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

//require_once($baseDir.'/../my-frameworks/brokenpottery/brokenpottery.php');

//require_once($baseDir.'/query-engine/ZeitfadenQueryEngine.php');
//require_once($baseDir.'/query-engine/context/Assembly.php');
//require_once($baseDir.'/query-engine/context/Handler.php');
//require_once($baseDir.'/query-engine/context/Interpreter.php');


require_once($baseDir.'/ZeitfadenExceptions.php');
//require_once($baseDir.'/TimeService.php');
//require_once($baseDir.'/ZeitfadenRouter.php');
require_once($baseDir.'/ZeitfadenApplication.php');




require_once($baseDir.'/AbstractZeitfadenController.php');
//require_once($baseDir.'/ZeitfadenFrontController.php');


require_once($baseDir.'/controller/ImageController.php');
require_once($baseDir.'/controller/VideoController.php');



require_once($baseDir.'/model/fly-image/fly-image-service.php');
require_once($baseDir.'/model/fly-video/fly-video-service.php');




//require_once($baseDir.'/ZeitfadenUUID.php');


