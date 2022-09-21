<?php

namespace Afas\Core\Soap;

use Afas\Component\Soap\SoapClient;
use Afas\Core\ServerInterface;

/**
 * Default factory for generating SoapClient instances.
 *
 * @package Afas\Core\Soap
 */
class DefaultSoapClientFactory implements SoapClientFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function create(ServerInterface $server) {
    $options = [
      'location' => '',
      'uri' => $server->getUri(),
      'trace' => 1,
      'style' => SOAP_DOCUMENT,
      'use' => SOAP_LITERAL,
    ];
    return new SoapClient(NULL, $options);
  }

}
