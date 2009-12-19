<?php

/**
 * bikes_Model_RelationalMapperAbstract{ 
 * 
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Model_Mapper_ManyToManyAbstract{

	/**
	 * app 
	 * 
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;

	/**
	 * table 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $table;

	/**
	 * columns 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $columns = array();

	/**
	 * dataSet 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $idMap = array();

	/**
	 * models 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $models = array();

	/**
	 * collections 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $collections = array();

	/**
	 * Master model
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $master;

	/**
	 * Slave model
	 * 
	 * @var mixed
	 * @access protected
	 */
	protected $slave;

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
	 * __call 
	 * 
	 * @param string $name 
	 * @param mixed $params 
	 * @access public
	 * @return void
	 */
	public function __call($name, $params){
		switch($name){
			case (0 === strncmp('loadBy', $name, 6)):
				$name = substr($name, 6);
				return $this->_loadBy($name, $params);
				break;
			default:
				trigger_error('Method does not exist in ' . get_class($this));
				break;
		}
	}

	/**
	 * addModel 
	 * 
	 * @param bikes_Model_Abstract $model 
	 * @access public
	 * @return void
	 */
	public function addModel(bikes_Model_Abstract $model){
		if($model->hasCollection()){
			$this->collections[$model->getName()] = $model->getCollection();
		}

		if(empty($this->master)){
			$this->master = $model;
		} else {
			$this->slave = $model;
		}
	}

	/**
	 * getMasterColumn 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function getMasterColumn(){
		return $this->master->getName() . 'Id';
	}

	/**
	 * getSlaveColumn 
	 * 
	 * @access protected
	 * @return void
	 */
	protected function getSlaveColumn(){
		return $this->slave->getName() . 'Id';
	}

	/**
	 * Returns the master collection object.
	 * 
	 * @access public
	 * @return void
	 */
	public function getMasterCollection(){
		if(!empty($this->collections[$this->master->getName()])){
			return $this->collections[$this->master->getName()];
		}
	}

	/**
	 * getMap 
	 * 
	 * @access public
	 * @return array $this->idMap
	 */
	public function getMap(){
		return (array)$this->idMap;
	}

	/**
	 * getSlaveIds 
	 * 
	 * @access protected
	 * @return array $ids
	 */
	protected function getSlaveIds(){
		$ids = array();
		foreach($this->idMap as $item){
			foreach($item as $id){
				$ids[$id] = $id;
			}
		}
		return (array)$ids;
	}

	/**
	 * fetchMasterMeta 
	 * 
	 * @access public
	 * @return void
	 */
	public function fetchMasterMeta(){
		$ids = $this->collections[$this->master->getName()]->getIds();
		$this->_loadBy($this->getMasterColumn(), $ids);
		$this->collections[$this->slave->getName()] = $this->slave->loadIn('id', $this->getSlaveIds());
	}

	/**
	 * getMetaByMasterId 
	 * 
	 * @param integer $id 
	 * @access public
	 * @return void
	 */
	public function getMetaByMasterId($id){
		$collection = get_class($this->collections[$this->slave->getName()]);
		$slaveCollection = $this->collections[$this->slave->getName()];
		$collection = new $collection($this->app);
		if(!empty($this->idMap[$id])){
			$slaveIds = $this->idMap[$id];
			foreach($slaveIds as $id){
				$collection[$id] = $slaveCollection[$id];
			}
		}
		return $collection;
	}

	/**
	 * loadByID 
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return void
	 */
	public function loadByID($id){

	}

	/**
	 * _loadBy 
	 * 
	 * @param mixed $column 
	 * @param array $ids 
	 * @access protected
	 * @return void
	 */
	protected function _loadBy($column, $ids){
		$column = strtolower($column);
		if(empty($ids)){
			return false;
		}

		$db = $this->app->getDb();

		$query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $column . '` in(';
		foreach($ids as $id){
			$query .= ':' . $id . ',';
		}
		$query = trim($query, ',');
		$query .= ');';
		$statement = $db->prepare($query);
		foreach($ids as $id){
			$statement->bindValue(':' . $id, $id);
		}
		$statement->execute();
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->indexIds($results, $this->getMasterColumn(), $this->getSlaveColumn());
	}

	/**
	 * indexIds 
	 * 
	 * @param array $resultSet 
	 * @param string $indexColumn 
	 * @param string $subColumn 
	 * @access protected
	 * @return array $this->idMap
	 */
	protected function indexIds($resultSet, $master, $slave){
		foreach($resultSet as $row){
			$this->idMap[$row[$master]][] = $row[$slave];
		}
		return $this->idMap;
	}
}
