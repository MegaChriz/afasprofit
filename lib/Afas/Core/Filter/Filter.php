<?php

/**
 * @file
 * Contains \Afas\Core\Filter\Filter.
 */

namespace Afas\Core\Filter;

use \InvalidArgumentException;

class Filter {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The name of the field to filter on.
   *
   * @var string
   */
  protected $field;

  /**
   * The value to test the field against.
   *
   * @var mixed
   */
  protected $value;

  /**
   * The comparison operator, such as =, <, or >=.
   *
   * @var int
   *
   * @see defined class constants
   */
  protected $operator;

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  // Operators.
  const OPERATOR_EQ               = 1; // Equal
  const OPERATOR_GE               = 2; // Greater than or equal
  const OPERATOR_LE               = 3; // Less than or equal
  const OPERATOR_GT               = 4; // Greater than
  const OPERATOR_LT               = 5; // Less than
  const OPERATOR_CONTAINS         = 6; // Contains
  const OPERATOR_NE               = 7; // Not equal
  const OPERATOR_EMPTY            = 8; // Is empty
  const OPERATOR_NOT_EMPTY        = 9; // Is not empty
  const OPERATOR_STARTS_WITH      = 10; // Starts with
  const OPERATOR_CONTAINS_NOT     = 11; // Does not contain
  const OPERATOR_STARTS_NOT_WITH  = 12; // Starts not with
  const OPERATOR_ENDS_WITH        = 13; // Ends with
  const OPERATOR_ENDS_NOT_WITH    = 14; // Ends not with
  const OPERATOR_QUICK            = 15; // Quickfilter

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Filter object constructor.
   *
   * @param string $field
   *   The name of the field to filter on.
   * @param mixed $value
   *   (optional) The value to test the field against.
   *   Defaults to NULL.
   * @param mixed $operator
   *   (optional) The comparison operator, such as =, <, or >=.
   *   Defaults to:
   *   - static::OPERATOR_EQ if value is set;
   *   - NULL otherwise.
   *
   * @return void
   */
  public function __construct($field, $value = NULL, $operator = NULL) {
    if (!isset($operator) && isset($value)) {
      $operator = static::OPERATOR_EQ;
    }
    $this->setField($field);
    $this->setValue($value);
    if (isset($operator)) {
      $this->setOperator($operator);
    }
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Magic getter.
   */
  public function __get($key) {
    switch ($key) {
      case 'field':
        return $this->field;
      case 'value':
        return $this->value;
      case 'operator':
        return $this->operator;
    }
  }

  /**
   * Return XML string.
   *
   * @return string
   *   The filter as XML.
   *
   * @todo Move to FilterGroup instead?
   */
  public function compile() {
    if ($this->value != '') {
      $output = '<Field FieldId="' . $this->field . '" OperatorType="' . $this->operator . '">' . $this->value . '</Field>';
    }
    else {
      $output = '<Field FieldId="' . $this->field . '" OperatorType="' . $this->operator . '" />';
    }
    return $output;
  }

  /**
   * Implements PHP magic __toString method to convert the filter to string.
   *
   * @return string
   *   A string version of the filter.
   */
  public function __toString() {
    return $this->compile();
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Magic setter.
   */
  public function __set($key, $value) {
    switch ($key) {
      case 'field':
        $this->setField($value);
        break;
      case 'value':
        $this->setValue($value);
        break;
      case 'operator':
        $this->setOperator($value);
        break;
    }
  }

  /**
   * Sets field of filter.
   *
   * @param string $field
   *   The field to set.
   *
   * @throws \InvalidArgumentException
   *   If the field is not of the right data type.
   *
   * @return self
   *   The called object.
   */
  public function setField($field) {
    if (!is_string($field)) {
      throw new InvalidArgumentException('Field must be a string.');
    }
    $this->field = $field;
    return $this;
  }

  /**
   * Sets value of filter.
   *
   * The operator may change, depending on the value that is set.
   *
   * @param mixed $value
   *   The value to set.
   *
   * @return self
   *   The called object.
   */
  public function setValue($value) {
    if (is_null($value)) {
      $this->value = $value;
      $this->setOperator(static::OPERATOR_EMPTY);
      return $this;
    }
    elseif (is_scalar($value)) {
      $this->value = $value;
      return $this;
    }
    // @todo other cases.
  }

  /**
   * Sets operator of filter (e.g. equal to).
   *
   * Accepts both ints and strings.
   *
   * @param mixed $operator
   *   The operator to set.
   *
   * @throws \InvalidArgumentException
   *   If an invalid operator is set.
   *
   * @return self
   *   The called object.
   */
  public function setOperator($operator) {
    if (is_numeric($operator)) {
      if ($operator > 0 && $operator < 16) {
        $this->operator = (int) $operator;

        // Erase value if 'empty' or 'not empty' operator is chosen.
        switch ($this->operator) {
          case self::OPERATOR_NOT_EMPTY:
          case self::OPERATOR_EMPTY:
            $this->value = NULL;
            break;
        }
        return $this;
      }
    }
    elseif (is_string($operator)) {
      $operator = strtolower($operator);
      switch ($operator) {
        case '=':
        case 'eq':
        case 'equal':
          return $this->setOperator(static::OPERATOR_EQ);
        case '>':
        case 'gt':
        case 'greater than':
          return $this->setOperator(static::OPERATOR_GT);
        case '>=':
        case 'ge':
        case 'ge':
          return $this->setOperator(static::OPERATOR_GE);
        case '<':
        case 'lt':
        case 'lesser than':
          return $this->setOperator(static::OPERATOR_LT);
        case '<=':
        case 'le':
          return $this->setOperator(static::OPERATOR_LE);
        case '!=':
        case 'ne':
        case 'not equal':
          return $this->setOperator(static::OPERATOR_NE);
        case 'is null':
        case 'empty':
          return $this->setOperator(static::OPERATOR_EMPTY);
        case 'is not null':
        case 'not empty':
          return $this->setOperator(static::OPERATOR_NOT_EMPTY);
        case 'contains':
          return $this->setOperator(static::OPERATOR_CONTAINS);
        case 'contains not':
          return $this->setOperator(static::OPERATOR_CONTAINS_NOT);
        case 'starts with':
          return $this->setOperator(static::OPERATOR_STARTS_WITH);
        case 'starts not with':
          return $this->setOperator(static::OPERATOR_STARTS_NOT_WITH);
        case 'ends with':
          return $this->setOperator(static::OPERATOR_ENDS_WITH);
        case 'ends not with':
          return $this->setOperator(static::OPERATOR_ENDS_NOT_WITH);
        case 'quick':
          return $this->setOperator(static::OPERATOR_QUICK);
      }
    }
    throw new InvalidArgumentException('The operator "' . htmlspecialchars($operator, ENT_QUOTES, 'UTF-8') . '" is not supported.');
  }
}
