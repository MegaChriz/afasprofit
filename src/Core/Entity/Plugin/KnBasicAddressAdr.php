<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Afas;
use Afas\Core\Entity\Entity;
use UnexpectedValueException;

/**
 * Class for a KnBasicAddressAdr entity.
 */
class KnBasicAddressAdr extends Entity {

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function init() {
    // By default the address is a mailbox address.
    $this->setField('PbAd', FALSE);
    // The city name should by default be resolved by the zip code.
    $this->setField('ResZip', TRUE);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
      case static::FIELDS_UPDATE:
        return [
          // Addresses must have a street, house number and a zip code.
          'Ad',
          'HmNr',
          'ZpCd',
          'CoId',
        ];
    }

    return [];
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setField($key, $value) {
    switch ($key) {
      case 'Ad':
        if (strtolower(trim($value)) == 'postbus') {
          // Special handling for mailbox addresses.
          $this->setField('PbAd', TRUE);
        }
        break;

      case 'CoId':
        /** @var \Afas\Core\Locale\CountryManagerInterface $country_manager */
        $country_manager = Afas::service('afas.country.manager');

        if (is_numeric($value)) {
          // Lookup country.
          $countries = $country_manager->getListNum3toIso2();
          if (!isset($countries[$value])) {
            // Do not allow to set a numeric value for this field.
            throw new UnexpectedValueException(sprintf('No ISO Alpha-2 country code found for country no. %d.', $value));
          }

          $value = $countries[$value];
        }

        if (strlen($value) === 2) {
          $countries = $country_manager->getList();
          if (!isset($countries[$value])) {
            // Country not found.
            throw new UnexpectedValueException(sprintf('No Profit country code found for country code "%s".', $value));
          }

          $value = $countries[$value];
        }
        break;
    }

    return parent::setField($key, $value);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    // When 'ResZip' is set to true, 'Rs' becomes required.
    if ($this->getField('ResZip') && !$this->fieldExists('Rs')) {
      $errors[] = strtr("The field 'Rs' is required in a !type object when the field 'ResZip' is set to true.", [
        '!type' => $this->getType(),
      ]);
    }

    return $errors;
  }

}
