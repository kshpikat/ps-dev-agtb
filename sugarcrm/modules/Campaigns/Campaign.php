<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
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
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Campaign.php 56965 2010-06-15 17:57:35Z jenny $
 * Description:
 ********************************************************************************/

class Campaign extends SugarBean {
	var $field_name_map;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $assigned_user_id;
	var $created_by;
	var $created_by_name;
    var $currency_id;
	var $modified_by_name;
	//BEGIN SUGARCRM flav=pro ONLY
	var $team_id;
	var $team_name;
	//END SUGARCRM flav=pro ONLY
	var $name;
	var $start_date;
	var $end_date;
	var $status;
	var $expected_cost;
	var $budget;
	var $actual_cost;
	var $expected_revenue;
	var $campaign_type;
	var $objective;
	var $content;
	var $tracker_key;
	var $tracker_text;
	var $tracker_count;
	var $refer_url;
    var $impressions;

	// These are related
	var $assigned_user_name;

	// module name definitions and table relations
	var $table_name = "campaigns";
	var $rel_prospect_list_table = "prospect_list_campaigns";
	var $object_name = "Campaign";
	var $module_dir = 'Campaigns';
	var $importable = true;

  	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = array(
				'assigned_user_name', 'assigned_user_id',
	);

	var $relationship_fields = Array('prospect_list_id'=>'prospect_lists');

	var $new_schema = true;

	function list_view_parse_additional_sections(&$listTmpl) {
		global $locale;

		// take $assigned_user_id and get the Username value to assign
		$assId = $this->getFieldValue('assigned_user_id');

		$query = "SELECT first_name, last_name FROM users WHERE id = '".$assId."'";
		$result = $this->db->query($query);
		$user = $this->db->fetchByAssoc($result);

		//_ppd($user);
		if(!empty($user)) {
			$fullName = $locale->getLocaleFormattedName($user->first_name, $user->last_name);
			$listTmpl->assign('ASSIGNED_USER_NAME', $fullName);
		}
	}


	function get_summary_text()
	{
		return $this->name;
	}

        function create_export_query(&$order_by, &$where, $relate_link_join='')
        {
        	$custom_join = $this->custom_fields->getJOIN(true, true,$where);
			if($custom_join)
				$custom_join['join'] .= $relate_link_join;
            $query = "SELECT
            campaigns.*,
            users.user_name as assigned_user_name ";
            //BEGIN SUGARCRM flav=pro ONLY
			$query .= ", teams.name AS team_name ";
			//END SUGARCRM flav=pro ONLY
        	if($custom_join){
				$query .=  $custom_join['select'];
			}
	        $query .= " FROM campaigns ";
			//BEGIN SUGARCRM flav=pro ONLY
			// We need to confirm that the user is a member of the team of the item.
			$this->add_team_security_where_clause($query);
			//END SUGARCRM flav=pro ONLY
			$query .= "LEFT JOIN users
                      ON campaigns.assigned_user_id=users.id";
           	//BEGIN SUGARCRM flav=pro ONLY
			$query .= getTeamSetNameJoin('campaigns');
			//END SUGARCRM flav=pro ONLY
        	if($custom_join){
				$query .=  $custom_join['join'];
			}

		$where_auto = " campaigns.deleted=0";

        if($where != "")
                $query .= " where $where AND ".$where_auto;
        else
                $query .= " where ".$where_auto;

        if($order_by != "")
                $query .= " ORDER BY $order_by";
        else
                $query .= " ORDER BY campaigns.name";
        return $query;
    }



	function clear_campaign_prospect_list_relationship($campaign_id, $prospect_list_id='')
	{
		if(!empty($prospect_list_id))
			$prospect_clause = " and prospect_list_id = '$prospect_list_id' ";
		else
			$prospect_clause = '';

		$query = "DELETE FROM $this->rel_prospect_list_table WHERE campaign_id='$campaign_id' AND deleted = '0' " . $prospect_clause;
	 	$this->db->query($query, true, "Error clearing campaign to prospect_list relationship: ");
	}



	function mark_relationships_deleted($id)
	{
		$this->clear_campaign_prospect_list_relationship($id);
	}

	function fill_in_additional_list_fields()
	{
		parent::fill_in_additional_list_fields();
	}

