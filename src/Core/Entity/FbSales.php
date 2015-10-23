<?php

/**
 * @file
 * Contains \Drupal\Core\Entity\FbSales.
 */

namespace Afas\Core\Entity;

/**
 * Defines the FbSales entity class.
 *
 * @EntityType(
 *   id = "FbSales",
 *   update_connector = "FbSales"
 * )
 */
class FbSales extends Entity {
  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  // Values for 'DeCo' (deliver conditions).
  const DELIVER_PARTLY = 0;
  const DELIVER_RULE_COMPLETE = 1;
  const DELIVER_ORDER_COMPLETE = 2;
  const DELIVER_NONE = 3;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Validates/Correct the structure of this element.
   *
   * @return array
   *   A list of errors that were found.
   */
  public function validate() {

  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * @todo read from xsd.
   */
  public function getFieldDefinition() {

  }
}
