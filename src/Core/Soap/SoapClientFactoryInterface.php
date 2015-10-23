<?php

/**
 * @file
 * Definition of \Afas\Core\Soap\SoapClientFactoryInterface.
 */

namespace Afas\Core\Soap;

use Afas\Core\ServerInterface;

interface SoapClientFactoryInterface {
  /**
   * Runs the query against the profit.
   *
   * @param Afas\Core\ServerInterface
   *   A server instance.
   *
   * @return \Afas\Component\Soap\SoapClientInterface
   *   An class to send soap requests with.
   */
  public function create(ServerInterface $server);
}
