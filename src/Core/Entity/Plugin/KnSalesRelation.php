<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;

/**
 * Base class for sales relations.
 */
abstract class KnSalesRelation extends Entity {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function init() {
    // By default the relation is a customer.
    $this->setField('IsDb', TRUE);
    // The default currency is euro.
    $this->setField('CuId', 'EUR');
    // Set relation profile to default administration.
    $this->setField('PfId', '*****');
    // VAT duty is required when relation is a customer.
    $this->setField('VaDu', '1');
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [
      'IsDb',
      'CuId',
      'PfId',
    ];
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = [];
    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
        // No DbId should be set when inserting. Delete it if set.
        $this->removeAttribute('DbId');
        break;

      case static::FIELDS_UPDATE:
      case static::FIELDS_DELETE:
        // When updating or deleting a relation, customer ID is required.
        if (!$this->getAttribute('DbId')) {
          $errors[] = strtr('Attribute DbId is not set for !entity.', [
            '!entity' => $this->getEntityType(),
          ]);
        }
        // When updating, do not update IsDb and PaCd fields.
        $this->removeField('IsDb');
        $this->removeField('PaCd');
        break;
    }

    // When field 'IsDb' == TRUE, fields 'PaCd' and 'VaDu' are required.
    if ($this->getField('IsDb')) {
      // Make sure that fields PaCd and VaDu have values.
      $reqs = [
        'PaCd',
        'VaDu',
      ];
      foreach ($reqs as $req) {
        if (!$this->getField($req)) {
          $errors[] = strtr('!field is a required field when IsDb is "true".', [
            '!field' => $req,
          ]);
        }
      }
    }

    return $errors;
  }

}
