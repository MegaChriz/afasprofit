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
  public function asXml() {
    // The results from an UpdateConnector includes the '<?xml' tag.
    return $this->getRaw();
  }

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    try {
      // Convert XML to array.
      $data = $this->getArrayData($this->asXml());
    }
    catch (EmptyException $e) {
      // Not an error.
      return [];
    }

    // Remove the metadata.
    unset($data['results']['xs:schema']);

    if (empty($data['results'])) {
      return [];
    }

    /*
     * Check if only one data row was given back. If so, adjust array so the
     * result becomes:
     *
     * @code
     * results =>
     *   MyUpdateConnector =>
     *     0 =>
     *       column1 => value1
     *       column2 => value2
     *
     * instead of:
     *
     * results =>
     *   MyUpdateConnector =>
     *     column1 => value1
     *     column2 => value2
     * @endcode
     */
    $keys = array_keys($data['results']);
    $key = reset($keys);
    if (!isset($data['results'][$key][0])) {
      // There is only one item, make sure that there is a 0 item.
      $data['results'][$key] = [$data['results'][$key]];
    }

    return $data['results'][$key];
  }

}
