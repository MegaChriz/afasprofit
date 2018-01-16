# Afas Profit API

An API to connect to Afas Profit written in PHP.

## Usage

```PHP
// Getting data from Profit.
$server = new \Afas\Core\Server('https://12345.afasonlineconnector.nl/profitservices', 'ABCDEFGHIJK1234');
$products = $server->get('Products')
  ->filter('sku', '2612')
  ->execute()
  ->asArray();

// Inserting data into Profit.
@todo
```
