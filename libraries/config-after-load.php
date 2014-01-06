<?php

/**
 * @file
 * This fixes up any config data.
 *
 * Don't edit this file.
 */

// --------------------------------------------------------------
// SERVERS
// --------------------------------------------------------------

// Make sure that every defined server has a name.
if (count($afas_conf['servers']) > 0) {
  foreach ($afas_conf['servers'] as $key => &$server) {
    $server['name'] = $key;
  }
  // Clear server variable.
  unset($server);

  // Make sure that there is a default server.
  if (!isset($afas_conf['default_server'])) {
    $server = reset($afas_conf['servers']);
    $afas_conf['default_server'] = $server['name'];
  }
}
