<?php

namespace Afas\Core\Result;

/**
 * Class for processing results from a Profit GetConnector.
 */
class GetConnectorResult extends ResultBase {

  /**
   * {@inheritdoc}
   */
  public function asArray() {
    // Convert XML to array.
    $data = $this->getArrayData($this->asXml());

    // Remove the metadata.
    unset($data['AfasGetConnector']['xs:schema']);

    if (empty($data['AfasGetConnector'])) {
      return [];
    }

    // Check if only one data row was given back. If so, adjust array so the result becomes:
    //
    // AfasGetConnector =>
    //   MyGetConnector =>
    //     0 =>
    //       column1 => value1
    //       column2 => value2
    //
    // instead of:
    //
    // AfasGetConnector =>
    //   MyGetConnector =>
    //     column1 => value1
    //     column2 => value2
    $keys = array_keys($data['AfasGetConnector']);
    $key = reset($keys);
    if (!isset($data['AfasGetConnector'][$key][0])) {
      // There is only one item, make sure that there is a 0 item.
      $data['AfasGetConnector'][$key] = [$data['AfasGetConnector'][$key]];
    }

    return $data['AfasGetConnector'][$key];
  }

}
