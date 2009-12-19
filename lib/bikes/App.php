<?php

/**
 * This class is the glue to it all. It handles communication between the various
 * parts of the framework.
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009 BikesFW Bikes Framework
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded) http://trythisbike.org/LICENSE
 */
class bikes_App{

	/**
	 * Holds the singleton instance of this awesome class.
	 *
	 * @var bikes_App
	 * @access private
	 * @static
	 */
	private static $instance;

	/**
	 * Holds an instance of bikes_Request.
	 * 
	 * @var bikes_Request
	 * @access protected
	 */
	protected $request;

	/**
	 * Holds an instance of bikes_Response.
	 * 
	 * @var bikes_Response
	 * @access protected
	 */
	protected $response;

	/**
	 * Holds an instance of bikes_Router. 
	 * 
	 * @var bikes_Router
	 * @access protected
	 */
	protected $router;

	/**
	 * Holds an instance of bikes_Cache_Adapter_Apc.
	 * 
	 * @var bikes_Cache_Adapter_Apc
	 * @access protected
	 */
	protected $apc;

	/**
	 * Holds an instance of PDO. 
	 * 
	 * @var PDO
	 * @access protected
	 */
	protected $db;

	/**
	 * Holds an instance of bikes_Session_Abstract. 
	 * 
	 * @var bikes_Session_Abstract
	 * @access protected
	 */
	protected $session;

	/**
	 * Holds an instance of bikes_Flash.
	 * 
	 * @var bikes_Flash
	 * @access protected
	 */
	protected $flash;

	/**
	 * Holds an instance of bikes_Layout. 
	 * 
	 * @var bikes_Layout
	 * @access protected
	 */
	protected $layout;

	/**
	 * Holds an instance of bikes_Config 
	 * 
	 * @var bikes_Config
	 * @access protected
	 */
	protected $config;

	/**
	 * Holds an instance of bikes_Acl 
	 * 
	 * @var bikes_Acl
	 * @access protected
	 */
	protected $acl;

	/**
	 * __construct. So ronery.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->config = new bikes_Config($this);
		$this->apc = new bikes_Cache_Adapter_Apc($this);
		self::$instance = $this;
	}
	
	/**
	 * Triggered when this object is woke up from a deep sleep inside of APC.
	 * 
	 * @return void
	 */
	public function __wakeUp(){
		self::$instance = $this;
	}

	/**
	 * getInstance 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function getInstance(){
		if(!self::$instance instanceof self){
			self::$instance = new self(new bikes_Config);
		}
		return self::$instance;
	}

	/**
	 * Initializes objects that shouldn't be persistent. This is called in the bootstrap class (bikes_Bootstap). 
	 * 
	 * @access public
	 * @return void
	 */
	public function init(){
		$this->request = new bikes_Request($this);
		$this->router = new bikes_Router($this);
	}

	/**
	 * Depricate. bikes_Cache_Adapter_Apc is a singleton and you should use it's static getInstance() method to retrieve it
	 * 
	 * @access public
	 * @return bikes_Cache_Adapter_Apc $this->apc
	 */
	public function getApc(){
		return $this->apc;
	}

	/**
	 * Gets a configuration value from the bikes_Config object. 
	 * 
	 * @access public
	 * @return bikes_Config $this->config
	 */
	public function getConfig($index){
		return $this->config->getConfig($index);
	}

	/**
	 * Sets a configuration value in the bikes_Config object.
	 * 
	 * @param string $index 
	 * @param string $value 
	 * @access public
	 * @return boolean true on success
	 * @return boolean false on failure
	 */
	public function setConfig($index, $value){
		return $this->config->setConfig($index, $value);
	}

	/**
	 * Public access to protected bikes_Request property.
	 * 
	 * @access public
	 * @return void
	 */
	public function getRequest(){
		if(!$this->request instanceof bikes_Request){
			$this->request = new bikes_Request($this);
		}
		return $this->request;
	}

	/**
	 * Public access to protected bikes_Response property.
	 * This is a 'loaded when you need it' object and is 
	 * instantiated when you call the getter method.
	 * 
	 * @access public
	 * @return void
	 */
	public function getResponse(){
		if(!$this->response instanceof bikes_Response){
			$this->response = new bikes_Response($this);
		}
		return $this->response;
	}

	/**
	 * Public access to protected bikes_Router property. 
	 * 
	 * @access public
	 * @return void
	 */
	public function getRouter(){
		if(!$this->router instanceof bikes_Router){
			$this->router = new bikes_Router($this);
		}
		return $this->router;
	}

	/**
	 * Public access to protected PDO property.
	 * This will instantiate a new PDO object if it's not net already.
	 * 'loaded when you need it'
	 * 
	 * @access public
	 * @return void
	 */
	public function getDb(){
		if(!$this->db instanceof PDO){
			try{
				$this->db = new PDO(
					new bikes_PDODsn($this->getConfig('db.engine'), $this->getConfig('db.database'), $this->getConfig('db.host')),
					$this->getConfig('db.user'),
					$this->getConfig('db.password'),
					array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
				);
			}
			catch(PDOException $e){
				throw new bikes_CoreException('There was a problem starting the database connection');
			}
		}
		return $this->db;
	}

	/**
	 * Public access to protected bikes_Flash property.
	 * 'loaded when you need it'
	 * 
	 * @access public
	 * @return void
	 */
	public function getFlash(){
		if(!$this->flash instanceof bikes_Flash){
			$this->flash = new bikes_Flash($this);
		}
		return $this->flash;
	}

	/**
	 * Public access to protected bikes_Session_Abstract property.
	 * This will look at the session type set in the configuration
	 * and instanciate that type of session adapter.
	 * 
	 * @access public
	 * @return bikes_Session_Abstract $this->session;
	 */
	public function getSession(){
		if(!$this->session instanceof bikes_Session_Abstract){
			if(!$type = strtolower($this->getConfig('session.type'))){
				throw new Exception('session.type must be set in your config.ini if you plan on using a session.');
			}
			switch($type){
				case 'DB':
				case 'Db':
				case 'db':
					$this->session = new bikes_Session_Adapter_Db($this);
					break;
			}
		}
		return $this->session;
	}

	/**
	 * Returns the core Acl object.
	 * 
	 * @access public
	 * @return void
	 */
	public function getAcl(){
		if(!$this->acl instanceof bikes_Acl){
			$this->acl = new bikes_Acl(bikes_App::getInstance());
		}
		return $this->acl;
	}

	/**
	 * Returns the application layout object.
	 * 
	 * @access public
	 * @return void
	 */
	public function getLayout(){
		if(!$this->layout instanceof bikes_Layout){
			$this->useLayout = true;
			$this->layout   = new bikes_Layout($this);
		}
		return $this->layout;
	}

	/**
	 * Oddly named method, but it returns the current server host that the request went to.
	 * 
	 * @access public
	 * @return string
	 */
	public function getDomain(){
		return $_SERVER['HTTP_HOST'];
	}

	/**
	 * log 
	 * 
	 * @access public
	 * @return void
	 */
	public function log(){
		return;
	}

	/**
	 * Dispatches a request and starts the process of running the application.
	 * This will choose a controller based on the requesting client type.
	 * 
	 * @access public
	 * @return mixed
	 */
	public function dispatch(){
		$requestClient = PHP_SAPI;
		
		if('cli' === $requestClient){
			$front = new bikes_Cli_Controller_Front($this);
		} else {
			$front = new bikes_Web_Controller_Front($this);
		}

		$front->init();
		
		return $this->getResponse()->respond($front->run());
	}
}
