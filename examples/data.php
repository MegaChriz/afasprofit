<?php

/**
 * @file
 * Code for updating XML schemes for update connectors.
 */

use Afas\Afas;
use Afas\Core\Connector\DataConnector;
use Afas\Core\Server;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize server connection.
$server = new Server('https://12345.afasonlineconnector.nl/profitservices', 'ABCDEFGHIJK1234');

$client = Afas::service('afas.soap_client_factory')->create($server);

$connector = new DataConnector($client, $server);

try {
  $connectors = [
    'FbDirectInvoice',
    'FbGoodsReceived',
    'FbPurch',
    'FbSales',
    'FbSubscription',
    'FiEntries',
    'KnContact',
    'KnCourseMember',
    'KnOrganisation',
    'KnProvApplication',
    'KnSalesRelationOrg',
    'KnSalesRelationPer',
    'KnSubject',
    'KnSubjectWorkflowReaction',
    'PtProject',
    'PtRealization',
  ];

  // For each connector, create a XML scheme *without* the custom fields.
  foreach ($connectors as $id) {
    $result = $connector->getXmlSchema($id);
    $schema = $result->removeCustomFieldsFromSchema()[0]['Schema'];
    file_put_contents($root . '/resources/XMLSchema/' . $id . '.xsd', $schema);
  }
}
catch (Exception $e) {
  var_dump($e);die();
}
