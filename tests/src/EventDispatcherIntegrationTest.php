<?php

namespace Afas\Tests;

use Afas\Afas;
use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\Connector\GetConnector;
use Afas\Core\Connector\GetConnectorInterface;
use Afas\Core\Connector\UpdateConnector;
use Afas\Core\Connector\UpdateConnectorInterface;
use Afas\Core\Event\AfasEvents;
use Afas\Core\Event\SendRequestEvent;
use Afas\Core\ServerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Tests integration with the event the subscriber.
 *
 * @group Afas
 */
class EventDispatcherIntegrationTest extends TestBase {

  /**
   * The soap client.
   *
   * @var \Afas\Component\Soap\SoapClientInterface
   */
  protected $client;

  /**
   * The profit server.
   *
   * @var \Afas\Core\ServerInterface
   */
  protected $server;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->client = $this->createMock(SoapClientInterface::class);
    $this->server = $this->createMock(ServerInterface::class);
  }

  /**
   * Tests if an event gets dispatched on a get query.
   */
  public function testGet(): void {
    // Add a subscriber.
    Afas::addSubscriberService('afas.test.subscriber', EventSubscriber::class);

    $connector = new GetConnector($this->client, $this->server);
    $result = $connector->getData('alpha');

    // Assert that an event got dispatched with the expected values.
    /** @var \Afas\Core\Event\SendRequestEvent $event */
    $event = Afas::service('afas.test.subscriber')->getEvent();
    $this->assertInstanceof(SendRequestEvent::class, $event);
    $this->assertInstanceof(GetConnectorInterface::class, $event->getConnector());
    $expected_args = [
      'connectorId' => 'alpha',
      'options' => '<options><Outputmode>1</Outputmode><Metadata>1</Metadata><Outputoptions>2</Outputoptions></options>',
      'token' => '',
      'skip' => -1,
      'take' => -1
    ];
    $this->assertEquals($expected_args, $event->getArguments());
  }

  /**
   * Tests if an event gets dispatched on an update query.
   */
  public function testUpdate(): void {
    // Add a subscriber.
    Afas::addSubscriberService('afas.test.subscriber', EventSubscriber::class);

    $connector = new UpdateConnector($this->client, $this->server, 'FbSales');
    $result = $connector->execute();

    // Assert that an event got dispatched with the expected values.
    /** @var \Afas\Core\Event\SendRequestEvent $event */
    $event = Afas::service('afas.test.subscriber')->getEvent();
    $this->assertInstanceof(SendRequestEvent::class, $event);
    $this->assertInstanceof(UpdateConnectorInterface::class, $event->getConnector());
    $expected_args = [
      'connectorType' => 'FbSales',
      'connectorVersion' => 1,
      'token' => '',
    ];
    $this->assertEquals($expected_args, $event->getArguments());
  }

}

/**
 * Subscriber service.
 */
class EventSubscriber implements EventSubscriberInterface {

  /**
   * The event that got dispatched.
   *
   * @var \Afas\Core\Event\SendRequestEvent|null
   */
  protected $event;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];

    $events[AfasEvents::SEND_REQUEST][] = 'onSendRequest';

    return $events;
  }

  /**
   * Acts on a request being send to Profit.
   *
   * @param \Afas\Core\Event\SendRequestEvent $event
   *   The event that was dispatched.
   */
  public function onSendRequest(SendRequestEvent $event) {
    $this->event = $event;
  }

  /**
   * Returns the arguments that were on the event.
   *
   * @return \Afas\Core\Event\SendRequestEvent|null
   *   The event that was dispatched. Null if no event was dispatched.
   */
  public function getEvent(): ?SendRequestEvent {
    return $this->event;
  }

}
