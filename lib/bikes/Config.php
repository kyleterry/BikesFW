<?php
/**
 * This class manages the configurations in the framework.
 * It is cached with APC when it caches the setup routine.
 * This object is housed in bikes_App.
 *
 * @author Kyle Terry
 * @class bikes_Config
 */
class bikes_Config{
	/**
	 * Initial Configuration file path
	 *
	 * @var string $_configFile
	 */
	private $_configFile;

	/**
	 * Application configs. These are set by both an ini file in the application/config
	 * and by ::setConfig()
	 *
	 * @var array $configs
	 */
	public $configs = array();

	/**
	 *
	 * @return void
	 */
	public function __construct(){
		$this->configs['lib.path'] = LIB_PATH;
		$this->configs['app.path'] = PROJECT_PATH;
		$this->_configFile = $this->configs['app.path'] . DS . 'Config' . DS . 'config.ini';

		if(!is_readable($this->_configFile)){
			throw new bikes_MissingOrInvalidConfigException('Invalid or missing config');
		}

		$this->configs = array_merge($this->configs, parse_ini_file($this->_configFile));
	}

	/**
	 * Gets a configuration.
	 *
	 * @return string $this->configs[$name] on success | false on fail
	 */
	public function getConfig($name){
		if(isset($this->configs[$name])){
			return $this->configs[$name];
		}
		return false;
	}

	/**
	 *
	 * @exception bikes_ConfigAlreadyExistsException
	 * @return true on success
	 */
	public function setConfig($name, $value){
		if(isset($this->config[$name])){
			throw new bikes_ConfigAlreadyExistsException('Configuration with name: 
				' . $name . ' already exists.');
		}
		$this->config[$name] = $value;
		return true;
	}
}
