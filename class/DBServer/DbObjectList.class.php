<?php
/**
 * @file
 * Contains the DbObjectList class.
 */

/**
 * Database base object
 */
abstract class DbObjectList {
  // -----------------------------------------------------------------------------
  // CONSTANTS
  // -----------------------------------------------------------------------------

  // Performance hint setting
  const PERF_HINT_LOAD_ONE = 0;
  const PERF_HINT_LOAD_ALL = 1;

  // Load by
  const BY_ID = 0;

  // ------------------------------------------------------------
  // STATIC PROPERTIES
  // ------------------------------------------------------------

  /**
   * Contains an array of DbObjectList instances
   * @var array
   * @static
   */
  private static $lists = array();

  // ------------------------------------------------------------
  // PROPERTIES
  // ------------------------------------------------------------
  
  /**
   * A list of objects currently loaded.
   *
   * @var array
   * @access private
   */
  private $objects = array();

  /**
   * Whether all objects are loaded or not
   *
   * @var boolean
   * @access private
   */
  private $allLoaded = FALSE;

  /**
   * Performance hint setting
   *
   * This setting determines how the list should operate when it loads
   * objects. The performance hint setting can be set to:
   * - PERF_HINT_LOAD_ONE
   * - PERF_HINT_LOAD_ALL
   *
   * @var int
   * @access private
   */
  private $performanceHint = self::PERF_HINT_LOAD_ONE;

  // ------------------------------------------------------------
  // SINGLETON METHODS
  // ------------------------------------------------------------

  /**
   * DbObjectList object constructor
   */
  final private function __construct() { }

  /**
   * Get list instance
   *
   * @access public
   * @static
   * @return DbObjectList
   */
  abstract public static function getInstance();
  
  /**
   * Get list instance helper
   *
   * @param string $className
   *
   * @static
   * @return DbObjectList
   */
  protected static function getInstanceHelper($className) {
    if (isset(self::$lists[$className])) {
      return self::$lists[$className];
    }
    else {
      $oList = new $className();
      self::$lists[$className] = $oList;
      return $oList;
    }
  }

  // ------------------------------------------------------------
  // INFO METHODS
  // ------------------------------------------------------------

  /**
   * Returns primary key.
   *
   * @return string
   *   The primary key of the table to load records from.
   */
  abstract public function getPrimaryKey();

  /**
   * Returns table name.
   *
   * @return string
   *   The name of the table to load records from.
   */
  abstract public function getTableName();

  /**
   * Returns module the table belongs to.
   *
   * @return string
   *   The name of the module the table belongs to.
   */
  abstract public function getModuleName();

  /**
   * Returns class to create instances for.
   *
   * @return string
   *   The name of the class to create instances for.
   */
  public function getClassName() {
    return 'DbObject';
  }

  /**
   * Returns an array of argument types that can be used to load by.
   * In most cases this method only returns unique table keys.
   *
   * @return array
   *   Supported argument types to load a record by.
   */
  public function getLoadBy() {
    return array();
  }

  // ------------------------------------------------------------
  // DBOBJECT METHODS
  // ------------------------------------------------------------

  /**
   * Get an object record
   *
   * @param int $object_id
   *   The object to get
   *
   * @return DbObject
   *   The object object
   *   Or FALSE is object does not exist.
   */
  public function get($object_id) {
    $this->loadOne(self::BY_ID, $object_id);
    if (isset($this->objects[$object_id])) {
      return $this->objects[$object_id];
    }
    return FALSE;
  }

  /**
   * Returns all object records
   *
   * @return array
   *   An array of DbObject instances
   */
  public function getAll() {
    $this->loadAll();
    return $this->getAllLoaded();
  }

  /**
   * Returns all object records currently loaded
   *
   * @return array
   *   An array of DbObject instances
   */
  public function getAllLoaded() {
    return $this->objects;
  }

  /**
   * Get an object record by an other unique source
   * (if supported by the subclass)
   *
   * @param int $type
   *   Type of the argument given
   * @param mixed $arg
   *   The ID or an other unique source
   *
   * @return DbObject
   *   The object object
   *   Or FALSE is object does not exist.
   */
  public function getBy($type, $arg) {
    $this->loadOne($type, $arg);
    return $this->findBy($type, $arg);
  }

  /**
   * Creates a new object item.
   *
   * @param object/array $item
   *   Either an array or an object
   *
   * @return DbObject
   * @throws Exception
   */
  public function create($item) {
    $key = $this->getPrimaryKey();
    if (is_array($item) && isset($item[$key])) {
      // First check if we already have this object.
      if ($object = $this->get($item[$key])) {
        $object->setMultipleFields();
        return $object;
      }
    }
    elseif (is_object($item) && isset($item->$key)) {
      // First check if we already have this object.
      if ($object = $this->get($item->$key)) {
        $object->setMultipleFields();
        return $object;
      }
    }
    $class = $this->getClassName();
    $object = new $class($this, $item);
    $this->objects[$object->getId()] = $object;
    return $object;
  }

  // -----------------------------------------------------------------------------
  // PERFORMANCE
  // -----------------------------------------------------------------------------

