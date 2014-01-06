<?php

/**
 * @file
 * Contains \Afas\Soap\SoapClientInterface.
 */

namespace Afas\Soap;

/**
 * Provides an interface for handling Soap Requests.
 */
interface SoapClientInterface {
  /**
   * Performs A SOAP request.
   *
   * @return string
   */
  public function __doRequest(string $request, string $location, string $action, int $version, int $one_way = 0);

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
  public function __setCookie(string $name, string $value);

  /**
   *
   */
  public function __setLocation(string $new_location);

  /**
   *
   */
  public function __setSoapHeaders($soapheaders);

  /**
   *
   */
  public function __soapCall(string $function_name, array $arguments, array $options = array(), $input_headers = NULL, &$output_headers = NULL);
}