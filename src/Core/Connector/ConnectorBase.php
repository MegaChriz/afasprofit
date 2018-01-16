<?php

namespace Afas\Core\Connector;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\ServerInterface;
use SoapParam;

/**
 * Base class for Profit connectors.
 */
abstract class ConnectorBase implements ConnectorInterface {

  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------

  /**
   * The Soap client.
   *
   * @var \Afas\Component\Soap\SoapClientInterface
   */
  private $client;

  /**
   * The Afas Server.
   *
   * @var \Afas\Core\ServerInterface
   */
  private $server;

  /**
   * The last called function.
   *
   * @var string
   */
  private $lastFunction;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * Constructs a new ConnectorBase object.
   *
   * @param \Afas\Component\Soap\SoapClientInterface $client
   *   A Soap Client.
   * @param \Afas\Core\ServerInterface $server
   *   An Afas server.
   */
  public function __construct(SoapClientInterface $client, ServerInterface $server) {
    $this->client = $client;
    $this->server = $server;
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getServer() {
    return $this->server;
  }

  /**
   * Returns arguments needed to construct a new \Afas\Core\Result\ResultInterface.
   *
   * @return array
   *   A list of arguments.
   */
  protected function getResultArguments() {
    return [
      $this->client->__getLastResponse(),
      $this->lastFunction,
    ];
  }

  /**
   * Returns the default Soap arguments to send with a Soap call.
   *
   * Subclasses should override this method to supply additional
   * arguments for the soap request.
   *
   * @return array
   *   A list of arguments.
   */
  protected function getSoapArguments() {
    return array(
      'token' => $this->server->getApiKeyAsXML(),
    );
  }

  /**
   * Returns the default Soap options to send with a Soap call.
   *
   * Subclasses should override this method to supply additional
   * options for the soap request.
   *
   * @return array
   *   A list of arguments.
   */
  protected function getSoapOptions() {
    return array(
      'location' => $this->getLocation(),
      'uri' => $this->server->getUri(),
    );
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * Sends a SOAP request.
   *
   * @param string $function
   *   The soap function to call.
   * @param array $arguments
   *   (optional) The Soap arguments to send with the request.
   *   Defaults to the result of getSoapArguments().
   * @param array $options
   *   (optional) An array of options to send with the request.
   *   Defaults to the result of getSoapOptions().
   *
   * @return void
   */
  protected function soapSendRequest($function, array $arguments = array(), array $options = array()) {
    // Set action to call.
    // @todo Evaluate if this is still needed.
    //$this->client->setAction($function);

    // Setup arguments.
    $arguments += $this->getSoapArguments();
    // Convert arguments to Soap parameters.
    // @todo Don't create an instance of SoapParam here?
    $soap_params = array();
    foreach ($arguments as $key => $value) {
      $soap_params[] = new SoapParam($value, $key);
    }

    // Setup options.
    $options += $this->getSoapOptions();
    $options += array(
      'soapaction' => $this->server->getUri() . '/' . $function,
    );

    // Finally, send the request!
    $this->lastFunction = $function;
    $this->client->__soapCall($function, $soap_params, $options);
  }

  // --------------------------------------------------------------
  // TEST
  // --------------------------------------------------------------

  /**
   * Outputs last response of soap client.
   *
   * @todo Remove this method?
   */
  public function outputResponse() {
    header("content-type: text/xml");
    print $this->client->__getLastResponse();
  }

  /**
   * Outputs last request + last response.
   *
   * @return string
   *   The last response and headers.
   */
  public function testResponse() {
    $output = '';
    $output .= "\nDumping request headers:\n" . $this->client->__getLastRequestHeaders();
    $output .= "\nDumping request:\n" . $this->client->__getLastRequest();
    $output .= "\nDumping response headers:\n" . $this->client->__getLastResponseHeaders();
    $output .= "\nDumping response:\n" . $this->client->__getLastResponse();
    return $output;
  }

}
