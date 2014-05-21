<?php

/**
 * @file
 * Tests if the Afas API works.
 */

// Afas php.
require_once('../afas.php');

// Initialize Afas environment.
afas_initialize();

// TEST code.
try {
  $oServer = AfasServer::get();
  $oConnector = $oServer->getConnector();
  $oConnector->sendRequest('GetData', 'KKB_Functie');
  $oConnector->outputDataResult();
  exit();
}
catch (Exception $e) {
  print '<pre>';
  throw $e;
}
