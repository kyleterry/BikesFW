<?php

/**
 * This class holds instances of a model's value objects (VO, bikes_VO_Abstract).
 * It contains a few methods to help with organization and retrieval of those
 * classes.
 * 
 * @uses ArrayAccess
 * @uses Iterator
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009 BikesFW Bikes Framework
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Collection_Abstract implements ArrayAccess, Iterator{

	/**
	 * Holds an instance of bikes_App
	 * 
	 * @var bikes_App
	 * @access protected
	 */
	protected $app;

	/**
	 * Array of bikes_VO_Abstract instances
	 * 
	 * @var array
	 * @access protected
	 */
	protected $vos = array();

	/**
	 * Id's of every VO object in the collection
	 * 
	 * @var array
	 * @access protected
	 */
	protected $idSet = array();

	/**
	 * The Constructor
	 * 
	 * @param bikes_App $app 
	 * @access public
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
	}

	/**
	 * Used for method calls on specific "columns" in the VOs.
	 * I.E. ->getAllName('Kyle'); will return all VOs with the
	 * value 'Kyle' in the "name" column.
	 * 
	 * @param string $name 
	 * @param mixed $args 
	 * @access public
	 * @return void
	 */
	public function __call($name, $args){
		switch($name){
			case (0 === strncmp('getAll', $name, 6)):
				$column = substr($name, 6);
				return $this->_getAll($column, $args);
				break;
			default:
				throw new bikes_MethodDoesNotExistException('The method ' . $name . ' does not exist in ' . get_class($this));
		}
	}

	/**
	 * We only need to serialize vos and idSet when storing this objects opcode.
	 * 
	 * @access public
	 * @return void
	 */
	public function __sleep(){
		return array('vos', 'idSet');
	}

	/**
	 * _getAll 
	 * 
	 * @param mixed $column 
	 * @param mixed $args 
	 * @access protected
	 * @return array
	 */
	protected function _getAll($column, $args = null){
		$set = array();
		if(!empty($args)){
			foreach($this->vos as $vo){
				if($args[0] == $vo->$column){
					$set[$vo->id] = $vo->$column;
				}
			}
		} else {
			foreach($this->vos as $vo){
				$set[$vo->id] = $vo->$column;
			}
		}
		return $set;
	}

	/**
	 * Counts the amount of VO objects stored in the collection.
	 * 
	 * @access public
	 * @return void
	 */
	public function countVOs(){
		return count($this->vos);
	}

	/**
	 * Iterator method to return current array position.
	 *
	 * @return mixed from $this->vos
	 */
	public function current() {
		return current($this->vos);
	}

	/**
	 * Iterator method to rewind array position
	 *
	 * @return void|return is ignored
	 */
	public function rewind() {
		return reset($this->vos);
	}

	/**
	 * Iterator method to return the key of the current element.
	 *
	 * @return mixed|integer 0 on failure
	 */
	public function key() {
		return key($this->vos);
	}

	/**
	 * Iterator method to jump to next array position
	 *
	 * @return void|return is ignored
	 */
	public function next() {
		return next($this->vos);
	}

	/**
	 * Iterator method to jump to previous array position
	 *
	 * @return void|return is ignored
	 */
	public function prev() {
		return prev($this->vos);
	}

	/**
	 * Iterator method to check if the current array position is valid
	 *
	 * @return boolean true on success|boolean false on failure
	 */
	public function valid() {
		return current($this->vos) !== false;
	}

	/**
	 * ArrayAccess method to see if offset exists
	 *
	 * @return true on success|false on failure
	 */
	public function offsetExists($index) {
		return isset($this->vos[$index]);
	}

	/**
	 * ArrayAccess method to get value from array
	 *
	 * @return mixed
	 */
	public function offsetGet($index) {
		return $this->getObject($index);
	}

	/**
	 * ArrayAccess method to set a new value in $this->vos array
	 *
	 * @return boolean true on success|boolean false on falure
	 */
	public function offsetSet($index, $data) {
		return $this->addObject($index, $data);
	}

	/**
	 * ArrayAccess method to unset a value from $this->vos array
	 *
	 * @return void
	 */
	public function offsetUnset($index) {
		unset( $this->$offset );
	}

	/**
	 * This method is executed by ArrayAccess::offsetSet.
	 *
	 * @param $index
	 * @param $data
	 * @return void
	 */
	protected function addObject($index, $data){
		$this->vos[$index] = $data;
		$this->idSet[] = $index;
		if(isset($this->vos[$index])){
			return true;
		}
		return false;
	}

	/**
	 * addVo 
	 * 
	 * @param integer $index 
	 * @param bikes_VO_Abstract $value 
	 * @access public
	 * @return bikes_Collection_Abstract
	 */
	public function addVo($index, $value){
		$this->vos[$index] = $value;
		$this->idSet[] = $index;
		return $this;
	}

	/**
	 * This method is called by ArrayAccess method $this->offsetGet().
	 *
	 * @param $object
	 * @access protected
	 * @return mixed
	 */
	protected function getObject($object){
		return $this->vos[$object];
	}

	/**
	 * Getter method to grab the current VO indexes.
	 *
	 * @access public
	 * @return array $ids
	 */
	public function getIds(){
		/*$ids = array();
		foreach($this->vos as $key => $value){
			$ids[] = $key;
		}*/
		return (array)$this->idSet;
	}

	/**
	 * Sorts the $this->vos array
	 *
	 * @param $type
	 * @return void
	 */
	public function sort($type = null){
		if(empty($this->vos)){
			throw new bikes_CollectionVosArrayIsEmptyException('You cannot sort an empty collection. Thank you, come again.');
		}
		if(empty($type) OR 'asc' == $type){
			asort($this->vos);
		}
		if('desc' == $type){
			arsort($this->vos);
		}
	}

	/**
	 * Merges one collection into another collection
	 * TODO document this well. It's misuse can be bad news bears.
	 *
	 * @param bikes_Collection_Abstract $collection
	 * @param mixed $indexUnder
	 * @return bikes_Collection_Abstract $this
	 */
	public function mergeCollection(bikes_Collection_Abstract $collection, $indexUnder){
		$split = $this->split($collection);
		foreach($split as $index => $collection){
			$this->vos[$index][$indexUnder] = $collection;
		}
		return $this;
	}

	/**
	 * This method splits a collection into several collections (one for each index).
	 * This is so you can nest many to many data in some kind of "master" collection.
	 * TODO I will document this further because the use of this method can get quite confusing.
	 *
	 * @param bikes_Collection_Abstract $collection
	 * @return array $split
	 */
	protected function split(bikes_Collection_Abstract $collection){
		$split = array();
		$class = get_class($collection);
		foreach($collection as $id => $values){
			$subCollection = new $class($this->app);
			foreach($values as $subId => $value){
				$subCollection[$id] = $value;
			}
			$split[$id] = $subCollection;
		}
		if(empty($split)){
			throw new bikes_AttemptedSplitOfEmptyCollectionException('You cannot split an empty collection ' . $class);
		}
		return $split;
	}

	/**
	 * Checks if the collection contains 0 VO objects
	 * 
	 * @access public
	 * @return boolean true|false
	 */
	public function isEmpty(){
		if(empty($this->vos)){
			return true;
		}
		return false;
	}
}
