<?php

namespace Afas\Tests\Core\Result;

use Afas\Core\Result\UpdateConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Result\UpdateConnectorResult
 * @group AfasCoreResult
 */
class UpdateConnectorResultTest extends ResultTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->setConnectorResult(new UpdateConnectorResult($this->getFileContents('UpdateConnector/ExecuteResponse.xml'), 'Execute'));
  }

  /**
   * @covers ::asArray
   */
  public function testAsArray() {
    $expected = [
      [
        'DbId' => 100001,
      ],
    ];
    $this->assertEquals($expected, $this->getConnectorResult()->asArray());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArrayWithMultipleRows() {
    $result = new UpdateConnectorResult($this->getFileContents('UpdateConnector/ExecuteResponse_multiple.xml'), 'Execute');

    $expected = [
      [
        'DbId' => 100002,
      ],
      [
        'DbId' => 100003,
      ],
    ];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArrayWithEmptyResult() {
    $result = new UpdateConnectorResult($this->getFileContents('UpdateConnector/ExecuteResponse_empty.xml'), 'Execute');

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

}
