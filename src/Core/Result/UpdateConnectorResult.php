<?php

namespace Afas\Core\Result;

/**
 * Class for processing results from a Profit UpdateConnector.
 */
class UpdateConnectorResult extends ResultBase {

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    // Convert XML to array.
    return $this->getArrayData($this->asXml());
  }

}
