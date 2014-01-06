<?php
/**
 * @file
 * Filter class
 * @todo Make operators more general
 */
 
class Filter implements iFilter
{
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var int $m_sTree_id
   * Unique identifier
   * @access protected
   */
  protected $m_sTree_id;
    
  /**
   * @var string $m_sField
   * The field to filter on
   * @access protected
   */
  protected $m_sField;
  
  /**
   * @var string $m_sValue
   * The value the field must be compared with
   * @access protected
   */
  protected $m_sValue;
  
  /**
   * @var int $m_iOperator
   * The operator to be used (e.g. equal to)
   * @access protected
   * @see defined class constants
   */
  protected $m_iOperator;
  
  /**
   * @var FilterGroup $m_oParent
   * The parent filter group this filter belongs to
   * @access protected
   */
  protected $m_oParent;
  
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------
  
  // Operators
  const OPERATOR_EQ               = 1; // Equal
  const OPERATOR_GE               = 2; // Greater than or equal
  const OPERATOR_LE               = 3; // Less than or equal
  const OPERATOR_GT               = 4; // Greater than
  const OPERATOR_LT               = 5; // Less than
  const OPERATOR_CONTAINS         = 6; // Contains
  const OPERATOR_NE               = 7; // Not equal
  const OPERATOR_EMPTY            = 8; // Is empty
  const OPERATOR_NOT_EMPTY        = 9; // Is not empty
  const OPERATOR_STARTS_WITH      = 10; // Starts with
  const OPERATOR_CONTAINS_NOT     = 11; // Does not contain
  const OPERATOR_STARTS_NOT_WITH  = 12; // Starts not with
  const OPERATOR_ENDS_WITH        = 13; // Ends with
  const OPERATOR_ENDS_NOT_WITH    = 14; // Ends not with
  const OPERATOR_QUICK            = 15; // Quickfilter
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * Filter object constructor
   * @param string $p_sField
   * @param string $p_sValue
   * @param int $p_iOperator
   * @access public
   * @return void
   */
  public function __construct($p_sField='', $p_sValue='', $p_iOperator=self::OPERATOR_EQ) {
    $this->__set('field', $p_sField);
    $this->__set('value', $p_sValue);
    $this->setOperator($p_iOperator);
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
    * Setter
    * @param string $p_sMember
    * @param mixed $p_mValue
    * @access public
    * @return boolean
    */
  public function __set($p_sMember, $p_mValue) {
    switch ($p_sMember) {
      case 'id':
      case 'tree_id':
        $this->m_sTree_id = (string) $p_mValue;
        return TRUE;
        
      case 'field':
        $this->m_sField = (string) $p_mValue;
        return TRUE;
        
      case 'operator':
        return $this->setOperator($p_mValue);
        
      case 'value':
        switch ($this->m_iOperator) {
          case self::OPERATOR_NOT_EMPTY:
          case self::OPERATOR_EMPTY:
            // Value may not be set if operator is 'empty' or 'not empty'.
            $this->m_sValue = NULL;
            return FALSE;
        }
        $this->m_sValue = (string) $p_mValue;
        return TRUE;
      
      case 'parent':
        if ($p_mValue instanceof FilterGroup) {
          $this->m_oParent = $p_mValue;
        }
        return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Load an existing item from an array.
   * @param array $p_aParams
   * @access public
   * @return void
   */
  public function from_array($p_aParams) {
    foreach ($p_aParams as $sKey => $mValue) {
      $this->__set($sKey, $mValue);
    }
  }
  
  /**
   * Sets operator (e.g. equal to)
   * Accepts both ints as strings
   * @param mixed $p_mOperator
   * @return boolean
   */
  public function setOperator($p_mOperator) {
    if (is_numeric($p_mOperator)) {
      if ($p_mOperator > 0 && $p_mOperator < 16) {
        $this->m_iOperator = (int) $p_mOperator;
        
        // Erase value if 'empty' or 'not empty' operator is chosen.
        switch ($this->m_iOperator) {
          case self::OPERATOR_NOT_EMPTY:
          case self::OPERATOR_EMPTY:
            $this->m_sValue = NULL;
            break;
        }
        return TRUE;
      }
    }
    elseif (is_string($p_mOperator)) {
      $p_mOperator = strtolower($p_mOperator);
      switch ($p_mOperator) {
        case '=':
        case 'eq':
        case 'equal':
          return $this->setOperator(self::OPERATOR_EQ);
          break;
        case '>':
        case 'gt':
        case 'greater than':
          return $this->setOperator(self::OPERATOR_GT);
          break;
        case '>=':
        case 'ge':
        case 'ge':
          return $this->setOperator(self::OPERATOR_GE);
          break;
        case '<':
        case 'lt':
        case 'lesser than':
          return $this->setOperator(self::OPERATOR_LT);
          break;
        case '<=':
        case 'le':
          return $this->setOperator(self::OPERATOR_LE);
          break;
        case '!=':
        case 'ne':
        case 'not equal':
          return $this->setOperator(self::OPERATOR_NE);
          break;
        case 'is null':
        case 'empty':
          return $this->setOperator(self::OPERATOR_EMPTY);
          break;
        case 'is not null':
        case 'not empty':
          return $this->setOperator(self::OPERATOR_NOT_EMPTY);
          break;
        case 'contains':
          return $this->setOperator(self::OPERATOR_CONTAINS);
          break;
        case 'contains not':
          return $this->setOperator(self::OPERATOR_CONTAINS_NOT);
          break;
        case 'starts with':
          return $this->setOperator(self::OPERATOR_STARTS_WITH);
          break;
        case 'starts not with':
          return $this->setOperator(self::OPERATOR_STARTS_NOT_WITH);
          break;
        case 'ends with':
          return $this->setOperator(self::OPERATOR_ENDS_WITH);
          break;
        case 'ends not with':
          return $this->setOperator(self::OPERATOR_ENDS_NOT_WITH);
          break;
        case 'quick':
          return $this->setOperator(self::OPERATOR_QUICK);
          break;
      }
    }
    return FALSE;
  }
  
  /**
   * Removes filter from group
   * @access public
   * @return boolean
   */
  public function remove() {
    if ($this->m_oParent instanceof FilterGroup) {
      return $this->m_oParent->removeFilter($this->m_sTree_id);
    }
    return FALSE;
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Getter
   * @param string $p_sMember
   * @return mixed
   */
  public function __get($p_sMember) {
    $sMember = strtolower($p_sMember);
    switch ($sMember) {
      case 'id':
      case 'tree_id':
        return $this->m_sTree_id;
        
      case 'field':
        return $this->m_sField;
      
      case 'operator':
        return $this->m_iOperator;
        
      case 'value':
        return $this->m_sValue;
      
      case 'parent':
        if ($this->m_oParent instanceof FilterGroup) {
          return $this->m_oParent;
        }
        else {
          return new FilterGroup();
        }
    }
  }
  
  /**
   * Return a filter by given up the ID
   * @param string $p_sFilter_id
   * @access public
   * @return iFilter
   */
  public function getFilter($p_sFilter_id) {
    return $this;
  }
  
  /**
   * Return as an array of values.
   * @return array
   */
  public function to_array() {
    return array(
      'field' => $this->m_sField,
      'value' => $this->m_sValue,
      'operator' => $this->m_iOperator,
    );
  }
  
  // --------------------------------------------------------------
  // LOGIC
  // --------------------------------------------------------------
  
  /**
   * Checks if filter is setup right:
   * - field is required
   * @access public
   * @return boolean
   */
  public function isValid() {
    if ($this->m_sField == '' || is_null($this->m_sField)) {
      return FALSE;
    }
    return TRUE;
  }
}