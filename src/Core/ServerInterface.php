<?php

/**
 * @file
 * Definition of \Afas\Core\ServerInterface.
 */

namespace Afas\Core;

interface ServerInterface {
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

  /**
   * Returns a get query object.
   *
   * @return \Afas\Core\Query\Get
   */
  public function get($connector_id);

  /**
   * Returns an update query object.
   *
   * @return \Afas\Core\Query\Update
   */
  public function update();
}
