<?php

/**
 * @file
 * Contains \Afas\Core\Entity\EntityBase.
 */

namespace Afas\Core\Entity;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Mapping\MappingInterface;

class EntityBase implements EntityInterface, MappingInterface {
  /**
   * List of fields.
   *
   * @var array
   */
  protected $fields;

  /**
   * List of objects.
   *
   * @var array
   */
  protected $objects;

  /**
   * @var \Afas\Core\Mapping\MappingInterface
   */
  private $mapper;

  /**
   * Sets a field.
   *
   * @param string $key
   *   The field to set.
   * @param string $value
   *   The field's value.
   *
   * @return void
   */
  public function setField($key, $value) {
    $keys = $this->map($key);
    foreach ($keys as $key) {
      $this->fields[$key] = (string) $value;
    }
  }

  /**
   * Sets mapper.
   */
  public function setMapper(MappingInterface $mapper) {
    $this->mapper = $mapper;
  }

  /**
   * Implements MappingInterface::map().
   */
  public function map($key) {
    if ($this->mapper instanceof MappingInterface) {
      return $this->mapper->map($key);
    }
    return array($key);
  }
}
