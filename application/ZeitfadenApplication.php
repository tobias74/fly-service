<?php 

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
	
	
	

	
    public function runRestful($serverContext)
    {
        //require_once('FirePHPCore/FirePHP.class.php');      
        $appTimer = $this->phpProfiler->startTimer('#####XXXXXXX A1A1-COMPLETE_RUN XXXXXXXXXXXX################');
        
        $serverContext->startSession();
        
        $request = $serverContext->getRequest();
        
        $response = new \PivoleUndPavoli\Response();
        


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
        
        $frontController = new \PivoleUndPavoli\FrontController($this);
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
		$routeManager = new \PivoleUndPavoli\RouteManager();
		

		$routeManager->addRoute(new \PivoleUndPavoli\Route(
			'/:controller/:action/*',
			array()
		));
		
		
		$routeManager->addRoute(new \PivoleUndPavoli\Route(
			'getUserById/:userId',
			array(
				'controller' => 'user',
				'action' => 'getById'
			)
		));

		$routeManager->addRoute(new \PivoleUndPavoli\Route(
			'getStationById/:stationId',
			array(
				'controller' => 'station',
				'action' => 'getById'
			)
		));

    $routeManager->addRoute(new \PivoleUndPavoli\Route(
        'getStationsByQuery/:query',
        array(
            'controller' => 'station',
            'action' => 'getByQuery'
        )
    ));

    $routeManager->addRoute(new \PivoleUndPavoli\Route(
        'getUsersByQuery/:query',
        array(
            'controller' => 'user',
            'action' => 'getByQuery'
        )
    ));
    		
    $routeManager->addRoute(new \PivoleUndPavoli\Route(
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




