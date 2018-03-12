<?php

namespace Afas\Core\Connector;

use Afas\Core\Result\DataConnectorResult;

/**
 * Class for the Profit DataConnector.
 */
class DataConnector extends ConnectorBase implements DataConnectorInterface {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getXmlSchema($update_connector_id, $encode = FALSE) {
    $encode = $encode ? 'true' : 'false';
    $arguments['dataID'] = 'GetXmlSchema';
    $arguments['parametersXml'] = '<DataConnector><UpdateConnectorId>' . $update_connector_id . '</UpdateConnectorId><EncodeBase64>' . $encode . '</EncodeBase64></DataConnector>';
    $this->soapSendRequest('Execute', $arguments);
    return $this->getResult();
  }

  /**
   * {@inheritdoc}
   */
  public function getResult() {
    list($result_xml, $last_function) = $this->getResultArguments();
    return new DataConnectorResult($result_xml, $last_function);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectordata.asmx';
  }

}
