<?php
/**
 * @file
 * KnSalesRelationOrg class
 *
 * Create Relations in AFAS
 */

class AfasKnSalesRelationOrg extends AFAS_Element
{
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * AFAS_Element object constructor
   * @param iAFAS_Element $p_oParent
   *   Can be AFAS_Element or AfasUpdateConnector
   * @param string $p_sType
   * @param string $p_sObject_id
   * @access public
   * @return void
   * @todo
   */
  public function __construct(iAFAS_Element $p_oParent, $p_sType, $p_sObject_id) {
    parent::__construct($p_oParent, 'KnSalesRelationOrg', $p_sObject_id);
  }

  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------

  /**
   * Adds organisation
   * @access public
   * @return AfasKnOrganisation
   */
  public function addOrganisation() {
    return $this->addChild('KnOrganisation');
  }

  /**
   * Sets basic address in Element
   * @param UcAddressesAddress $p_oAddress
   * @param AFAS_Element $p_oOrganisation
   * @access public
   * @return void
   * @throws AfasException
   */
  public function setBasicAddress(UcAddressesAddress $p_oAddress, $p_oOrganisation = NULL) {
    if ($p_oOrganisation instanceof AFAS_Element) {
      if ($p_oOrganisation->type != 'KnOrganisation') {
        throw new AfasException('Given element is not a KnOrganisation');
      }
    }
    else {
      $p_oOrganisation = $this->addOrganisation();
    }
    
    // Get basic address of organisation
    try {
      $oKnBasicAddress = $p_oOrganisation->getChild('KnBasicAddressAdr');
    }
    catch (Exception $e) {
      $oKnBasicAddress = $p_oOrganisation->addChild('KnBasicAddressAdr', 'KnBasicAddressAdr');
    }
    
    // Fill in address
    $oKnBasicAddress->setField('Ad', $p_oAddress->getField('street1'));
    $oKnBasicAddress->setField('HmNr', $p_oAddress->getField('ucxf_huisnummer'));
    $oKnBasicAddress->setField('ZpCd', $p_oAddress->getField('postal_code'));
    $oKnBasicAddress->setField('Rs', $p_oAddress->getField('city'));
    $oKnBasicAddress->setField('Cold', $p_oAddress->getField('country'));
    
    // Get contact of organisation
    try {
      $oKnContact = $p_oOrganisation->getChild('KnContact');
    }
    catch (Exception $e) {
      $oKnContact = $p_oOrganisation->addChild('KnContact', 'KnContact');
    }
    // Get person of contact
    try {
      $oKnPerson = $oKnContact->getChild('KnPerson');
    }
    catch (Exception $e) {
      $oKnPerson = $oKnContact->addChild('KnPerson', 'KnPerson');
    }
    
    // Fill in person
    $oKnPerson->setField('FiNm', $p_oAddress->getField('first_name'));
    $oKnPerson->setField('Is', $p_oAddress->getField('ucxf_tussenvoegsel'));
    $oKnPerson->setField('LaNm', $p_oAddress->getField('last_name'));
    $oKnPerson->setField('TeNr', $p_oAddress->getField('phone'));
  }
}