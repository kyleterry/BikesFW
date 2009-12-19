<?php

class bikes_View_Helper_Form_Checkbox{

	protected $name;

	protected $checked = null;

	protected $class = null;
	
	public function __construct($name, $checked = false, $class = null){
		$this->name = $name;

		if(true === $checked){
			$this->checked = 'checked';
		}

		if(!empty($class)){
			$this->class = 'class="' . $class . '"';
		}
	}

	public function render(){
		$checkbox = '<input type="checkbox" name="%s" %s %s />';
		return sprintf($checkbox, $this->name, $this->class, $this->checked);
	}
}
