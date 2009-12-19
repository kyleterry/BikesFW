<?php

class demo_Config_AppRoutes{

	protected $app;

	protected $router;

	public function __construct(bikes_App $app){
		$this->app = $app;
		$this->router = $this->app->getRouter();
	}

	public function map(){
		//not required but it sets the default controller.
		$this->router->setController('demo');
		//not required but it sets the default action.
		$this->router->setAction('index');
		$this->router->map('article/:id', array('controller' => 'demo', 'action' => 'test'));
		$this->router->map('', array('controller' => 'post', 'action' => 'index'));
		$this->router->map(':controller/:action/:id');
	}
}
