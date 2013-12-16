<?php

namespace SugarLoaf;

class DependencyList
{
	public function __construct()
	{
		$this->_list = array();
	}
	
	protected function addDependencyObject($dependency)
	{
		array_push($this->_list, $dependency);
	}
	
	public function addDependency($interfaceName, $component)
	{
		$this->addDependencyObject(new Dependency($interfaceName, $component));
		return $this;
	}
	
	public function getList()
	{
		return $this->_list;
	}
	
	
}
