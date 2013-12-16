<?php 
require_once('../application/application-files.php');

$serverContext = new ApacheServerContext();
$config = new ZeitfadenConfig($_SERVER['HTTP_HOST']);

$application = new ZeitfadenApplication($config);

$response = $application->run($serverContext);

if ($response->isEnabled())
{
	$serverContext->sendResponse($response);
}








