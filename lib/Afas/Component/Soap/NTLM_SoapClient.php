<?php

/**
 * @file
 * Contains \Afas\Component\Soap\NTLM_SoapClient.
 */

namespace Afas\Component\Soap;

/**
 * A child of SoapClient with support for ntlm proxy authentication
 *
 * @author Meltir <meltir@meltir.com>
 *
 */
class NTLM_SoapClient extends \SoapClient implements SoapClientInterface {
  public function __construct($wsdl, $options = array()) {
    if (empty($options['login']) || empty($options['password'])) {
      throw new \Exception('Login and password are required for NTLM authentication.');
    }
    $this->login = $options['login'];
    $this->password = $options['password'];
    parent::__construct($wsdl, $options);
  }

  /**
   * Override of SoapClient::__doRequest().
   */
  public function __doRequest($request, $location, $action, $version, $one_way = 0) {
    $request = preg_replace('/<(ns1\:[a-z0-9\:\ ]*)>/i', '<${1} xmlns="' . $this->uri . '">', $request);
    return $this->callCurl($location, $request);
  }

  /**
   * Call a url using curl with ntlm auth.
   *
   * @param string $url
   * @param string $data
   * @return string
   * @throws SoapFault on curl connection error
   */
  protected function callCurl($url, $data) {
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);
    //curl_setopt($handle, CURLOPT_FAILONERROR, TRUE);
    $headers = array(
      "Content-type: text/xml;charset=\"utf-8\"",
      "Accept: text/xml",
      "Cache-Control: no-cache",
      "Pragma: no-cache",
      "Content-length: " . strlen($data),
    );
    curl_setopt($handle, CURLOPT_USERPWD, $this->login . ':' . $this->password);
    curl_setopt($handle, CURLOPT_POST, TRUE);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($handle);
    if (empty($response)) {
      throw new SoapFault('CURL error: ' . curl_error($handle), curl_errno($handle));
    }
    curl_close($handle);
    return $response;
  }
}
