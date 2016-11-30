<?php

/**
 * @file
 * Definition of Afas\Core\Server.
 */

namespace Afas\Core;

use Afas\Core\Query\Get;
use Afas\Core\Query\Update;

class Server implements ServerInterface {
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The base url of the server.
   *
   * @var string
   */
  private $baseUrl;

  /**
   * The uri of the server.
   *
   * @var string
   */
  private $uri;

  /**
   * The API key to use.
   *
   * @var string
   */
  private $apiKey;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Server object constructor.
   *
   * @param string $base_url
   *   The servers base URL.
   *   Should be something like:
   *   @url
   *   https://12345.afasonlineconnector.nl/profitservices
   *   @endurl
   * @param string $user_id
   *   The username to use to login to Profit.
   * @param string $password
   *   The password to use to login to Profit.
   *
   * @return \Afas\Core\Server
   */
  public function __construct($base_url, $api_key) {
    $this->baseUrl = $base_url;
    $this->uri = 'urn:Afas.Profit.Services';
    $this->apiKey = $api_key;
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Implements ServerInterface::get().
   */
  public function get($connector_id) {
    return new Get($this, $connector_id);
  }

  /**
   * Implements ServerInterface::insert().
   */
  public function insert($connector_id, array $data, $mapper = NULL) {
    return new Insert($this, $connector_id, $data, $mapper);
  }

  /**
   * Implements ServerInterface::update().
   */
  public function update($connector_id, array $data, $mapper = NULL) {
    return new Update($this, $connector_id, $data, $mapper);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Implements ServerInterface::getBaseUrl().
   */
  public function getBaseUrl() {
    return $this->baseUrl;
  }

  /**
   * Implements ServerInterface::getUri().
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Implements ServerInterface::getApiKey().
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * Implements ServerInterface::getApiKeyAsXML().
   */
  public function getApiKeyAsXML() {
    return '<token><version>1</version><data>' . $this->apiKey . '</data></token>';
  }
}
