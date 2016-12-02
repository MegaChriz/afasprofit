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
 * 'O12345AA' => array(
 *   'name' => 'O12345AA',
 *   'host' => 'https://12345.afasonlineconnector.nl/profitservices',
 *   'token' => 'iYAiHnM80KDJXaUEgtthJKVl9H13ZF55LlbZrCACZ1nrxfQYYKih0jQ1Nvr3z0Hr',
 * )
 * @endcode
 */
$afas_conf['servers'] = array(
  'O12345AA' => array(
    'name' => 'O12345AA',
    'host' => 'https://12345.afasonlineconnector.nl/profitservices',
    'token' => 'iYAiHnM80KDJXaUEgtthJKVl9H13ZF55LlbZrCACZ1nrxfQYYKih0jQ1Nvr3z0Hr',
  ),
  'O12345AB' => array(
    'name' => 'O12345AB',
    'host' => 'https://12345.afasonlineconnector.nl/profitservices',
    'token' => 'cQzwPXwOa44qvQK5rFougrx7VjtRJwjxXTfM0hsn4ZSwxHD5RKssm1TYEfFG28wA',
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
