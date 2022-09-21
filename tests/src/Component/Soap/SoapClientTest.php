<?php

namespace Afas\Tests\Component\Soap;

use Afas\Component\Soap\SoapClient;
use Afas\Tests\TestBase;

/**
 * @coversDefaultClass \Afas\Component\Soap\SoapClient
 * @group AfasComponentUtility
 */
class SoapClientTest extends TestBase {

  /**
   * Tests that a SoapClient object can be created.
   *
   * In PHP 8, PHP's built in SoapClient got return types.
   */
  public function testConstruct() {
    $client = new SoapClient(NULL, [
      'location' => '',
      'uri' => 'https://www.example.com/profitservices',
      'trace' => 1,
      'style' => SOAP_DOCUMENT,
      'use' => SOAP_LITERAL,
    ]);
    $this->assertInstanceof(SoapClient::class, $client);
  }

}
