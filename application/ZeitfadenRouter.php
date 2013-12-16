<?php 

class ZeitfadenRouteManager
{
	protected $_routes = array();
	
	public function addRoute($route)
	{
		array_push($this->_routes, $route);
	}

	protected function extractArgumentsFromURI($requestURI)
	{
		foreach (array_reverse($this->_routes) as $route)
		{
			$result = $route->match($requestURI);
			if ($result !== false)
			{
				return $result;
			}
		}
		
		return array();
		
	}
	
	public function analyzeRequest($request)
	{
		$args = $this->extractArgumentsFromURI($request->getRequestURI());
		$request->addArguments($args);
	}
	
}




class ZeitfadenRoute
{
	protected $_urlDelimiter = "/";
	protected $_urlVariable = ":";
    protected $_parts = array();
    protected $_defaults = array();
    protected $_variables = array();
    protected $_wildcardData = array();
    protected $_staticCount = 0;
    
    
	public function __construct($route, $defaults = array())
	{
		$route = trim($route, $this->_urlDelimiter);
		$this->_defaults = (array) $defaults;
	      
        if ($route !== '') 
        {
            foreach (explode($this->_urlDelimiter, $route) as $pos => $part) 
            {
                if (substr($part, 0, 1) == $this->_urlVariable) 
                {
                    $name = substr($part, 1);
                    
                    $this->_parts[$pos]     = null;
                    $this->_variables[$pos] = $name;
                }
                else
                {
                    $this->_parts[$pos] = $part;
                    
                    if ($part !== '*') 
                    {
                    	$this->_staticCount++;
                    }
                }
            }
        }
	}
	
	
    public function match($requestURI)
    {
        $path = trim($requestURI, $this->_urlDelimiter);

        $questionMark = strpos($path,'?');
        if ($questionMark !== false)
        {
          $path = substr($path,0,$questionMark);
        }  
    
        $pathStaticCount = 0;
        $values          = array();
        
        
        if ($path !== '') 
        {
            $path = explode($this->_urlDelimiter, $path);

            foreach ($path as $pos => $pathPart) 
            {
            	//echo "<br> running through $pos with $pathPart   ";
            	
                // Path is longer than a route, it's not a match
                if (!array_key_exists($pos, $this->_parts)) 
                {
                	//echo "wehy return false here?";
	                return false;
                }
                
                
                // If it's a wildcard, get the rest of URL as wildcard data and stop matching
                if ($this->_parts[$pos] == '*') 
                {
                    $count = count($path);
                    for($i = $pos; $i < $count; $i+=2) 
                    {
                        $var = urldecode($path[$i]);
                        if (!isset($this->_wildcardData[$var]) && !isset($this->_defaults[$var]) && !isset($values[$var])) 
                        {
                            $this->_wildcardData[$var] = (isset($path[$i+1])) ? urldecode($path[$i+1]) : null;
                        }
                    }
                    break;
                }

                $name     = isset($this->_variables[$pos]) ? $this->_variables[$pos] : null;
                $pathPart = urldecode($pathPart);
                $part = $this->_parts[$pos];
                

                // If it's a static part, match directly
                if ($name === null && $part != $pathPart) 
                {
                	//echo "stumbled uppon something that did not match return false: $part did not match $pathPart<br>";
                    return false;
                }

                
                // If it's a variable store it's value for later
                if ($name !== null) 
                {
                    $values[$name] = $pathPart;
                }
                else
                {
                    $pathStaticCount++;
                }  
            }
        }

        // Check if all static mappings have been matched
        if ($this->_staticCount != $pathStaticCount) 
        {
            return false;
        }

        $return = $values + $this->_wildcardData + $this->_defaults;

        // Check if all map variables have been initialized
        foreach ($this->_variables as $var) 
        {
            if (!array_key_exists($var, $return)) 
            {
                return false;
            }
        }
        
        return $return;

    }
	
	
    
	
	
	
    
}


/*
$some = new ZeitfadenRoute('/user/:userId/:imageMode/',
	array(
		'controller' => 'user',
		'action' => 'getById',
		'imageMode' => 'withSmallImage'
	)
);

echo "<hr>";
$val = $some->match('/user/5');
if ($val !== false)
{
	print_r($val);
}
else
{
	echo "why false?";
}
die();

*/



