<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

use Sugarcrm\Sugarcrm\Entitlements\SubscriptionManager;

// Opportunity is used to store customer information.
class Opportunity extends SugarBean
{
    const STAGE_CLOSED_WON = 'Closed Won';
    const STAGE_CLOSED_LOST = 'Closed Lost';

    const STATUS_NEW = 'New';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_CLOSED_WON = 'Closed Won';
    const STATUS_CLOSED_LOST = 'Closed Lost';

    // Stored fields
    public $id;
    public $lead_source;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $assigned_user_id;
    public $created_by;
    public $created_by_name;
    public $modified_by_name;
    public $description;
    public $name;
    public $opportunity_type;
    public $amount;
    public $amount_usdollar;
    public $currency_id;
    public $base_rate;
    public $date_closed;
    public $date_closed_timestamp;
    public $next_step;
    public $sales_stage;
    public $sales_status;
    public $probability;
    public $campaign_id;
    public $team_name;
    public $team_id;
    public $quote_id;

    // These are related
    public $account_name;
    public $account_id;
    public $contact_id;
    public $task_id;
    public $note_id;
    public $meeting_id;
    public $call_id;
    public $email_id;
    public $assigned_user_name;

    public $table_name = "opportunities";
    public $rel_account_table = "accounts_opportunities";
    public $rel_contact_table = "opportunities_contacts";
    public $module_dir = "Opportunities";
    public $rel_quote_table = "quotes_opportunities";
    public $best_case;
    public $worst_case;
    public $timeperiod_id;
    public $commit_stage;

    //Marketo
    var $mkto_sync;
    var $mkto_id;

    /**
     * holds the settings for the Forecast Module
     *
     * @var array
     */
    public static $settings = array();

    public $importable = true;
    public $object_name = "Opportunity";

    // This is used to retrieve related fields from form posts.
    public $additional_column_fields = Array(
        'assigned_user_name',
        'assigned_user_id',
        'account_name',
        'account_id',
        'contact_id',
        'task_id',
        'note_id',
        'meeting_id',
        'call_id',
        'email_id'
    ,
        'quote_id'
    );

    public $relationship_fields = Array(
        'task_id' => 'tasks',
        'note_id' => 'notes',
        'account_id' => 'accounts',
        'meeting_id' => 'meetings',
        'call_id' => 'calls',
        'email_id' => 'emails',
        'project_id' => 'project',
        // Bug 38529 & 40938
        'currency_id' => 'currencies',
        'quote_id' => 'quotes',
    );


    public function __construct()
    {
        parent::__construct();
        global $sugar_config;

        if (empty($sugar_config['require_accounts'])) {
            unset($this->required_fields['account_name']);
        }
    }


    public $new_schema = true;


