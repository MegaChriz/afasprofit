<?php

namespace Afas\Core\Mapping;

use Afas\Core\Entity\EntityInterface;

/**
 * Default aliases for Profit Update Connector fields.
 */
class DefaultMapping extends MappingBase implements EntityMappingInterface {

  /**
   * The entity to which the mapping applies.
   *
   * @var \Afas\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Constructs a new DefaultMapping object.
   *
   * @param \Afas\Core\Entity\EntityInterface $entity
   *   The entity to which to apply the mapping.
   */
  public function __construct(EntityInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(EntityInterface $entity) {
    return new static($entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function getMappings() {
    switch ($this->entity->getType()) {
      case 'FbOrderBatchLines':
        return [
          'batch_number' => 'BaNu',
          'unit_type' => 'BiUn',
          'quantity_units' => 'QuUn',
          'quantity' => 'Qu',
          'qty' => 'Qu',
          'quantity_invoice' => 'QuIn',
          'comment' => 'Re',
          'length' => 'QuLe',
          'width' => 'QuWi',
          'height' => 'QuHe',
        ];

      case 'FbOrderSerialLines':
        return [
          'serial_number' => 'SeNu',
          'unit_type' => 'BiUn',
          'quantity_units' => 'QuUn',
          'quantity' => 'Qu',
          'qty' => 'Qu',
          'quantity_invoice' => 'QuIn',
          'comment' => 'Re',
        ];

      case 'FbSales':
        return [
          'order_id' => 'OrNu',
          'customer_id' => 'DbId',
          'sales_relation' => 'DbId',
          'date' => 'OrDa',
          'delivery_date_req' => 'DaDe',
          'delivery_date_ack' => 'DaPr',
          'currency_code' => 'CuId',
          'currency_rate' => 'Rate',
          'reference' => 'RfCs',
          'sales_channel' => 'SaCh',
          'vat_due' => 'VaDu',
          'includes_vat' => 'InVa',
          'payment_type' => 'PaTp',
          'comment' => 'Re',
          'warehouse' => 'War',
          'delivery_prio' => 'DlPr',
          'language' => 'LgId',
          'delivery_cond' => 'DeCo',
          'cbs_type' => 'CsTy',
          'barcode_type' => 'VaBc',
          'journal' => 'JcCo',
          'invoice_to' => 'FaTo',
          'future_order' => 'FuOr',
          'delivery_type' => 'DtId',
          'project' => 'PrId',
          'project_stage' => 'PrSt',
          'delivery_state' => 'SeSt',
          'weight' => 'SeWe',
          'package_type' => 'PkTp',
          'shipping_company' => 'TrPt',
          'shipping_service' => 'SsId',
          'order_processing' => 'OrPr',
          'block_order' => 'FxBl',
        ];

      case 'FbSalesLines':
        return [
          'item_type' => 'VaIt',
          'sku' => 'ItCd',
          'item_code' => 'ItCd',
          'description' => 'Ds',
          'vat_type' => 'VaRc',
          'unit_type' => 'BiUn',
          'quantity' => 'QuUn',
          'qty' => 'QuUn',
          'length' => 'QuLe',
          'width' => 'QuWi',
          'height' => 'QuHe',
          'quantity_ordered' => 'Qu',
          'quantity_deliver' => 'QuDl',
          'price_list' => 'PrLi',
          'warehouse' => 'War',
          'weight_unit' => 'VaWt',
          'weight_net' => 'NeWe',
          'weight_gross' => 'GrWe',
          'unit_price' => 'Upri',
          'cost_price' => 'CoPr',
          'discount_perc' => 'PRDc',
          'comment' => 'Re',
          'guid' => 'GuLi',
          'dimension_1' => 'StL1',
          'dimension_2' => 'StL2',
        ];

      case 'FbSubscription':
        return [
          'organisation_id' => 'BcId',
          'contact_id' => 'CtPe',
          'customer_id' => 'DbId',
          'sales_relation' => 'DbId',
          'date' => 'SuSt',
          'order_date' => 'SuSt',
        ];

      case 'FbSubscriptionLines':
        return [
          'guid' => 'Id',
          'sku' => 'ItCd',
          'item_code' => 'ItCd',
          'quantity' => 'Qu',
          'qty' => 'Qu',
          'order_date' => 'DaSt',
        ];

      case 'KnBasicAddressAdr':
      case 'KnBasicAddressPad':
        return [
          'country_code' => 'CoId',
          'country' => 'CoId',
          'is_po_box' => 'PbAd',
          'address_line1' => 'Ad',
          'street' => 'Ad',
          'house_number' => 'HmNr',
          'house_number_ext' => 'HmAd',
          'house_number_extra' => 'HmAd',
          'postal_code' => 'ZpCd',
          'zip_code' => 'ZpCd',
          'city' => 'Rs',
          'locality' => 'Rs',
          'town' => 'Rs',
          'resolve_zip' => 'ResZip',
        ];

      case 'KnContact':
        return [
          'organisation_id' => 'BcCoOga',
          'organisation_code' => 'BcCoOga',
          'person_id' => 'BcCoPer',
          'person_code' => 'BcCoPer',
          'contact_id' => 'CdId',
          'contact_type' => 'ViKc',
          'department' => 'ExAd',
          'job_title' => 'FuDs',
          'phone' => 'TeNr',
          'fax' => 'FaNr',
          'mobile' => 'MbNr',
          'email' => 'EmAd',
          'homepage' => 'HoPa',
          'comment' => 'Re',
          'blocked' => 'Bl',
          'facebook' => 'Face',
          'linkedin' => 'Link',
          'twitter' => 'Twtr',
        ];

      case 'KnCourseMember':
        return [
          'organisation_id' => 'BcCo',
          'customer_id' => 'DeId',
          'sales_relation' => 'DeId',
          'event_id' => 'CrId',
          'contact_id' => 'CdId',
          'date' => 'SuDa',
          'subscription_date' => 'SuDa',
          'price' => 'DfPr',
          'sell_price' => 'DfPr',
          'discount_perc' => 'DiPc',
          'invoice' => 'Invo',
          'comment' => 'Rm',
          'blocked' => 'Bl',
        ];

      case 'KnOrganisation':
        return [
          'postal_address_is_address' => 'PbAd',
          'auto_num' => 'AutoNum',
          'match_method' => 'MatchOga',
          'organisation_id' => 'BcCo',
          'code' => 'BcCo',
          'search_name' => 'SeNm',
          'name' => 'Nm',
          'org_type' => 'ViLe',
          'branche' => 'ViLb',
          'kvk' => 'CcNr',
          'coc_number' => 'CcNr',
          'phone' => 'TeNr',
          'fax' => 'FaNr',
          'mobile' => 'MbNr',
          'email' => 'EmAd',
          'homepage' => 'HoPa',
          'comment' => 'Re',
          'fiscal_number' => 'FiNr',
          'facebook' => 'Face',
          'linkedin' => 'Link',
          'twitter' => 'Twtr',
        ];

      case 'KnPerson':
        return [
          'match_method' => 'MatchPer',
          'person_id' => 'BcCo',
          'code' => 'BcCo',
          'search_name' => 'SeNm',
          'name' => 'CaNm',
          'first_name' => 'FiNm',
          'initials' => 'In',
          'prefix' => 'Is',
          'surname_prefix' => 'Is',
          'last_name' => 'LaNm',
          'birthday' => 'DaBi',
          'gender' => 'ViGe',
          'bsn' => 'SoSe',
          'title' => 'TtId',
          'title2' => 'TtEx',
          'phone' => 'TeNr',
          'fax' => 'FaNr',
          'mobile' => 'MbNr',
          'email' => 'EmAd',
          'homepage' => 'HoPa',
          'comment' => 'Re',
          'facebook' => 'Face',
          'linkedin' => 'Link',
          'twitter' => 'Twtr',
        ];

      case 'KnProvApplication':
        return [
          'organisation_id' => 'BcCo',
          'rapport_type' => 'PvCd',
          'contact_id' => 'PvCt',
          'verstrekkingswijze' => 'VaPt',
        ];

      case 'KnSalesRelationOrg':
      case 'KnSalesRelationPer':
        return [
          'customer_id' => 'DbId',
          'sales_relation' => 'DbId',
          'vat_number' => 'VaId',
          'currency_code' => 'CuId',
          'verzamelreking_debiteur' => 'ColA',
        ];

      case 'KnSubject':
        return [
          'type' => 'StId',
          'description' => 'Ds',
          'comment' => 'SbTx',
          'date' => 'Da',
          'responsible' => 'EmId',
          'action_type' => 'SaId',
          'source' => 'ScId',
          'start_date' => 'DtFr',
          'end_date' => 'DtTo',
          'done' => 'St',
          'done_date' => 'DtSt',
          'blocked' => 'SbBl',
          'attachment' => 'SbPa',
        ];

      case 'KnSubjectLink':
        return [
          'is_org_person' => 'ToBC',
          'is_employee' => 'ToEm',
          'is_sales_relation' => 'ToSR',
          'is_purchase_relation' => 'ToPR',
          'is_client_ib' => 'ToCl',
          'is_client_vpb' => 'ToCV',
          'is_employer' => 'ToEr',
          'is_applicant' => 'ToAp',
          'destination_type' => 'SfTp',
          'destination_id' => 'SfId',
          'org_person' => 'BcId',
          'contact' => 'CdId',
          'sales_invoice_type' => 'SiTp',
          'sales_invoice' => 'SiId',
          'purchase_invoice_type' => 'PiTp',
          'purchase_invoice' => 'PiId',
          'fiscal_year' => 'FiYe',
          'project' => 'PjId',
          'campaign' => 'CaId',
          'subscription' => 'SuNr',
          'item_type' => 'VaIt',
          'item_code' => 'BiId',
          'course_event' => 'CrId',
        ];
    }

    return [];
  }

}
