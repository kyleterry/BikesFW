<?php

class bikes_View_Helper_Form_File{
	
	protected $name;
	
	protected $class = null;
	
	public function __construct($name, $class = null){
		
		$this->name = $name;
		
		if(!empty($class)){
			$this->class = 'class="' . $class . '"';
		}
	}
	
	public function render(){
		$file = '<input type="file" name="%s" id="%s" %s />';
		return sprintf($file, $this->name, $this->name, $this->class);
	}
}