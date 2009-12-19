<?php

/**
 * bikes_View_Helper_Form 
 * 
 * @uses bikes
 * @uses _View_Helper_Abstract
 * @package 
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com 
 * @license BSD (Inlcuded)
 */
class bikes_View_Helper_Form extends bikes_View_Helper_Abstract{

	/**
	 * app 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $app;

	/**
	 * postBack 
	 * 
	 * @var boolean
	 * @access protected
	 */
	protected $postBack = false;

	/**
	 * post 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $post = array();
	
	/**
	 * validMethods 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $validMethods = array('POST', 'post', 'GET', 'get');

	/**
	 * open 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $open = null;

	/**
	 * elements 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $elements = array();

	/**
	 * close 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $close = null;

	/**
	 * __construct 
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	/**
	 * setPostBack 
	 * 
	 * @param array $post 
	 * @access public
	 * @return void
	 */
	public function setPostBack($post){
		$this->postBack = true;
		$this->post = $post;
	}

	/**
	 * open 
	 * 
	 * @param mixed $name 
	 * @param string $method 
	 * @param mixed $action 
	 * @param mixed $id 
	 * @param mixed $enctype 
	 * @access public
	 * @return string
	 */
	public function open($name, $method = 'POST', $action = null, $id = null, $enctype = null){
		if(empty($id)){
			$id = $name;
		}

		if(empty($action)){
			$action = $this->app->getRequest()->getUri();
		}
		
		if(!in_array($method, $this->validMethods)){
			throw new Exception('Form method ' . $method . ' is not a valid method.');
		}

		if(isset($enctype)){
			$enctype = 'enctype="' . $enctype . '"';
		}
		
		$form = '<form name="%s" id="%s" method="%s" action="%s" %s>' . "\n";
		$this->open = sprintf($form, $name, $id, $method, $action, $enctype);
		return $this->open;
	}

	/**
	 * close 
	 * 
	 * @param mixed $submitName 
	 * @param mixed $class 
	 * @access public
	 * @return string
	 */
	public function close($submitName, $class = null){
		if(!empty($class)){
			$class = 'class="' . $class . '"';
		}

		$form = '<input type="submit" value="%s" %s />' . "\n" . '</form>';
		$this->close = sprintf($form, $submitName, $class);
		return $this->close;
	}

	/**
	 * addInput 
	 * 
	 * @param mixed $name 
	 * @param mixed $value 
	 * @param mixed $class 
	 * @access public
	 * @return string
	 */
	public function addInput($type, $name, $value = null, $class = null){
		if(true === $this->postBack && isset($this->post[$name])){
			$value = $this->post[$name];
		}
		$input = new bikes_View_Helper_Form_Input($type, $name, $value, $class);
		$this->elements[] = $input;
		return $input->render();
	}

	/**
	 * addTextArea 
	 * 
	 * @param string $type 
	 * @param string $name 
	 * @param string $value 
	 * @param string $class 
	 * @access public
	 * @return string
	 */
	public function addTextArea($name, $id, $value = null, $class = null){
		if(true === $this->postBack && isset($this->post[$name])){
			$value = $this->post[$name];
		}
		$textArea = new bikes_View_Helper_Form_TextArea($name, $id, $value, $class);
		$this->elements[] = $textArea;
		return $textArea->render();
	}

	/**
	 * addCheckbox 
	 * 
	 * @param string $name 
	 * @param string $checked 
	 * @param string $class 
	 * @access public
	 * @return string
	 */
	public function addCheckbox($name, $checked = false, $class = null){
		if(true === $this->postBack && isset($this->post[$name])){
			$checked = true;
		}

		if(true === $this->postBack && !isset($this->post[$name])){
			$checked = false;
		}

		$checkbox = new bikes_View_Helper_Form_Checkbox($name, $checked, $class);
		$this->elements[] = $checkbox;
		return $checkbox->render();
	}

	/**
	 * addFileUpload 
	 * 
	 * @param string $name 
	 * @param string $class 
	 * @access public
	 * @return string
	 */
	public function addFile($name, $class = null){
		$upload = new bikes_View_Helper_Form_File($name, $class);
		$this->elements[] = $upload;
		return $upload->render();
	}
}
