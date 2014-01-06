<?php
/**
 * @file
 * This class generates filter forms for Drupal Form API
 */

class FilterForm
{
  // --------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------
  
  /**
   * @var FilterGroup $m_oFilterGroup
   * The 'root' filter group to work with. There is always one.
   * @access protected
   */
  protected $m_oFilterGroup;
  
  /**
   * @var string $m_sName
   * The name of this filter form
   * @access protected
   */
  protected $m_sName;
  
  /**
   * @var array $m_aEnabled
   * Enabled operations
   * @access protected
   */
  protected $m_aEnabled;
  
  // --------------------------------------------------------------
  // CONSTRUCT
  // --------------------------------------------------------------
  
  /**
   * DrupalFilterForm object constructor
   * @access public
   * @return void
   */
  public function __construct($p_sName) {
    $this->m_sName = $p_sName;
    $this->m_oFilterGroup = new FilterGroup();
    $this->m_oFilterGroup->id = $this->m_sName . '-root';
    $this->m_oFilterGroup->addFilter();
    $this->m_aOperations = array(
      'add' => TRUE,
      'addgroup' => TRUE,
      'remove' => TRUE,
    );
  }
  
  // --------------------------------------------------------------
  // SETTERS
  // --------------------------------------------------------------
  
  /**
   * Construct filters and filter groups from an array
   * @param array $p_aData
   * @access public
   * @return void
   */
  public function from_array($p_aData) {
    // Check if there is someting to import. We don't allow empty data.
    if (!is_array($p_aData) || count($p_aData) < 1 || !isset($p_aData['filters']) || count($p_aData['filters']) < 1) {
      return $this->m_oFilterGroup;
    }
    $this->m_oFilterGroup = new FilterGroup();
    $this->m_oFilterGroup->id = $this->m_sName . '-root';
    $this->m_oFilterGroup->from_array($p_aData);
    return $this->m_oFilterGroup;
  }
  
  /**
   * Update filters
   * @param string $p_sOperation
   * @param string $p_sFilter_id
   * @access public
   * @return void
   */
  public function update($p_sOperation, $p_sFilter_id) {
    if (!$this->m_aOperations[$p_sOperation]) {
      return;
    }
    
    switch ($p_sOperation) {
      case 'add':
        $oFilterGroup = $this->lookupFilter($p_sFilter_id);
        $oFilterGroup->addFilter();
        break;
      case 'addgroup':
        $oFilterGroup = $this->lookupFilter($p_sFilter_id);
        $oFilterGroup->addFilterGroup()->addFilter();
        break;
      case 'remove':
        $oFilter = $this->lookupFilter($p_sFilter_id);
        $oFilter->remove();
        break;
    }
  }
  
  /**
   * Enable operation
   * @param string $p_sOperation
   * @return void
   */
  public function enable($p_sOperation) {
    if (isset($this->m_aOperations[$p_sOperation])) {
      $this->m_aOperations[$p_sOperation] = TRUE;
    }
  }
  
  /**
   * Disable operation
   * @param string $p_sOperation
   * @return void
   */
  public function disable($p_sOperation) {
    if (isset($this->m_aOperations[$p_sOperation])) {
      $this->m_aOperations[$p_sOperation] = FALSE;
    }
  }
  
  // --------------------------------------------------------------
  // GETTERS
  // --------------------------------------------------------------
  
  /**
   * Returns FilterGroup
   * @access public
   * @return FilterGroup
   */
  public function getFilterGroup() {
    return $this->m_oFilterGroup;
  }
  
  /**
   * Looks up a filter giving a filter id
   * @param string $p_sFilter_id
   * @return iFilter
   */
  public function lookupFilter($p_sFilter_id) {
    $aTree = explode("-", $p_sFilter_id);
    
    $oFilter = $this->m_oFilterGroup;
    if (count($aTree) > 2) {
      $sCurrentTree = $aTree[0] . '-' . $aTree[1];
      
      // Delete first element as this is supposed to be the name of this filter form.
      unset($aTree[0]);
      // Delete the second one too, as this is the 'root'.
      unset($aTree[1]);
      
      foreach ($aTree as $sTreePart) {
        $sCurrentTree .= '-' . $sTreePart;
        $oFilter = $oFilter->getFilter($sCurrentTree);
      }
    }
    return $oFilter;
  }
  
