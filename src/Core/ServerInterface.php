<?php

namespace Afas\Core;

/**
 * Interface for connecting a Profit server.
 */
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
   *   A get query.
   */
  public function get($connector_id);

  /**
   * Returns an update query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   * @param array $data
   *   The data to insert.
   * @param ??? $mapper
   *
   * @return \Afas\Core\Query\Insert
   *   An insert query.
   */
  public function insert($connector_id, array $data, $mapper = NULL);

  /**
   * Returns an update query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   * @param array $data
   *   The data to update.
   * @param ??? $mapper
   *
   * @return \Afas\Core\Query\Update
   *   An update query.
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
  public function getApiKeyAsXml();

}
