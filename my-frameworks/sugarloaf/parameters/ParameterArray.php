<?php 

namespace SugarLoaf;


class ParameterArray extends ParameterProvider
{
	public function __construct()
	{
		$this->_parameters = array();
	}

	public function appendParameter($parameter)
	{
		$this->_parameters[] = $parameter;
	}
	
	public function getParameters()
	{
		$output = array();
		foreach ($this->_parameters as $parameter)
		{
		  $parameter->setManager($this->dependencyManager);
			$output[] = $parameter->getParameter();
		}
		
		return $output;
	}
}





class OneArrayAsParameter extends ParameterProvider
{
  public function __construct()
  {
    $this->_parameters = array();
  }

  public function appendNamedParameter($name, $parameter)
  {
    $this->_parameters[$name] = $parameter;
  }
  
  public function appendParameter($parameter)
  {
    $this->_parameters[] = $parameter;
  }
  
  public function getParameters()
  {
    $output = array();
    foreach ($this->_parameters as $index => $parameter)
    {
      $parameter->setManager($this->dependencyManager);
      $output[$index] = $parameter->getParameter();
    }
    
    return array($output);
  }
}