	function fill_in_additional_detail_fields()
	{
        parent::fill_in_additional_detail_fields();
		//format numbers.

		//don't need additional formatting here.
		//$this->budget=format_number($this->budget);
		//$this->expected_cost=format_number($this->expected_cost);
		//$this->actual_cost=format_number($this->actual_cost);
		//$this->expected_revenue=format_number($this->expected_revenue);
	}


	function update_currency_id($fromid, $toid){
	}


	function get_list_view_data(){

		$temp_array = $this->get_list_view_array();
		if ($this->campaign_type != 'Email') {
			$temp_array['OPTIONAL_LINK']="display:none";
		}
		$temp_array['TRACK_CAMPAIGN_TITLE'] = translate("LBL_TRACK_BUTTON_TITLE",'Campaigns');
		$temp_array['TRACK_CAMPAIGN_IMAGE'] = SugarThemeRegistry::current()->getImageURL('view_status.gif');
		$temp_array['LAUNCH_WIZARD_TITLE'] = translate("LBL_TO_WIZARD_TITLE",'Campaigns');
		$temp_array['LAUNCH_WIZARD_IMAGE'] = SugarThemeRegistry::current()->getImageURL('edit_wizard.gif');
        $temp_array['TRACK_VIEW_ALT_TEXT'] = translate("LBL_TRACK_BUTTON_TITLE",'Campaigns');
        $temp_array['LAUNCH_WIZ_ALT_TEXT'] = translate("LBL_TO_WIZARD_TITLE",'Campaigns');

		return $temp_array;
	}
	/**
		builds a generic search based on the query string using or
		do not include any $this-> because this is called on without having the class instantiated
	*/
	function build_generic_where_clause ($the_query_string)
	{
		$where_clauses = Array();
		$the_query_string = $this->db->quote($the_query_string);
		array_push($where_clauses, "campaigns.name like '$the_query_string%'");

		$the_where = "";
		foreach($where_clauses as $clause)
		{
			if($the_where != "") $the_where .= " or ";
			$the_where .= $clause;
		}


		return $the_where;
	}

	function save($check_notify = FALSE) {

			//US DOLLAR
			if(isset($this->amount) && !empty($this->amount)){

				$currency = new Currency();
				$currency->retrieve($this->currency_id);
				$this->amount_usdollar = $currency->convertToDollar($this->amount);

			}

		$this->unformat_all_fields();

		return parent::save($check_notify);

	}


	function mark_deleted($id){
        $query = "update contacts set campaign_id = null where campaign_id = '{$id}' ";
        $this->db->query($query);
		return parent::mark_deleted($id);
	}

	function set_notification_body($xtpl, $camp)
	{
		$xtpl->assign("CAMPAIGN_NAME", $camp->name);
		$xtpl->assign("CAMPAIGN_AMOUNT", $camp->budget);
		$xtpl->assign("CAMPAIGN_CLOSEDATE", $camp->end_date);
		$xtpl->assign("CAMPAIGN_STATUS", $camp->status);
		$xtpl->assign("CAMPAIGN_DESCRIPTION", $camp->content);

		return $xtpl;
	}

	function track_log_entries($type=array()) {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id ='';

		$this->load_relationship('log_entries');
		$query_array = $this->log_entries->getQuery(true);

        //if one of the arguments is marketing ID, then we need to filter by it
        foreach($args as $arg){
            if(isset($arg['EMAIL_MARKETING_ID_VALUE'])){
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }

            if(isset($arg['group_by'])) {
            	$query_array['group_by'] = $arg['group_by'];
            }
        }



		if (empty($type))
			$type[0]='targeted';

		$query_array['select'] ="SELECT campaign_log.* ";
		$query_array['where'] = $query_array['where']. " AND activity_type='{$type[0]}' AND archived=0";
        //add filtering by marketing id, if it exists
        if (!empty($mkt_id)) $query_array['where'] = $query_array['where']. " AND marketing_id ='$mkt_id' ";

        //B.F. #37943
        if( isset($query_array['group_by']))
        {
			//perform the inner join with the group by if a marketing id is defined, which means we need to filter out duplicates.
			//if no marketing id is specified then we are displaying results from multiple marketing emails and it is understood there might be duplicate target entries
			if (!empty($mkt_id)){
				$group_by = str_replace("campaign_log", "cl", $query_array['group_by']);
				$join_where = str_replace("campaign_log", "cl", $query_array['where']);
				$query_array['from'] .= " INNER JOIN (select min(id) as id from campaign_log cl $join_where GROUP BY $group_by  ) secondary
					on campaign_log.id = secondary.id	";
			}
            unset($query_array['group_by']);
        } else if(isset($query_array['group_by'])) {
           $query_array['where'] = $query_array['where'] . ' GROUP BY ' . $query_array['group_by'];
           unset($query_array['group_by']);
        }

        $query = (implode(" ",$query_array));
        return $query;
	}


