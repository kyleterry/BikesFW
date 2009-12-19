<?php

/**
 * bikes_Model_Mapper_BelongsTo{ 
 * 
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Model_Mapper_BelongsToAbstract{
	
	/**
	 * Holds an instance of bikes_App
	 * 
	 * @var bikes_App $app
	 * @access protected
	 */
	protected $app;

	/**
	 * idMap 
	 * 
	 * @var array $idMap
	 * @access protected
	 */
	protected $idMap = array();

	/**
	 * collections 
	 * 
	 * @var array $collections
	 * @access protected
	 */
	protected $collections = array();

	/**
	 * master 
	 * 
	 * @var mixed $master
	 * @access protected
	 */
	protected $master;

	/**
	 * slave 
	 * 
	 * @var mixed $slave
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
	 * addModel 
	 * 
	 * @param bikes_Model_Abstract $model 
	 * @access public
	 * @return void
	 */
	public function addModel(bikes_Model_Abstract $model){
		if(empty($this->master)){
			if($model->hasCollection()){
				$this->collections['master'] = $model->getCollection();
			}
			$this->master = $model;
		} else {
			if($model->hasCollection()){
				$this->collections['slave'] = $model->getCollection();
			}
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
	 * 
	 * @access protected
	 * @return unknown_type
	 */
	public function getMasterCollection(){
		return $this->collections['master'];
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
		$collection = $this->collections['master'];
		$slaveColumn = $this->getSlaveColumn();
		$ids = array();
		foreach($collection as $item){
			$ids[] = $item->$slaveColumn;
		}

		if(!empty($ids)){
			$this->slave->loadIn('id', $ids);
			
			if(empty($this->collections['slave'])){
				$this->collections['slave'] = $this->slave->getCollection();
			}
		}
	}

	/**
	 * getMetaByMasterId 
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return void
	 */
	public function getMetaByMasterId($id){
		$masterCollection = $this->collections['master'];
		$slaveCollection = $this->collections['slave'];
		$collection = get_class($slaveCollection);
		$collection = new $collection($this->app);
		if(isset($masterCollection[$id])){
			$slaveColumn = $this->getSlaveColumn();
			$item = $masterCollection[$id];
			$slaveId = $item->$slaveColumn;
			if(isset($slaveCollection[$slaveId])){
				$collection[$slaveId] = $slaveCollection[$slaveId];
			}
		}
		return $collection;
	}
}