  /**
   * Counts all filters (all instances of Filter)
   * @param FilterGroup $p_oFilterGroup
   * @access public
   * @return int
   */
  public function countFilters(FilterGroup $p_oFilterGroup) {
    $iCount = 0;
    foreach ($p_oFilterGroup->filters as $oFilter) {
      if ($oFilter instanceof Filter) {
        $iCount++;
      }
      elseif ($oFilter instanceof FilterGroup) {
        $iCount += $this->countFilters($oFilter);
      }
    }
    return $iCount;
  }
  
  // --------------------------------------------------------------
  // ACTION (public)
  // --------------------------------------------------------------
  
  /**
   * generate()
   * @param object $p_oObject
   * @access public
   * @return array
   */
  public function generate() {
    $form['filter_wrapper'] = array(
      '#tree' => FALSE,
      '#prefix' => '<div class="clear-block" id="filterform-wrapper">',
      '#suffix' => '</div>',
    );
    
    // If we have only one filter, disable 'remove'
    if ($this->countFilters($this->m_oFilterGroup) < 2) {
      $this->disable('remove');
    }
    
    // Container for the filters.
    $form['filter_wrapper']['filterform_filters'] = $this->_generate($this->m_oFilterGroup);
    $form['filter_wrapper']['filterform_filters']['#prefix'] = '<div id="filterform-filters">';
    $form['filter_wrapper']['filterform_filters']['#suffix'] = '</div>';
    
    $form['filter_wrapper']['filterform_buttons'] = array(
      'filterform_update' => array(
        '#type' => 'hidden',
        '#ahah' => array(
          'path' => 'filterform/js',
          'wrapper' => 'filterform-filters',
          'method' => 'replace',
          'effect' => 'fade',
          'event' => 'filterform',
        ),
        '#attributes' => array(
          'id' => 'filterform-update',
        ),
      ),
    );
    return $form;
  }
  
  // --------------------------------------------------------------
  // ACTION (protected)
  // --------------------------------------------------------------
  
  /**
   * Generates form elements
   * @param iFilter $p_oObject
   * @access protected
   * @return array
   */
  protected function _generate(iFilter $p_oObject) {
    if ($p_oObject instanceof Filter) {
      return $this->_generateFilter($p_oObject);
    }
    if ($p_oObject instanceof FilterGroup) {
      return $this->_generateFilterGroup($p_oObject);
    }
  }
    
  /**
   * Generates filter group
   * @param FilterGroup $p_oFilterGroup
   * @access protected
   * @return array
   */
  protected function _generateFilterGroup(FilterGroup $p_oFilterGroup) {
    $fieldgroupform = array();
    
    $aFilters = $p_oFilterGroup->filters;
    if (count($aFilters) > 0) {
      $fieldgroupform = array(
        '#type' => 'fieldset',
        '#tree' => TRUE,
        '#title' => $p_oFilterGroup->name,
        '#attributes' => array(
          'class' => 'filtergroup',
          'id' => $p_oFilterGroup->id,
        ),
        '#theme' => array('filterform_filtergroup'),
      );
      
      // Modus field
      if (count($aFilters) > 1) {
        $fieldgroupform['modus'] = array(
          '#type' => 'select',
          '#options' => array(
            FilterGroup::MODUS_AND => t('AND'),
            FilterGroup::MODUS_OR => t('OR'),
          ),
          '#default_value' => $p_oFilterGroup->modus,
          '#attributes' => array(
            'class' => 'field-filtergroup-modus',
          ),
        );
      }
      else {
        $fieldgroupform['modus'] = array(
          '#type' => 'value',
          '#value' => $p_oFilterGroup->modus,
          '#attributes' => array(
            'class' => 'field-filtergroup-modus',
          ),
        );
      }
      
      // Type
      $fieldgroupform['type'] = array(
        '#type' => 'hidden',
        '#value' => get_class($p_oFilterGroup),
        '#attributes' => array(
          'class' => 'field-filtergroup-type',
        ),
      );
      
      // Name
      $fieldgroupform['name'] = array(
        '#type' => 'value',
        '#value' => $p_oFilterGroup->name,
        '#attributes' => array(
          'class' => 'field-filtergroup-name',
        ),
      );
      
      // Filters
      $fieldgroupform['filters'] = array(
        '#tree' => TRUE,
      );
      foreach ($aFilters as $iFilter_id => $oFilter) {
        $fieldgroupform['filters'][$iFilter_id] = $this->_generate($oFilter);
      }
    }
    
    return $fieldgroupform;
  }
  
