<?php
/**
 * @file
 * Contains MSSoapClient class.
 *
 * @see http://www.webdeveloper.com/forum/showthread.php?t=192064
 */

/**
 * Extends SoapClient class, with additional code to talk with ASP.net based servers.
 */
class MSSoapClient extends SoapClient {
  // Override so that we can append the xmlns attribute to the "action" node.
  function __doRequest($request, $location, $action, $version, $one_way = 0) {
    $request = preg_replace('/<(ns1\:[a-z0-9\:\ ]*)>/i', '<${1} xmlns="' . $this->uri . '">', $request);
    return parent::__doRequest($request, $location, $action, $version);
  }
}
