<?php

namespace Afas\Core;

/**
 * Interface for objects that can be converted to XML for Profit Connectors.
 */
interface CompilableInterface {

  /**
   * Return XML string.
   *
   * @return string
   *   XML generated string.
   */
  public function compile();

}
