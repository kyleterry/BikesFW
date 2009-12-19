<?php

/**
 * This class sets and shows a quick message for the views.
 * <code>
 * 	if(something happened){
 * 		$this->app->flash->setFlash('Login Failed: Username or password invalid.');
 * 	}
 *
 * 	...
 *
 * 	if($this->app->flash->flashExists()){
 * 		echo $this->app->showFlash();
 * 	}
 * </code>
 *  
 * @package BikesFW
 * @version $id$
 * @copyright 2009 BikesFW Bikes Framework
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded) http://trythisbike.org/LICENSE
 */
class bikes_Flash{

	/**
	 *
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;

	/**
	 *
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	/**
	 * Sets the flash message that will be displayed.
	 *
	 * @return bikes_Flash
	 * @return false
	 */
	public function setFlash($message){
		if(empty($message)){
			return false;
		}
		bikes_Cache_Adapter_Apc::getInstance()->add('flash', $message);
		return $this;
	}

	/**
	 *
	 * @return boolean true on success
	 */
	public function flashExists(){
		if($flash = bikes_Cache_Adapter_Apc::getInstance()->get('flash')){
			return true;
		}
		return false;
	}

	/**
	 *
	 * @return string $this->message
	 */
	public function getFlash(){
		$flash = bikes_Cache_Adapter_Apc::getInstance()->get('flash');
		bikes_Cache_Adapter_Apc::getInstance()->delete('flash');
		return (string)$flash;
	}
}
