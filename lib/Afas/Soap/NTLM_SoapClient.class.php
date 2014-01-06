<?php

/**
 * @file
 * Contains \Afas\Soap\NTLM_SoapClient.
 */

namespace Afas\Soap;

use Afas\Soap\SoapClientInterface;

/**
 * A child of SoapClient with support for ntlm proxy authentication
 *
 * @author Meltir <meltir@meltir.com>
 *
 */
class NTLM_SoapClient extends SoapClient implements SoapClientInterface {
  /**
   * Overrides SoapClient::__construct().
   */
  public function __construct($wsdl, $options = array()) {
    if (empty($options['login']) || empty($options['password'])) {
      throw new SoapFault('Login and password are required for NTLM authentication.');
    }
    $this->login = $options['login'];
    $this->password = $options['password'];
    parent::__construct($wsdl, $options);
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
    $text_pattern = '[a-z0-9\.\:]';

    // Find NS1.
    $uri = NULL;
    $matches = array();
    $pattern = '/xmlns\:ns1=\"(' . $text_pattern . '+)\"/i';
    preg_match($pattern, $data, $matches);
    if (!empty($matches[1])) {
      $uri = $matches[1];
    }

    // Get rid of NS1.
    if ($uri) {
      // Get rid of ns1 in soap envelope body.
      $ns1_pattern = 'ns1\:(' . $text_pattern . '+)';
      $xml_open = '<' . $ns1_pattern;
      $xml_close = '<\/' . $ns1_pattern;
      $data = preg_replace('/' . $xml_open . '/i', '<${1} xmlns="' . $uri . '"', $data);
      $data = preg_replace('/' . $xml_close . '/i', '</${1}', $data);

      // Get rid of ns1 in soap envelope header.
      $xmlns_pattern = '/xmlns\:ns1=\"' . $text_pattern . '+\"/i';
      $data = preg_replace($xmlns_pattern, '', $data);
    }

    $handle = curl_init();
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($handle, CURLOPT_TIMEOUT, 10);
    curl_setopt($handle, CURLOPT_FAILONERROR, TRUE);
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

  /**
   * Override of SoapClient::__doRequest().
   */
  public function __doRequest($request, $location, $action, $version, $one_way = 0) {
    return $this->callCurl($location,$request);
  }
}