  /**
   * Sets the performance hint setting
   *
   * @param int $hint
   *
   * @return void
   * @throws Exception
   */
  public function setPerformanceHint($hint) {
    switch ($hint) {
      case self::PERF_HINT_LOAD_ONE:
      case self::PERF_HINT_LOAD_ALL:
        $this->performanceHint = $hint;
        break;
      default:
        throw new Exception('Tried to set an invalid performance hint.');
    }
  }

  /**
   * Returns the performance hint setting
   *
   * @return int
   */
  public function getPerformanceHint() {
    return $this->performanceHint;
  }

  // ------------------------------------------------------------
  // SAVING
  // ------------------------------------------------------------
  
  /**
   * Saves an object to the database.
   *
   * @param DbObject $object
   *
   * @return boolean
   * @throws Exception
   */
  public function save(DbObject $object) {
    if ($object->getList() !== $this) {
      throw new Exception('The given object does not belong to this object list.');
    }
    $id = $object->getId();
    $record = $object->privGetRecord();
    $result = FALSE;
    if ($object->isNew()) {
      if (!isset($record->created)) {
        $record->created = time();
      }
      $record->modified = time();
      $result = server_write_record($this->getModuleName(), $this->getTableName(), $record);
      unset($this->objects[$id]);
      $this->objects[$object->getId()] = $object;
    }
    else {
      $record->modified = time();
      $result = server_write_record($this->getModuleName(), $this->getTableName(), $record, array($this->getPrimaryKey()));
    }
    return $result;
  }
   
  // ------------------------------------------------------------
  // DATABASE REQUESTS
  // @private
  // ------------------------------------------------------------

  /**
   * Loads a single records from the database if not already loaded
   *
   * No database call is done in these cases:
   * - Record is already loaded
   * - All records are already loaded
   *
   * @param int $type
   *   Type of the argument given
   * @param mixed $arg
   *   The ID or an other unique source
   *
   * @access private
   * @return void
   * @throws UcAddressesDbException
   */
  private function loadOne($type, $arg) {
    // Reasons to skip out early
    if ($this->allLoaded) {
      return;
    }
    if ($type == self::BY_ID) {
      if (isset($this->objects[$arg])) {
        return;
      }
    }
    else {
      if ($this->findBy($type, $arg)) {
        return;
      }
      elseif (!$this->isSupportedArgumentType($type)) {
        return;
      }
    }

    // If performance hint is set to load all, then load all instead
    if ($this->performanceHint == self::PERF_HINT_LOAD_ALL) {
      $this->loadAll();
      return;
    }

    // Read the database.
    $result = FALSE;
    if ($type === self::BY_ID) {
      $key = $this->getPrimaryKey();
      $query = "SELECT * FROM {" . $this->getTableName() . "} WHERE " . $key . " = %d";
      $result = db_query($query, $arg);
    }
    else {
      $query = "SELECT * FROM {" . $this->getTableName() . "} WHERE " . $type . " = '%s'";
      $result = db_query($query, $arg);
    }

    $this->dbResultToObjects($result);
  }

  /**
   * Loads all subscriptions currently in the database.
   *
   * @return void
   */
  private function loadAll() {
    // Reasons to skip out early
    if ($this->allLoaded) {
      return;
    }

    // Update the performance hint setting
    $this->performanceHint = self::PERF_HINT_LOAD_ALL;

    $query = "SELECT * FROM {" . $this->getTableName() . "}";
    $result = db_query($query);
    $this->dbResultToObjects($result);
    $this->allLoaded = TRUE;
  }

  /**
   * Creates objects from a database resource.
   *
   * @param resource $result
   *   Database result
   *
   * @access private
   * @return void
   * @throws Exception
   */
  private function dbResultToObjects($result) {
    if ($result === FALSE) {
      throw new Exception(t('Failed to read from database table ' . $this->getTableName()));
    }

    $key = $this->getPrimaryKey();

    // Create object from each database record
    while ($obj = db_fetch_object($result)) {
      // Skip objects that have already been loaded (and perhaps modified)
      if (!isset($this->objects[$obj->$key])) {
        $class = $this->getClassName();
        $oObject = new $class($this, $obj);
        $oObject->privSetProperty('new', FALSE);
        $this->objects[$oObject->getId()] = $oObject;
      }
    }
  }

  // ------------------------------------------------------------
  // HELPERS
  // ------------------------------------------------------------

  /**
   * Search for an object
   *
   * @param int $type
   *   Type of the argument given
   * @param mixed $arg
   *   The ID or an other unique source
   *
   * @access private
   * @return
   *   DbObject if object is found
   *   FALSE otherwise
   */
  private function findBy($type, $arg) {
    if ($arg) {
      foreach ($this->objects as $oObject) {
        if ($oObject->getField($type) == $arg) {
          return $oObject;
        }
      }
    }
    return FALSE;
  }

  /**
   * Check if the type to load/delete by is supported.
   *
   * @param int $type
   *   The argument type
   *
   * @return boolean
   */
  protected function isSupportedArgumentType($type) {
    return in_array($type, $this->getLoadBy());
  }
}
