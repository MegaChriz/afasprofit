<?php

namespace Afas\Tests\Core\Query;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\ServerInterface;
use Afas\Core\Query\QueryInterface;
use Afas\Tests\TestBase;
use InvalidArgumentException;

/**
 * Base class for query tests.
 */
abstract class QueryTestBase extends TestBase {

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
   * The query object under test.
   *
   * @var \Afas\Core\Query\QueryInterface
   */
  protected $query;

  /**
   * Setups required dependencies.
   */
  public function setUp() {
    parent::setUp();
    $this->client = $this->createMock(SoapClientInterface::class);
    $this->server = $this->createMock(ServerInterface::class);
  }

  /**
   * Creates a mocked query object.
   *
   * @param string $class
   *   The query class to create an object for.
   * @param array $params
   *   (optional) The constructor arguments.
   *
   * @return \Afas\Core\Query\QueryInterface
   *   The created query.
   *
   * @throws \InvalidArgumentException
   *   In case the class does not implement QueryInterface.
   */
  protected function createQuery($class, array $params = []) {
    $interfaces = class_implements($class);
    if (!isset($interfaces[QueryInterface::class])) {
      throw InvalidArgumentException('Given class does not implement \Afas\Core\Query\QueryInterface.');
    }

    // Default params.
    $params += [
      0 => $this->server,
      1 => 'Dummy',
    ];
    ksort($params);

    $query = $this->getMockBuilder($class)
      ->setConstructorArgs($params)
      ->setMethods(['getClient'])
      ->getMock();

    $query->expects($this->any())
      ->method('getClient')
      ->will($this->returnValue($this->client));

    return $query;
  }

}
