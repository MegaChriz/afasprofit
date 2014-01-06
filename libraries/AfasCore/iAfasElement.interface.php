<?php
/**
 * @file
 * Interface for parenting systems in AFAS objects.
 */

interface iAfasElement {
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds a child element object.
   *
   * @param string $p_sType
   * @param string $p_sObject_id
   * @param array $p_aFields
   * @param string $p_sClass
   *
   * @access public
   * @return AfasElement
   */
  public function addChild($p_sType, $p_sObject_id = '', $p_aFields = array(), $p_sClass = 'AfasElement');

  /**
   * Removes a child object.
   *
   * @param string $p_sObject_id
   *
   * @access public
   * @return void
   */
  public function removeChild($p_sObject_id);

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns specific child object.
   *
   * @access public
   * @return AfasElement
   * @throws AfasException
   */
  public function getChild($p_sObject_id);

  /**
   * Returns the server-object this element is in.
   *
   * @return AfasServer
   *   The server-object used by the update-connector.
   *   Or NULL, if this element isn't attached to a server.
   */
  public function getServer();
}
