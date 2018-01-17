# Afas Profit API

[![Build Status](https://travis-ci.org/MegaChriz/afasprofit.svg?branch=3.x)](https://travis-ci.org/MegaChriz/afasprofit)

An API to connect to Afas Profit written in PHP.

Features:
- Getting data from Profit GetConnectors.
- Inserting data into Profit using Profit UpdateConnectors (in progress).

## Usage

Getting data from Profit:
```php
use Afas\Core\Server;

// Initialize server.
$server = new Server('https://12345.afasonlineconnector.nl/profitservices', 'ABCDEFGHIJK1234');

// Get data from GetConnector 'Products', filter by 'sku'.
$products = $server->get('Products')
  ->filter('sku', '2612')
  ->execute()
  ->asArray();
```
