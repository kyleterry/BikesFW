<?php

class demo_Controller_Demo extends bikes_Web_Controller_Application{

	public function index(){
		$params = $this->getUriParams();
		return 'Demo::index';
	}

	public function hello(){
		return 'hello, world!';
	}
}
