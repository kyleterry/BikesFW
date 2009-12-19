<?php

/**
 * bikes_Model_Mapper_OneToManyAbstract{ 
 * 
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Model_Mapper_OneToManyAbstract{
	
	/**
	 * app 
	 * 
	 * @var bikes_App $app
	 * @access protected
	 */
	protected $app;

	/**
	 * idMap 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $idMap = array();

	/**
	 * master 
	 * 
	 * @var bikes_Model_Abstract
	 * @access protected
	 */
	protected $master;

	/**
	 * slave 
	 * 
	 * @var bikes_Model_Abstract
	 * @access protected
	 */
	protected $slave;

	/**
	 * collections 
	 * 
	 * @var array
	 * @access protected
	 */
	protected $collections = array();

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
	 * Adds the related models to the mapper.
	 * Relational mappers only take 2 related models.
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
		} elseif(empty($this->slave)){
			if($model->hasCollection()){
				$this->collections['slave'] = $model->getCollection();
			}
			$this->slave = $model;
		} else {
			throw new bikes_CoreException(get_class($this) . ' can only take two related models.');
		}
	}

	/**
	 * Gets the name of the master ID column.
	 * 
	 * @access protected
	 * @return void
	 */
	protected function getMasterColumn(){
		return $this->master->getName() . 'Id';
	}

	/**
	 * Loads the right meta data for the master model
	 * 
	 * @access public
	 * @return void
	 */
	public function fetchMasterMeta(){
		$ids = $this->collections['master']->getIds();
		$collection = $this->slave->loadIn($this->getMasterColumn(), $ids);
		$this->collections['slave'] = $collection;
	}

	/**
	 * Gets a populated or empty collection.
	 * A populated collection will contain the VOs of the master's meta data.
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return bikes_Collection_Abstract $collection
	 */
	public function getMetaByMasterId($mid){
		$collection = get_class($this->collections['slave']);
		$collection = new $collection($this->app);
		$masterColumn = $this->getMasterColumn();
		foreach($this->collections['slave'] as $sid => $vo){
			if($vo->$masterColumn === $mid){
				$collection[$sid] = $vo;
			}
		}

		return $collection;
	}
}
