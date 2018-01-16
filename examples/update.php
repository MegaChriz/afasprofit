<?php

/**
 * @file
 * Psuedo-code for using a update-connector.
 */

use Afas\Afas;
use Afas\Core\Server;
use Afas\Core\Soap\DefaultSoapClientFactory;
use Symfony\Component\DependencyInjection\Container;

// Bootstrap.
require_once __DIR__ . '/../includes/bootstrap.php';

// Create dependency container.
// @todo set defaults somewhere.
$container = new Container();
$container->set('afas_soap_client_factory', new DefaultSoapClientFactory());
Afas::setContainer($container);

// Create AfasServer.
$server = new Server();

// Send a new order to Profit.
$order = array(
  'order_id' => 'TEST3_001',
  'afas_dbid' => 102889,
  'contact_id' => 18538,
  'warehouse' => 1,
  'line_items' => array(
    array(
      'sku' => 2302000,
      'qty' => 1,
    ),
  ),
);
$server->insert('FbSales', $order);

// Send an existing order to Profit, but with a new product.
$order = array(
  'order_id' => 'TEST3_001',
  'line_items' => array(
    array(
      'sku' => 2500,
      'qty' => 2,
    ),
  ),
);
$server->update('FbSales', $order);

// Change qty of a certain product.
$order = array(
  'order_id' => 'TEST3_001',
  'line_items' => array(
    array(
      'id' => '{6BA270E1-BFA7-4B67-86FF-72391A2CB5E3}',
      'qty' => 3,
    ),
  ),
);
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
