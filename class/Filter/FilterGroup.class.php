<?php
/**
 * @file
 * FilterGroup class
 */

class FilterGroup implements iFilter
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
   * @var array $m_aFilters
   * List of filters
   * @access protected
   */
  protected $m_aFilters;
  
  /**
   * @var string $m_sName
   * Name of the group (can be blank)
   * @access protected
   */
  protected $m_sName;
  
  /**
   * @var int $m_iModus
   * The modus of this group
   * @access protected
   * @see defined class constants
   */
  protected $m_iModus;
  
  /**
   * @var FilterGroup $m_oParent
   * The parent filter group this filter group belongs to
   * @access protected
   */
  protected $m_oParent;
  
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------
  
  // Modi
  const MODUS_AND = 1;
  const MODUS_OR  = 2;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * FilterGroup object constructor
   * @param int $p_iModus
   * @param string $p_sName
   * @access public
   * @return void
   */
  public function __construct($p_sName='', $p_iModus=self::MODUS_AND) {
    $this->m_aFilters = array();
    $this->__set('name', $p_sName);
    $this->__set('modus', $p_iModus);
  }
  
  // --------------------------------------------------------------
  // SETTERS (public)
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
      
      case 'name':
        $this->m_sName = (string) $p_mValue;
        return TRUE;
      
      case 'modus':
        switch ($p_mValue) {
          case self::MODUS_AND:
          case self::MODUS_OR:
            $this->m_iModus = (int) $p_mValue;
            return TRUE;
        }
        
      case 'filters':
        // Import filters from array
        if (!is_array($p_mValue)) {
          return FALSE;
        }
        foreach ($p_mValue as $mFilter_id => $aFilter) {
          if (isset($aFilter['type'])) {
            $oObject = new $aFilter['type'];
          }
          else {
            $oObject = new Filter();
          }
          if ($oObject instanceof iFilter) {
            $oObject->from_array($aFilter);
            $oObject->id = $mFilter_id;
            $oObject->parent = $this;
            $this->m_aFilters[$mFilter_id] = $oObject;
          }
        }
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
   * Adds a filter
   * @param string $p_sField
   * @param string $p_sValue
   * @param mixed $p_mOperator
   * @access public
   * @return Filter
   */
  public function addFilter($p_sField='', $p_sValue='', $p_mOperator='=') {
    $oFilter = new Filter($p_sField, $p_sValue, $p_mOperator);
    return $this->_addFilter($oFilter);
  }
    
  /**
   * Adds a filter group
   * @param int $p_iModus
   * @param string $p_sName
   * @access public
   * @return FilterGroup
   */
  public function addFilterGroup($p_sName='', $p_iModus=self::MODUS_AND) {
    $oFilterGroup = new FilterGroup($p_sName, $p_iModus);
    return $this->_addFilterGroup($oFilterGroup);
  }
  
  /**
   * Removes group from parent
   * @access public
   * @return boolean
   */
  public function remove() {
    if ($this->m_oParent instanceof FilterGroup) {
      return $this->m_oParent->removeFilter($this->m_sTree_id);
    }
    return FALSE;
  }
  
  /**
   * Removes a filter if filter exists.
   * @param string $p_sFilter_id
   * @access public
   * @return boolean
   */
  public function removeFilter($p_sFilter_id) {
    if (isset($this->m_aFilters[$p_sFilter_id])) {
      unset($this->m_aFilters[$p_sFilter_id]);
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Removes any filters that are not valid.
   * @access public
   * @return int
   *   The number of filters removed
   */
  public function removeInvalidFilters() {
    $iFiltersRemoved = 0;
    foreach ($this->m_aFilters as $iFilter_id => $oFilter) {
      if (!$oFilter->isValid()) {
        $iFiltersRemoved++;
        $this->removeFilter($iFilter_id);
      }
    }
    return $iFiltersRemoved;
  }
  
  // --------------------------------------------------------------
  // SETTERS (protected)
  // --------------------------------------------------------------
  
  /**
   * Adds a filter
   * @param Filter $p_oFilter
   * @access protected
   * @return Filter
   */
  protected function _addFilter(Filter $p_oFilter) {
    $p_oFilter->parent = $this;
    if ($this->m_sTree_id) {
      if (count($this->m_aFilters) < 1) {
        $iSerial = 0;
      }
      else {
        // Generate ID, use the key of the last element and increase it with one.
        $aKeys = array_keys($this->m_aFilters);
        $sLastKey = end($aKeys);
        $aLastKeyExplode = explode('-', $sLastKey);
        $iSerial = end($aLastKeyExplode) + 1;
      }
      $iCount = count($this->m_aFilters);
      $p_oFilter->tree_id = $this->m_sTree_id . '-' . $iSerial;
      $this->m_aFilters[$p_oFilter->tree_id] = $p_oFilter;
    }
    else {
      $this->m_aFilters[] = $p_oFilter;
    }
    return $p_oFilter;
  }
    
  /**
   * Adds a filter group
   * @param FilterGroup $p_oFilterGroup
   * @access protected
   * @return FilterGroup
   */
  protected function _addFilterGroup(FilterGroup $p_oFilterGroup) {
    $p_oFilterGroup->parent = $this;
    if ($this->m_sTree_id) {
      $iCount = count($this->m_aFilters);
      $p_oFilterGroup->tree_id = $this->m_sTree_id . '-' . $iCount;
      $this->m_aFilters[$p_oFilterGroup->tree_id] = $p_oFilterGroup;
    }
    else {
      $this->m_aFilters[] = $p_oFilterGroup;
    }
    return $p_oFilterGroup;
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
      
      case 'modus':
        return $this->m_iModus;
      
      case 'name':
        return $this->m_sName;
        
      case 'filters':
        return $this->m_aFilters;
        
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
    if (isset($this->m_aFilters[$p_sFilter_id])) {
      return $this->m_aFilters[$p_sFilter_id];
    }
    return $this;
  }
  
  /**
   * Return as an array of values.
   * @return array
   */
  public function to_array() {
    $aData = array(
      'id' => $this->m_sTree_id,
      'tree_id' => $this->m_sTree_id,
      'modus' => $this->m_iModus,
      'name' => $this->name,
      'filters' => array(),
    );
    foreach ($this->m_aFilters as $oFilter) {
      $aData['filters'][] = $oFilter->to_array();
    }
    return $aData;
  }
  
  /**
   * Counts the number of filters in this group
   * @access public
   * @return int
   */
  public function countFilters() {
    return count($this->m_aFilters);
  }
}