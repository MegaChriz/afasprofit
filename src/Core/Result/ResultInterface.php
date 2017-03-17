<?php

namespace Afas\Core\Result;

/**
 * Interface for a result from a profit connector call.
 */
interface ResultInterface {
  /**
   * Returns the result as XML.
   *
   * @return string
   *   The data result, as XML.
   */
  public function asXML();

  /**
   * Returns the headers of the returned data.
   *
   * Works only if metadata was send along.
   *
   * @return []
   *   An array of headers.
   */
  public function getHeaders();

  /**
   * Returns the result as PHP array.
   *
   * @return []
   *   The complete data result, as an array.
   */
  public function asArray();
}
