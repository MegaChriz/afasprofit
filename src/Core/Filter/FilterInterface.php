<?php

namespace Afas\Core\Filter;

use Afas\Core\CompilableInterface;

/**
 * Interface for setting a filter for a Profit GetConnector.
 */
interface FilterInterface extends CompilableInterface {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Equal operator.
   */
  const OPERATOR_EQ = 1;
  const OP_EQUAL = 1;

  /**
   * Greater than or equal operator.
   */
  const OPERATOR_GE = 2;
  const OP_LARGER_OR_EQUAL = 2;

  /**
   * Less than or equal or equal.
   */
  const OPERATOR_LE = 3;
  const OP_SMALLER_OR_EQUAL = 3;

  /**
   * Greater than operator.
   */
  const OPERATOR_GT = 4;
  const OP_LARGER_THAN = 4;

  /**
   * Less than operator.
   */
  const OPERATOR_LT = 5;
  const OP_SMALLER_THAN = 5;

  /**
   * Contains operator.
   */
  const OPERATOR_CONTAINS = 6;
  const OP_CONTAINS = 6;

  /**
   * Not equal operator.
   */
  const OPERATOR_NE = 7;
  const OP_NOT_EQUAL = 7;

  /**
   * Is empty operator.
   */
  const OPERATOR_EMPTY = 8;
  const OP_EMPTY = 8;

  /**
   * Is not empty operator.
   */
  const OPERATOR_NOT_EMPTY = 9;
  const OP_NOT_EMPTY = 9;

  /**
   * Starts with operator.
   */
  const OPERATOR_STARTS_WITH = 10;
  const OP_STARTS_WITH = 10;

  /**
   * Does not contain operator.
   */
  const OPERATOR_CONTAINS_NOT = 11;
  const OP_NOT_LIKE = 11;
  const OP_NOT_CONTAINS = 11;

  /**
   * Starts not with operator.
   */
  const OPERATOR_STARTS_NOT_WITH = 12;
  const OP_NOT_STARTS_WITH = 12;

  /**
   * Ends with operator.
   */
  const OPERATOR_ENDS_WITH = 13;
  const OP_ENDS_WITH = 13;

  /**
   * Ends not with operator.
   */
  const OPERATOR_ENDS_NOT_WITH = 14;
  const OP_NOT_ENDS_WITH = 14;

  /**
   * Quickfilter.
   */
  const OPERATOR_QUICK = 15;
  const OP_QUICK = 15;

}
