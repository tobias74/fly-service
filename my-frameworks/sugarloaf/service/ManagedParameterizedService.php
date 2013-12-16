<?php 
namespace sugarloaf;

class ManagedParameterizedService extends ManagedService
{
  
  public function __construct($serviceName, $serviceClassRef=false, $parameters=false)
  {
    parent::__construct($serviceName, $serviceClassRef);
    $this->_parameters = $parameters;
  }
  
  public function getImplementation($parameters=array(),$di)
  {
    if (count($parameters)>0)
    {
      throw new \ErrorException('you cannot parameterize this service on the fly. It has already been configured. This is my name: '.$this->getServiceName());
    }
    
    if ($this->_parameters != false)
    {
      $this->_parameters->setManager($di);
      return parent::getImplementation($this->_parameters->getParameters(), $di);
    }
    else 
    {
      return parent::getImplementation($parameters, $di);
    }
    
  }
  
}


