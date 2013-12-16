<?php

require_once(dirname(__FILE__).'/DependencyInjectable.php');
require_once(dirname(__FILE__).'/component/AbstractComponent.php');
require_once(dirname(__FILE__).'/component/ManagedComponent.php');
require_once(dirname(__FILE__).'/component/ManagedComponentProvider.php');
require_once(dirname(__FILE__).'/component/ManagedComponentCurriedProvider.php');
require_once(dirname(__FILE__).'/component/UnmanagedInstance.php');

require_once(dirname(__FILE__).'/service/ManagedService.php');
require_once(dirname(__FILE__).'/service/ManagedParameterizedService.php');
require_once(dirname(__FILE__).'/service/ManagedSingleton.php');

require_once(dirname(__FILE__).'/CyclicProofFactory.php');
require_once(dirname(__FILE__).'/Dependency.php');
require_once(dirname(__FILE__).'/DependencyList.php');
require_once(dirname(__FILE__).'/DependencyManager.php');

require_once(dirname(__FILE__).'/parameters/ParameterProvider.php');
require_once(dirname(__FILE__).'/parameters/ConstantParameter.php');
require_once(dirname(__FILE__).'/parameters/ConstantParameterArray.php');
require_once(dirname(__FILE__).'/parameters/ManagedParameter.php');
require_once(dirname(__FILE__).'/parameters/ParameterArray.php');


