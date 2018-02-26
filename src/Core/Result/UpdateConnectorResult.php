<?php

namespace Afas\Core\Result;

use Afas\Core\Exception\EmptyException;

/**
 * Class for processing results from a Profit UpdateConnector.
 */
class UpdateConnectorResult extends ResultBase {

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    try {
      // Convert XML to array.
      return $this->getArrayData($this->asXml());
    }
    catch (EmptyException $e) {
      // Not an error.
      return [];
    }
  }

}
