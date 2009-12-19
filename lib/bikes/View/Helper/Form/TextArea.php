<?php

/**
 * bikes_View_Helper_Form_TextArea{ 
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_View_Helper_Form_TextArea{
	
	/**
	 * name 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $name;
	
	protected $id;

	/**
	 * value 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $value = null;

	/**
	 * class 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $class = null;

	/**
	 * __construct 
	 * 
	 * @param string $name 
	 * @param string $value 
	 * @param string $class 
	 * @access public
	 * @return void
	 */
	public function __construct($name, $id, $value = null, $class = null){
		$this->name = $name;
		
		$this->id = $id;

		if(!empty($value)){
			$this->value = $value;
		}

		if(!empty($class)){
			$this->class = 'class="' . $class . '"';
		}
	}

	/**
	 * render 
	 * 
	 * @access public
	 * @return string
	 */
	public function render(){
		$textArea = '<textarea name="%s" id="%s" %s>%s</textarea>';
		return sprintf($textArea, $this->name, $this->id, $this->class, $this->value);
	}
}
