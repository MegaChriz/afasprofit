<?php

/**
 * @file
 * Contains \Afas\Component\Soap\NTLM_SoapClient.
 */

namespace Afas\Component\Soap;

use \SoapClient;
use \SoapFault;

/**
 * A child of SoapClient with support for ntlm proxy authentication
 *
 * @author Meltir <meltir@meltir.com>
 *
 */
class NTLM_SoapClient extends SoapClient implements SoapClientInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * Login name.
   *
   * @var string
   */
  private $login;

  /**
   * Password.
   *
   * @var string
   */
  private $password;

  /**
   * Options for curl.
   *
   * @var array
   */
  private $curl_options = array();

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * NTLM_SoapClient object constructor.
   */
  public function __construct($wsdl, $options = array()) {
    // Set login and password.
    if (empty($options['login']) || empty($options['password'])) {
      throw new \Exception('Login and password are required for NTLM authentication.');
    }
    $this->login = $options['login'];
    $this->password = $options['password'];

    // Set curl options.
    $options += array(
      'curl' => array(),
    );
    $this->curl_options = array_replace_recursive($this->curlDefaults(), $options['curl']);

    parent::__construct($wsdl, $options);
  }

  /**
   * Returns default settings for curl.
   *
   * @return array
   *   A list of default settings for curl.
   */
  public function curlDefaults() {
    return array(
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_POST => TRUE,
      CURLOPT_HTTPHEADER => array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
      ),
    );
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

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
   *   The url to call.
   * @param string $data
   *   The data to send along.
   *
   * @return string
   * @throws SoapFault on curl connection error.
   */
  protected function callCurl($url, $data) {
    $handle = curl_init();

    // Set curl options.
    foreach ($this->curl_options as $key => $value) {
      curl_setopt($handle, $key, $value);
    }

    // Set curl headers.
    $headers = $this->curl_options[CURLOPT_HTTPHEADER];
    $headers[] = "Content-length: " . strlen($data);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);

    // Set user + password.
    curl_setopt($handle, CURLOPT_USERPWD, $this->login . ':' . $this->password);

    // Set url.
    curl_setopt($handle, CURLOPT_URL, $url);

    // Set data.
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

    // Execute curl.
    $response = curl_exec($handle);
    if (empty($response)) {
      throw new SoapFault('CURL error: ' . curl_error($handle), curl_errno($handle));
    }
    curl_close($handle);

    return $response;
  }
}
