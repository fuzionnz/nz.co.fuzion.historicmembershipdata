<?php
use CRM_Historicmembershipdata_ExtensionUtil as E;

/**
 * A custom contact search
 */
class CRM_Historicmembershipdata_Form_Search_PastMembersearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {

  /**
   * Class constructor.
   */
  function __construct(&$formValues) {
    parent::__construct($formValues);
  }

  /**
   * Prepare a set of search fields
   *
   * @param CRM_Core_Form $form modifiable
   * @return void
   */
  function buildForm(&$form) {
    CRM_Utils_System::setTitle(E::ts('Find Current Members on a specific date'));
    $form->add('datepicker', 'active_on', E::ts('Active on'), [], TRUE, ['time' => FALSE]);
    $membershipTypes = CRM_Member_PseudoConstant::membershipType();
    $form->add('select', 'membership_type_id', ts('Membership Type'), $membershipTypes, FALSE,
      ['multiple' => 1, 'class' => 'crm-select2', 'placeholder' => ts('- select -')]
    );
    $form->addYesNo('member_is_primary', ts('Primary Member?'), TRUE);
    $form->assign('elements', array('active_on', 'membership_type_id', 'member_is_primary'));
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
    return NULL;
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  function &columns() {
    // return by reference
    $columns = array(
      E::ts('Contact Id') => 'contact_id',
      E::ts('Contact Type') => 'contact_type',
      E::ts('Name') => 'sort_name',
      E::ts('Membership ID') => 'membership_id',
    );
    return $columns;
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @param int $offset
   * @param int $rowcount
   * @param null $sort
   * @param bool $includeContactIDs
   * @param bool $justIDs
   * @return string, sql
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    return $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string
   *  sql fragment with SELECT arguments
   */
  function select() {
    return " DISTINCT
      contact_a.id           as contact_id  ,
      contact_a.contact_type as contact_type,
      contact_a.sort_name    as sort_name,
      m.id as membership_id
    ";
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string
   *  sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "FROM civicrm_contact contact_a
    INNER JOIN civicrm_membership m ON (m.contact_id = contact_a.id)
    left join civicrm_membership_log as ml on ml.membership_id = m.id";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @param bool $includeContactIDs
   * @return string
   *  sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = [];
    $where = '';
    $clause[] = "contact_a.is_deleted != 1";
    if (!empty($this->_formValues['membership_type_id'])) {
      $clause[] = "ml.membership_type_id IN (" . implode($this->_formValues['membership_type_id']) . ")";
    }
    if (!CRM_Utils_System::isNull($this->_formValues['member_is_primary'])) {
      if (!empty($this->_formValues['member_is_primary'])) {
        $clause[] = "m.owner_membership_id IS NULL";
      }
      else {
        $clause[] = "m.owner_membership_id IS NOT NULL";
      }
    }

    $activeOn = date('Y-m-d', strtotime($this->_formValues['active_on']));
    $params[1] = [$activeOn, 'String'];
    if ($activeOn) {
      $clause[] = "ml.status_id IN (1,2,3)";
      $clause[] = "ml.modified_date <= %1 AND (ml.end_date >= %1 or ml.end_date IS NULL)";
    }

    if (!empty($clause)) {
      $where .= implode(' AND ', $clause);
    }
    return $this->whereClause($where, $params);
  }

  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */
  // function alterRow(&$row) {
  //   $row['sort_name'] .= ' ( altered )';
  // }
}
