<?php

/**
 * Parses the config.ini database settings and creates a DSN for PDO
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
class bikes_PDODsn{

	/**
	 *  
	 * 
	 * @var string $dsn
	 * @access protected
	 */
	protected $dsn;

	/**
	 * ctor
	 * 
	 * @param string $engine 
	 * @param string $database 
	 * @param string $host 
	 * @access public
	 * @return void
	 */
	public function __construct($engine, $database, $host){
		switch($engine){
			case 'mysql':
				$this->dsn = 'mysql:dbname='.$database.';host='.$host;
		}
	}

	/**
	 * __toString 
	 * 
	 * @access public
	 * @return string $this->dsn
	 */
	public function __toString(){
		return $this->dsn;
	}
}
