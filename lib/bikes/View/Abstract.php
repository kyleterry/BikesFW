<?php

class bikes_View_Abstract{

	/**
	 *
	 * @var bikes_Layout $layout
	 */
	protected $layout;

	/**
	 *
	 * @var bikes_App $app
	 */
	protected $app;

	/**
	 *
	 * @var bikes_Collection_Abstract
	 */
	protected $collection;

	/**
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 *
	 * @var array $helpers
	 */
	protected $helpers = array('Html', 'Form');

	/**
	 *
	 * @param bikes_Layout $layout
	 * @param bikes_App $app
	 * @return void
	 */
	public function __construct(bikes_Layout $layout, bikes_App $app, bikes_Collection_Abstract $collection = null){
		$this->layout = $layout;
		$this->app = $app;
		$this->collection = $collection;
	}

	/**
	 *
	 * @return string $viewTemplatePath
	 */
	public function render(){
		$viewTemplatePath = $this->app->getConfig('app.path') . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $this->name . '.btmp';
		if(!file_exists($viewTemplatePath)){
			$this->app->log("ViewTemplateDoesNotExistException thrown\n\tThe template {$this->name} does not exist or is not readable", 'cache');
			throw new bikes_ViewTemplateDoesNotExistException('The template ' . $this->name . ' does not exist or is not readable');
		}
		return $viewTemplatePath;
	}
}