	function get_queue_items() {
        //get arguments being passed in
        $args = func_get_args();
        $mkt_id ='';

        $this->load_relationship('queueitems');
		$query_array = $this->queueitems->getQuery(true);

        //if one of the arguments is marketing ID, then we need to filter by it
        foreach($args as $arg){
            if(isset($arg['EMAIL_MARKETING_ID_VALUE'])){
                $mkt_id = $arg['EMAIL_MARKETING_ID_VALUE'];
            }

            if(isset($arg['group_by'])) {
            	$query_array['group_by'] = $arg['group_by'];
            }
        }

        //add filtering by marketing id, if it exists, and if where key is not empty
        if (!empty($mkt_id) && !empty($query_array['where'])){
             $query_array['where'] = $query_array['where']. " AND marketing_id ='$mkt_id' ";
        }

		//get select query from email man
		$man = new EmailMan();
		$listquery= $man->create_queue_items_query('',str_replace(array("WHERE","where"),"",$query_array['where']),null,$query_array);
		return $listquery;

	}
//	function get_prospect_list_entries() {
//		$this->load_relationship('prospectlists');
//		$query_array = $this->prospectlists->getQuery(true);
//
//		$query=<<<EOQ
//			SELECT distinct prospect_lists.*,
//			(case  when (email_marketing.id is null) then default_message.id else email_marketing.id end) marketing_id,
//			(case  when  (email_marketing.id is null) then default_message.name else email_marketing.name end) marketing_name
//
//			FROM prospect_lists
//
//			INNER JOIN prospect_list_campaigns ON (prospect_lists.id=prospect_list_campaigns.prospect_list_id AND prospect_list_campaigns.campaign_id='{$this->id}')
//
//			LEFT JOIN email_marketing on email_marketing.message_for = prospect_lists.id and email_marketing.campaign_id = '{$this->id}'
//			and email_marketing.deleted =0 and email_marketing.status='active'
//
//			LEFT JOIN email_marketing default_message on default_message.message_for = prospect_list_campaigns.campaign_id and
//			default_message.campaign_id = '{$this->id}' and default_message.deleted =0
//			and default_message.status='active'
//
//			WHERE prospect_list_campaigns.deleted=0 AND prospect_lists.deleted=0
//
//EOQ;
//		return $query;
//	}

	 function bean_implements($interface){
		switch($interface){
			case 'ACL':return true;
		}
		return false;
	}


	/**
	 * create_list_count_query
	 * Overrode this method from SugarBean to handle the distinct parameter used to filter out
	 * duplicate entries for some of the subpanel listivews.  Without the distinct filter, the
	 * list count would be inaccurate because one-to-many email_marketing entries may be associated
	 * with a campaign.
     *
     * @param string $query Select query string
     * @param array $param array of arguments
     * @return string count query
     *
	 */
    function create_list_count_query($query, $params=array())
    {
		//include the distinct filter if a marketing id is defined, which means we need to filter out duplicates by the passed in group by.
		//if no marketing id is specified, it is understood there might be duplicate target entries so no need to filter out
		if((strpos($query,'marketing_id') !== false )&& isset($params['distinct'])) {
		   $pattern = '/SELECT(.*?)(\s){1}FROM(\s){1}/is';  // ignores the case
    	   $replacement = 'SELECT COUNT(DISTINCT ' . $params['distinct'] . ') c FROM ';
    	   $query = preg_replace($pattern, $replacement, $query, 1);
    	   return $query;
		}

		//If distinct parameter not found, default to SugarBean's function
    	return parent::create_list_count_query($query);
    }
}
?>