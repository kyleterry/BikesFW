<?php

/**
 * Acl for the bikes framework.
 * bikes_Acl is whitelist only. Black lists are for losers.
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_Acl{

	private static $instance;
	
	/**
	 * app 
	 * 
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;

	/**
	 * roles 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $roles = array();

	/**
	 * resources 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $resources = array();

	/**
	 * allowList 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $allowList = array();

	/**
	 * denyList 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $denyList = array();

	/**
	 * activeUser 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $activeUser;

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
	 * getInstance 
	 * 
	 * @static
	 * @access public
	 * @return void
	 */
	public static function getInstance(){
		if(!self::$instance instanceof self){
			self::$instance =  new self(bikes_App::getInstance());
		}
		return self::$instance;
	}

	/**
	 * addRole 
	 * 
	 * @param bikes_Acl_Role|string $role 
	 * @access public
	 * @return void
	 */
	public function addRole($role){
		if(is_string($role)){
			$role = new bikes_Acl_Role($role);
		}

		if(!in_array($role, $this->roles)){
			$this->roles[$role->getName()] = $role;
		}
		return $this;
	}

	/**
	 * addResource 
	 * 
	 * @param bikes_Acl_Resource|string $resource 
	 * @access public
	 * @return void
	 */
	public function addResource($resource){
		if(is_string($resource)){
			$resource = new bikes_Acl_Resource($resource);
		}

		if(!in_array($resource, $this->resources)){
			$this->resources[$resource->getName()] = $resource;
		}
		return $this;
	}

	/**
	 * allow 
	 * 
	 * @param string $role 
	 * @param string|array $resource 
	 * @access public
	 * @return void
	 */
	public function allow($role, $resources){
		if(!in_array($role, array_keys($this->roles))){
			throw new bikes_AclException('Roles must be added before allowing them access to resources');
		}

		if(is_string($resources) && 'all' === $resources){
			foreach($this->resources as $resource){
				$this->allowList[$role][$resource->getName()] = true;
			}
			return $this;
		}

		if(is_string($resources)){
			$resources = array($resources);
		}

		foreach($resources as $resource){
			if(!in_array($resource, array_keys($this->resources))){
				throw new bikes_AclException('Resources must be added before assigning them to roles');
			}
			
			if(isset($this->allowList[$role][$resource])){
				continue;
			}

			$this->allowList[$role][$this->resources[$resource]->getName()] = true;
		}
		return $this;
	}

	/**
	 * activeUserIs 
	 * 
	 * @param mixed $role 
	 * @access public
	 * @return void
	 */
	public function activeUserIs($role){
		if(!in_array($role, array_keys($this->roles))){
			throw new bikes_AclException('Roles must be added before setting them as active.');
		}

		$this->activeRole = $role;
		return $this;
	}

	/**
	 * Checks if the current loaded role is allowed to view a resource.
	 * 
	 * @param mixed $resource 
	 * @access public
	 * @return true|false
	 */
	public function isAllowed($resource){
		if(empty($resource)){
			throw new bikes_AclException('What? Are you crazy? isAllowed cannot take a blank resource!');
		}
		if(isset($this->allowList[$this->activeRole][$resource]) && true === $this->allowList[$this->activeRole][$resource]){
			return true;
		}
		return false;
	}
}
