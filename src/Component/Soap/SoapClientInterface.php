<?php

namespace Afas\Component\Soap;

if (version_compare(PHP_VERSION, '8.0.0') >= 0) {

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

} else {

  /**
   * Provides an interface for handling Soap Requests.
   */
  interface SoapClientInterface {

    /**
     * Performs A SOAP request.
     *
     * {@inheritdoc}
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0);

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
    public function __setCookie($name, $value);

    /**
     *
     */
    public function __setLocation($new_location);

    /**
     *
     */
    public function __setSoapHeaders($soapheaders);

    /**
     *
     */
    public function __soapCall($function_name, $arguments, $options = [], $input_headers = NULL, &$output_headers = NULL);

  }

}