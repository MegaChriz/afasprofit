<?php

namespace Afas\Core\Connector;

interface ConnectorInterface {
  /**
   * Location of the soap service to call, usually an url.
   *
   * @return string
   *   The location of the soap service.
   */
  public function getLocation();
}