  /**
   * Generates filter
   * @param Filter $p_oFilter
   * @return array
   * @todo Not all options might be appropriate for field, we'll leave that for now
   * @todo In the ideal world, 'field' would be a select field instead of a textfield.
   */
  protected function _generateFilter(Filter $p_oFilter) {
    $form = array(
      'type' => array(
        '#type' => 'hidden',
        '#value' => get_class($p_oFilter),
      ),
      
      // Fields
      'field' => array(
        '#type' => 'textfield',
        '#default_value' => $p_oFilter->field,
        '#attributes' => array(
          'class' => 'field-filter-field',
        ),
      ),
      'operator' => array(
        '#type' => 'select',
        '#options' => array(
          Filter::OPERATOR_CONTAINS => t('Contains'),
          Filter::OPERATOR_CONTAINS_NOT => t('Does not contain'),
          Filter::OPERATOR_EQ => t('Is equal to'),
          Filter::OPERATOR_NE => t('Is not equal to'),
          Filter::OPERATOR_GT => t('Is greater than'),
          Filter::OPERATOR_GE => t('Is greater than or equal to'),
          Filter::OPERATOR_LT => t('Is less than'),
          Filter::OPERATOR_LE => t('Is less than or equal to'),
          Filter::OPERATOR_STARTS_WITH => t('Starts with'),
          Filter::OPERATOR_STARTS_NOT_WITH => t('Starts not with'),
          Filter::OPERATOR_ENDS_WITH => t('Ends with'),
          Filter::OPERATOR_ENDS_NOT_WITH => t('Ends not with'),
          Filter::OPERATOR_EMPTY => t('Is empty'),
          Filter::OPERATOR_NOT_EMPTY => t('Is not empty'),
        ),
        '#default_value' => $p_oFilter->operator,
        '#attributes' => array(
          'class' => 'field-filter-operator',
        ),
      ),
      'value' => array(
        '#type' => 'textfield',
        '#default_value' => $p_oFilter->value,
        '#attributes' => array(
          'class' => 'field-filter-value',
        ),
      ),
      'buttons' => array(
        '#tree' => TRUE,
        'remove' => array(
          '#type' => 'button',
          '#value' => t('Remove filter'),
          '#attributes' => array(
            'onclick' => "$('#edit-filterform-update').val('remove," . $p_oFilter->id . "').trigger('filterform'); return false;",
            'title' => $p_oFilter->id,
            'class' => 'field-filter-button field-filter-remove',
          ),
        ),
        'add' => array(
          '#type' => 'button',
          '#value' => t('Add filter'),
          '#attributes' => array(
            'onclick' => "$('#edit-filterform-update').val('add," . $p_oFilter->parent->id . "').trigger('filterform'); return false;",
            'title' => $p_oFilter->parent->id,
            'class' => 'field-filter-button field-filter-add',
          ),
        ),
        'addgroup' => array(
          '#type' => 'button',
          '#value' => t('Add filter group'),
          '#attributes' => array(
            'onclick' => "$('#edit-filterform-update').val('addgroup," . $p_oFilter->parent->id . "').trigger('filterform'); return false;",
            'title' => $p_oFilter->parent->id,
            'class' => 'field-filter-button field-filter-addgroup',
          ),
        ),
      ),
      
      // Properties
      '#tree' => TRUE,
      '#attributes' => array(
        'class' => 'filter',
        'id' => $p_oFilter->id,
      ),
      '#theme' => array('filterform_filter'),
    );
    
    // Disabled buttons?
    foreach ($this->m_aOperations as $sOperation => $bEnabled) {
      if (!$bEnabled) {
        $form['buttons'][$sOperation]['#disabled'] = TRUE;
        $form['buttons'][$sOperation]['#attributes']['class'] .= ' disabled';
      }
    }
    
    return $form;
  }
}