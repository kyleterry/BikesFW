<?php

class demo_Controller_Page extends bikes_Web_Controller_Application{
	public function index($uri){
		return 'hello, world: called from ' . get_class($this) . '::index()';
	}
}
