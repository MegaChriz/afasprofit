<?php
/**
 * @file
 * AFAS Element class
 *
 * Wordt gebruikt door AfasUpdateConnector
 */

class AfasElement implements iAfasElement {
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Bij deze instelling worden er nieuwe records in AFAS aangemaakt.
   * @var string
   */
  const FIELDS_INSERT = 'insert';

  /**
   * Bij deze instelling worden er bestaande records in AFAS overschreven.
   * @var string
   */
  const FIELDS_UPDATE = 'update';

  /**
   * Bij deze instelling worden er bestaande records in AFAS verwijderd.
   * @var string
   */
  const FIELDS_DELETE = 'delete';

  // --------------------------------------------------------------
  // STATIC PROPERTIES
  // --------------------------------------------------------------

  /**
   * A registry of all created objects
   * @access private
   * @static
   */
  private static $s_aObjects = array();

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * Identifier for this object
   *
   * @var string
   * @access private
   */
  private $m_sObject_id;

  /**
   * Naam van het type object zoals bekend in AFAS
   *
   * @var string
   * @access private
   */
  private $m_sType;

  /**
   * Of er een insert of een update plaats moet vinden.
   * Standaard "insert"
   *
   * @var string
   * @access private
   */
  private $m_sFieldsAction;

  /**
   * Een lijst met eigenschappen van het object
   *
   * @var array
   * @access private
   */
  private $m_aFields;

  /**
   * Een lijst met objecten van het type AfasElement
   *
   * @var array
   * @access private
   */
  private $m_aObjects;

  /**
   * Een lijst met attributen voor het Element-element.
   *
   * @var array
   * @access private
   */
  private $m_aAttributes;

  /**
   * Parent object
   *
   * @var object
   * @access private
   */
  private $m_oParent;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * AfasElement object constructor.
   *
   * @param iAfasElement $p_oParent
   *   Can be AfasElement or AfasUpdateConnector.
   * @param string $p_sType
   *   Object type as known in Afas (e.g. KnPerson).
   * @param string $p_sObject_id
   *   Identifier for this object.
   *
   * @access public
   * @return void
   */
  public function __construct(iAfasElement $p_oParent, $p_sType, $p_sObject_id = NULL) {
    $this->m_oParent = $p_oParent;
    $this->m_sType = (string) $p_sType;
    $this->m_sFieldsAction = self::FIELDS_INSERT;
    $this->m_aFields = array();
    $this->m_aObjects = array();
    $this->m_aAttributes = array();

    if (!$p_sObject_id) {
      // Generate object ID.
      $iNumb = 0;
      while (isset(self::$s_aObjects[$p_sType . $iNumb])) {
        $iNumb++;
      }
      $p_sObject_id = $p_sType . $iNumb;
    }

    $this->m_sObject_id = $p_sObject_id;
    // Add object to registry
    self::$s_aObjects[$p_sObject_id] = $this;

    // Initialize further.
    $this->init();
  }

  /**
   * Can be used by subclasses to do some initialization upon object creation.
   *
   * @return void
   */
  public function init() { }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Validates/Correct the structure of this element.
   *
   * Should be implemented by subclasses to ensure the structure is valid
   * before the data is send to Afas.
   *
   * @return array
   *   An array of errors (if any).
   */
  public function validate() {
    return array();
  }

