<?php

namespace Afas\Tests\Core\Query;

use Afas\Component\Soap\SoapClientInterface;
use Afas\Core\ServerInterface;
use Afas\Tests\TestBase;

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
    $this->client = $this->getMock(SoapClientInterface::class);
    $this->server = $this->getMock(ServerInterface::class);
  }

}
