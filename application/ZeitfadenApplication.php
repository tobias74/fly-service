<?php 




abstract class ServerContext
{
	abstract function getRequest();
	abstract function startSession();
	abstract function updateSession($hash);
	abstract function sendResponse($response);
	
}


class ApacheServerContext extends ServerContext
{
	public function getRequest()
	{
		$request = new ZeitfadenRequest();
		$request->setRequest($_REQUEST);
		$request->setSession($_SESSION);
		$request->setServer($_SERVER);
		$request->setFiles($_FILES);
		return $request;
	}
	
	public function startSession()
	{
		session_start();
	}
	
	public function updateSession($hash)
	{
		foreach ($hash as $name => $value)
		{
			$_SESSION[$name] = $value;
		}
	}
	
  function sendZipped($contents)
  {
    $startTime = microtime(true);
    
      $HTTP_ACCEPT_ENCODING = isset($_SERVER["HTTP_ACCEPT_ENCODING"]) ? $_SERVER["HTTP_ACCEPT_ENCODING"] : '';
      if( headers_sent() )
          $encoding = false;
      else if( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false )
          $encoding = 'x-gzip';
      else if( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false )
          $encoding = 'gzip';
      else
          $encoding = false;
     
      if( $encoding )
      {
          header('Content-Encoding: '.$encoding);
          print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
          $contents = gzcompress($contents, 9);
          header('X-Zeitfaden-Zipping-Time: '.(microtime(true) - $startTime));
          print($contents);
      }
      else
      {
          print($contents);        
      }
  } 
	
	public function sendResponse($response)
	{
	  if ($response->isFile())
	  {
	    foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      readfile($response->getFileName());
	    
	  }
    else if ($response->isBytes())
    {
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      echo($response->getBytes());
      
    }
    else if ($response->isStream())
    {
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      
      http_send_stream($response->getStream());
      
    }
    else
    {
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Expose-Headers: FooBar');
      header('Access-Control-Allow-Headers: X-Requested-With');            
      foreach($response->getHeaders() as $header)
      {
        header($header['header'],$header['replace'],$header['code']);
      }
      $this->sendZipped(json_encode($response->getHash()));
      
    }
	}
	
}

use SugarLoaf as SL;

class ZeitfadenApplication
{
	
	const STATUS_OK = true;
	const STATUS_ERROR_NOT_LOGGED_IN = -10; 
	const STATUS_GENERAL_ERROR = -100; 
	const STATUS_EMAIL_ALREADY_TAKEN = -15;
	const STATUS_ERROR_INVALID_ACTION = -1001;
	const STATUS_ERROR_WRONG_INPUT = -5;
	const STATUS_ERROR_SOLE_NOT_FOUND = -5001; 
	
	public function __construct($config)
	{
		
		//$this->config = $config;
		
		$this->dependencyManager = SL\DependencyManager::getInstance();
		$this->dependencyManager->setProfilerName('PhpProfiler');
		$this->configureDependencies();
		
		$this->mySqlProfiler = $this->dependencyManager->get('SqlProfiler');
		$this->phpProfiler = $this->dependencyManager->get('PhpProfiler');

	}
	
	
	
