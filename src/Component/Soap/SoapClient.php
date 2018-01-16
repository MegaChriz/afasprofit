<?php

namespace Afas\Component\Soap;

use SoapClient as SoapClientBase;

/**
 * A child of SoapClient that exist to just implement the interface.
 */
class SoapClient extends SoapClientBase implements SoapClientInterface {

  /**
   * Override of SoapClient::__doRequest().
   */
  public function __doRequest($request, $location, $action, $version, $one_way = 0) {
    $request = preg_replace('/<(ns1\:[a-z0-9\:\ ]*)>/i', '<${1} xmlns="' . $this->uri . '">', $request);
    return parent::__doRequest($request, $location, $action, $version, $one_way);
  }

}
