<?php

namespace SugarLoaf;

class ManagedComponentProvider extends AbstractComponent
{
	
	
	public function getImplementation()
	{
		return $this;
	}
	
	public function provide()
	{
		$parameters = func_get_args();
		
		// do not use the cyclic favtory here, this will be called long after dependency management.
		return DependencyManager::getInstance()->get($this->_instanceName, $parameters);
	}
}
