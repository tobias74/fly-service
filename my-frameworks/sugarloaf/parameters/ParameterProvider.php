<?php 

namespace SugarLoaf;

abstract class ParameterProvider
{
  public function setManager($manager)
  {
    $this->dependencyManager = $manager;
  }
  
  
}
