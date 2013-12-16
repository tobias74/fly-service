<?php 

namespace SugarLoaf;

class ManagedParameter extends ParameterProvider
{
	public function __construct($component)
	{
		$this->componentName = $component;
	}
	
	public function getParameter()
	{
		return $this->dependencyManager->get($this->componentName, array());	
	}
}


