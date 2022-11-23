<?php

/**
 * @file
 * Examples for sending data to Profit by creating objects first.
 *
 * This can be useful when you want to pass these objects around in your
 * application.
 * An example is when you create a class in your application that is capable of
 * retrieving data from Profit, but also for sending it. Before sending data to
 * Profit you may want to check via a getconnector call if the data you are
 * about to send already exist and based on that decide whether you pass the
 * object to an insert or to an update query.
 */

use Afas\Afas;
use Afas\Core\Server;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Create a FbSales object.
/** @var \Afas\Core\Entity\Plugin\FbSales $order */
$order = Afas::service('afas.entity.manager')->createInstance('FbSales');

// Set fields with making use of aliases.
// @see \Afas\Core\Mapping\DefaultMapping
$order->setField('order_id', 'TEST3_002');
$order->setField('customer_id', 12345);

// Call a generic method to add a line item to the order.
/** @var \Afas\Core\Entity\Plugin\FbSalesLines $line_item1 */
$line_item1 = $order->add('FbSalesLines');

// Or call a specific method for that.
/** @var \Afas\Core\Entity\Plugin\FbSalesLines $line_item2 */
$line_item2 = $order->addLineItem();

// Now send this order.
/** @var \Afas\Core\Query\InsertInterface $query */
$query = $server->insert('FbSales', []);
$query->getEntityContainer()
  ->addObject($order);
$query->execute();
