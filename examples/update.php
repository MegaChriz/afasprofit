<?php

/**
 * @file
 * Psuedo-code for using a update-connector.
 */

use Afas\Afas;
use Afas\Core\Server;

// Bootstrap.
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize server.
$server = new Server('https://12345.soap.afas.online/profitservices', 'ABCDEFGHIJK1234');

// Example 1: Send a new order to Profit.
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
$server->insert('FbSales', $order)
  ->execute();


// Example 2: Send an existing order to Profit, but with a new product.
$order = [
  'order_id' => 'TEST3_001',
  'line_items' => [
    [
      'sku' => 2500,
      'qty' => 2,
    ],
  ],
];
$server->update('FbSales', $order)
  ->execute();


// Example 3: Change qty of a certain product.
$order = [
  'order_id' => 'TEST3_001',
  'line_items' => [
    [
      'id' => '{6BA270E1-BFA7-4B67-86FF-72391A2CB5E3}',
      'qty' => 3,
    ],
  ],
];
$server->update('FbSales', $order)
  ->execute();


// Example 4: example using attributes.
$data = [
  'id' => 1234,
  'name' => 'Foo',
];
// 'id' is set as attribute.
$query = $server->update('Foo', $data, ['id']);

// Check how the XML looks like.
$xml = $query->getEntityContainer()
  ->compile();

// The XML will look something like this:
// @code
// <Foo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
//   <Element id="1234">
//     <Fields Action="update">
//       <name>Foo</name>
//     </Fields>
//   </Element>
// </Foo>


// Example 5: update existing sales relation using attributes.
$relation = [
  'DbId' => 12345,
  'KnOrganisation' => [
    'Nm' => 'Example BV',
    'EmAd' => 'example-bv@example.com',
    'BcCo' => 23899,
  ],
];
// 'DbId' is set as attribute.
$query = $server->update('KnSalesRelationOrg', $relation, ['DbId']);

// Check how the XML looks like.
$xml = $query->getEntityContainer()
  ->compile();

// The XML will look something like the following. There is much more data
// because for this connector there are a lot of default values set.
// @code
// <KnSalesRelationOrg xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
//   <Element DbId="12345">
//     <Fields Action="update">
//       <CuId>EUR</CuId>
//       <PfId>*****</PfId>
//       <VaDu>1</VaDu>
//       <PrLi>0</PrLi>
//       <PrFc>0</PrFc>
//       <ClPc>0</ClPc>
//       <PrPt>0</PrPt>
//       <Krli>0</Krli>
//     </Fields>
//     <Objects>
//       <KnOrganisation>
//         <Element>
//           <Fields Action="update">
//             <Nm>Example BV</Nm>
//             <EmAd>example-bv@example.com</EmAd>
//             <BcCo>23899</BcCo>
//             <MatchOga>0</MatchOga>
//           </Fields>
//         </Element>
//       </KnOrganisation>
//     </Objects>
//   </Element>
// </KnSalesRelationOrg>
// @endcode

// And send the data to Profit.
$query->execute();
