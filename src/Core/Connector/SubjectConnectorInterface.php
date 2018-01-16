<?php

namespace Afas\Core\Connector;

/**
 * Interface for the Profit SubjectConnector.
 */
interface SubjectConnectorInterface extends ConnectorInterface {

  /**
   * Retrieves info about file names and GUID.
   *
   * @param int $subject_id
   *   The unique identifier of the subject item.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result of the call.
   */
  public function getAttachmentInfo($subject_id);

  /**
   * Retrieves a single file.
   *
   * @param int $subject_id
   *   The unique identifier of the subject item.
   * @param string $file_id
   *   The ID of the file as known in Profit.
   *
   * @return string
   *   The base64-encoded file.
   */
  public function getAttachment($subject_id, $file_id);

  /**
   * Retrieves a collection of file data, together with their file names.
   *
   * @param int $subject_id
   *   The unique identifier of the subject item.
   *
   * @return \Afas\Core\Result\ResultInterface
   *   The result of the call.
   */
  public function getSubjectAttachmentData($subject_id);

}
