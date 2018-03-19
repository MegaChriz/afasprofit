<?php

namespace Afas\Core\Soap;

use Afas\Core\ServerInterface;

/**
 * Interface for generating SoapClient instances.
 */
interface SoapClientFactoryInterface {

  /**
   * Creates a new soap client.
   *
   * @param \Afas\Core\ServerInterface $server
   *   A server instance.
   *
   * @return \Afas\Component\Soap\SoapClientInterface
   *   A class to send soap requests with.
   */
  public function create(ServerInterface $server);

}
