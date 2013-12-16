<?php 

namespace SugarLoaf;

class ConstantParameter extends ParameterProvider
{
	public function __construct($parameter)
	{
		$this->parameter = $parameter;
	}
	
	public function getParameter()
	{
		return $this->parameter;
	}
}
