<?php
/**
 * @file
 * AFAS Element Exception class.
 */

/**
 * The AfasElementException is thrown in AfasElement.
 * The Element in question should be attached.
 */
class AfasElementException extends AfasException {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The element in which the error occurred.
   *
   * @var AfasElement
   * @access private
   */
  private $element;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * @param AfasElement $element
   *  The element in which the error occurred.
   *
   * @return void
   */
  public function __construct(AfasElement $element, $message = '', $code = 0, $previous = NULL) {
    $this->element = $element;
    parent::__construct($message, $code, $previous);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns the attached element.
   */
  public function getElement() {
    return $this->element;
  }
}
