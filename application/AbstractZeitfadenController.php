<?php 
use SugarLoaf as SL;

abstract class AbstractZeitfadenController extends SL\DependencyInjectable
{
	protected $context;
	protected $request;
	protected $response;
	
	protected $service = false;
	
	
	public function __construct($request, $response)
	{
		$this->_request = $request;
		$this->_response = $response;
	}

	protected function declareDependencies()
	{
		return array(
			'Profiler' => 'profiler',
      'OAuth2Service' => 'oAuth2Service',
			'QueryEngine' => '_queryEngine',
			'Facebook' => '_facebook',
			'ZeitfadenService' => 'service'
		);	
	}
	
  protected function getOAuth2Service()
  {
    return $this->oAuth2Service;
  }
		
	public function getService()
	{
		return $this->service;
	}
	
	
	protected function declareActionsThatNeedLogin()
	{
		return array();
	}
	
	public function execute($actionName)
	{
		if (!method_exists($this, $actionName.'Action'))
		{
			throw new Exception("wrong Action? or what? Name:".$actionName, ZeitfadenApplication::STATUS_ERROR_INVALID_ACTION);
		}
		
  	$actionName = $actionName."Action";
		$this->$actionName();
		
	}
	
	
	
	public function getRequestParameter($name,$default)
	{
		return $this->_request->getParam($name,$default);
	}
	
	
	public function getLoggedInUser()
	{
		return $this->getService()->getLoggedInUser();
	}
	
	
	protected function isUserLoggedIn()
	{
		if ($this->getLoggedInUser()->getId() !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function updateLoginIdentity()
	{
	    //
    if ($this->getService()->isOAuth2Request())
    {
      // this is an oauth2-request.
      $this->getService()->loginUserByOAuth2();
    }
    else 
    {
      // it is not an oauth2 request. carry on.
      $email = $this->_request->getSessionVar('email','');
      $password = $this->_request->getSessionVar('password','');
      $facebookUserId = $this->getFacebook()->getUser();
    
      $this->getService()->loginUserByCredentials(array(
        'email' => $email,
        'password' => $password,
        'facebookUserId' => $facebookUserId 
      ));
            
    }
	    	    
			
		
	}
	
	
}








