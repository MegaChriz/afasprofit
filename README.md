# Afas Profit API

[![Build Status](https://travis-ci.com/MegaChriz/afasprofit.svg?branch=3.x)](https://travis-ci.com/github/MegaChriz/afasprofit)

An API to connect to Afas Profit written in PHP.

Features:
- Getting data from Profit GetConnectors.
- Insert, update or delete data into Profit using Profit UpdateConnectors.
- Getting files from Profit using Profit Subjectconnectors.
- Getting UpdateConnectors XML Schema's using the Profit DataConnector.

## Usage

Get data from Profit (GetConnector):
```php
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Get data from GetConnector 'Products', filter by 'sku'.
$products = $server->get('Products')
  ->filter('sku', '2612')
  ->execute()
  ->asArray();
```

Insert data into Profit (UpdateConnector):
```php
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Insert an order into Profit.
$server->insert('FbSales', [
  'OrNu' => 'test_001',
  'DbId' => 123456,
  'War' => 1,
  'FbSalesLines' => [
    [
      'ItCd' => 1201,
      'QuUn' => 1,
    ],
    [
      'ItCd' => 1202,
      'QuUn' => 1,
    ],
  ],
])
  ->execute();
```

Update data into Profit (UpdateConnector):
```php
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Update an existing order, change quantity of existing item.
$server->update('FbSales', [
  'OrNu' => 'test_001',
  'FbSalesLines' => [
    [
      'GuLi' => '{6BA270E1-BFA7-4B67-86FF-72391A2CB5E3}',
      'QuUn' => 2,
    ],
  ],
])
  ->execute();
```

Delete data from Profit (UpdateConnector):
```php
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

$server->delete('FbSales', [
  'OrNu' => 'test_001',
])
  ->execute();
```

Get a file from Profit (SubjectConnector):
```php
use Afas\Afas;
use Afas\Core\Connector\SubjectConnector;
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Create Soap client.
/** @var \Afas\Component\Soap\SoapClientInterface $client */
$client = Afas::service('afas.soap_client_factory')->create($server);

// Create SubjectConnector.
$connector = new SubjectConnector($client, $server);

// To get an attachment, pass a subject ID and a file ID.
$attachment = $connector->getAttachment(300001, '8D36870843A516C2514D74BE69F87E15');
```

Get a XML Schema for a Profit UpdateConnector from Profit (DataConnector):
```php
use Afas\Afas;
use Afas\Core\Connector\DataConnector;
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Create Soap client.
/** @var \Afas\Component\Soap\SoapClientInterface $client */
$client = Afas::service('afas.soap_client_factory')->create($server);

// Create DataConnector.
$connector = new DataConnector($client, $server);

// Get schema.
/** @var \Afas\Core\Result\DataConnectorResult $result */
$result = $connector->getXmlSchema('FbSales');

// Save complete schema.
file_put_contents('FbSales.xsd', $result->asArray()[0]['Schema']);

// Or save without custom fields.
file_put_contents('FbSales.xsd', $result->removeCustomFieldsFromSchema()[0]['Schema']);
```
