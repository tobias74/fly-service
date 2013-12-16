<?php

namespace SugarLoaf;

class ManagedComponentCurriedProvider extends AbstractComponent
{
  
  
  public function getImplementation()
  {
    return $this;
  }
  
  public function curry()
  {
    $this->curriedParameters = func_get_args();
    return $this; 
  }
  
  public function provide()
  {
    $parameters = func_get_args();

    // do not use the cyclic favtory here, this will be called long after dependency management.
    return DependencyManager::getInstance()->get($this->_instanceName, array_merge($this->curriedParameters, $parameters));
  }
}




