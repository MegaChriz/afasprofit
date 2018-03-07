<?php

namespace Afas\Tests\Core\Result;

use Afas\Core\Result\ResultInterface;
use Afas\Tests\TestBase;

/**
 * Base class for result tests.
 */
abstract class ResultTestBase extends TestBase {

  /**
   * The result class under test.
   *
   * @var \Afas\Core\Result\ResultInterface
   */
  private $result;

  /**
   * Set the result.
   *
   * @param \Afas\Core\Result\ResultInterface $result
   *   The result from a Profit connector call.
   */
  protected function setConnectorResult(ResultInterface $result) {
    $this->result = $result;
  }

  /**
   * Returns the result.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result from a Profit connector call.
   */
  protected function getConnectorResult() {
    return $this->result;
  }

  /**
   * @covers ::getRaw
   */
  public function testGetRaw() {
    $this->assertInternalType('string', $this->result->getRaw());
  }

  /**
   * @covers ::asXml
   */
  public function testAsXml() {
    $this->assertInternalType('string', $this->result->asXml());
  }

  /**
   * @covers ::getHeaders
   */
  public function testGetHeaders() {
    $this->assertInternalType('array', $this->result->getHeaders());
  }

  /**
   * @covers ::asArray
   */
  public function testAsArray() {
    $this->assertInternalType('array', $this->result->asArray());
  }

}
