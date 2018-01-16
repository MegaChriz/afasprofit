<?php

namespace Afas\Core\Connector;

/**
 * Interface for all Profit connectors.
 */
interface ConnectorInterface {

  /**
   * Returns the Server object.
   *
   * @return \Afas\Core\Server
   *   The server that is used to send a request to.
   */
  public function getServer();

  /**
   * Location of the soap service to call, usually an url.
   *
   * @return string
   *   The location of the soap service.
   */
  public function getLocation();

  /**
   * Returns result of last call.
   *
   * @return \Afas\Core\Result\Result
   *   An instance of Result.
   */
  public function getResult();

}
