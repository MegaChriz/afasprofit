<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;
use InvalidArgumentException;

/**
 * Class for a KnOrganisation entity.
 */
class KnOrganisation extends Relation {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Values for 'MatchOga'.
   *
   * @var string
   */
  const MATCH_BCCO           = 0;
  const MATCH_KVK            = 1;
  const MATCH_FISC           = 2;
  const MATCH_NAME           = 3;
  const MATCH_ADDRESS        = 4;
  const MATCH_POSTAL_ADDRESS = 5;
  const MATCH_NEW            = 6;

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->setField('AutoNum', TRUE);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function isValidChild(EntityInterface $entity) {
    switch ($entity->getType()) {
      case 'KnBankAccount':
      case 'KnContact':
        return TRUE;
    }

    return parent::isValidChild($entity);
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setField($key, $value) {
    switch ($key) {
      case 'MatchOga':
        switch ($value) {
          case static::MATCH_BCCO:
          case static::MATCH_KVK:
          case static::MATCH_FISC:
          case static::MATCH_NAME:
          case static::MATCH_ADDRESS:
          case static::MATCH_POSTAL_ADDRESS:
          case static::MATCH_NEW:
            break;

          default:
            throw new InvalidArgumentException(strtr('Invalid value for MatchOga: !value', [
              '!value' => @(string) $value,
            ]));
        }
    }

    return parent::setField($key, $value);
  }

  /**
   * Adds a contact to the organisation.
   *
   * @param array $values
   *   (optional) The values to fill the new contact with.
   *
   * @return \Afas\Core\Entity\EntityInterface
   *   The created contact.
   */
  public function addContact(array $values = []) {
    return $this->add('KnContact', $values);
  }

  // --------------------------------------------------------------
  // ACTION
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function validate() {
    $errors = parent::validate();

    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
        // When inserting, ensure that there is an address.
        if (!$this->hasObjectType('KnBasicAddressAdr')) {
          $errors[] = 'An object of type KnOrganisation does not contain a KnBasicAddressAdr object.';
        }

        // Don't allow 'BcCo' to be set when inserting a company.
        if ($this->getField('BcCo')) {
          $this->removeField('BcCo');
        }
        break;
    }

    if ($this->getAction() == static::FIELDS_INSERT && !$this->fieldExists('MatchOga')) {
      // When inserting, insert as new if no match method was specified.
      $this->setField('MatchOga', static::MATCH_NEW);
    }
    elseif ($this->getField('BcCo')) {
      // If a organisation ID is given, then match on this field.
      $this->setField('MatchOga', static::MATCH_BCCO);
      $this->setField('AutoNum', FALSE);
    }
    elseif ($this->getField('CcNr')) {
      // If an organisation's KVK is given, then match on this field.
      $this->setField('MatchOga', static::MATCH_KVK);
      $this->setField('AutoNum', FALSE);
    }
    elseif ($this->getField('FiNr')) {
      // If an organisation's FISC is given, then match on this field.
      $this->setField('MatchOga', static::MATCH_FISC);
      $this->setField('AutoNum', FALSE);
    }

    return $errors;
  }

}
