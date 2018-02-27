<?php

namespace Afas\Core\Query;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\ServerInterface;

/**
 * Updating existing data into Profit.
 */
class Update extends UpdateBase implements UpdateInterface {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new Update object.
   *
   * @param \Afas\Core\ServerInterface $server
   *   The server to send data to.
   * @param string $connector_id
   *   The name of the UpdateConnector.
   * @param array $data
   *   The data to insert.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   */
  public function __construct(ServerInterface $server, $connector_id, array $data, array $attribute_keys = []) {
    parent::__construct($server, $connector_id);

    if (!empty($attribute_keys)) {
      if ($this->isAssociative($data)) {
        $this->convertAttributes($data, $attribute_keys);
      }
      else {
        foreach ($data as &$subdata) {
          $this->convertAttributes($subdata, $attribute_keys);
        }
      }
    }

    $this->entityContainer->setAction(EntityInterface::FIELDS_UPDATE);
    $this->entityContainer->fromArray($data);
  }

  /**
   * Sets attributes on item array, if available.
   *
   * @param array $item
   *   The item to convert attributes for.
   * @param array $attribute_keys
   *   The attribute keys.
   */
  protected function convertAttributes(array &$item, array $attribute_keys) {
    foreach ($attribute_keys as $attribute_key) {
      if (isset($item[$attribute_key])) {
        $item['@attributes'][$attribute_key] = $item[$attribute_key];
        unset($item[$attribute_key]);
      }
    }
  }

  /**
   * Checks if an array is associative.
   */
  protected function isAssociative($arr) {
    foreach ($arr as $key => $value) {
      if (is_string($key)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
