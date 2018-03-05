<?php

namespace Afas\Core\Entity\Plugin;

use Afas\Core\Entity\Entity;
use InvalidArgumentException;

/**
 * Class for a KnProvApplication entity.
 */
class KnProvApplication extends Entity {

  // --------------------------------------------------------------
  // CONSTANTS
  // --------------------------------------------------------------

  /**
   * Report types.
   *
   * @var string
   */
  const VERKOOPORDER            = '02';
  const PAKBON                  = '03';
  const AANMANING               = '04';
  const REKENINGOVERZICHT       = '05';
  const VOORCALCULATIE          = '06';
  const VERKOOPFACTUUR          = '10';
  const PROJECTFACTUUR          = '11';
  const CURSUSFACTUUR           = '12';
  const ABONNEMENTSFACTUUR      = '13';
  const BALIEFACTUUR            = '15';
  const VERKOOPBASISORDER       = '16';
  const INCASSOSPECIFICATIE     = '18';
  const TERUGBETAALSPECIFICATIE = '19';
  const TERMIJNFACTUUR          = '20';
  const CONCEPT                 = '21';
  const PROFORMA                = '23';

  /**
   * Method of provision.
   *
   * @var string
   */
  const PROV_PRINT     = 'A';
  const PRINT_EMAIL    = 'B';
  const EMAIL          = 'E';
  const PRINT_EDI      = 'P';
  const DOSSIER        = 'In dossier zetten';
  const EMAIL_EFACTUUR = 'U';
  const EDI            = 'V';
  const NO_PROVISION   = 'X';

  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->setField('VaPt', static::EMAIL_EFACTUUR);
  }

  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields() {
    return [
      'BcCo',
      'PvCd',
      'PvCt',
      'VaPt',
    ];
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * {@inheritdoc}
   */
  public function setField($key, $value) {
    switch ($key) {
      case 'VaPt':
        switch ($value) {
          case static::PROV_PRINT:
          case static::PRINT_EMAIL:
          case static::EMAIL:
          case static::PRINT_EDI:
          case static::DOSSIER:
          case static::EMAIL_EFACTUUR:
          case static::EDI:
          case static::NO_PROVISION:
            break;

          default:
            throw new InvalidArgumentException(strtr('Invalid value for VaPt: !value', [
              '!value' => @(string) $value,
            ]));
        }
    }

    return parent::setField($key, $value);
  }

}
