<?php

namespace Afas\Component\Utility;

/**
 * Provides array helper methods.
 *
 * @ingroup utility
 */
class ArrayHelper {

  /**
   * Checks if an array is associative.
   *
   * @param array $arr
   *   The array to check.
   *
   * @returns bool
   *   True if the array is associative, false otherwise.
   */
  public static function isAssociative(array $arr) {
    foreach ($arr as $key => $value) {
      if (is_string($key)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
