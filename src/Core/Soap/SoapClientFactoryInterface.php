<?php

namespace Afas\Core\Soap;

use Afas\Core\ServerInterface;

/**
 * Interface for generating SoapClient instances.
 */
interface SoapClientFactoryInterface {

  /**
   * Runs the query against the profit.
   *
   * @param \Afas\Core\ServerInterface $server
   *   A server instance.
   *
   * @return \Afas\Component\Soap\SoapClientInterface
   *   An class to send soap requests with.
   */
  public function create(ServerInterface $server);

}
