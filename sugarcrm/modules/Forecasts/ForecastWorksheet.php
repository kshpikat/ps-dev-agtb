<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

class ForecastWorksheet extends SugarBean
{

    public $id;
    public $worksheet_id;
    public $currency_id;
    public $base_rate;
    public $args;
    public $name;
    public $commit_stage;
    public $probability;
    public $best_case;
    public $likely_case;
    public $worst_case;
    public $sales_stage;
    public $product_id;
    public $assigned_user_id;
    public $timeperiod_id;
    public $draft = 0; // default to 0, it will be set to 1 by the args that get passed in;
    public $parent_type;
    public $parent_id;
    public $object_name = 'ForecastWorksheet';
    public $module_name = 'ForecastWorksheets';
    public $module_dir = 'Forecasts';
    public $table_name = 'forecast_worksheets';
    public $disable_custom_fields = true;

    /**
     * Override save here to handle saving to the real tables.  Currently forecast is mapped to opportunities
     * and likely_case, worst_case and best_case go to both worksheets and opportunities.
     *
     *
     * @param bool $check_notify        Should we send the notifications
     * @return string                   SugarGUID for the Worksheet that was modified or created
     */
    public function saveWorksheet($check_notify = false)
    {
        $commitForecast = true;
        if ($this->draft == 1) {
            $commitForecast = false;
        }

        $opp_id = $this->findOpportunityId();

        //Update the Opportunities bean -- should update the product line item as well through SaveOverload.php
        /* @var $opp Opportunity */
        $opp = BeanFactory::getBean('Opportunities', $opp_id);
        $opp->probability = $this->probability;
        $opp->best_case = $this->best_case;
        $opp->amount = $this->likely_case;
        $opp->sales_stage = $this->sales_stage;
        $opp->commit_stage = $this->commit_stage;
        $opp->worst_case = $this->worst_case;
        $opp->commit_stage = $this->commit_stage;
        $opp->save($check_notify);

        if ($commitForecast) {
            // find the product
            /* @var $product Product */
            $product = BeanFactory::getBean('Products');
            $product->retrieve_by_string_fields(array(
                    'opportunity_id'=>$opp->id
                ));

            /**
             * This is required for 6.7.  This could be removed
             */
            //Update the Worksheet bean
            /* @var $worksheet Worksheet */
            $worksheet = BeanFactory::getBean('Worksheet');
            $worksheet->retrieve_by_string_fields(array(
                    'related_id' => $product->id,
                    'related_forecast_type' => 'Product',
                    'forecast_type' => 'Direct'
            ));
            $worksheet->timeperiod_id = $this->timeperiod_id;
            $worksheet->user_id = $this->assigned_user_id;
            $worksheet->best_case = $this->best_case;
            $worksheet->likely_case = $this->likely_case;
            $worksheet->worst_case = $this->worst_case;
            $worksheet->op_probability = $this->probability;
            $worksheet->commit_stage = $this->commit_stage;
            $worksheet->forecast_type = 'Direct';
            $worksheet->related_forecast_type = 'Product';
            $worksheet->related_id = $product->id;
            $worksheet->currency_id = $this->currency_id;
            $worksheet->base_rate = $this->base_rate;
            $worksheet->version = 1; // default it to 1 as it will always be on since this is always
            $worksheet->save($check_notify);
        }

        //return $worksheet->id;
    }

    /**
     * Find the Opportunity Id for this worksheet
     *
     * @return bool|string      Return the SugarGUID for the opportunity if found, otherwise, return false
     */
    protected function findOpportunityId()
    {
        // check parent type
        if($this->parent_type == 'Opportunities') {
            return $this->parent_id;
        } else if ($this->parent_type == 'Products') {
            // load the product to get the opp id
            /* @var $product Product */
            $product = BeanFactory::getBean('Products', $this->parent_id);

            if($product->id == $this->parent_id) {
                return $product->opportunity_id;
            }
        }

        // this should never happen.
        return false;
    }

    /**
     * Sets Worksheet args so that we save the supporting tables.
     * @param array $args Arguments passed to save method through PUT
     */
    public function setWorksheetArgs($args)
    {
        // save the args variable
        $this->args = $args;

        // loop though the args and assign them to the corresponding key on the object
        foreach ($args as $arg_key => $arg) {
            $this->$arg_key = $arg;
        }
    }

    /**
     * Save an Opportunity as a worksheet
     *
     * @param Opportunity $opp      The Opportunity that we want to save a snapshot of
     * @param bool $isDraft         Is the Opportunity a Draft or the live version
     */
    public function saveRelatedOpportunity(Opportunity $opp, $isDraft = false)
    {
        $this->retrieve_by_string_fields(
            array(
                'parent_type' => 'Opportunities',
                'parent_id' => $opp->id,
                'draft' => ($isDraft) ? 1 : 0,
                'deleted' => 0,
            )
        );

        $fields = array(
            'name',
            'account_id',
            'amount',
            array('likely_case' => 'amount'),
            'best_case',
            'base_rate',
            'worst_case',
            'amount_usdollar',
            'currency_id',
            'date_closed',
            'date_closed_timestamp',
            'sales_stage',
            'probability',
            'commit_stage',
            'assigned_user_id',
            'created_by',
            'date_entered',
            'deleted',
            'team_id',
            'team_set_id'
        );

        $this->copyValues($fields, $opp);

        // set the parent types
        $this->parent_type = 'Opportunities';
        $this->parent_id = $opp->id;
        $this->draft = ($isDraft) ? 1 : 0;

        $this->save(false);
    }

