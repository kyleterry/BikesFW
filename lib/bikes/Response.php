<?php

class bikes_Response{

	/**
	 * Holds an instance of bikes_App
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $app;

	/**
	 * __construct 
	 * 
	 * @param bikes_App $app 
	 * @access public
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	/**
	 * Issues a Location header.
	 * Default response code is 302.
	 * 
	 * @param mixed $url 
	 * @param int $code 
	 * @access public
	 * @return void
	 */
	public function redirect($url, $code = 302){
		if(!is_numeric($code)){
			 throw new Exception('Redirect code must be numberic: kt_Response::setRedirect()');
		}
		header('Location: ' . $url, true, $code);
		exit;
	}

	/**
	 * Responds to the client with a 404 header. 
	 * 
	 * @access public
	 * @return void
	 */
	public function send404Header(){
		header("HTTP/1.0 404 Not Found");
	}

	/**
	 * Send a custom header to the client. 
	 * 
	 * @param mixed $value 
	 * @access public
	 * @return void
	 */
	public function sendCustomHeader($value){
		header($value);
	}

	/**
	 * Sets a cookie.
	 * TODO refactor this method.
	 * 
	 * @param array $params 
	 * @access public
	 * @return void
	 */
	public function setCookie($params){
		if(!is_array($params)){
			throw new bikes_ResponseException('Cookie params must be an array');
		}

		setcookie(
			$params['name'],
			$params['value'],
			$params['expire'] = 0,
			$params['path'],
			$params['domain'],
			$params['secure'] = false,
			$params['httponly'] = false
		);
	}

	/**
	 * Responds to the client.
	 * 
	 * @param mixed $controllerResponse 
	 * @access public
	 * @return void
	 */
	public function respond($controllerResponse){
		switch(true){
			case ($controllerResponse instanceof bikes_Layout):
				echo $controllerResponse->render();
				break;

			case (is_string($controllerResponse)):
				echo $controllerResponse;
				break;

			default:
				$this->sendCustomHeader('HTTP/1.1 503 Service Unavailable');
				echo 'Unknown controller reponse. Unable to handle request.';
				break;
		}
	}
}
