<?php

namespace SugarLoaf;

class Dependency
{
	public function __construct($interfaceName, $component)
	{
		$this->interfaceName = $interfaceName;
		$this->component = $component;
		
	}

	public function setManager($manager)
	{
		$this->manager = $manager;	
	}
	
	public function getInterfaceName()
	{
		return $this->interfaceName;
	}
	
	public function getImplementationName()
	{
		return $this->component->getImplementationName();
	}

	public function getImplementation($parameters=array())
	{
		$this->component->setManager($this->manager);
		return $this->component->getImplementation($parameters);
	}
	
}
