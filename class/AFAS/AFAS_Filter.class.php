<?php
/**
 * @file
 * AFAS Filter class
 *
 * Wordt gebruikt door AfasGetConnector
 */
 
class AFAS_Filter extends Filter
{
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Return XML string
   * @access public
   * @return string XML
   */
  public function getXML() {
    if ($this->m_sValue != '') {
      $sOutput = '<Field FieldId="' . $this->m_sField . '" OperatorType="' . $this->m_iOperator . '">' . $this->m_sValue . '</Field>';
    }
    else {
      $sOutput = '<Field FieldId="' . $this->m_sField . '" OperatorType="' . $this->m_iOperator . '" />';
    }
    return $sOutput;
  }
}