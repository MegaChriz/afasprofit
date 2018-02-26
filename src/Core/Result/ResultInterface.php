<?php

namespace Afas\Core\Result;

/**
 * Interface for a result from a profit connector call.
 */
interface ResultInterface {

  /**
   * Returns the raw result.
   *
   * @return string
   *   The raw data result.
   *
   * @throws \Afas\Core\Exception\EmptyException
   *   In case the data result was empty (not an error).
   */
  public function getRaw();

  /**
   * Returns the result as XML.
   *
   * @return string
   *   The data result, as XML.
   */
  public function asXml();

  /**
   * Returns the headers of the returned data.
   *
   * Works only if metadata was send along.
   *
   * @return array
   *   An array of headers.
   */
  public function getHeaders();

  /**
   * Returns the result as PHP array.
   *
   * @return array
   *   The complete data result, as an array.
   */
  public function asArray();

}
