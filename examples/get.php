<?php

/**
 * @file
 * Examples for using a get-connector.
 */

use Afas\Core\Server;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Example 1: get products from the get-connector 'Products'.
$products = $server->get('Products')
  ->filter('model', '2612')
  ->execute()
  ->asArray();

print '<pre>';
print_r($products);


// Example 2: check if an item is modified since a specific time.
/**
 * Checks if the item has changed since the given timestamp.
 *
 * @param \Afas\Core\ServerInterface
 *   The server to call.
 * @param int $contact_id
 *   The ID of the contact in Profit.
 * @param int $timestamp
 *   The time to check for the last change.
 *
 * @return bool
 *   True if the item had changed. False otherwise.
 */
function hasChangedSince(ServerInterface $server, int $contact_id, int $timestamp): bool {
  $result = $server->get('Contacts')
    ->filter('contact_id', $contact_id)
    ->filter('modified', date('Y-m-d\TH:i:s', $timestamp), '>')
    ->execute()
    ->asArray();

  return !empty($result);
}

// Check if contact 1234 has changed since yesterday.
$has_changed = hasChangedSince($server, 1234, time() - 86400);


// Example 3: more advanced query using filter groups.
// Get the member data of three contacts for event 1234.
$event_id = 1234;
$contact_ids = [
  1203,
  1204,
  1205,
];
$query = $server->get('members');
foreach ($member_ids as $member_id) {
  // Each filter group acts as an "OR", while each filter acts as an "AND".
  $group = $query->group();
  $group->filter('event_id', $event_id)
    ->filter('contact_id', $member_id);
}
$members = $query->execute()
  ->asArray();
