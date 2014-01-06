<?php
/**
 * @file
 * Interface for parenting systems in AFAS objects 
 */

interface iAFAS_Element
{
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Adds a child element object
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   * @param string $p_sClass
   * @access public
   * @return AFAS_Element
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields=array(), $p_sClass = 'AFAS_Element');
  
  /**
   * Removes a child object
   * @param string $p_sObject_id
   * @access public
   * @return void
   */
  public function removeChild($p_sObject_id);
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
    
  /**
   * Returns specific child object
   * @access public
   * @return AFAS_Element
   * @throws AfasException
   */
  public function getChild($p_sObject_id);
}