<?php 


class ZeitfadenFrontController
{
	
	public function __construct()
	{
		
	}
	
	public function setDependencyManager($dm)
	{
		$this->dependencyManager = $dm;
	} 
	
	public function dispatch($request, $response)
	{
		$controller = $this->getController($request, $response);
		$actionName = $this->getActionName($request);
		$controller->execute($actionName);
	}
	
	
	private function getController($request, $response)
	{
	  
		$className = ucfirst($request->getParam("controller",'')."Controller");
		if (!class_exists($className))
		{
			throw new ZeitfadenException("wrong controller or what? Name:".$className);
		}
		
		//$controller = new $className($request, $response);
		$controller = $this->dependencyManager->get($className, array($request,$response));
		
		return $controller;
	}
	
	private function getActionName($request)
	{
		return $request->getParam('action','index');
	}
}





class ZeitfadenRequest
{
	protected $_request;
	protected $_session;
	protected $_files;
	protected $_server;
	
	public function __construct()
	{
		
	}
	
	public function setSession($val)
	{
		$this->_session = $val;
	}

	public function getSession()
	{
		return $this->_session;	
	}
	
	public function setServer($val)
	{
		$this->_server = $val;
	}
	
	public function setRequest($val)
	{
		$this->_request = $val;
	}
	
	public function setFiles($val)
	{
		$this->_files = $val;
	}
	
	public function getRequestURI()
	{
		return isset($this->_server['REQUEST_URI']) ? $this->_server['REQUEST_URI'] : "";
	}
	
  public function getRequestMethod()
  {
    return isset($this->_server['REQUEST_METHOD']) ? $this->_server['REQUEST_METHOD'] : "";    
  }
  
	public function addArguments($hash)
	{
		$this->_request = array_merge($this->_request, $hash);
	}
	
	public function getParam($name, $default)
	{
		return (isset($this->_request[$name]) && ($this->_request[$name] !== '')) ? $this->_request[$name] : $default;
	}
	
	public function setParam($name, $value)
	{
		$this->_request[$name] = $value;
	}
	
	public function getSessionVar($name, $default)
	{
		return isset($this->_session[$name]) ? $this->_session[$name] : $default;
	}

	public function setSessionVar($name, $val)
	{
		$this->_session[$name] = $val;
	}
	
	
	public function hasUploadedFile($inputName)
	{
		if (isset($this->_files[$inputName]['tmp_name']) && ($_FILES[$inputName]['tmp_name'] != "")) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function getUploadedFile($inputName)
	{
		if (isset($this->_files[$inputName]['tmp_name']) && ($_FILES[$inputName]['tmp_name'] != "")) 
		{
			$input_file       = $_FILES[$inputName]['tmp_name'];
			$input_file_name  = $_FILES[$inputName]['name'];
			$input_file_size  = $_FILES[$inputName]['size'];
			$input_file_type  = $_FILES[$inputName]['type'];
			$input_file_error = $_FILES[$inputName]['error'];
			
			return $this->_files[$inputName];
		}
		else
		{
			throw new ZeitfadenException('file not uploaded');
		}
		
	}
	
}


class ZeitfadenResponse
{
	protected $_hash;
	protected $_enabled=true;
	protected $_headers=array();
	protected $_isFile = false;
  protected $_isBytes = false;
  protected $_isStream = false;
	protected $fileName = "";
	
	public function __construct()
	{
		$this->_hash = array();
	}
	
	public function setFileName($fileName)
	{
	   $this->_isFile = true;
	   $this->fileName = $fileName;
	}

  public function setBytes($val)
  {
     $this->_isBytes = true;
     $this->bytes = $val;
  }
	
	public function getBytes()
	{
	 return $this->bytes;  
	} 

  public function isBytes()
  {
    return $this->_isBytes;
  }


  public function setStream($val)
  {
     $this->_isStream = true;
     $this->stream = $val;
  }
  
  public function getStream()
  {
   return $this->stream;  
  } 

  public function isStream()
  {
    return $this->_isStream;
  }
	 
  public function getFileName()
  {
     return $this->fileName;
  }

		
	public function isFile()
	{
	  return $this->_isFile;
	}

		
	public function disable()
	{
		$this->_enabled = false;
	}
	
	public function enable()
	{
		$this->_enabled = true;
	}
	
	public function addHeader($header,$replace=true,$code=200)
	{
	  $this->_headers[] = array('header'=> $header,'replace' => $replace,'code' => $code);
	}
	
	public function getHeaders()
	{
	  return $this->_headers;  
	}
	
	public function isEnabled()
	{
		return $this->_enabled;
	}
	
	public function appendValue($name, $value)
	{
		$this->_hash[$name] = $value;
	}
	
	public function hasValue($name)
	{
		if (isset($this->_hash[$name]))
		{
			return true;
		}	
		else
		{
			return false;
		}
	}
	public function getValue($name)
	{
		return $this->_hash[$name];
	}
	
	public function getHash()
	{
		return $this->_hash;
	}
	
	public function setHash($value)
	{
		$this->_hash = $value;
	}
	
}