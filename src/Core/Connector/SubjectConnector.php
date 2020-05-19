<?php

namespace Afas\Core\Connector;

use Afas\Core\Result\SubjectConnectorResult;

/**
 * Class for the Profit SubjectConnector.
 */
class SubjectConnector extends ConnectorBase implements SubjectConnectorInterface {

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getAttachmentInfo($subject_id) {
    $arguments['subjectID'] = $subject_id;
    $this->soapSendRequest('GetSubjectAttachmentInfo', $arguments);
    return $this->getResult();
  }

  /**
   * {@inheritdoc}
   */
  public function getAttachment($subject_id, $file_id) {
    $arguments['subjectID'] = $subject_id;
    $arguments['fileId'] = $file_id;
    $this->soapSendRequest('GetAttachment', $arguments);
    return $this->getResult()->getRaw();
  }

  /**
   * {@inheritdoc}
   */
  public function getSubjectAttachmentData($subject_id) {
    $arguments['subjectID'] = $subject_id;
    $this->soapSendRequest('GetSubjectAttachmentData', $arguments);
    return $this->getResult();
  }

  /**
   * {@inheritdoc}
   */
  public function getResult() {
    list($result_xml, $last_function) = $this->getResultArguments();
    return new SubjectConnectorResult($result_xml, $last_function);
  }

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->getServer()->getBaseUrl() . '/appconnectorsubject.asmx';
  }

}
