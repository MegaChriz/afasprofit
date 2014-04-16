<?php

/**
 * @file
 * Definition of Afas\Core\Server.
 */

namespace Afas\Core;

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
   * The Profit environment to use.
   *
   * @var string
   */
  private $environmentId;

  /**
   * The username to use to login to Profit.
   *
   * @var string
   */
  private $userId;

  /**
   * The password to use to login to Profit.
   *
   * @var string
   */
  private $password;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Server object constructor.
   *
   * @return \Afas\Core\Server
   */
  public function __construct($environmentId, $userId, $password) {
    $this->baseUrl = 'https://profitweb.afasonline.nl/profitservices';
    $this->uri = 'urn:Afas.Profit.Services';
    $this->environmentId = $environmentId;
    $this->userId = $userId;
    $this->password = $password;
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
   * Implements ServerInterface::getEnvironmentId().
   */
  public function getEnvironmentId() {
    return $this->environmentId;
  }

  /**
   * Implements ServerInterface::getUserId().
   */
  public function getUserId() {
    return $this->userId;
  }

  /**
   * Implements ServerInterface::getPassword().
   */
  public function getPassword() {
    return $this->password;
  }

  /**
   * Implements ServerInterface::getLogonAs().
   */
  public function getLogonAs() {
    return '';
  }
}
