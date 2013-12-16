<?php
namespace SugarLoaf;

abstract class DependencyInjectable
{
	protected $dependencyServices = array();

	public function __construct()
	{
		
	}
	
	protected function declareDependencies()
	{
		// array('officialServiceName' => 'myInternalServiceName);
		return array();
	}
	
	public function __call($name, $arguments)
	{
		if ( substr($name,0,3) == "set" )
		{
			$property = substr($name,3);
			$dependencies = $this->declareDependencies();
			
			if (!isset($dependencies[$property]))
			{
				print_r($dependencies);
				throw new \Exception("Called Setter on non declaered dependecy. ".$property." in ".get_class($this));
			}
			
			$internalServiceName = $dependencies[$property];
			// we will leave the first version in, but it's only for backwrads compatibility
			$this->$internalServiceName = array_shift($arguments);
			$this->dependencyServices[$internalServiceName] = $this->$internalServiceName;
			
		}
		elseif ( substr($name,0,3) == "get" )
		{
			$property = substr($name,3);

			$dependencies = $this->declareDependencies();
			if (!isset($dependencies[$property]))
			{
				throw new \Exception("Called Getter on non existing dependency. ".$property." in ".get_class($this));
			}

			$internalServiceName = $dependencies[$property];
			
			return $this->dependencyServices[$internalServiceName];
		}
		else
		{
			throw new \ErrorException("call to undefined method ".$name);
		}
		
	}
	
	
}

