<?php

namespace Afas\Core\Event;

use Afas\Core\Connector\ConnectorInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Fired when a request is about to be send to Profit.
 */
class SendRequestEvent extends Event {

  /**
   * The connector sending the request.
   *
   * @var \Afas\Core\Connector\ConnectorInterface
   */
  protected $connector;

  /**
   * The soap function that will be called.
   *
   * @var string
   */
  protected $function;

  /**
   * The soap arguments to send with the request.
   *
   * @var array
   */
  protected $arguments;

  /**
   * An array of options to send with the request.
   *
   * @var array
   */
  protected $options;

  /**
   * Constructs an SendRequestEvent object.
   *
   * @param \Afas\Core\Connector\ConnectorInterface $connector
   *   The connector.
   * @param string $function
   *   The soap function that will be called.
   * @param array $arguments
   *   The soap arguments to send with the request.
   *   Defaults to the result of getSoapArguments().
   * @param array $options
   *   An array of options to send with the request.
   *   Defaults to the result of getSoapOptions().
   */
  public function __construct(ConnectorInterface $connector, $function, array $arguments, array $options) {
    $this->connector = $connector;
    $this->function = $function;
    $this->arguments = $arguments;
    $this->options = $options;
  }

  /**
   * Returns the connector that is sending the request.
   *
   * @return \Afas\Core\Connector\ConnectorInterface
   *   The connector.
   */
  public function getConnector() {
    return $this->connector;
  }

  /**
   * Returns the soap function that will be called.
   *
   * @return string
   *   The soap function.
   */
  public function getFunction() {
    return $this->function;
  }

  /**
   * The soap arguments to send with the request.
   *
   * @return array
   *   The request arguments.
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * Returns the options to send with the request.
   *
   * @return array
   *   The request options.
   */
  public function getOptions() {
    return $this->options;
  }

}