  /**
   * Throws Exception in case of errors.
   *
   * @return void
   */
  public function validateWithExceptions() {
    $errors = $this->validate();
    if (count($errors) > 0) {
      throw new AfasElementException($this, get_class($this) . ':' . "\n" . implode("\n", $errors));
    }
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Sets multiple fields
   * @param array $p_aFields
   * @access public
   * @return void
   */
  public function setFields($p_aFields) {
    if (!is_array($p_aFields) || count($p_aFields) < 1) {
      return;
    }
    foreach ($p_aFields as $sName => $sValue) {
      $this->setField($sName, $sValue);
    }
  }

  /**
   * Alias of setFields().
   *
   * @param array $p_aFields
   *   An array of name => value.
   *
   * @access public
   * @return void
   */
  public function setMultipleFields($p_aFields) {
    return $this->setFields($p_aFields);
  }

  /**
   * Sets a field value
   * @param string $p_sName
   * @param string $p_sValue
   * @access public
   * @return void
   */
  public function setField($p_sName, $p_sValue) {
    if (is_string($p_sName)) {
      $this->m_aFields[$p_sName] = (string) $p_sValue;
    }
  }

  /**
   * Removes a field.
   *
   * @param string $p_sName
   *   The name of the field to remove.
   *
   * @access public
   * @return void
   */
  public function removeField($p_sName) {
    if (isset($this->m_aFields[$p_sName])) {
      unset($this->m_aFields[$p_sName]);
    }
  }

  /**
   * Set an attribute for the Element-element.
   *
   * @param string $p_sName
   *   The name of the attribute.
   * @param string $p_sValue
   *   The value of the attribute.
   *
   * @access public
   * @return void
   */
  public function setAttribute($p_sName, $p_sValue) {
    if (is_string($p_sName)) {
      $this->m_aAttributes[$p_sName] = (string) $p_sValue;
    }
  }

  /**
   * Removes an attribute.
   *
   * @param string $p_sName
   *   The name of the attribute to remove.
   *
   * @access public
   * @return void
   */
  public function removeAttribute($p_sName) {
    if (isset($this->m_aAttributes[$p_sName])) {
      unset($this->m_aAttributes[$p_sName]);
    }
  }

  /**
   * Sets fields action
   * @param string $p_sAction
   * @access public
   * @return void
   * @throws AfasElementException
   */
  public function setAction($p_sAction) {
    switch ($p_sAction) {
      case self::FIELDS_INSERT:
      case self::FIELDS_UPDATE:
      case self::FIELDS_DELETE:
        $this->m_sFieldsAction = $p_sAction;
        return;
    }
    throw new AfasElementException($this, t('Fields action %action not available', array('%action' => $p_sAction)));
  }

  /**
   * Adds a child object.
   *
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   * @param string $p_sClass
   *
   * @access public
   * @return AfasElement
   * @throws AfasElementException
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields = array(), $p_sClass = 'AfasElement') {
    $p_sObject_id = (string) $p_sObject_id;

    // Check if class extends AfasElement.
    if ($p_sClass != 'AfasElement') {
      $aParents = class_parents($p_sClass);
      if (!isset($aParents['AfasElement'])) {
        throw new AfasElementException($this, 'Element must be of type AfasElement');
      }
    }
    // Guess class for this child automatically.
    elseif (class_exists('Afas' . $p_sType)) {
      $p_sClass = 'Afas' . $p_sType;
    }

    $oElement = new $p_sClass($this, $p_sType, $p_sObject_id);
    $oElement->setFields($p_aFields);
    $this->m_aObjects[$oElement->object_id] = $oElement;
    return $oElement;
  }

  /**
   * Adds an element object.
   *
   * @param AfasElement $p_oElement
   * @access public
   * @return void
   */
  public function addChildByObject(AfasElement $p_oElement) {
    $this->m_aObjects[$p_oObject->object_id] = $p_oElement;
    $p_oElement->changeParent($this);
  }

  /**
   * Removes a child object
   * @param string $p_sObject_id
   * @access public
   * @return void
   */
  public function removeChild($p_sObject_id) {
    if (isset($this->m_aObjects[$p_sObject_id])) {
      unset($this->m_aObjects[$p_sObject_id]);
    }
  }

  /**
   * Removes this element
   * @access public
   * @return void
   */
  public function remove() {
    $this->m_oParent->removeChild($this->m_sObject_id);
  }

  /**
   * Changes the parent the element belongs to.
   *
   * @param iAfasElement $p_oParent
   * @access public
   * @return void
   */
  public function changeParent(iAfasElement $p_oParent) {
    if ($p_oParent->getChild($this->m_sObject_id)) {
      $this->m_oParent = $p_oParent;
    }
  }

  /**
   * Map fields to their real names.
   *
   * @param array $p_aFields
   *   The fields to map to their connector names.
   *
   * @return array
   *   Valid field names.
   */
  public function mapFields($p_aFields) {
    $map = $this->getMappings();
    if (count($map) < 1) {
      // If no mappings are known, skip mapping as else
      // no field will be valid.
      return $p_aFields;
    }
    $return = array();
    foreach ($p_aFields as $fieldname => $value) {
      if (isset($map[$fieldname])) {
        $return[$map[$fieldname]] = $value;
      }
    }
    return $return;
  }

  /**
   * Returns mappings for fields.
   *
   * Should be used by subclasses to declare
   * which field names are valid and which field
   * names should be mapped.
   *
   * @return array
   *   An associative array of fieldname source => fieldname target.
   */
  public function getMappings() {
    return array();
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Getter.
   *
   * @param string $p_sMember
   *   The member to get.
   *
   * @access public
   * @return mixed
   */
  public function __get($p_sMember) {
    switch ($p_sMember) {
      case 'type':
        return $this->m_sType;
      case 'object_id':
        return $this->m_sObject_id;
      case 'fields':
        return $this->m_aFields;
      case 'attributes':
        return $this->m_aAttributes;
      case 'childs':
      case 'objects':
        return $this->m_aObjects;
    }
    return NULL;
  }

  /**
   * Returns parent.
   *
   * @access public
   * @return iAfasElement
   */
  public function getParent() {
    return $this->m_oParent;
  }

  /**
   * Returns the server-object this element is in.
   *
   * @return AfasServer
   *   The server-object used by the update-connector.
   *   Or NULL, if this element isn't attached to a server.
   */
  public function getServer() {
    $parent = $this->getParent();
    if (method_exists($parent, 'getServer')) {
      return $parent->getServer();
    }
    return NULL;
  }

  /**
   * Gets an attribute.
   *
   * @param string $p_sName
   *   The name of the attribute to get.
   *
   * @access public
   * @return mixed
   *   String, in case the attribute exists.
   *   NULL otherwise.
   */
  public function getAttribute($p_sName) {
    if (isset($this->m_aAttributes[$p_sName])) {
      return $this->m_aAttributes[$p_sName];
    }
    return NULL;
  }

  /**
   * Returns if an attribute exist.
   *
   * @param string $p_sName
   *   The name of the attribute to check.
   *
   * @access public
   * @return boolean
   *   True if the attribute exists.
   *   False otherwise.
   */
  public function attributeExists($p_sName) {
    if (isset($this->m_aAttributes[$p_sName])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Gets a field value.
   *
   * @param string $p_sName
   *   The name of the field to get.
   *
   * @access public
   * @return mixed
   *   String, in case the field exists.
   *   NULL otherwise.
   */
  public function getField($p_sName) {
    if (isset($this->m_aFields[$p_sName])) {
      return $this->m_aFields[$p_sName];
    }
    return NULL;
  }

  /**
   * Returns if a field exist.
   *
   * @param string $p_sName
   *   The name of the field to check.
   *
   * @access public
   * @return boolean
   *   True if the field exists.
   *   False otherwise.
   */
  public function fieldExists($p_sName) {
    if (isset($this->m_aFields[$p_sName])) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get the action set for this element.
   *
   * @return string
   */
  public function getAction() {
    return $this->m_sFieldsAction;
  }

  /**
   * Returns specific child object.
   *
   * @access public
   * @return AfasElement
   * @throws AfasElementException
   */
  public function getChild($p_sObject_id) {
    if (!isset($this->m_aObjects[$p_sObject_id])) {
      throw new AfasElementException($this, 'Child ' . $p_sObject_id  . ' does not exists.');
    }
    return $this->m_aObjects[$p_sObject_id];
  }

  /**
   * Returns all child objects.
   *
   * @access public
   * @return array
   */
  public function getChilds() {
    return $this->m_aObjects;
  }

  /**
   * Returns if a specific child exists.
   *
   * @access public
   * @return AfasElement
   * @throws AfasElementException
   */
  public function childExists($p_sObject_id) {
    return isset($this->m_aObjects[$p_sObject_id]);
  }

  /**
   * Returns how many childs of given type this element has.
   *
   * @param string|array $p_mType
   *   The type of child to look for.
   *   If a string is given, only one child type is searched.
   *   If an array is given, all parts in the array will be
   *   be searched.
   *
   * @return int
   *   The number of childs.
   * @throws AfasElementException
   *   In case an invalid parameter was given.
   */
  public function hasChildType($p_mType) {
    if (is_string($p_mType)) {
      $p_mType = array($p_mType);
    }
    if (!is_array($p_mType)) {
      // No array. Unwanted.
      throw AfasElementException($this, 'AfasElement::hasChildType() only accepts array and strings as parameter.');
    }
    $count = 0;
    foreach ($this->m_aObjects as $oObject) {
      if (in_array($oObject->type, $p_mType)) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Return XML string.
   *
   * @param DOMDocument $p_oDoc
   *   (optional) An instance of DOMDocument.
   *
   * @access public
   * @return DOMNode
   *   An instance of DOMNode.
   */
  public function getXML(DOMDocument $p_oDoc = NULL) {
    $this->validateWithExceptions();

    // Create DOMDocument if empty.
    if (empty($p_oDoc)) {
      $p_oDoc = new DOMDocument();
    }

    // Creates Element.
    $oXMLElement = $p_oDoc->createElement('Element');
    if (count($this->m_aAttributes) > 0) {
      foreach ($this->m_aAttributes as $sName => $sValue) {
        $oXMLElement->setAttribute($sName, $sValue);
      }
    }

    // Fields XML.
    if (count($this->m_aFields) > 0) {
      $oXMLFields = $p_oDoc->createElement('Fields');
      $oXMLElement->appendChild($oXMLFields);
      $oXMLFields->setAttribute('Action', $this->m_sFieldsAction);
      foreach ($this->m_aFields as $sFieldName => $sFieldValue) {
        $oXMLField = $p_oDoc->createElement($sFieldName);
        $oXMLFields->appendChild($oXMLField);
        if ($sFieldValue === '' || is_null($sFieldValue)) {
          $oXMLField->setAttribute('xsi:nil', 'true');
        }
        else {
          $oText = $p_oDoc->createTextNode($sFieldValue);
          $oXMLField->appendChild($oText);
        }
      }
    }

    // Object XML.
    if (count($this->m_aObjects) > 0) {
      $oXMLObjects = $p_oDoc->createElement('Objects');
      $oXMLElement->appendChild($oXMLObjects);

      // Sort objects by type first.
      $aObjectsByType = array();
      foreach ($this->m_aObjects as $oObject) {
        $aObjectsByType[$oObject->type][] = $oObject;
      }

      // Generate XML for each object, grouped by type.
      foreach ($aObjectsByType as $sType => $aObjects) {
        $oXMLObject = $p_oDoc->createElement($sType);
        $oXMLObjects->appendChild($oXMLObject);
        foreach ($aObjects as $oObject) {
          $oXMLObjectChilds = $oObject->getXML($p_oDoc);
          $oXMLObject->appendChild($oXMLObjectChilds);
        }
      }
    }

    return $oXMLElement;
  }
}
