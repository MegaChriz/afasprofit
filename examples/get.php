<?php

/**
 * @file
 * Psuedo-code for using a get-connector.
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

$products = $server->get('KKB_Studiedagen')
  ->filter('model', '2612')
  ->execute()
  ->asArray();

print '<pre>';
print_r($products);
