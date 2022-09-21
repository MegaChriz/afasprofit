<?php

namespace Afas\Component\Soap;

use SoapClient as SoapClientBase;

if (version_compare(PHP_VERSION, '8.0.0') >= 0) {

  /**
   * A child of SoapClient that exist to just implement the interface.
   */
  class SoapClient extends SoapClientBase implements SoapClientInterface {

    /**
     * Override of SoapClient::__doRequest().
     */
    public function __doRequest(string $request, string $location, string $action, int $version, bool $oneWay = false): ?string {
      return parent::__doRequest($request, $location, $action, $version, $oneWay);
    }

  }

}
else {

  /**
   * A child of SoapClient that exist to just implement the interface.
   */
  class SoapClient extends SoapClientBase implements SoapClientInterface {

    /**
     * Override of SoapClient::__doRequest().
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
      return parent::__doRequest($request, $location, $action, $version, $one_way);
    }

  }

}
