<?php

namespace Afas\Tests\Core\Connector;

use Afas\Core\Connector\SubjectConnector;
use Afas\Core\Result\SubjectConnectorResult;

/**
 * @coversDefaultClass \Afas\Core\Connector\SubjectConnector
 * @group AfasCoreConnector
 */
class SubjectConnectorTest extends ConnectorTestBase {

  /**
   * @covers ::getAttachmentInfo
   * @covers ::getResult
   * @covers \Afas\Core\Result\SubjectConnectorResult::asArray
   */
  public function testGetAttachmentInfo() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('SubjectConnector/GetSubjectAttachmentInfoResponse.xml')));

    $connector = new SubjectConnector($this->client, $this->server);
    $result = $connector->getAttachmentInfo(7023);
    $this->assertInstanceOf(SubjectConnectorResult::class, $result);

    $expected = [
      [
        'fileId' => '38D436288D83AFE2A62B407A84E76D98',
        'filename' => 'example.pdf',
      ],
    ];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::getAttachmentInfo
   * @covers ::getResult
   * @covers \Afas\Core\Result\SubjectConnectorResult::asArray
   */
  public function testEmptyGetAttachmentInfo() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('SubjectConnector/GetSubjectAttachmentInfoResponse_empty.xml')));

    $connector = new SubjectConnector($this->client, $this->server);
    $result = $connector->getAttachmentInfo(7023);
    $this->assertInstanceOf(SubjectConnectorResult::class, $result);

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::getAttachment
   */
  public function testGetAttachment() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('SubjectConnector/GetAttachmentResponse.xml')));

    $connector = new SubjectConnector($this->client, $this->server);

    $expected = base64_encode($this->getFileContents('images/image.jpg'));
    $this->assertEquals($expected, $connector->getAttachment(7023, '38D436288D83AFE2A62B407A84E76D98'));
  }

  /**
   * @covers ::getSubjectAttachmentData
   * @covers ::getResult
   * @covers \Afas\Core\Result\SubjectConnectorResult::asArray
   */
  public function testGetSubjectAttachmentData() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('SubjectConnector/GetSubjectAttachmentDataResponse.xml')));

    $connector = new SubjectConnector($this->client, $this->server);
    $result = $connector->getSubjectAttachmentData(7023);
    $this->assertInstanceOf(SubjectConnectorResult::class, $result);

    $expected = [
      [
        'filename' => 'image.jpg',
        'Data' => base64_encode($this->getFileContents('images/image.jpg')),
      ],
    ];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::getSubjectAttachmentData
   * @covers ::getResult
   * @covers \Afas\Core\Result\SubjectConnectorResult::asArray
   */
  public function testEmptyGetSubjectAttachmentData() {
    $this->client->expects($this->once())
      ->method('__getLastResponse')
      ->will($this->returnValue($this->getFileContents('SubjectConnector/GetSubjectAttachmentDataResponse_empty.xml')));

    $connector = new SubjectConnector($this->client, $this->server);
    $result = $connector->getSubjectAttachmentData(7023);
    $this->assertInstanceOf(SubjectConnectorResult::class, $result);

    $expected = [];
    $this->assertEquals($expected, $result->asArray());
  }

  /**
   * @covers ::getLocation
   */
  public function testGetLocation() {
    $this->server->expects($this->once())
      ->method('getBaseUrl')
      ->will($this->returnValue('https://www.example.com'));

    $connector = new SubjectConnector($this->client, $this->server);
    $this->assertEquals('https://www.example.com/appconnectorsubject.asmx', $connector->getLocation());
  }

}
