<?php

/**
 * @file
 * Definition of \Afas\Core\ServerInterface.
 */

namespace Afas\Core;

interface ServerInterface {
  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Returns a get query object.
   *
   * @param string $connector_id
   *   The Get connector to use.
   *
   * @return \Afas\Core\Query\Get
   *   An instance of Get.
   */
  public function get($connector_id);

  /**
   * Returns an update query object.
   *
   * @return \Afas\Core\Query\Update
   *   An instance of Update.
   */
  public function update();

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Returns the base url of the Profit server.
   *
   * @return string
   *   The server's base url.
   */
  public function getBaseUrl();

  /**
   * Returns the uri of the Profit server.
   *
   * @return string
   *   The server's uri.
   */
  public function getUri();

  /**
   * Returns the Profit environment to connect to.
   *
   * @return string
   *   The server's environment.
   */
  public function getEnvironmentId();

  /**
   * Returns the username to use to login to Profit.
   *
   * @return string
   *   The username.
   */
  public function getUserId();

  /**
   * Returns the password to use to login to Profit.
   *
   * @return string
   *   The password.
   */
  public function getPassword();

  /**
   * Returns 'logonAs' variable.
   *
   * @return string
   *   The 'logonAs' variable.
   */
  public function getLogonAs();
}
