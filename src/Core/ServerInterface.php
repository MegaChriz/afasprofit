<?php

namespace Afas\Core;

use Afas\Core\Query\GetInterface;
use Afas\Core\Query\InsertInterface;
use Afas\Core\Query\UpdateInterface;
use Afas\Core\Query\DeleteInterface;

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
   * @return \Afas\Core\Query\GetInterface
   *   A get query.
   */
  public function get($connector_id): GetInterface;

  /**
   * Returns an insert query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   * @param array $data
   *   The data to insert.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   * @param string $entity_type_id
   *   (optional) The type of entity that needs to be inserted.
   *
   * @return \Afas\Core\Query\InsertInterface
   *   An insert query.
   */
  public function insert($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): InsertInterface;

  /**
   * Returns an update query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   * @param array $data
   *   The data to update.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   * @param string $entity_type_id
   *   (optional) The type of entity that needs to be updated.
   *
   * @return \Afas\Core\Query\UpdateInterface
   *   An update query.
   */
  public function update($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): UpdateInterface;

  /**
   * Returns a delete query object.
   *
   * @param string $connector_id
   *   The Update connector to use.
   * @param array $data
   *   The data to delete.
   * @param array $attribute_keys
   *   (optional) The keys belonging to attributes.
   * @param string $entity_type_id
   *   (optional) The type of entity that needs to be deleted.
   *
   * @return \Afas\Core\Query\DeleteInterface
   *   A delete query.
   */
  public function delete($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): DeleteInterface;

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
