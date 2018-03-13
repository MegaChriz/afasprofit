<?php

namespace Afas\Core\Entity;

use Afas\Core\Exception\EntityValidationException;

/**
 * Trait for requiring validation.
 */
trait MustValidateTrait {

  /**
   * If validation is enabled.
   *
   * @var bool
   */
  protected $mustValidate = TRUE;

  /**
   * Enables validation during compiling.
   */
  public function enableValidation() {
    $this->mustValidate = TRUE;
  }

  /**
   * Disables validation during compiling.
   */
  public function disableValidation() {
    $this->mustValidate = FALSE;
  }

  /**
   * Returns if validation is enabled.
   *
   * @return bool
   *   True if validation is enabled, false otherwise.
   */
  public function isValidationEnabled() {
    return $this->mustValidate;
  }

  /**
   * Validates/corrects the structure of this element.
   *
   * This should be implemented to ensure that the structure is valid before the
   * data is send to Afas Profit.
   *
   * @return string[]
   *   An array of error messages.
   */
  abstract public function validate();

  /**
   * Validates the entity and throws an exception if validation fails.
   *
   * @throws \Afas\Core\Exception\EntityValidationException
   *   When validation fails.
   */
  protected function mustValidate() {
    $errors = $this->validate();
    if (!empty($errors)) {
      throw new EntityValidationException($this, $errors);
    }
  }

}
