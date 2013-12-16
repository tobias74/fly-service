<?php 

namespace sugarloaf;

class ManagedService
{
	public function __construct($serviceName, $serviceClassRef=false)
	{
		$this->_serviceName = $serviceName;
		
		if ($serviceClassRef === false)
		{
			$this->_serviceClassRef = $serviceName;
		}
		else
		{
			$this->_serviceClassRef = $serviceClassRef;
		}
		
	}
	
	public function isFullyInstantiated()
	{
	  return false;
	}
	
	public function setFullyInstantiated()
	{
	  // nothing
	}
	
	public function getServiceName()
	{
		return $this->_serviceName;
	}
	
	public function getImplementation($parameters=array(),$di)
	{
		$reflectionObject = new \ReflectionClass($this->_serviceClassRef);
		$instance = $reflectionObject->newInstanceArgs($parameters);
		return $instance;
	}
	
}


