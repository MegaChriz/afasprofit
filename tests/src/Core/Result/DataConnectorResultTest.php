<?php

namespace Afas\Tests\Core\Result;

use Afas\Core\Result\DataConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Result\DataConnectorResult
 * @group AfasCoreResult
 */
class DataConnectorResultTest extends ResultTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->setConnectorResult(new DataConnectorResult($this->getFileContents('DataConnector/ExecuteResponse.xml'), 'Execute'));
  }

}
