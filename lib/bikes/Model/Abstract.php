<?php

/**
 * bikes_Model_Abstract{ 
 * 
 * @abstract
 * @package BikesFW
 * @version $id$
 * @copyright 2009
 * @author Kyle Terry <kyle@kyleterry.com>
 * @license BSD (Inlcuded)
 */
abstract class bikes_Model_Abstract{

	/**
	 *
	 * @var bikes_App $app
	 */
	protected $app;

	/**
	 * Convenience property to access the applications request object.
	 * Set in the ctor.
	 *
	 * @var bikes_Request
	 */
	protected $request;

	/**
	 *
	 * @var bikes_Collection_Abstract $collection
	 */
	protected $collection;

	/**
	 * collectionName 
	 * 
	 * @var string
	 * @access protected
	 */
	protected $collectionName;

	/**
	 *
	 * @var array $subCollections
	 */
	protected $subCollections = array();

	/**
	 *
	 * @var string $table
	 */
	protected $table;

	/**
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 *
	 * @var array $columns
	 */
	protected $columns = array();

	/**
	 *
	 * @var array $hasMany
	 */
	protected $hasMany = array();

	/**
	 *
	 * @var array $belongsTo
	 */
	protected $belongsTo = array();

	/**
	 *
	 * @return void
	 */
	public function __construct(bikes_App $app){
		$this->app = $app;
		$this->request = $this->app->getRequest();
	}

	/**
	 *
	 * @return void
	 */
	public function __call($name, $args){
		switch($name){
			case (0 === strncmp('loadBy', $name, 6)):
				$clause = substr($name, 6);
				return $this->_loadBy($clause, $args);
				break;
			default:
				throw new bikes_MethodDoesNotExistException('The method ' . $name . ' does not exist in ' . get_class($this));
		}
	}

	/**
	 * Returns the name of the model.
	 * 
	 * @access public
	 * @return string $this->name
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * Checks if the model has loaded a collection of data yet.
	 * 
	 * @access public
	 * @return void
	 */
	public function hasCollection(){
		if(!empty($this->collection)){
			return true;
		}
		return false;
	}

	/**
	 * Gets the PDO databse object from bikes_App
	 *
	 * @return PDO $this->app->getDB()
	 */
	protected function getDB(){
		return $this->app->getDB();
	}

	/**
	 * getModel 
	 * 
	 * @param mixed $model 
	 * @access public
	 * @return bike_Model_Abstract $model
	 */
	public function getModel($model){
		$class = PROJECT . '_Model_' . ucwords($model);
		$model = new $class($this->app);
		return $model;
	}

	/**
	 * getCollection 
	 * 
	 * @access public
	 * @return bikes_Collection_Abstract $this->collection
	 */
	public function getCollection(){
		return $this->collection;
	}

	/**
	 *
	 * @param string $column
	 * @param mixed $value
	 * @return bikes_Collection_Abstract
	 */
	protected function _loadBy($column, $value){
		$column = strtolower(substr($column, 0,1)) . substr($column, 1);
		$value = $value[0];
		if(!in_array($column, $this->getTableColumns())){
			throw new bikes_ColumnDoesNotExistInTableException('The column ' . $column . ' does not exist in ' . get_class($this));
		}
		$db = $this->getDB();
		$query = 'SELECT * FROM `' . $this->table . '` WHERE `' . $column . '` = :' . $column . ';';
		$statement = $db->prepare($query);
		$statement->bindValue(':' . $column, $value);
		$statement->execute();
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->fillCollection($results);
	}

	/**
	 * Load everything not flagged as deleted from the table
	 *
	 * @param string $order
	 * @return bikes_Collection_Abstract
	 */
	public function load($excludeDeleted = true, $order = 'ASC', $limit = null){
		if(true === $excludeDeleted){
			$deleted = 'deleted = :deleted';
		}
		$db = $this->getDB();
		$query = 'SELECT * FROM `' . $this->table . '` WHERE deleted = :deleted ORDER BY id ' . $order . ';';
		$statement = $db->prepare($query);
		$statement->bindValue(':deleted', '0');
		$statement->execute();
		//$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->fetchIntoCollection($statement);
	}

