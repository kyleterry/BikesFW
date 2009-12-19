<?php

/**
 * TODO this whole class needs to be abstract with html, xml, and feed classes that extend it.
 * Old layout object
 *
 * @author Kyle Terry
 * @class bikes_Layout
 */
class bikes_Layout{

	/**
	 * Holds and isntance of bikes_App
	 * 
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;
	
	/**
	 *
	 * @var array $views
	 */
	protected $views = array();

	/**
	 *
	 * @var array $viewData
	 */
	protected $viewData = array();

	/**
	 *
	 * @var array $regions
	 */
	protected $regions = array();

	/**
	 *
	 * @var string $template
	 */
	protected $template;

	/**
	 *
	 * @var string $layoutBuffer
	 */
	protected $layoutBuffer;

	/**
	 *
	 * @var string $useLayout
	 */
	protected $useLayout;

	/**
	 *
	 * @var string $pageTitle
	 */
	protected $pageTitle;

	/**
	 *
	 * @var array $helpers
	 */
	protected $helpers = array('Html', 'Form');

	/**
	 *
	 * @var array $loadedHelpers
	 */
	protected $loadedHelpers = array();

	/**
	 *
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
		if($this->app->getConfig('layout.region')){
			$this->regions = $this->app->getConfig('layout.region');
		} else {
			throw new bikes_MissingRegionDefinitionsInConfigIniException('There are no regions set in the application config.ini. Please set your regions in lib/application/config/config.ini');
		}
	}

	/**
	 *
	 * @return void
	 */
	public function setView($region, $view){
		if(!in_array($region, $this->regions)){
			$this->app->log("AssigningViewToNonDefinedRegionException thrown\n\tRegion {$region} not defined in config.ini");
			throw new bikes_AssigningViewToNonDefinedRegionException($region . ' region is not defined');
		}
		$class = PROJECT . '_View_' . ucwords($view);
		$this->views[$region] = new $class($this, $this->app);
	}

	/**
	 * addDataSet 
	 * 
	 * @param string $index 
	 * @param mixed $set 
	 * @access public
	 * @return void
	 */
	public function addDataSet($index, $set){
		if(!is_string($index)){
			throw new bikes_LayoutException('$index in bikes_Layout::addDataSet must be a string');
		}

		if(!is_object($set) && !is_array($set) && empty($set)){
			throw new bikes_LayoutException('$set in bikes_Layout::addDataSet can be an empty object or array but cannot be an empty string');
		}

		$this->viewData[$index] = $set;
	}

	/**
	 *
	 * @return void
	 */
	private function startOutputBuffer(){
		ob_start();
	}

	/**
	 *
	 * @return string $output
	 */
	private function getAndCleanOutputBuffer(){
		$output = ob_get_contents();
		ob_clean();
		return $output;
	}

	/**
	 *
	 * @return void
	 */
	private function endOutputBuffer(){
		ob_end_clean();
	}

	/**
	 *
	 * @return void
	 */
	public function setLayout($name){
		$this->useLayout = $name;
	}

	/**
	 *
	 * @return void
	 */
	protected function readLayoutTemplate(){
		if(empty($this->useLayout)){
			$templateName = $this->app->getConfig('layout.default');
		} else {
			$templateName = $this->useLayout . '.btmp';
		}
		$template = $this->app->getConfig('lib.path') . DS . $this->app->getConfig('template.path') . DS . $this->app->getConfig('layout.default');
		if(!file_exists($template)){
			$this->app->log("Template is not readable or the file does not exist.\n\tPlease set a correct template name in the config.ini value `layout.default`");
			throw new bikes_TemplateIsNotThereException('Template ' . $this->app->getConfig('layout.default') . ' does not exist.');
		}
		$templateBuffer = '';
		$handle = fopen($template, 'r');
		while(!feof($handle)){
			$templateBuffer .= fgets($handle);
		}
		fclose($handle);
		$this->template = $templateBuffer;
	}

	/**
	 * setPageTitle 
	 * 
	 * @param mixed $title 
	 * @access public
	 * @return void
	 */
	public function setPageTitle($title){
		$this->pageTitle = $title;
	}

	/**
	 * Instantiates helper classes and puts them in $this->loadedHelpers
	 *
	 * @return void
	 */
	protected function loadHelpers(){
		foreach($this->helpers as $helper){
			$class =  'bikes_View_Helper_' . $helper;
			$object = new $class($this->app);
			$this->loadedHelpers[$helper] = $object;
		}
	}

	/**
	 * Turns on output buffering, loads the main template into the buffer and iterates
	 * through the views and renders them. Each rendered view is stored in an output buffer,
	 * then the main template buffer regions are string replaced with the view content.
	 *
	 * @return string $layoutBuffer
	 */
	public function render(){
		$bikesFlash = $this->app->getFlash();
		$title = $this->pageTitle;
		$this->loadHelpers();

		if(!empty($this->loadedHelpers)){
			foreach(array_keys($this->loadedHelpers) as $helper){
				$helper_lower = strtolower($helper);
				$$helper_lower = $this->loadedHelpers[$helper];
			}
		}

		$this->startOutputBuffer();

		if(empty($this->useLayout)){
			$templateName = $this->app->getConfig('layout.default');
		} else {
			$templateName = $this->useLayout . '.btmp';
		}

		$template = $this->app->getConfig('app.path') . DS . 'templates' . DS . $templateName;

		if(!file_exists($template)){
			$this->app->log("Template is not readable or the file does not exist.\n\tPlease set a correct template name in the config.ini value `layout.default`", 'cache');
			throw new bikes_TemplateIsNotThereException('Template ' . $templateName . ' does not exist.');
		}

		include $template;

		$layoutBuffer = $this->getAndCleanOutputBuffer();

		if(!empty($this->viewData)){
			extract($this->viewData);
		}

		foreach($this->views as $region => $view){
			include $view->render();
			$viewBuffer = $this->getAndCleanOutputBuffer();
			$layoutBuffer = str_replace('<--{'.$region.'}-->', $viewBuffer, $layoutBuffer);
			unset($content);
		}
		$this->endOutputBuffer();
		return $layoutBuffer;
	}
}
