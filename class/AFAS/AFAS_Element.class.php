<?php
/**
 * @file
 * AFAS Element class
 *
 * Wordt gebruikt door AfasUpdateConnector
 */
 
class AFAS_Element implements iAFAS_Element
{
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
   * Een lijst met objecten van het type AFAS_Element
   *
   * @var array
   * @access private
   */
  private $m_aObjects;
  
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
   * AFAS_Element object constructor
   * @param iAFAS_Element $p_oParent
   *   Can be AFAS_Element or AfasUpdateConnector
   * @param string $p_sType
   * @param string $p_sObject_id
   * @access public
   * @return void 
   */
  public function __construct(iAFAS_Element $p_oParent, $p_sType, $p_sObject_id = '') {
    $this->m_oParent = $p_oParent;
    $this->m_sType = (string) $p_sType;
    $this->m_sFieldsAction = self::FIELDS_INSERT;
    $this->m_aFields = array();
    $this->m_aObjects = array();
    
    if (!$p_sObject_id) {
      // Generate object ID
      $iNumb = 0;
      while (isset(self::$s_aObjects[$p_sType . $iNumb])) {
        $iNumb++;
      }
      $p_sObject_id = $p_sType . $iNumb;
    }
    
    $this->m_sObject_id = $p_sObject_id;
    // Add object to registry
    self::$s_aObjects[$p_sObject_id] = $this;
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
   * Removes a field
   * @param string $p_sName
   * @access public
   * @return void
   */
  public function removeField($p_sName) {
    if (isset($this->m_aFields[$p_sName])) {
      unset($this->m_aFields[$p_sName]);
    }
  }
  
  /**
   * Sets fields action
   * @param string $p_sAction
   * @access public
   * @return void
   * @throws AfasException
   */
  public function setAction($p_sAction) {
    switch ($p_sAction) {
      case self::FIELDS_INSERT:
      case self::FIELDS_UPDATE:
      case self::FIELDS_DELETE:
        $this->m_sFieldsAction = $p_sAction;
        return;
    }
    throw new AfasException(t('Fields action %action not available', array('%action' => $p_sAction)));
  }
  
  /**
   * Adds a child object
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   * @param string $p_sClass
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields = array(), $p_sClass = 'AFAS_Element') {
    $p_sObject_id = (string) $p_sObject_id;
    
    // Check if class extends AFAS_Element
    if ($p_sClass != 'AFAS_Element') {
      $aParents = class_parents($p_sClass);
      if (!isset($aParents['AFAS_Element'])) {
        throw new AfasException('Element must be of type AFAS_Element');
      }
    }
    
    $oElement = new $p_sClass($this, $p_sType, $p_sObject_id);
    $oElement->setFields($p_aFields);
    $this->m_aObjects[$oElement->object_id] = $oElement;
    return $oElement;
  }
  
  /**
   * Adds an element object
   * @param AFAS_Element $p_oElement
   * @access public
   * @return void
   */
  public function addChildByObject(AFAS_Element $p_oElement) {
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
   * Changes the parent the element belongs to
   * @param iAFAS_Element $p_oParent
   * @access public
   * @return void
   */
  public function changeParent(iAFAS_Element $p_oParent) {
    if ($p_oParent->getChild($this->m_sObject_id)) {
      $this->m_oParent = $p_oParent;
    }
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Getter
   * @param string $p_sMember
   * @access public
   * @return mixed
   */
  public function __get($p_sMember) {
    switch ($p_sMember) {
      case 'type':
        return $this->m_sType;
      case 'object_id':
        return $this->m_sObject_id;
    }
    return NULL;
  }

  /**
   * Returns parent
   * @access public
   * @return iAFAS_Element
   */
  public function getParent() {
    return $this->m_oParent;
  }

  /**
   * Gets a field value
   * @param string $p_sName
   * @access public
   * @return mixed
   */
  public function getField($p_sName) {
    if (isset($this->m_aFields[$p_sName])) {
      return $this->m_aFields[$p_sName];
    }
    return NULL;
  }
  
  /**
   * Returns specific child object
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function getChild($p_sObject_id) {
    if (!isset($this->m_aObject[$p_sObject_id])) {
      throw new AfasException('Child ' . $p_sObject_id  . ' does not exists');
    }
    return $this->m_aObject[$p_sObject_id];
  }
  
  /**
   * Return XML string
   * @access public
   * @return string XML
   */
  public function getXML() {
    $sOutput = '<Element>';
    
    // Fields XML
    if (count($this->m_aFields) > 0) {
      $sOutput .= '<Fields Action="' . $this->m_sFieldsAction . '">';
      foreach ($this->m_aFields as $sFieldName => $sFieldValue) {
        if ($sFieldValue === '') {
          $sOutput .= '<' . $sFieldName . ' xsi:nil="true"/>';
        }
        else {
          $sOutput .= '<' . $sFieldName . '>' . $sFieldValue . '</' . $sFieldName . '>';
        }
      }
      $sOutput .= '</Fields>';
    }
    
    // Objects XML
    if (count($this->m_aObjects) > 0) {
      $sOutput .= '<Objects>';
      
      // Sort objects by type first
      $aObjectsByType = array();
      foreach ($this->m_aObjects as $oObject) {
        $aObjectsByType[$oObject->type][] = $oObject;
      }
      
      // Generate XML
      foreach ($aObjectsByType as $sType => $aObjects) {
        $sOutput .= '<' . $sType . '>';
        foreach ($aObjects as $oObject) {
          $sOutput .= $oObject->getXML();
        }
        $sOutput .= '</' . $sType . '>';
      }
      
      $sOutput .= '</Objects>';
    }
    
    $sOutput .= '</Element>';
    return $sOutput;
  }
}