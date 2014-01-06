<?php

/**
 * @file
 * Contains \Afas\Core\UpdateConnector.
 */

namespace Afas\Core;

class UpdateConnector extends Connector {
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Overrides Connector::getSoapArguments().
   */
  protected function getSoapArguments() {
    $arguments = parent::getSoapArguments();
    if (count($this->elements) > 0) {
      $arguments['dataXml'] = $this->getElementsXML();
    }
    return $arguments;
  }
}