	public function run($serverContext)
	{
		$serverContext->startSession();
		
		$request = $serverContext->getRequest();
		
		$response = new ZeitfadenResponse();
		
		$this->getRouteManager()->analyzeRequest($request);
		
		$frontController = new ZeitfadenFrontController();
		$frontController->setDependencyManager($this->dependencyManager);
		
		try
		{
			$frontController->dispatch($request,$response);
			$response->appendValue('status',ZeitfadenApplication::STATUS_OK);
			$response->appendValue('requestCompletedSuccessfully',true);
		}
		catch (ZeitfadenException $e)
		{
			$response->enable();
			$response->appendValue('status',$e->getCode());
			$response->appendValue('errorMessage',$e->getMessage());
			$response->appendValue('stackTrace',$e->getTraceAsString());
		}
		catch (ZeitfadenNoMatchException $e)
		{
			$response->appendValue('error', ZeitfadenApplication::STATUS_ERROR_SOLE_NOT_FOUND);
			$response->appendValue('errorMessage',$e->getMessage());
			$response->appendValue('stackTrace',$e->getTraceAsString());
		}
		
		$response->appendValue('profilerData',array(
			'phpProfiler'   => $this->phpProfiler->getHash(),
			'mysqlProfiler' => $this->mySqlProfiler->getHash()	
		));	
		
		
		$serverContext->updateSession($request->getSession());
		
		$service = $this->dependencyManager->get('ZeitfadenSessionFacade');
        $loggedInUser = $service->getLoggedInUser();
        
        if ($loggedInUser->getFacebookUserId() != false) 
        {
            $response->appendValue('isFacebookUser', true);
        } 
        else 
        {
            $response->appendValue('isFacebookUser', false);
        }        
        
        $response->appendValue('loginId', $loggedInUser->getId());
        $response->appendValue('loginEmail', $loggedInUser->getEmail());
        $response->appendValue('loginUserId', $loggedInUser->getId());
        $response->appendValue('loginUserEmail', $loggedInUser->getEmail());
        $response->appendValue('loginFacebookUserId', $loggedInUser->getFacebookUserId());
				
		
		
		return $response;
		
	}

	
	
	
	
	
    public function runRestful($serverContext)
    {
        //require_once('FirePHPCore/FirePHP.class.php');      
        $appTimer = $this->phpProfiler->startTimer('#####XXXXXXX A1A1-COMPLETE_RUN XXXXXXXXXXXX################');
        
        $serverContext->startSession();
        
        $request = $serverContext->getRequest();
        
        $response = new ZeitfadenResponse();
        


        // check for options-reuqest
        if ($request->getRequestMethod() === 'OPTIONS')
        {
          $appTimer->stop();
          
          $profilerJson = json_encode(array(
              'phpLog' => $this->phpProfiler->getHash(),
              'dbLog' => $this->mySqlProfiler->getHash()
          ));
          
          return $response;
        }        

        
        
        $this->getRouteManager()->analyzeRequest($request);
        
        $frontController = new ZeitfadenFrontController();
        $frontController->setDependencyManager($this->dependencyManager);
        
        try
        {
            $frontController->dispatch($request,$response);
        }
        catch (ZeitfadenException $e)
        {
            die($e->getMessage());
        }
        catch (ZeitfadenNoMatchException $e)
        {
            die($e->getMessage());
        }
        
        $appTimer->stop();
        
        $profilerJson = json_encode(array(
            'phpLog' => $this->phpProfiler->getHash(),
            'dbLog' => $this->mySqlProfiler->getHash()
        ));
        
        //header("ZeitfadenProfiler: ".$profilerJson);
        $response->addHeader("ZeitfadenProfiler: ".$profilerJson);
        
        $serverContext->updateSession($request->getSession());
        
        return $response;
        
    }
		
	
	
	public function getRouteManager()
	{
		$routeManager = new ZeitfadenRouteManager();
		

		$routeManager->addRoute(new ZeitfadenRoute(
			'/:controller/:action/*',
			array()
		));
		
		
		$routeManager->addRoute(new ZeitfadenRoute(
			'getUserById/:userId',
			array(
				'controller' => 'user',
				'action' => 'getById'
			)
		));

		$routeManager->addRoute(new ZeitfadenRoute(
			'getStationById/:stationId',
			array(
				'controller' => 'station',
				'action' => 'getById'
			)
		));

    $routeManager->addRoute(new ZeitfadenRoute(
        'getStationsByQuery/:query',
        array(
            'controller' => 'station',
            'action' => 'getByQuery'
        )
    ));

    $routeManager->addRoute(new ZeitfadenRoute(
        'getUsersByQuery/:query',
        array(
            'controller' => 'user',
            'action' => 'getByQuery'
        )
    ));
    		
    $routeManager->addRoute(new ZeitfadenRoute(
        'oauth/:action/*',
        array(
            'controller' => 'OAuth2'
        )
    ));
    								
		return $routeManager;
	}
	
	
	
	protected function configureDependencies()
	{
		$dm = SL\DependencyManager::getInstance();
				
		$depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('SqlProfiler','\Tiro\Profiler'));
		
		$depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('PhpProfiler','\Tiro\Profiler'));
    
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('StationFlyImageService', 'ZeitfadenFlyImageService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
						
    $depList = $dm->registerDependencyManagedService(new SL\ManagedSingleton('StationFlyVideoService', 'FlyVideoService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));

            		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('ImageController'));
    $depList->addDependency('FlyImageService', new SL\ManagedComponent('StationFlyImageService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
            		
    $depList = $dm->registerDependencyManagedService(new SL\ManagedService('VideoController'));
    $depList->addDependency('FlyVideoService', new SL\ManagedComponent('StationFlyVideoService'));
    $depList->addDependency('Profiler', new SL\ManagedComponent('PhpProfiler'));
		
	}
	
}




