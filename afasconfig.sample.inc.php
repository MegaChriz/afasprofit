<?php

/**
 * @file
 * Config file for Afas.
 *
 * Include this file into your application.
 */

/**
 * Specify which servers are used and name them
 * by key.
 *
 * Each server is described this way:
 * @code
 * 'OKERCKAA' => array(
 *   'host' => '192.168.1.1',
 *   'environment' => 'OKERCKAA',
 *   'user' => 'username',
 *   'password' => 'password',
 * );
 * @endcode
 */
$afas_conf['servers'] = array(
  'OKERCKAA' => array(
    'host' => '192.168.2.26',
    'environment' => 'OKERCKAA',
    'user' => 'administrator',
    'password' => 'wingosta',
  ),
  'OKERCKAB' => array(
    'host' => '192.168.2.26',
    'environment' => 'OKERCKAB',
    'user' => 'administrator',
    'password' => 'wingosta',
  ),
);

/**
 * Specify per connector per server which extra fields are used.
 *
 * - Servername (key)
 *   The name of the server used in $conf['afas']['servers'].
 *   This is an array containing:
 *   - Connector name (key)
 *     The name of the connector to specify extra fields for.
 *     This is an array containing:
 *     - fields
 *       An array of extra fields.
 *       Each field may contain the following properties:
 *         - name (key)
 *           REQUIRED. This specifies the name in the update-connector.
 *         - description
 *           (optional) Describes where the field is used for.
 *         - type
 *           (optional) the data type of the field.
 *         - minoccurs
 *           (optional) How many values the field must have at least.
 *         - nillable
 *           (optional) If the field may be NULL.
 *         - environment
 *           An array of the environments this field is valid for.
 * Example:
 * @code
 * $afas_conf['afas'][$servername][$connectorname]['fields'] = array(
 *   'U8A5FF6024907AE96D84B08A298234219' => array(
 *     'description' => 'Database ID field',
 *     'type' => 'int',
 *     'minoccurs' => 0,
 *     'nillable' => TRUE,
 *     'environment' => array('OKERCKAB'),
 *   ),
 * );
 * @endcode
 */
