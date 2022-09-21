<?php

namespace Afas\Core\Connector;

use Afas\Afas;
use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\Event\AfasEvents;
use Afas\Core\Event\SendRequestEvent;
use Afas\Core\ServerInterface;
use SoapParam;
use SoapVar;

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
   * Returns arguments needed to construct a new result.
   *
   * The result implements \Afas\Core\Result\ResultInterface.
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
    return [
      'token' => $this->server->getApiKeyAsXML(),
    ];
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
    return [
      'location' => $this->getLocation(),
      'uri' => $this->server->getUri(),
    ];
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
   */
  protected function soapSendRequest($function, array $arguments = [], array $options = []) {
    // Setup arguments.
    $arguments += $this->getSoapArguments();
    // Convert arguments to namespaced Soap variables.
    $params = [];
    foreach ($arguments as $key => $value) {
      $params[] = new SoapVar($value, XSD_STRING, null, null, $key, $this->server->getUri());
    }

    // Setup options.
    $options += $this->getSoapOptions();
    $options += [
      'soapaction' => $this->server->getUri() . '/' . $function,
    ];

    // Dispatch event.
    if (Afas::hasEventDispatcher()) {
      $event = new SendRequestEvent($this, $function, $arguments, $options);
      Afas::service('event_dispatcher')->dispatch($event, AfasEvents::SEND_REQUEST);
    }

    // Wrap soap variables to ensure they are properly namespaced.
    $function_wrapper = new SoapVar($params, SOAP_ENC_OBJECT, null, null, $function, $this->server->getUri());
    $soap_params = new SoapParam($function_wrapper, $function);

    // Finally, send the request!
    $this->lastFunction = $function;
    $this->client->__soapCall($function, [$soap_params], $options);
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
