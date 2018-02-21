<?php

namespace Afas\Core\Entity;

/**
 * Interface for an entity validator.
 */
interface EntityValidatorInterface {

  /**
   * Validates an entity container.
   *
   * @param \Afas\Core\Entity\EntityContainerInterface $container
   *   The entity container to validate.
   *
   * @return array
   *   The errors that occurred.
   */
  public function validate(EntityContainerInterface $container);

}
