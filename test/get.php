<?php

/**
 * @file
 * Psuedo-code for using a get-connector.
 */

use Afas\Core\Connector\GetConnector;
use Afas\Core\Server;
use Afas\Component\Soap\NTLM_SoapClient;

// Bootstrap.
require_once __DIR__ . '/../includes/bootstrap.php';

// Create AfasServer.
$server = new Server();

// Create Soap client.
$options = array(
  'location' => '',
  'uri' => '',
  'trace' => 1,
  'style' => SOAP_RPC,
  'use' => SOAP_ENCODED,
  'login' => 'name',
  'password' => 'pass',
);
$client = new NTLM_SoapClient(NULL, $options);

// Create connector.
$connector = new GetConnector($client, $server);
$connector->sendRequest('GetData', 'KKB_Accreditatie');
$result_xml = $connector->getResult()->asXML();
$result_array = $connector->getResult()->asArray();