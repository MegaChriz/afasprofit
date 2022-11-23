<?php

namespace Afas\Core;

use Afas\Core\Query\Get;
use Afas\Core\Query\GetInterface;
use Afas\Core\Query\Insert;
use Afas\Core\Query\InsertInterface;
use Afas\Core\Query\Update;
use Afas\Core\Query\UpdateInterface;
use Afas\Core\Query\Delete;
use Afas\Core\Query\DeleteInterface;

/**
 * Class for connecting a Profit server.
 */
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
   * Constructs a new Server object.
   *
   * @param string $base_url
   *   The servers base URL.
   *   Should be something like:
   *   @url
   *   https://12345.afasonlineconnector.nl/profitservices
   *   @endurl
   * @param string $api_key
   *   The API key to use.
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
   * {@inheritdoc}
   */
  public function get($connector_id): GetInterface {
    return new Get($this, $connector_id);
  }

  /**
   * {@inheritdoc}
   */
  public function insert($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): InsertInterface {
    return new Insert($this, $connector_id, $data, $attribute_keys, $entity_type_id);
  }

  /**
   * {@inheritdoc}
   */
  public function update($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): UpdateInterface {
    return new Update($this, $connector_id, $data, $attribute_keys, $entity_type_id);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($connector_id, array $data, array $attribute_keys = [], string $entity_type_id = ''): DeleteInterface {
    return new Delete($this, $connector_id, $data, $attribute_keys, $entity_type_id);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getBaseUrl() {
    return $this->baseUrl;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * {@inheritdoc}
   */
  public function getApiKeyAsXml() {
    return '<token><version>1</version><data>' . $this->apiKey . '</data></token>';
  }

}
