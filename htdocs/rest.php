<?php 
require_once('../application/application-files.php');

$serverContext = new ApacheServerContext();
//$config = new ZeitfadenConfig($_SERVER['HTTP_HOST']);

$config = false;
$application = new ZeitfadenApplication($config);

$response = $application->runRestful($serverContext);

if ($response->isEnabled())
{
    $serverContext->sendResponse($response);
}








