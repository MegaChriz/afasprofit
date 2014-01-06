<?php
/**
 * @file
 * AFAS Filter Group class
 *
 * Wordt gebruikt door AfasGetConnector
 */

class AFAS_FilterGroup extends FilterGroup
{
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Adds a single filter in a new group
   * @param $p_sField
   * @param $p_sValue
   * @param mixed $p_mOperator
   * @access public
   * @return AFAS_Filter
   */
  public function addFilter($p_sField='', $p_sValue='', $p_mOperator='=') {
    $oFilter = new AFAS_Filter($p_sField, $p_sValue, $p_mOperator);
    return $this->_addFilter($oFilter);
  }
  
  /**
   * Adds a filter group
   * @param int $p_iModus
   * @param string $p_sName
   * @access public
   * @return FilterGroup
   * @throws AfasException
   */
  public function addFilterGroup($p_sName='', $p_iModus=self::MODUS_AND) {
    throw new AfasException('AFAS ondersteunt geen groepen in groepen!');
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Return XML string
   * @access public
   * @return string XML
   */
  public function getXML() {
    if (!$this->m_sName) {
      $this->m_sName = "Filter1";
    }
    $sOutput = '<Filter FilterId="'.$this->m_sName.'">';
    foreach ($this->m_aFilters as $oFilter) {
      $sOutput .= $oFilter->getXML();
    }
    $sOutput .= '</Filter>';
    return $sOutput;
  }
}