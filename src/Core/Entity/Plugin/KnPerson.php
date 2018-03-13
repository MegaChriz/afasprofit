<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\EntityInterface;
use Afas\Core\Exception\UndefinedParentException;
use InvalidArgumentException;

/**
 * Class for a KnPerson entity.
 */
class KnPerson extends Relation {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Values for 'MatchPer'.
   *
   * @var string
   */
  const MATCH_BCCO                 = 0;
  const MATCH_BSN                  = 1;
  const MATCH_NAME_GENDER          = 2;
  const MATCH_NAME_GENDER_EMAIL    = 3;
  const MATCH_NAME_GENDER_MOBILE   = 4;
  const MATCH_NAME_GENDER_PHONE    = 5;
  const MATCH_NAME_GENDER_BIRTHDAY = 6;
  const MATCH_NEW                  = 7;

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
      case 'MatchPer':
        switch ($value) {
          case static::MATCH_BCCO:
          case static::MATCH_BSN:
          case static::MATCH_NAME_GENDER:
          case static::MATCH_NAME_GENDER_EMAIL:
          case static::MATCH_NAME_GENDER_MOBILE:
          case static::MATCH_NAME_GENDER_PHONE:
          case static::MATCH_NAME_GENDER_BIRTHDAY:
          case static::MATCH_NEW:
            break;

          default:
            throw new InvalidArgumentException(strtr('Invalid value for MatchPer: !value', [
              '!value' => @(string) $value,
            ]));
        }
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

    switch ($this->getAction()) {
      case static::FIELDS_INSERT:
        // Either first name or initials are required.
        if (!$this->getField('In') && !$this->getField('FiNm')) {
          $errors[] = 'The person must either have intials (In) or a first name (FiNm).';
        }
        if (!$this->getField('LaNm')) {
          $errors[] = 'When inserting a new person, their last name (LaNm) is required.';
        }

        try {
          // If this person is part of a KnSalesRelationPer, having an address
          // is required.
          if ($this->getParent()->getType() == 'KnSalesRelationPer' && !$this->hasObjectType('KnBasicAddressAdr')) {
            $errors[] = 'An object of type KnPerson does not contain a KnBasicAddressAdr object.';
          }
        }
        catch (UndefinedParentException $e) {
          // Ignore exceptions on which the parent is not set.
        }

        // Don't allow 'BcCo' to be set when inserting a person.
        if ($this->getField('BcCo')) {
          $this->removeField('BcCo');
        }

        // Set default value for birth name if not set.
        if (!$this->getField('SpNm')) {
          $this->setField('SpNm', FALSE);
        }

        // Set default gender if not set.
        if (!$this->getField('ViGe')) {
          $this->setField('ViGe', 'O');
        }
        break;

      case static::FIELDS_UPDATE:
      case static::FIELDS_DELETE:
        // When updating or deleting, autonumbering doesn't make sense.
        $this->removeField('AutoNum');

        // When updating, either 'BcCo' or 'SoSe' is required if there is no
        // match method.
        if (!$this->getField('BcCo') && !$this->getField('SoSe') && !$this->getField('MatchPer')) {
          $errors[] = "When updating a person either 'BcCo' or 'SoSe' must be set if there is no match method (MatchPer) specified.";
        }
        break;
    }

    if ($this->getAction() == static::FIELDS_INSERT && !$this->fieldExists('MatchPer')) {
      // When inserting, insert as new if no match method was specified.
      $this->setField('MatchPer', static::MATCH_NEW);
    }
    elseif ($this->getField('BcCo')) {
      // If a person ID is given, then match on this field.
      $this->setField('MatchPer', static::MATCH_BCCO);
    }
    elseif ($this->getField('SoSe')) {
      // If a person's BSN is given, then match on this field.
      $this->setField('MatchPer', static::MATCH_BSN);
      if ($this->fieldExists('AutoNum')) {
        $this->setField('AutoNum', FALSE);
      }
    }

    return $errors;
  }

}
