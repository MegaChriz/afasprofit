<?php

/**
 * @file
 * Psuedo-code for using a update-connector.
 */

use Afas\Core\Server;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

// Create AfasServer.
$server = new Server();

// Send a new order to Profit.
$order = [
  'order_id' => 'TEST3_001',
  'afas_dbid' => 102889,
  'contact_id' => 18538,
  'warehouse' => 1,
  'line_items' => [
    [
      'sku' => 2302000,
      'qty' => 1,
    ],
  ],
];
$server->insert('FbSales', $order);

// Send an existing order to Profit, but with a new product.
$order = [
  'order_id' => 'TEST3_001',
  'line_items' => [
    [
      'sku' => 2500,
      'qty' => 2,
    ],
  ],
];
$server->update('FbSales', $order);

// Change qty of a certain product.
$order = [
  'order_id' => 'TEST3_001',
  'line_items' => [
    [
      'id' => '{6BA270E1-BFA7-4B67-86FF-72391A2CB5E3}',
      'qty' => 3,
    ],
  ],
];
$server->update('FbSales', $order);

// Create a new order object.
$order = $entityFactory->create('FbSales');

$order->order_id = 'TEST3_002';
// Make it an "existing" order.
$order->is_new = FALSE;
// Call a generic method to add line item.
$line_item = $order->add('FbSalesLines');
// Or call a specific method for that.
$line_item = $order->addLineItem();
