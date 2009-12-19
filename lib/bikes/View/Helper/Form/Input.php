<?php

/**
 * bikes_View_Helper_Form_Input{ 
 * 
 * @package 
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com 
 * @license BSD (Inlcuded)
 */
class bikes_View_Helper_Form_Input{

	/**
	 * validTypes 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $validTypes = array(
		'submit',
		'text',
		'hidden',
		'password'
	);

	/**
	 * type 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $type;
	
	/**
	 * name 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $name;

	/**
	 * value 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $value = null;

	/**
	 * class 
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $class = null;

	/**
	 * __construct 
	 * 
	 * @param string $type 
	 * @param string $name 
	 * @param string $value 
	 * @param string $class 
	 * @access public
	 * @return void
	 */
	public function __construct($type, $name, $value = null, $class = null){
		if(!in_array($type, $this->validTypes)){
			throw new bikes_FormHelperException('Type ' . $type . ' is not a valid input element type.');
		}
		
		$this->type = $type;

		$this->name = $name;

		if(!empty($value)){
			$this->value = 'value="' . $value . '"';
		}

		if(!empty($class)){
			$this->class = 'class="' . $class . '"';
		}
	}

	/**
	 * render 
	 * 
	 * @access public
	 * @return void
	 */
	public function render(){
		$input = '<input type="%s" name="%s" id="%s" %s %s />';
		return sprintf($input, $this->type, $this->name, $this->name, $this->value, $this->class);
	}
}
