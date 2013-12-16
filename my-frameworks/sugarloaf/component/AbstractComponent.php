<?php
namespace SugarLoaf;

abstract class AbstractComponent
{
	public function __construct($instanceName, $parameterProvider=false)
	{
		$this->_instanceName = $instanceName;
		$this->parameterProvider = $parameterProvider;
	}
	
	abstract public function getImplementation();
	
	
	public function setManager($manager)
	{
		$this->dependencyManager = $manager;
	}
	
	protected function getManager()
	{
	  return $this->dependencyManager;
	}
	
	public function getParameters()
	{
		if ($this->parameterProvider!==false)
		{
		  $this->parameterProvider->setManager($this->dependencyManager);
			return $this->parameterProvider->getParameters();
		}
		else
		{
			return array();			
		}
	}
	
}

