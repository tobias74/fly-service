<?php

namespace SugarLoaf;

class CyclicProofFactory
{
	public function __construct($context)
	{
		$this->_context = $context;
		
		$this->_cyclicRecorder = array();
	
    $this->_cyclicDependencyStack = array();		
	}
	
	protected function getProfilerName()
	{
		return $this->_context->getProfilerName();	
	}
	
	protected function getProfiler()
	{
		if ($this->getProfilerName() != "")
		{
			return $this->get($this->getProfilerName());	
		}
		else 
		{
			return false;	
		}
	}
	
	protected function getStartedTimer($description)
	{
		if ($this->getProfiler() != false)
		{
			$timer = $this->getProfiler()->startTimer($description);
			return $timer;
		}
		else
		{
			return false;
		}
	}
	
	protected function stopTimer($timer)
	{
		if ($timer != false)
		{
			$timer->stop();
		}
	}
	
	protected function push($name)
	{
	  array_push($this->_cyclicDependencyStack,$name);
	}
	
  protected function pop()
  {
    return array_pop($this->_cyclicDependencyStack);
  }
  
	public function build($implementationName, $parameters=array())
	{
	  $this->push($implementationName);
	  
    $serviceHandle = $this->_context->getManagedServiceHandle($implementationName);
    
    $instance = $serviceHandle->getImplementation($parameters, $this->_context);
    
    if (!$serviceHandle->isFullyInstantiated())
    {
      $this->_cyclicRecorder[$implementationName] = $instance;        

      foreach ($this->_context->getDependencyList($implementationName)->getList() as $dependency)
      {
  
        $dependency->setManager($this);
        
        //$timer = $this->getStartedTimer('DM: requesting implementation for '.$implementationName);
        $timer2 = $this->getStartedTimer('DM: requesting implementation');
        $implementation = $dependency->getImplementation($parameters);
        //$this->stopTimer($timer);
        $this->stopTimer($timer2);
                
        $setImplementation = "set".ucfirst($dependency->getInterfaceName());
        //$timer = $this->getStartedTimer('DM: setting implementation');
        $instance->$setImplementation($implementation);
        //$this->stopTimer($timer);
              
      }
      
      
      $serviceHandle->setFullyInstantiated();
      if (method_exists($instance,'afterSugarLoafConstruct'))
      {
        $instance->afterSugarLoafConstruct();
      }
    }
    
		
		$this->pop();
		return $instance;
	}
	
	public function get($implementationName, $parameters = array())
	{
		if (isset($this->_cyclicRecorder[$implementationName]))
		{
		  if (array_search($implementationName,$this->_cyclicDependencyStack) !== false)
		  {
        // this is a cyclic dependency!
        error_log('NOTICE SUGARLOAF: We have a cyclic dependency with '.$implementationName.' ### STACK: '.print_r($this->_cyclicDependencyStack, true));  
		  }
			$implementation = $this->_cyclicRecorder[$implementationName];				
		}
		else
		{
			$implementation = $this->build($implementationName, $parameters);
		}
		
		return $implementation;
	}
}

