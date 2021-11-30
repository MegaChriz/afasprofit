<?php

namespace Afas\Tests\Core;

use Afas\Core\Server;
use Afas\Core\Query\Get;
use Afas\Core\Query\Insert;
use Afas\Core\Query\Update;
use Afas\Core\Query\Delete;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Core\Server
 * @group AfasCore
 */
class ServerTest extends TestBase {

  /**
   * The server under test.
   *
   * @var \Afas\Core\Server
   */
  private $server;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->server = new Server('https://12345.afasonlineconnector.nl/profitservices', 'ABCDE');
  }

  /**
   * @covers ::get
   */
  public function testGet() {
    $this->assertInstanceOf(Get::class, $this->server->get('Products'));
  }

  /**
   * @covers ::insert
   */
  public function testInsert() {
    $this->assertInstanceOf(Insert::class, $this->server->insert('Dummy', []));
  }

  /**
   * @covers ::insert
   */
  public function testInsertWithData() {
    $insert = $this->server->insert('Dummy', [
      'DbId' => 12345,
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="insert">
          <DbId>12345</DbId>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $insert->getEntityContainer()->compile());
  }

  /**
   * @covers ::insert
   */
  public function testInsertWithAttributes() {
    $insert = $this->server->insert('Dummy', [
      'DbId' => 12345,
      'CdId' => 10001,
      'Foo' => 'Bar',
    ], ['DbId', 'CdId']);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element DbId="12345" CdId="10001">
        <Fields Action="insert">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $insert->getEntityContainer()->compile());
  }

  /**
   * @covers ::update
   */
  public function testUpdate() {
    $this->assertInstanceOf(Update::class, $this->server->update('Dummy', []));
  }

  /**
   * @covers ::update
   */
  public function testUpdateWithData() {
    $update = $this->server->update('Dummy', [
      'DbId' => 12345,
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="update">
          <DbId>12345</DbId>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $update->getEntityContainer()->compile());
  }

  /**
   * @covers ::update
   */
  public function testUpdateWithAttributes() {
    $update = $this->server->update('Dummy', [
      'DbId' => 12345,
      'CdId' => 10001,
      'Foo' => 'Bar',
    ], ['DbId', 'CdId']);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element DbId="12345" CdId="10001">
        <Fields Action="update">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $update->getEntityContainer()->compile());
  }

  /**
   * @covers ::delete
   */
  public function testDelete() {
    $this->assertInstanceOf(Delete::class, $this->server->delete('Dummy', []));
  }

  /**
   * @covers ::delete
   */
  public function testDeleteWithData() {
    $delete = $this->server->delete('Dummy', [
      'DbId' => 12345,
    ]);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element>
        <Fields Action="delete">
          <DbId>12345</DbId>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $delete->getEntityContainer()->compile());
  }

  /**
   * @covers ::delete
   */
  public function testDeleteWithAttributes() {
    $delete = $this->server->delete('Dummy', [
      'DbId' => 12345,
      'CdId' => 10001,
      'Foo' => 'Bar',
    ], ['DbId', 'CdId']);

    $expected = '<Dummy xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
      <Element DbId="12345" CdId="10001">
        <Fields Action="delete">
          <Foo>Bar</Foo>
        </Fields>
      </Element>
    </Dummy>';
    $this->assertXmlStringEqualsXmlString($expected, $delete->getEntityContainer()->compile());
  }

  /**
   * @covers ::getBaseUrl
   * @covers ::__construct
   */
  public function testGetBaseUrl() {
    $this->assertEquals('https://12345.afasonlineconnector.nl/profitservices', $this->server->getBaseUrl());
  }

  /**
   * @covers ::getUri
   * @covers ::__construct
   */
  public function testGetUri() {
    $this->assertEquals('urn:Afas.Profit.Services', $this->server->getUri());
  }

  /**
   * @covers ::getApiKey
   * @covers ::__construct
   */
  public function testGetApiKey() {
    $this->assertEquals('ABCDE', $this->server->getApiKey());
  }

  /**
   * @covers ::getApiKeyAsXml
   * @covers ::__construct
   */
  public function testGetApiKeyAsXml() {
    $this->assertXmlStringEqualsXmlString('<token><version>1</version><data>ABCDE</data></token>', $this->server->getApiKeyAsXml());
  }

}
