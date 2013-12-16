<?php 
namespace sugarloaf;

class ConstantParameterArray extends ParameterProvider
{
	public function __construct($parameters)
	{
		$this->parameters = $parameters;
	}
	
	public function getParameters()
	{
		return $this->parameters;
	}
}