	/**
	 * fetchBySql 
	 * 
	 * @param mixed $sql 
	 * @param mixed $data 
	 * @param mixed $fillCollection 
	 * @access public
	 * @return PDO if $fillCollection is false
	 * @return bikes_Collection_Abstract if $fillCollection is true
	 */
	public function fetchBySql($sql, $data, $fillCollection = false){
		$sql = (string) $sql;
		if(!is_array($data)){
			throw new bikes_ModelException('$data (param 2) must be an array of values to bind');
		}
		$db = $this->getDb();
		$statement = $db->prepare($sql);
		$statement->execute();
		if(true === $fillCollection){
			return $this->fetchIntoCollection($statement);
		}
		return $statement;
	}

	/**
	 *
	 * @param integer $id
	 * @return bikes_Collection_Abstract from $this->fillCollection
	 */
	public function loadByID($id){
		$db = $this->getDB();
		$query = 'SELECT * FROM `' . $this->table . '` WHERE id = :id;';
		$statement = $db->prepare($query);
		$statement->bindValue(':id', $id);
		$statement->execute();
		//$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->fetchIntoCollection($statement);
	}

	/**
	 * Loads every deleted record from the table.
	 *
	 * @return bikes_Collection_Abstract
	 */
	public function loadDeleted(){
		$db = $this->getDB();
		$query = 'SELECT * FROM `' . $this->table . '` WHERE deleted = :deleted;';
		$statement = $db->prepare($query);
		$statement->bindValue(':deleted', 'Y');
		$statement->execute();
		//$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->fetchIntoCollection($statement);
	}

	/**
	 * 
	 * @param string $column
	 * @param array $values
	 * @return bikes_Collection_Abstract
	 */
	public function loadIn($column, array $values){
		if(empty($values)){
			$collection = PROJECT . '_Collection_' . ucwords($this->name);
			$this->collection = new $collection($this->app);
			return $this->collection;
		}
		$db = $this->getDB();
		$query = 'SELECT * FROM `' . $this->table . '` where `' . $column . '` in(';
		foreach($values as $value){
			$query .= ':' . $value . ',';
		}
		$query = trim($query, ',');
		$query .= ');';
		$statement = $db->prepare($query);
		foreach($values as $value){
			$statement->bindValue(':' . $value, $value);
		}
		$statement->execute();
		//$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->fetchIntoCollection($statement);
	}

