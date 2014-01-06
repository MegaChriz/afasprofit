<?php
/**
 * @file
 * Contains the DBObject class.
 *
 * @todo Move all static methods to DBObjectList
 */

/**
 * Database base object
 */
class DbObject {
  // -----------------------------------------------------------------------------
  // STATIC PROPERTIES
  // -----------------------------------------------------------------------------

  /**
   * Which ID a new object will get when constructed.
   *
   * This value will be decreased with 1 every time
   * a new object is constructed.
   * A new object in this case is an object not coming
   * from the database.
   *
   * @var int
   * @access private
   * @static
   */
  static private $nextNewId = -1;
  
  // ------------------------------------------------------------
  // PROPERTIES
  // ------------------------------------------------------------

  /**
   * Whether this is a new record or not.
   *
   * @var boolean
   * @access private
   */
  private $isNewRecord;
  
  /**
   * The instance of DbObjectList this DbObject belongs to.
   *
   * @var DbObjectList
   * @access private
   */
  private $objectList;
  
  /**
   * The aggregrated record object
   *
   * @var object
   * @access private
   */
  private $record;

  // ------------------------------------------------------------
  // CONSTRUCT
  // ------------------------------------------------------------

  /**
   * DBobject object constructor
   *
   * @param DbObjectList $objectList
   *   The list this object belongs to
   * @param object/array $item
   *   Either an array or an object
   *
   * @access public
   * @final
   * @return void
   */
  final public function __construct(DbObjectList $objectList, $item = NULL) {
    $this->isNewRecord = TRUE;
    $this->objectList = $objectList;
    $this->record = new StdClass();

    $fields = array();
    if (is_object($item)) {
      $fields = get_object_vars($item);
    }
    elseif (is_array($item)) {
      $fields = $item;
    }
    $this->setMultipleFields($fields);

    $key = $this->objectList->getPrimaryKey();
    if (!isset($this->record->$key) || (!$this->record->$key && $this->record->$key !== 0 && $this->record->$key !== '0')) {
      // We always need an ID
      $this->record->$key = self::$nextNewId--;
    }

    $this->init($this->record);
  }
  
  /**
   * This function can be used by subclasses to do some initialization upon object creation
   *
   * @param object $obj
   *
   * @access public
   * @return void
   */
  public function init($obj) { }

  // ------------------------------------------------------------
  // GETTERS
  // ------------------------------------------------------------

  /**
   * Automatic Getter
   *
   * Return field of record (if exists)
   *
   * @param string $member
   *
   * @access public
   * @return mixed
   */
  public function __get($member) {
    return $this->getField($member);
  }

  /**
   * Returns the ID of this item.
   *
   * @access public
   * @return int
   */
  public function getId() {
    $key = $this->objectList->getPrimaryKey();
    return $this->record->$key;
  }
  
  /**
   * Returns the instance of the list this object belongs to.
   *
   * @access public
   * @return DbObjectList
   */
  public function getList() {
    return $this->objectList;
  }
  
  /**
   * Return field of record (if exists)
   *
   * @param string $field
   *
   * @access public
   * @return mixed
   */
  public function getField($field) {
    if (isset($this->record->$field)) {
      return $this->record->$field;
    }
    return NULL;
  }

  /**
   * Converts object to array
   *
   * @access public
   * @return array
   */
  public function toArray() {
    return (array) $this->record;
  }

  /**
   * Returns if this a new record or not.
   *
   * @access public
   * @return boolean
   */
  public function isNew() {
    return $this->isNewRecord;
  }

  /**
   * Gets the aggregrated record object
   *
   * @access protected
   * @return object
   */
  protected function getRecord() {
    return $this->record;
  }

  // ------------------------------------------------------------
  // SETTERS
  // ------------------------------------------------------------

  /**
   * Sets field of record
   *
   * @param string $field
   * @param mixed $value
   *
   * @access public
   * @return void
   * @todo validation etc.
   */
  public function setField($field, $value) {
    $this->record->$field = $value;
  }
  
  /**
   * Set multiple fields
   *
   * @param array $fields
   *
   * @access public
   * @return void
   */
  public function setMultipleFields($fields) {
    foreach ($fields as $fieldname => $value) {
      $this->setField($fieldname, $value);
    }
  }

  // ------------------------------------------------------------
  // SAVING
  // ------------------------------------------------------------

  /**
   * This function can be used by subclasses to perform actions perform a record is saved
   *
   * @access protected
   * @return void
   */
  protected function presave() { }

  /**
   * Saves a single subscription record to the database.
   *
   * @return boolean
   */
  public function save() {
    $this->presave();
    return $this->objectList->save($this);
  }
  
  // ------------------------------------------------------------
  // DBOBJECTLIST METHODS
  // ------------------------------------------------------------

  /**
   * Sets a private variable.
   *
   * This method should only be called by the object list.
   *
   * @param string $property
   * @param string $value
   *
   * @access private
   * @return void
   */
  public function privSetProperty($property, $value) {
    switch ($property) {
      case 'new':
      case 'isNewRecord':
        $this->isNewRecord = ($value) ? TRUE : FALSE;
        break;
    }
  }
  
  /**
   * Gets the aggregrated record object
   *
   * This method should only be called by the object list.
   *
   * @access private
   * @return object
   */
  public function privGetRecord() {
    return $this->record;
  }
}
