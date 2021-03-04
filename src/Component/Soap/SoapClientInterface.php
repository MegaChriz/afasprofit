<?php

namespace Afas\Component\Soap;

/**
 * Provides an interface for handling Soap Requests.
 */
interface SoapClientInterface {

  /**
   * Performs A SOAP request.
   *
   * {@inheritdoc}
   */
  public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false);

  /**
   * Returns list of available SOAP functions.
   *
   * @return array
   */
  public function __getFunctions();

  /**
   *
   */
  public function __getLastRequest();

  /**
   *
   */
  public function __getLastRequestHeaders();

  /**
   *
   */
  public function __getLastResponse();

  /**
   *
   */
  public function __getLastResponseHeaders();

  /**
   *
   */
  public function __getTypes();

  /**
   *
   */
  public function __setCookie(string $name, ?string $value = null);

  /**
   *
   */
  public function __setLocation(string $location);

  /**
   *
   */
  public function __setSoapHeaders($headers = null);

  /**
   *
   */
  public function __soapCall(string $name, array $args, ?array $options = null, $inputHeaders = null, &$outputHeaders = null);

}