    public function saveRelatedProduct(Product $product, $isDraft = false)
    {
        $this->retrieve_by_string_fields(
            array(
                'parent_type' => 'Products',
                'parent_id' => $product->id,
                'draft' => ($isDraft) ? 1 : 0,
                'deleted' => 0,
            )
        );

        // since we don't have sales_stage in 6.7 we need to pull it from the related opportunity
        /* @var $opp Opportunity */
        $product->sales_stage = '';
        $opp = BeanFactory::getBean('Opportunities', $product->opportunity_id);
        if($opp instanceof Opportunity) {
            $product->sales_stage = $opp->sales_stage;
        }

        $fields = array(
            'name',
            'account_id',
            array('amount' => 'likely_case'),
            'likely_case',
            'best_case',
            'base_rate',
            'worst_case',
            array('amount_usdollar' => 'cost_usdollar'),
            'currency_id',
            'date_closed',
            'date_closed_timestamp',
            'probability',
            'commit_stage',
            'sales_stage',
            'assigned_user_id',
            'created_by',
            'date_entered',
            'deleted',
            'team_id',
            'team_set_id'
        );

        $this->copyValues($fields, $product);

        // set the parent types
        $this->parent_type = 'Products';
        $this->parent_id = $product->id;
        $this->draft = ($isDraft) ? 1 : 0;

        $this->save(false);
    }

    /**
     * Copy the fields from the $seed bean to the worksheet object
     *
     * @param array $fields
     * @param SugarBean $seed
     */
    protected function copyValues($fields, SugarBean $seed)
    {
        foreach ($fields as $field) {
            $key = $field;
            if (is_array($field)) {
                // if we have an array it should be a key value pair, where the key is the destination value and the value,
                // is the seed value
                $key = array_shift(array_keys($field));
                $field = array_shift($field);
            }
            // make sure the field is set, as not to cause a notice since a field might get unset() from the $seed class
            if(isset($seed->$field)) {
                $this->$key = $seed->$field;
            }
        }
    }

    public static function reassignForecast($fromUserId, $toUserId)
    {
        global $current_user;

        $db = DBManagerFactory::getInstance();

        // reassign Opportunities
        $_object = BeanFactory::getBean('Opportunities');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}'";
        $res = $db->query($_query, true);
        $affected_rows = $db->getAffectedRowCount($res);

        // Products
        // reassign only products that have related opportunity - products created from opportunity::save()
        // other products will be reassigned if module Product is selected by user
        $_object = BeanFactory::getBean('Products');
        $_query = "update {$_object->table_name} set " .
            "assigned_user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.assigned_user_id = '{$fromUserId}' and {$_object->table_name}.opportunity_id IS NOT NULL ";
        $db->query($_query, true);

        // delete Forecasts
        $_object = BeanFactory::getBean('Forecasts');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // delete Expected Opportunities
        $_object = BeanFactory::getBean('ForecastSchedule');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // delete Quotas
        $_object = BeanFactory::getBean('Quotas');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}'";
        $db->query($_query, true);

        // clear reports_to for inactive users
        $objFromUser = BeanFactory::getBean('Users');
        $objFromUser->retrieve($fromUserId);
        $fromUserReportsTo = !empty($objFromUser->reports_to_id) ? $objFromUser->reports_to_id : '';
        $objFromUser->reports_to_id = '';
        $objFromUser->save();

        if (User::isManager($fromUserId)) {
            // setup report_to for user
            $objToUserId = BeanFactory::getBean('Users');
            $objToUserId->retrieve($toUserId);
            $objToUserId->reports_to_id = $fromUserReportsTo;
            $objToUserId->save();

            // reassign users (reportees)
            $_object = BeanFactory::getBean('Users');
            $_query = "update {$_object->table_name} set " .
                "reports_to_id = '{$toUserId}', " .
                "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
                "modified_user_id = '{$current_user->id}' " .
                "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.reports_to_id = '{$fromUserId}' " .
                "and {$_object->table_name}.id != '{$toUserId}'";
            $db->query($_query, true);
        }

        // Worksheets
        // reassign worksheets for products (opportunities)
        $_object = BeanFactory::getBean('Worksheet');
        $_query = "update {$_object->table_name} set " .
            "user_id = '{$toUserId}', " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 and {$_object->table_name}.user_id = '{$fromUserId}' ";
        $db->query($_query, true);

        // delete worksheet where related_id is user id - rollups
        $_object = BeanFactory::getBean('Worksheet');
        $_query = "update {$_object->table_name} set " .
            "deleted = 1, " .
            "date_modified = '" . TimeDate::getInstance()->nowDb() . "', " .
            "modified_user_id = '{$current_user->id}' " .
            "where {$_object->table_name}.deleted = 0 " .
            "and {$_object->table_name}.forecast_type = 'Rollup' and {$_object->table_name}.related_forecast_type = 'Direct' " .
            "and {$_object->table_name}.related_id = '{$fromUserId}' ";
        $db->query($_query, true);

        //todo: forecast_tree

        return $affected_rows;
    }
}
