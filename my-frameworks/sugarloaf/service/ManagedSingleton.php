<?php 
namespace sugarloaf;

class ManagedSingleton extends ManagedService
{
	protected $_instance = false;
	protected $_isFullyInstantiated = false;
	
	public function __construct($serviceName, $serviceClassRef=false, $parameters=false)
	{
		parent::__construct($serviceName, $serviceClassRef);
		$this->_parameters = $parameters;
	}
	
	
  public function isFullyInstantiated()
  {
    return $this->_isFullyInstantiated;
  }
		
		
	public function setFullyInstantiated()
	{
	 $this->_isFullyInstantiated = true;  
	}	
	
	public function getImplementation($parameters=array(),$di)
	{
		if (count($parameters)>0)
		{
			throw new \ErrorException('you cannot parameterize singletons on the fly. they start with what they stat. This is my name: '.$this->getServiceName());
		}
		
		if ($this->_instance === false)
		{
		    if ($this->_parameters != false)
        {
            $this->_parameters->setManager($di);
            $this->_instance = parent::getImplementation($this->_parameters->getParameters(),$di);
        }
        else 
        {
            $this->_instance = parent::getImplementation($parameters, $di);
        }
        
            
		}
		
		return $this->_instance; 
	}
	
}