    /**
     * Return the a Summary for the Record
     *
     * @return string
     */
    public function get_summary_text()
    {
        return "$this->name";
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function create_list_query($order_by, $where, $show_deleted = 0)
    {
        $GLOBALS['log']->deprecated('Opportunity::create_list_query() has been deprecated in 7.8');
        $custom_join = $this->custom_fields->getJOIN();
        $query = "SELECT ";

        $query .= "
                            accounts.id as account_id,
                            accounts.name as account_name,
                            accounts.assigned_user_id account_id_owner,
                            users.user_name as assigned_user_name ";
        $query .= ",teams.name AS team_name ";
        if ($custom_join) {
            $query .= $custom_join['select'];
        }
        $query .= " ,opportunities.*
                            FROM opportunities ";

        // We need to confirm that the user is a member of the team of the item.
        $this->add_team_security_where_clause($query);
        $query .= "LEFT JOIN users
                            ON opportunities.assigned_user_id=users.id ";
        $query .= getTeamSetNameJoin('opportunities');

        $query .= " LEFT JOIN timeperiods
                        ON timeperiods.start_date_timestamp <= opportunities.date_closed_timestamp
                        AND timeperiods.end_date_timestamp >= opportunities.date_closed_timestamp ";

        $query .= "LEFT JOIN $this->rel_account_table
                            ON opportunities.id=$this->rel_account_table.opportunity_id
                            LEFT JOIN accounts
                            ON $this->rel_account_table.account_id=accounts.id ";
        if ($custom_join) {
            $query .= $custom_join['join'];
        }
        $where_auto = '1=1';
        if ($show_deleted == 0) {
            $where_auto = "
			($this->rel_account_table.deleted is null OR $this->rel_account_table.deleted=0)
			AND (accounts.deleted is null OR accounts.deleted=0)
			AND opportunities.deleted=0";
        } else {
            if ($show_deleted == 1) {
                $where_auto = " opportunities.deleted=1";
            }
        }

        if ($where != "") {
            $query .= "where ($where) AND " . $where_auto;
        } else {
            $query .= "where " . $where_auto;
        }

        if ($order_by != "") {
            $query .= " ORDER BY $order_by";
        } else {
            $query .= " ORDER BY opportunities.name";
        }

        return $query;
    }

    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function fill_in_additional_list_fields()
    {
        $GLOBALS['log']->deprecated('Opportunity::fill_in_additional_list_fields() has been deprecated in 7.8');
        if ($this->force_load_details == true) {
            $this->fill_in_additional_detail_fields();
        }
    }


    /**
     * @deprecated Not used in the REST API, will be removed in a future version
     */
    public function fill_in_additional_detail_fields()
    {
        $GLOBALS['log']->deprecated('Opportunity::fill_in_additional_detail_fields() has been deprecated in 7.8');
        parent::fill_in_additional_detail_fields();

        if (!empty($this->currency_id)) {
            $currency = BeanFactory::getBean('Currencies', $this->currency_id);
            if ($currency->id != $this->currency_id || $currency->deleted == 1) {
                $this->amount = $this->amount_usdollar;
                $this->currency_id = $currency->id;
            }
        }
    }

    /**
     * Returns a list of the associated contacts
     *
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_contacts()
    {
        $GLOBALS['log']->deprecated('Opportunity::get_contacts() has been deprecated in 7.8');
        $this->load_relationship('contacts');
        $query_array = $this->contacts->getQuery(true);

        //update the select clause in the retruned query.
        $query_array['select'] = "SELECT contacts.id, contacts.first_name, contacts.last_name, contacts.title, contacts.email1, contacts.phone_work, opportunities_contacts.contact_role as opportunity_role, opportunities_contacts.id as opportunity_rel_id ";

        $query = '';
        foreach ($query_array as $qstring) {
            $query .= ' ' . $qstring;
        }
        $temp = Array(
            'id',
            'first_name',
            'last_name',
            'title',
            'email1',
            'phone_work',
            'opportunity_role',
            'opportunity_rel_id'
        );
        return $this->build_related_list2($query, BeanFactory::newBean('Contacts'), $temp);
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     * @param string $fromid
     * @param string $toid
     */
    public function update_currency_id($fromid, $toid)
    {
        $GLOBALS['log']->deprecated('Opportunity::update_currency_id() has been deprecated in 7.8');
        $idequals = '';

        $currency = BeanFactory::getBean('Currencies', $toid);
        foreach ($fromid as $f) {
            if (!empty($idequals)) {
                $idequals .= ' or ';
            }
            $idequals .= "currency_id=" . $this->db->quoted($f);
        }

		if ( !empty($idequals) ) {
			$query  = "select amount, id from opportunities where (" . $idequals . ") and deleted=0 and opportunities.sales_stage <> '".self::STAGE_CLOSED_WON."' AND opportunities.sales_stage <> '".self::STAGE_CLOSED_LOST."';";
            $result = $this->db->query($query);

            while ($row = $this->db->fetchByAssoc($result)) {
                $query = sprintf(
                    "UPDATE opportunities SET currency_id = %s, amount_usdollar = %s, base_rate = %s WHERE id = %s;",
                    $this->db->quoted($currency->id),
                    $this->db->quoted(SugarCurrency::convertAmountToBase($row['amount'], $currency->id)),
                    $this->db->quoted($currency->conversion_rate),
                    $this->db->quoted($row['id'])
                );
                $this->db->query($query);
            }
        }
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_list_view_data()
    {
        $GLOBALS['log']->deprecated('Opportunity::get_list_view_data() has been deprecated in 7.8');
        global $locale, $current_language, $current_user, $mod_strings, $app_list_strings, $sugar_config;
        $app_strings = return_application_language($current_language);
        $params = array();

        $temp_array = $this->get_list_view_array();
        $temp_array['SALES_STAGE'] = empty($temp_array['SALES_STAGE']) ? '' : $temp_array['SALES_STAGE'];
        $temp_array["ENCODED_NAME"] = $this->name;
        return $temp_array;
    }


    /**
     * This is no longer used and is considered deprecated.  It will be removed in a future release.
     *
     * @deprecated
     */
    public function get_currency_symbol()
    {
        $GLOBALS['log']->deprecated('Opportunity::get_currency_symbol() has been deprecated in 7.8');
        if (isset($this->currency_id)) {
            $cur_qry = "select * from currencies where id ='" . $this->currency_id . "'";
            $cur_res = $this->db->query($cur_qry);
            if (!empty($cur_res)) {
                $cur_row = $this->db->fetchByAssoc($cur_res);
                if (isset($cur_row['symbol'])) {
                    return $cur_row['symbol'];
                }
            }
        }
        return '';
    }

    /**
     * To check whether currency_id field is changed during save.
     * @return bool true if currency_id is changed, false otherwise
     */
    protected function isCurrencyIdChanged() {
        // if both are defined, compare
        if (isset($this->currency_id) && isset($this->fetched_row['currency_id'])) {
            if ($this->currency_id != $this->fetched_row['currency_id']) {
                return true;
            }
        }
        // one is not defined, the other one is not empty, means changed
        if (!isset($this->currency_id) && !empty($this->fetched_row['currency_id'])) {
            return true;
        }
        if (!isset($this->fetched_row['currency_id']) && !empty($this->currency_id)) {
            return true;
        }

        return false;
    }

    /**
     * builds a generic search based on the query string using or
     * do not include any $this-> because this is called on without having the class instantiated
     */
    public function build_generic_where_clause($the_query_string)
    {
        $where_clauses = Array();
        $the_query_string = $GLOBALS['db']->quote($the_query_string);
        array_push($where_clauses, "opportunities.name like '$the_query_string%'");
        array_push($where_clauses, "accounts.name like '$the_query_string%'");

        $the_where = "";
        foreach ($where_clauses as $clause) {
            if ($the_where != "") {
                $the_where .= " or ";
            }
            $the_where .= $clause;
        }

        return $the_where;
    }

    /**
     * Bean specific logic for when SugarFieldCurrency_id::save() is called to make sure we can update the base_rate
     *
     * @return bool
     */
    public function updateCurrencyBaseRate()
    {
        return !in_array($this->sales_stage, $this->getClosedStages());
    }

    public function save($check_notify = false)
    {
        //if probability is empty, set it based on the sales stage
        if ($this->probability === '' && !empty($this->sales_stage)) {
            $this->mapProbabilityFromSalesStage();
        }

        //BEGIN SUGARCRM flav=ent ONLY
        //if the id is set (previously saved bean) and sales_status is still New, update to in progress
        if (isset($this->id) && !$this->new_with_id && $this->sales_status == Opportunity::STATUS_NEW) {
            $this->sales_status = Opportunity::STATUS_IN_PROGRESS;
        }
        //END SUGARCRM flav=ent ONLY

        // verify that base_rate is set to the correct amount, moved in from SugarBean
        // as we need this to run before perform_save (which does calculations with base_rate)
        if (isset($this->field_defs['currency_id']) && isset($this->field_defs['base_rate'])) {
            SugarCurrency::verifyCurrencyBaseRateSet($this);
        }

        SugarAutoLoader::requireWithCustom('modules/Opportunities/SaveOverload.php');
        perform_save($this);

        return parent::save($check_notify);
    }

    public function save_relationship_changes($is_update, $exclude = array())
    {
        //if account_id was replaced unlink the previous account_id.
        //this rel_fields_before_value is populated by sugarbean during the retrieve call.
        if (!empty($this->account_id) and !empty($this->rel_fields_before_value['account_id']) and
            (trim($this->account_id) != trim($this->rel_fields_before_value['account_id']))
        ) {
            //unlink the old record.
            $this->load_relationship('accounts');
            $this->accounts->delete($this->id, $this->rel_fields_before_value['account_id']);
            //propagate change down to related beans
            $relationshipsToBeTouched = array('products', 'revenuelineitems');
            foreach ($relationshipsToBeTouched as $relationship) {
                $this->load_relationship($relationship);
                foreach ($this->$relationship->getBeans() as $bean) {
                    $bean->account_id = $this->account_id;
                    $bean->save();
                }
            }
        }
        // Bug 38529 & 40938 - exclude currency_id
        parent::save_relationship_changes($is_update, array('currency_id'));

        if (!empty($this->contact_id)) {
            $this->set_opportunity_contact_relationship($this->contact_id);
        }
    }


    public function set_opportunity_contact_relationship($contact_id)
    {
        global $app_list_strings;
        $default = $app_list_strings['opportunity_relationship_type_default_key'];
        $this->load_relationship('contacts');
        $this->contacts->add($contact_id, array('contact_role' => $default));
    }


    public function set_notification_body($xtpl, $oppty)
    {
        global $app_list_strings;

        $xtpl->assign("OPPORTUNITY_NAME", $oppty->name);
        $xtpl->assign("OPPORTUNITY_AMOUNT", $oppty->amount);
        $xtpl->assign("OPPORTUNITY_CLOSEDATE", $oppty->date_closed);

        $oppStage = '';
        if(isset($oppty->sales_stage) && !empty($oppty->sales_stage)) {
            $oppStage = $app_list_strings['sales_stage_dom'][$oppty->sales_stage];
        }
        $xtpl->assign("OPPORTUNITY_STAGE", $oppStage);

        $xtpl->assign("OPPORTUNITY_DESCRIPTION", $oppty->description);

        return $xtpl;
    }


    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }


    /**
     * This is no longer used since Opportunities is not in BWC.  This will be removed in a future version
     *
     * @deprecated
     *
     * @return array
     */
    public function listviewACLHelper()
    {
        $GLOBALS['log']->deprecated('Opportunity::listviewACLHelper() has been deprecated in 7.8');
        $array_assign = parent::listviewACLHelper();
        $is_owner = false;
        if (!empty($this->account_id)) {

            if (!empty($this->account_id_owner)) {
                global $current_user;
                $is_owner = $current_user->id == $this->account_id_owner;
            }
        }
        if (!ACLController::moduleSupportsACL('Accounts') ||
            ACLController::checkAccess('Accounts', 'view', $is_owner)) {
            $array_assign['ACCOUNT'] = 'a';
        } else {
            $array_assign['ACCOUNT'] = 'span';
        }

        return $array_assign;
    }

    /**
     * getClosedStages
     *
     * Return an array of closed stage names from the admin bean.
     *
     * @access public
     * @return array array of closed stage values
     */
    public function getClosedStages()
    {
        // TODO: move closed stages to a global setting.
        // For now, get them from forecasting.
        static $stages;
        if (!isset($stages)) {
            $admin = BeanFactory::newBean('Administration');
            $settings = $admin->getConfigForModule('Forecasts');

            // get all possible closed stages
            $stages = array_merge(
                isset($settings['sales_stage_won']) ? (array)$settings['sales_stage_won'] : array(),
                isset($settings['sales_stage_lost']) ? (array)$settings['sales_stage_lost'] : array()
            );
        }
        return $stages;
    }

    /**
     * Handling mapping the probability from the sales stage.
     */
    protected function mapProbabilityFromSalesStage()
    {
        global $app_list_strings;
        $prob_arr = $app_list_strings['sales_probability_dom'];
        if (isset($prob_arr[$this->sales_stage])) {
            $this->probability = $prob_arr[$this->sales_stage];
        }
    }

    public static function getSettings($reload = false)
    {
        /* @var $admin Administration */
        if (empty(static::$settings) || $reload === true) {
            $admin = BeanFactory::newBean('Administration');
            static::$settings = $admin->getConfigForModule('Opportunities');
        }

        return static::$settings;
    }

    //BEGIN SUGARCRM flav=ent ONLY
    /**
     * Return an array of RLI closed won stage names.
     *
     * @return array array of RLI closed won stage values
     */
    public function getRliClosedWonStages(): array
    {
        return Forecast::getSettings()['sales_stage_won'] ?? ['Closed Won'];
    }

    /**
     * Return an array of RLI closed lost stage names.
     *
     * @return array array of RLI closed lost stage values
     */
    public function getRliClosedLostStages(): array
    {
        return Forecast::getSettings()['sales_stage_lost'] ?? ['Closed Lost'];
    }

    /**
     * Check if we can renew opportunity.
     *
     * @return bool
     */
    public function canRenew(): bool
    {
        // get the OpportunitySettings
        $settings = Opportunity::getSettings();
        $useRli = isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems';
        // get licenses
        $licenses = SubscriptionManager::instance()->getSystemSubscriptionKeysInSortedValueArray();
        return $useRli && in_array('SUGAR_SELL', $licenses);
    }
    //END SUGARCRM flav=ent ONLY

    /**
     * Retrieve and update fetched_row['sales_status'] from db.
     */
    public function retrieveSalesStatus()
    {
        if (!empty($this->id)) {
            $query = new \SugarQuery();
            $query->from($this, ['add_deleted' => 1, 'team_security' => false]);
            $query->select('sales_status');
            $query->where()->equals('id', $this->id);
            $query->limit(1);
            $results = $query->execute();
            if (!empty($results)) {
                $row = $results[0];
                $row = $this->convertRow($row);
                if (empty($this->fetched_row)) {
                    $this->fetched_row = [];
                }
                $this->fetched_row['sales_status'] = $row['sales_status'];
            }
        }
    }

    //BEGIN SUGARCRM flav=ent ONLY
    /**
     * Get renewal parent.
     *
     * @return Opportunity|NULL
     */
    public function getRenewalParent(): ?Opportunity
    {
        if (!empty($this->renewal_parent_id)) {
            return BeanFactory::getBean($this->getModuleName(), $this->renewal_parent_id);
        }
        return null;
    }

    /**
     * Get 'Closed Won' and renewable RLIs for this opportunity.
     *
     * @return array
     */
    public function getClosedWonRenewableRLIs(): array
    {
        $rliBeans = [];
        $closedWon = $this->getRliClosedWonStages();

        if ($this->load_relationship('revenuelineitems')) {
            $rliBean = BeanFactory::getBean($this->revenuelineitems->getRelatedModuleName());
            $whereTable = $rliBean->getTableName();

            $params = [
                "$whereTable.service = 1",
                "$whereTable.sales_stage in ('" . implode("','", $closedWon) . "')",
                "$whereTable.renewable = 1",
            ];

            $rliBeans = $this->revenuelineitems->getBeans([
                'where' => implode(' AND ', $params),
            ]);
        }

        return $rliBeans;
    }

    /**
     * Get existing renewal opportunity.
     *
     * @return Opportunity|NULL
     */
    public function getExistingRenewalOpportunity(): ?Opportunity
    {
        $renewalBean = null;
        if ($this->load_relationship('renewal_opportunities')) {
            $whereTable = $this->getTableName();

            $params = [
                "$whereTable.sales_status != " . $this->db->quoted(Opportunity::STATUS_CLOSED_WON),
                "$whereTable.sales_status != " . $this->db->quoted(Opportunity::STATUS_CLOSED_LOST),
                "$whereTable.renewal = 1",
            ];

            $renewalBeans = $this->renewal_opportunities->getBeans([
                'where' => implode(' AND ', $params),
            ]);

            if (!empty($renewalBeans)) {
                $renewalBean = array_shift($renewalBeans);
            }
        }

        return $renewalBean;
    }

    /**
     * Create a new renewal opportuinty.
     *
     * @return Opportunity
     */
    public function createNewRenewalOpportunity(): Opportunity
    {
        $copyOpFields = [
            'name',
            'assigned_user_id',
            'assigned_user_name',
            'team_id',
            'team_set_id',
            'acl_team_set_id',
        ];

        $newBean = BeanFactory::newBean($this->getModuleName());
        $newBean->renewal = 1;
        $newBean->renewal_parent_id = $this->id;

        foreach ($copyOpFields as $field) {
            if (isset($this->$field)) {
                $newBean->$field = $this->$field;
            }
        }

        $duplicates = $newBean->findDuplicates();
        if (!empty($duplicates['records'])) {
            // check if its renewal
            foreach ($duplicates['records'] as $opp) {
                if (!empty($opp->renewal) && $opp->renewal_parent_id === $this->id) {
                    return $opp;
                }
            }
        }

        $newBean->save();

        if ($newBean->load_relationship('accounts')) {
            $newBean->accounts->add([$this->account_id]);
        }

        return $newBean;
    }

    /**
     * Create a new renewal RLI from an existing RLI.
     *
     * @param RevenueLineItem $rli
     * @return RevenueLineItem
     */
    public function createNewRenewalRLI(RevenueLineItem $rli): RevenueLineItem
    {
        $copyRliFields = [
            'name',
            'account_id',
            'product_template_id',
            'category_id',
            'tax_class',
            'likely_case',
            'currency_id',
            'base_rate',
            'quantity',
            'list_price',
            'cost_price',
            'discount_price',
            'renewable',
            'service',
            'service_duration_value',
            'service_duration_unit',
            'assigned_user_id',
            'team_id',
            'team_set_id',
            'acl_team_set_id',
        ];

        $newRliBean = BeanFactory::newBean($rli->getModuleName());
        $timeDate = TimeDate::getInstance();
        $newStartDate = $timeDate->fromDbDate($rli->service_end_date)->modify('+1 day')->asDbDate();
        $newRliBean->service_start_date = $newStartDate;
        $newRliBean->date_closed = $newStartDate;
        $newRliBean->product_type = 'Existing Business';
        $newRliBean->opportunity_id = $this->id;

        foreach ($copyRliFields as $field) {
            if (isset($rli->$field)) {
                $newRliBean->$field = $rli->$field;
            }
        }

        $newRliBean->save();

        if ($this->load_relationship('revenuelineitems')) {
            $this->revenuelineitems->add($newRliBean);
        }

        return $newRliBean;
    }

    /**
     * Updates the non-SugarLogic rollup fields on the Opportunity
     *
     * @return $this
     * @throws SugarQueryException
     */
    public function updateRLIRollupFields()
    {
        $settings = Opportunity::getSettings();
        $rliMode = isset($settings['opps_view_by']) && $settings['opps_view_by'] === 'RevenueLineItems';
        if (!empty($this->id) && $rliMode) {
            $rollupFields = [
                'service_start_date' => $this->calculateOpportunityServiceStartDate(),
            ];

            // Update the Opportunity with the calculated rollup values. If any
            // values have changed on the Opportunity, then save it afterward
            $shouldSave = false;
            foreach ($rollupFields as $field => $calculatedValue) {
                if ($this->$field !== $calculatedValue) {
                    $this->$field = $calculatedValue;
                    $shouldSave = true;
                }
            }
            if ($shouldSave) {
                $this->save();
            }
        }

        return $this;
    }

    /**
     * Runs a DB query to calculate the rollup value for the Service Start Date
     * field from the related RLIs
     *
     * @return string containing the calculated Service Start Date
     * @throws SugarQueryException
     */
    private function calculateOpportunityServiceStartDate()
    {
        $closedWonStages = $this->getRliClosedWonStages();
        $closedLostStages = $this->getRliClosedLostStages();

        // Build the case statement for the query. This will be used to order the
        // query results so that open RLIs come before closed-won
        $quotedWonStages = implode(',', $this->getQuotedStringArray($closedWonStages));
        $caseStatement = 'CASE WHEN sales_stage IN (' . $quotedWonStages . ') THEN 1 ELSE 0 END';

        // Get the earliest Service Start Date of a non-closed-lost service RLI
        // related to the Opportunity. If any of the related service RLIs are
        // open, their value takes precedence over closed-won service RLIs.
        $q = new SugarQuery();
        $q->from(BeanFactory::newBean('RevenueLineItems'));
        $q->select(['service_start_date'])
            ->fieldRaw($caseStatement, 'is_closed');
        $q->where()->queryAnd()
            ->equals('opportunity_id', $this->id)
            ->equals('service', 1)
            ->notIn('sales_stage', $closedLostStages);
        $q->orderByRaw('is_closed', 'ASC');
        $q->orderBy('service_start_date', 'ASC');
        $result = $q->getDBManager()->fromConvert($q->getOne(), 'date');

        return !empty($result) ? $result : '';
    }

    /**
     * Adds proper DB quotation to an array of strings for use in SQL queries
     * @param array $array the array of strings to quote
     * @return array an array of the passed-in strings quoted correctly for the DB
     */
    private function getQuotedStringArray(array $array): array
    {
        $db = DBManagerFactory::getInstance();
        $quotedArray = [];
        foreach ($array as $key => $value) {
            $quotedArray[] = $db->quoted($value);
        }
        return $quotedArray;
    }
    //END SUGARCRM flav=ent ONLY
}
