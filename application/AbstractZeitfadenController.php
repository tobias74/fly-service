<?php 
use SugarLoaf as SL;

abstract class AbstractZeitfadenController extends SL\DependencyInjectable
{
	protected $context;
	protected $request;
	protected $response;
	
	protected $service = false;
	
	
	public function __construct($request, $response)
	{
		$this->_request = $request;
		$this->_response = $response;
	}

	protected function declareDependencies()
	{
		return array(
			'Profiler' => 'profiler',
			'ZeitfadenService' => 'service'
		);	
	}
	
	public function getService()
	{
		return $this->service;
	}
	
	public function execute($actionName)
	{
		if (!method_exists($this, $actionName.'Action'))
		{
			throw new Exception("wrong Action? or what? Name:".$actionName, ZeitfadenApplication::STATUS_ERROR_INVALID_ACTION);
		}
		
  	$actionName = $actionName."Action";
		$this->$actionName();
		
	}
	
	
	
	public function getRequestParameter($name,$default)
	{
		return $this->_request->getParam($name,$default);
	}
	
	
	
}








