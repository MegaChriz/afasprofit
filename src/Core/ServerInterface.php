<?php

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
   * @param string $connector_id
   *   The Update connector to use.
   *
   * @return \Afas\Core\Query\Insert
   *   An instance of Insert.
   */
  public function insert($connector_id, array $data, $mapper = NULL);

  /**
   * Returns an update query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   *
   * @return \Afas\Core\Query\Update
   *   An instance of Update.
   */
  public function update($connector_id, array $data, $mapper = NULL);

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
   * Returns the Profit API key to use.
   *
   * @return string
   *   The server's api key.
   */
  public function getApiKey();

  /**
   * Returns the Profit API key to use as XML.
   *
   * @return string
   *   The server's api key, generated as XML.
   */
  public function getApiKeyAsXML();
}
