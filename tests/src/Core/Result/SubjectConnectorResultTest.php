<?php

namespace Afas\Tests\Core\Result;

use Afas\Core\Result\SubjectConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Result\SubjectConnectorResult
 * @group AfasCoreResult
 */
class SubjectConnectorResultTest extends ResultTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->setConnectorResult(new SubjectConnectorResult($this->getFileContents('SubjectConnector/GetSubjectAttachmentDataResponse.xml'), 'GetSubjectAttachmentData'));
  }

}
