<?php

/**
 * bikes_Web_Controller_ActionAbstract
 * 
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009 BikesFW Bikes Framework
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Web_Controller_ActionAbstract{
	
	/**
	 * Holds the instance of the application front controller.
	 * 
	 * @var bikes_Web_Controller_Front $front
	 * @access protected
	 */
	protected $front;

	/**
	 * Holds the instance of the bikes_App object.
	 * 
	 * @var bikes_App $app
	 * @access protected
	 */
	protected $app;

	/**
	 * An array that holds model objects that have been instantiated
	 * 
	 * @var array $models
	 * @access protected
	 */
	protected $models = array();

	/**
	 * __construct 
	 * 
	 * @param bikes_Web_Controller_Front $front 
	 * @param bikes_App $app 
	 * @access public
	 * @return void
	 */
	public function __construct(bikes_Web_Controller_Front $front, bikes_App $app){
		$this->front = $front;
		$this->app = $app;
	}

	/**
	 * Loads and hands over a model object. Unless the model has already
	 * been loaded. Then it just hands it.
	 * 
	 * @param string $name 
	 * @access public
	 * @return bikes_Model_Abstract
	 */
	public function getModel($name){
		if(!in_array($name, array_keys($this->models))){
			$model = PROJECT . '_Model_' . ucwords($name);
			$model = new $model($this->app);
			$this->models[$name] = $model;
		}
		return $this->models[$name];
	}

	/**
	 * Forward to another controller/action.
	 * 
	 * @param string $controller 
	 * @param string $action 
	 * @param array $args 
	 * @access public
	 * @return mixed
	 */
	protected function forward($controller, $action, $args = null){
		if(empty($controller)){
			throw new bikes_ActionControllerException('bikes_Web_Controller_ActionAbstract::forward cannot take a blank controller');
		}

		if(empty($controller)){
			throw new bikes_ActionControllerException('bikes_Web_Controller_ActionAbstract::forward cannot take a blank action');
		}
		
		$controllerName = PROJECT . '_Controller_' . ucwords($controller);
		if($this instanceof $controllerName){
			return call_user_func_array(array($this, $action), array($args));
		} else {
			$controller = new $controllerName($this->front, $this->app);
			return call_user_func_array(array($controller, $action), array($args));
		}
	}

	/**
	 * Grabs the URI Parameters from the router.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function getUriParams(){
		$route = $this->app->getRouter()->getMatch();
		
		if(isset($route['controller'])){
			unset($route['controller']);	
		}

		if(isset($route['action'])){
			unset($route['action']);	
		}

		return $route;
	}
}
