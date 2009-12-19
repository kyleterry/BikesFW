<?php

abstract class bikes_Session_Abstract{

	protected $app;

	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	abstract public function start();

	abstract protected function save();

	public function __destruct(){
		$this->save();
	}
}
