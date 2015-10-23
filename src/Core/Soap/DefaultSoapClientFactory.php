<?php

/**
 * @file
 * Contains \Afas\Core\Soap\DefaultSoapClientFactory.
 */

namespace Afas\Core\Soap;

use Afas\Component\Soap\NTLMSoapClient;
use Afas\Core\ServerInterface;

/**
 * Class DefaultSoapClientFactory
 * @package Afas\Core\Soap
 */
class DefaultSoapClientFactory implements SoapClientFactoryInterface {
  /**
   * Implements SoapClientFactoryInterface::create().
   */
  public function create(ServerInterface $server) {
    $options = array(
      'location' => '',
      'uri' => $server->getUri(),
      'trace' => 1,
      'style' => SOAP_RPC,
      'use' => SOAP_ENCODED,
      'login' => $server->getUserId(),
      'password' => $server->getPassword(),
    );
    return new NTLMSoapClient(NULL, $options);
  }
}
