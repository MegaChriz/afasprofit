<?php

namespace Afas\Core\Mapping;

use Afas\Core\Entity\EntityInterface;
use InvalidArgumentException;

/**
 * Class for generating mapping objects.
 */
class EntityMappingFactory implements EntityMappingFactoryInterface {

  /**
   * The mapping class.
   *
   * @var string
   */
  protected $class;

  /**
   * Constructs a new MappingFactory object.
   */
  public function __construct() {
    $this->setClass(DefaultMapping::class);
  }

  /**
   * {@inheritdoc}
   */
  public function setClass($class) {
    if (!in_array(EntityMappingInterface::class, class_implements($class))) {
      throw new InvalidArgumentException(strtr('The given class !class does not implement !interface.', [
        '!class' => @(string) $class,
        '!interface' => EntityMappingInterface::class,
      ]));
    }
    $this->class = $class;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function createForEntity(EntityInterface $entity) {
    $class = $this->class;
    return $class::create($entity);
  }

}
