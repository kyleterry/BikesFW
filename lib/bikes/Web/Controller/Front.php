<?php

/**
 * bikes_Web_Controller_Front 
 * 
 * @uses bikes_Web_Controller_Abstract
 * @package 
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com 
 * @license BSD (Inlcuded)
 */
class bikes_Web_Controller_Front extends bikes_Web_Controller_Abstract{

	/**
	 * init shit fool
	 * 
	 * @access public
	 * @return void
	 */
	public function init(){
		$appRoutes = PROJECT . '_Config_AppRoutes';
		$appRoutes = new $appRoutes($this->app);
		$appRoutes->map();
	}

	/**
	 * run 
	 * 
	 * @access public
	 * @return mixed
	 */
	public function run(){
		$request = $this->app->getRequest();
		$router = $this->app->getRouter();

		if(!$router->match($request->getUri())){
			return $this->dispatchToPageController();
		}

		$route = $router->getMatch();

		$controller = PROJECT . '_Controller_' . ucwords($route['controller']);
		$path = LIB_PATH . DS . 'application' . DS . str_replace('_', DS, $controller) . '.php';

		if(!file_exists($path)){
			return $this->dispatchToPageController();
		}

		$controller = new $controller($this, $this->app);

		return call_user_func_array(array($controller, $route['action']), array());
	}

	/**
	 * dispatchToPageController 
	 * 
	 * @access public
	 * @return void
	 */
	public function dispatchToPageController(){
		$controller = PROJECT . '_Controller_Page';
		$controller = new $controller($this, $this->app);
		return $controller->index($this->app->getRequest()->getUri());
	}
}
