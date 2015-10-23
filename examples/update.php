<?php

/**
 * @file
 * Psuedo-code for using a get-connector.
 */

use Afas\Afas;
use Afas\Core\Server;
use Afas\Core\Soap\DefaultSoapClientFactory;
use Afas\Core\Entity\FbSales;
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

// Send orders to Profit via order object.
$order = new FbSales();
$order->Re = 'reaction';
$order->addLine();
$order->save();
