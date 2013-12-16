<?php

namespace SugarLoaf;

class ManagedComponent extends AbstractComponent
{
	
	public function getImplementation()
	{
		return $this->dependencyManager->get($this->_instanceName, $this->getParameters());
	}
	
}
