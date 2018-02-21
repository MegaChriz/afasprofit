<?php

namespace Afas\Core\Exception;

use Afas\Core\Entity\EntityContainerInterface;

/**
 * Thrown when validation of an entity fails.
 */
class EntityValidationException extends AfasException {

  /**
   * The entity that failed validation.
   *
   * @var \Afas\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The errors for this entity.
   *
   * @var array
   */
  protected $errors;

  /**
   * Constructs a new EntityValidationException object.
   *
   * @param \Afas\Core\Entity\EntityContainerInterface $entity
   *   The entity that failed validation.
   * @param array $errors
   *   The errors for this entity.
   */
  public function __construct(EntityContainerInterface $entity, array $errors) {
    parent::__construct(implode("\n", $errors));

    $this->entity = $entity;
    $this->errors = $errors;
  }

  /**
   * Returns the entity that failed validation.
   *
   * @return \Afas\Core\Entity\EntityContainerInterface
   *   The entity that failed validation.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Return a list of error messages.
   *
   * @return array
   *   The errors that occurred.
   */
  public function getErrors() {
    return $this->errors;
  }

}
