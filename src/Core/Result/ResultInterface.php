<?php

/**
 * @file
 * Definition of \Afas\Core\Result\ResultInterface.
 */

namespace Afas\Core\Result;

/**
 *
 */
interface ResultInterface {
  /**
   * Returns the result as XML.
   *
   * @return string
   *   ...
   */
  public function asXML();

  /**
   * Returns the result as PHP array.
   *
   * @return array
   *   ...
   */
  public function asArray();
}
