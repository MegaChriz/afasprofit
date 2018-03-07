<?php

namespace Afas\Tests\Core\Result;

use Afas\Core\Result\GetConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Result\GetConnectorResult
 * @group AfasCoreResult
 */
class GetConnectorResultTest extends ResultTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->setConnectorResult(new GetConnectorResult($this->getFileContents('GetConnector/GetDataWithOptionsResponse.xml'), 'GetDataWithOptions'));
  }

  /**
   * @covers ::getHeaders
   */
  public function testGetHeaders() {
    $expected = [
      'sku',
      'title',
      'price',
    ];
    $this->assertEquals($expected, $this->getConnectorResult()->getHeaders());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArray() {
    $expected = [
      [
        'sku' => 'product1',
        'title' => 'Product 1',
        'price' => '16.00',
      ],
    ];
    $this->assertEquals($expected, $this->getConnectorResult()->asArray());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArrayWithMultipleRows() {
    $result = new GetConnectorResult($this->getFileContents('GetConnector/GetDataWithOptionsResponse_multiple.xml'), 'GetDataWithOptions');

    $expected = [
      [
        'sku' => 'product1',
        'title' => 'Product 1',
        'price' => '16.00',
      ],
      [
        'sku' => 'product2',
        'title' => 'Product 2',
        'price' => '62.00',
      ],
    ];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArrayWithEmptyResult() {
    $result = new GetConnectorResult($this->getFileContents('GetConnector/GetDataWithOptionsResponse_empty.xml'), 'GetDataWithOptions');

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArrayWithEmptyDocument() {
    $result = new GetConnectorResult($this->getFileContents('GetConnector/GetDataWithOptionsResponse_empty2.xml'), 'GetDataWithOptions');

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

}
