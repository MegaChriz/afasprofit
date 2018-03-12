<?php

namespace Afas\Core\Connector;

/**
 * Class for the Profit DataConnector.
 */
interface DataConnectorInterface extends ConnectorInterface {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * Retrieves the XSD file for a certain update connector.
   *
   * @param string $update_connector_id
   *   The update connector for which to retrieve the XML schema.
   * @param bool $encode
   *   (optional Whether not the returned data must be base64-encoded.
   *   Defaults to false.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result of the call.
   */
  public function getXmlSchema($update_connector_id, $encode = FALSE);

}
