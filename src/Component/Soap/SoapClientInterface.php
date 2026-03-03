<?php

namespace Afas\Component\Soap;

if (version_compare(PHP_VERSION, '8.5.0') >= 0) {

  /**
   * Provides an interface for handling Soap Requests.
   */
  interface SoapClientInterface {

    /**
     * Performs A SOAP request.
     *
     * {@inheritdoc}
     */
    public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false, ?string $uriParserClass = null): ?string;

    /**
     * Returns list of available SOAP functions.
     *
     * @return array
     */
    public function __getFunctions();

    /**
     * Returns the SOAP request XML from the most recent request.
     *
     * @return string|null
     */
    public function __getLastRequest();

    /**
     * Returns the SOAP request headers from the most recent request.
     *
     * @return string|null
     */
    public function __getLastRequestHeaders();

    /**
     * Returns the SOAP response XML from the most recent request.
     *
     * @return string|null
     */
    public function __getLastResponse();

    /**
     * Returns the SOAP response headers from the most recent request.
     *
     * @return string|null
     */
    public function __getLastResponseHeaders();

    /**
     * Returns list of SOAP types available in the WSDL.
     *
     * @return array
     */
    public function __getTypes();

    /**
     * Defines a cookie to be sent with SOAP requests.
     */
    public function __setCookie(string $name, ?string $value = null);

    /**
     * Sets the endpoint URL for SOAP requests.
     */
    public function __setLocation(string $location);

    /**
     * Sets SOAP headers to be sent with SOAP requests.
     */
    public function __setSoapHeaders($headers = null);

    /**
     * Calls a SOAP function.
     */
    public function __soapCall(string $name, array $args, ?array $options = null, $inputHeaders = null, &$outputHeaders = null);

  }

}
elseif (version_compare(PHP_VERSION, '8.0.0') >= 0) {

  /**
   * Provides an interface for handling Soap Requests.
   */
  interface SoapClientInterface {

    /**
     * Performs A SOAP request.
     *
     * {@inheritdoc}
     */
    public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false): ?string;

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

}
else {

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
