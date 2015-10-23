<?php

/**
 * @file
 * Contains \Afas\Core\Connector\UpdateConnector.
 */

namespace Afas\Core\Connector;

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