	/**
	 * Creates a new record
	 * 
	 * @param bikes_VO_Abstract $vo 
	 * @access public
	 * @return void
	 */
	public function create(bikes_VO_Abstract $vo){
		$required = $this->getRequiredColumns();
		$voProperties = $this->getVOPropertyValues($vo);
		$missing = array();
		foreach($required as $column){
			if(empty($voProperties[$column])){
				$missing[] = $column;
			}
		}
		if(!empty($missing)){
			$this->app->triggerError(get_class($this), REQ_COLS_MISSING, $missing);
			return false;
		}
		unset($missing);
		$db = $this->getDB();
		$columns = array();
		$columnsPrepared = array();
		$columnsValues = array();
		foreach($voProperties as $column => $value){
			if(!empty($value)){
				$columns[] = '`' . $column . '`';
				$columnsPrepared[] = ':' . $column;
				$columnValues[':'.$column] = $value;
			}
		}
		$query = 'INSERT INTO `' . $this->table . '` (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $columnsPrepared) . ');';
		$statement = $db->prepare($query);
		$statement->execute($columnValues);
		$vo->id = $db->lastInsertID();
		$this->collection[$vo->id] = $vo;
		return $vo->id;
	}

	/**
	 * Updates a VO
	 * 
	 * @param bikes_VO_Abstract $vo 
	 * @access public
	 * @return integer
	 */
	public function update(bikes_VO_Abstract $vo){
		$voProperties = $this->getVOProperties($vo);
		$columnsPrepared = array();
		$columnValues = array();
		foreach($voProperties as $column => $value){
			if(!empty($vo->$column)){
				$columnsPrepared[] = '`' . $column . '` = :' . $column;
				$columnValues[':'.$column] = $vo->$column;
			}
		}
		$columnValues[':id'] = $vo->id;
		$db = $this->getDB();
		$query = 'UPDATE `' . $this->table . '` SET ' . implode(', ', $columnsPrepared) . ' WHERE id = :id;';
		$statement = $db->prepare($query);
		$statement->execute($columnValues);
		return $db->lastInsertID();
	}

	/**
	 * Saves a VO. If the id is set in the VO it will attempt to update the record.
	 * 
	 * @param bikes_VO_Abstract $vo 
	 * @param mixed $whereClause 
	 * @access public
	 * @return integer
	 */
	public function save(bikes_VO_Abstract $vo, $whereClause = null){
		if(isset($vo->id)){
			return $this->update($vo);
		} else {
			return $this->create($vo);
		}
	}

	/**
	 *
	 * @param $id
	 * @param $keepButFlag
	 * @return boolean
	 */
	public function delete($id, $keepButFlag = false){
		if(false === $keepButFlag){
			$db = $this->getDB();
			$query = 'DELETE FROM `' . $this->table . '` WHERE `id` = :id;';
			$statement = $db->prepare($query);
			$statement->execute(array(':id' => $id));
			return true;
		} else {
			$db = $this->getDB();
			$query = 'UPDATE `' . $this->table . '` SET `deleted` = 1 WHERE `id` = :id;';
                        $statement = $db->prepare($query);
			$statement->execute(array(':id' => $id));
			return true;
		}
	}

	/**
	 * TODO take PDO and not an array to make shit faster!
	 *
	 * @return bikes_Collection_Abstract $this->collection
	 */
	protected function fillCollection(array $results){
		$class = PROJECT . '_Collection_' . ucwords($this->name);
		$this->collection = new $class($this->app);
		if(empty($results)){
			return $this->collection;
		}
		foreach($results as $row){
			$vo = $this->populateVO($row);
			$this->collection[$vo->id] = $vo;
		}
		return $this->collection;
	}

	/**
	 * Fetches database results into a model-collection object. 
	 * 
	 * @param PDOStatement $pdo 
	 * @access public
	 * @return bikes_Collection_Abstract
	 */
	public function fetchIntoCollection(PDOStatement $pdo){
		$vo = PROJECT . '_VO_' . ucwords($this->name);
		$collection = PROJECT . '_Collection_' . ucwords($this->name);
		$collection = new $collection(bikes_App::getInstance());
		$pdo->setFetchMode(PDO::FETCH_CLASS, $vo);
		while($v = $pdo->fetch()){
			$collection->addVo($v->id, $v);
		}
		$this->collection = $collection;
		return $this->collection;
	}

	/**
	 * Merges a sub collection with the models main collection.
	 *
	 * @return void
	 */
	public function mergeSubCollection(string $collectionName, string $mergeAs, integer $inRowID){
		if(!in_array($collectionName, $this->subCollections)){
			throw new bikes_ValueNotInSubCollectionArray('Value ' . $collectionName . ' was not found in the subCollection array in ' . get_class($this));
		}


	}

	/**
	 * Getter method to snag an array of the model column names.
	 *
	 * @return array
	 */
	protected function getTableColumns(){
		return array_keys($this->columns);
	}

	/**
	 * Getter method to snag an array of the required model column names.
	 *
	 * @return array $required
	 */
	protected function getRequiredColumns(){
		$required = array();
		foreach($this->columns as $column => $args){
			if($args['required'] === true){
				$required[] = $column;
			}
		}
		return $required;
	}

	/**
	 * This gives you the VO class name for this model.
	 *
	 * @return string $class
	 */
	protected function getVO(){
		$class = PROJECT . '_VO_' . ucwords($this->name);
		return new $class;
	}

	/**
	 * Getter method to grab the names and values of a VO's
	 * properties
	 *
	 * @param bikes_VO_Abstract $vo
	 * @return array $properties
	 */
	protected function getVOProperties(bikes_VO_Abstract $vo){
		$properties = get_class_vars(get_class($vo));
		return $properties;
	}

	/**
	 *
	 * @param bikes_VO_Abstract $vo
	 * @return array $properties
	 */
	protected function getVOPropertyValues(bikes_VO_Abstract $vo){
		$properties = get_object_vars($vo);
		return $properties;
	}

	/**
	 * Takes the values from the array PDO returned and
	 * populates the correct object properties in a
	 * VO object then returns it.
	 *
	 * @param array $row
	 * @return bikes_VO_Abstract $vo
	 */
	protected function populateVO(array $row){
		if(empty($row)){
			return false;
		}
		$vo = $this->getVO();
		$voProperties = $this->getVOProperties($vo);
		foreach(array_keys($voProperties) as $column){
			if(isset($row[$column])){
				$vo->$column = $row[$column];
			}
		}
		return $vo;
	}
}
