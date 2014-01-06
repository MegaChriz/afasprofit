<?php
/**
 * @file
 * Interface for filter objects
 */

interface iFilter
{
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Load an existing item from an array.
   * @param array $p_aParams
   * @access public
   * @return void
   */
  public function from_array($p_aParams);
  
  /**
   * Removes this from parent
   * @return boolean
   */
  public function remove();
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Return as an array of values.
   * @access public
   * @return array
   */
  public function to_array();
  
  /**
   * Return a filter by given up the ID
   * @param string $p_sFilter_id
   * @access public
   * @return iFilter
   */
  public function getFilter($p_sFilter_id);
}