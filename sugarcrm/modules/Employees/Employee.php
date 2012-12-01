<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End Employee License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or employee interface.
 * All copies of the Covered Code must include on each employee interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: Employee.php 53498 2010-01-06 23:42:10Z clee $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


require_once('include/SugarObjects/templates/person/Person.php');

// Employee is used to store customer information.
class Employee extends Person {
	// Stored fields
	var $name = '';
	var $id;
	var $is_admin;
	var $first_name;
	var $last_name;
	var $full_name;
	var $user_name;
	var $title;
	var $description;
	var $department;
	var $reports_to_id;
	var $reports_to_name;
	var $phone_home;
	var $phone_mobile;
	var $phone_work;
	var $phone_other;
	var $phone_fax;
	var $email1;
	var $email2;
	var $address_street;
	var $address_city;
	var $address_state;
	var $address_postalcode;
	var $address_country;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $created_by;
	var $created_by_name;
	var $modified_by_name;
	var $status;
	var $messenger_id;
	var $messenger_type;
	var $employee_status;
	var $error_string;

	var $module_dir = "Employees";

	//BEGIN SUGARCRM flav=pro ONLY
	var $default_team;
	//END SUGARCRM flav=pro ONLY

	var $table_name = "users";

	var $object_name = "Employee";
	var $user_preferences;

	var $encodeFields = Array("first_name", "last_name", "description");

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('reports_to_name');



	var $new_schema = true;

    /**
     * This is a depreciated method, please start using __construct() as this method will be removed in a future version
     *
     * @see __construct
     * @deprecated
     */
    public function Employee()
    {
        $this->__construct();
    }

	public function __construct() {
		parent::__construct();
		$this->setupCustomFields('Users');
		//BEGIN SUGARCRM flav=pro ONLY
		$this->disable_row_level_security =true;
		//END SUGARCRM flav=pro ONLY
		$this->emailAddress = BeanFactory::getBean('EmailAddresses');
	}


	function get_summary_text() {
        $this->_create_proper_name_field();
        return $this->name;
    }


	function fill_in_additional_list_fields() {
		$this->fill_in_additional_detail_fields();
	}

	function fill_in_additional_detail_fields()
	{
		global $locale;
		$query = "SELECT u1.first_name, u1.last_name from users u1, users u2 where u1.id = u2.reports_to_id AND u2.id = '$this->id' and u1.deleted=0";
		$result =$this->db->query($query, true, "Error filling in additional detail fields") ;

		$row = $this->db->fetchByAssoc($result);

		if($row != null)
		{
			$this->reports_to_name = stripslashes($locale->getLocaleFormattedName($row['first_name'], $row['last_name']));
		}
		else
		{
			$this->reports_to_name = '';
		}
		parent::fill_in_additional_detail_fields();
	}


	/**
	 * @return -- returns a list of all employees in the system.
	 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
	 * All Rights Reserved..
	 * Contributor(s): ______________________________________..
	 */
	function verify_data()
	{
		//none of the checks from the users module are valid here since the user_name and
		//is_admin_on fields are not editable.
		return TRUE;
	}

	function get_list_view_data(){

        global $current_user;
		$this->_create_proper_name_field(); // create proper NAME (by combining first + last)
		$user_fields = $this->get_list_view_array();
		// Copy over the reports_to_name
		if ( isset($GLOBALS['app_list_strings']['messenger_type_dom'][$this->messenger_type]) )
            $user_fields['MESSENGER_TYPE'] = $GLOBALS['app_list_strings']['messenger_type_dom'][$this->messenger_type];
		if ( isset($GLOBALS['app_list_strings']['employee_status_dom'][$this->employee_status]) )
            $user_fields['EMPLOYEE_STATUS'] = $GLOBALS['app_list_strings']['employee_status_dom'][$this->employee_status];
		$user_fields['REPORTS_TO_NAME'] = $this->reports_to_name;
		$user_fields['NAME'] = empty($this->name) ? '' : $this->name;
		$user_fields['EMAIL1'] = $this->emailAddress->getPrimaryAddress($this,$this->id,'Users');
		$this->email1 = $user_fields['EMAIL1'];
        $user_fields['EMAIL1_LINK'] = $current_user->getEmailLink('email1', $this, '', '', 'ListView');
		return $user_fields;
	}

	function list_view_parse_additional_sections(&$list_form, $xTemplateSection){
		return $list_form;
	}

	//BEGIN SUGARCRM flav=pro ONLY
	/**
	 * When the user's reports to id is changed, this method is called.  This method needs to remove all
	 * of the implicit assignements that were created based on this user, then recreated all of the implicit
	 * assignments in the new location
	 */

	function update_team_memberships($old_reports_to_id)
	{

		$team = BeanFactory::getBean('Teams');
		$team->user_manager_changed($this->id, $old_reports_to_id, $this->reports_to_id);
	}
	//END SUGARCRM flav=pro ONLY

	function create_export_query($order_by, $where) {
		include('modules/Employees/field_arrays.php');

		$cols = '';
		foreach($fields_array['Employee']['export_fields'] as $field) {
			$cols .= (empty($cols)) ? '' : ', ';
			$cols .= $field;
		}

		$query = "SELECT {$cols} FROM users ";

		$where_auto = " users.deleted = 0";

		if($where != "")
			$query .= " WHERE $where AND " . $where_auto;
		else
			$query .= " WHERE " . $where_auto;

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY users.user_name";

		return $query;
	}

	//BEGIN SUGARCRM flav=int ONLY
	//C.L. - Comment this out... the parent class Person has the proper implementation
	//and the method is now protected so either make this protected as well or just
	//END SUGARCRM flav=int ONLY
	//use parent class
	/**
	 * Generate the name field from the first_name and last_name fields.
	 */
	/*
	function _create_proper_name_field() {
        global $locale;
        $full_name = $locale->getLocaleFormattedName($this->first_name, $this->last_name);
        $this->name = $full_name;
        $this->full_name = $full_name;
	}
	*/

	function preprocess_fields_on_save(){
		parent::preprocess_fields_on_save();

		//BEGIN SUGARCRM flav!=com ONLY
        require_once('include/upload_file.php');
		$upload_file = new UploadFile("picture");

		//remove file
		if (isset($_REQUEST['remove_imagefile_picture']) && $_REQUEST['remove_imagefile_picture'] == 1)
		{
			UploadFile::unlink_file($this->picture);
			$this->picture="";
		}

		//uploadfile
		if (isset($_FILES['picture']))
		{
			//confirm only image file type can be uploaded
			$imgType = array('image/gif', 'image/png', 'image/bmp', 'image/jpeg', 'image/jpg', 'image/pjpeg');
			if (in_array($_FILES['picture']["type"], $imgType))
			{
				if ($upload_file->confirm_upload())
				{
					$this->picture = create_guid();
					$upload_file->final_move($this->picture);
				}
			}
		}
		//END SUGARCRM flav!=com ONLY
	}


    /**
     * create_new_list_query
     *
     * Return the list query used by the list views and export button. Next generation of create_new_list_query function.
     *
     * We overrode this function in the Employees module to add the additional filter check so that we do not retrieve portal users for the Employees list view queries
     *
     * @param string $order_by custom order by clause
     * @param string $where custom where clause
     * @param array $filter Optioanal
     * @param array $params Optional     *
     * @param int $show_deleted Optional, default 0, show deleted records is set to 1.
     * @param string $join_type
     * @param boolean $return_array Optional, default false, response as array
     * @param object $parentbean creating a subquery for this bean.
     * @param boolean $singleSelect Optional, default false.
     * @return String select query string, optionally an array value will be returned if $return_array= true.
     */
    function create_new_list_query($order_by, $where, $filter=array(), $params=array(), $show_deleted=0, $join_type='', $return_array=false, $parentbean=null, $singleSelect=false)
    {
        //create the filter for portal only users, as they should not be showing up in query results
        if(empty($where)){
            $where = ' users.portal_only = 0 ';
        }else{
            $where .= ' and users.portal_only = 0 ';
        }

        //return parent method, specifying for array to be returned
        return parent::create_new_list_query($order_by, $where, $filter,$params, $show_deleted, $join_type, $return_array, $parentbean, $singleSelect);
    }

    /*
     * Overwrite Sugar bean which returns the current objects custom fields.  Lets return User custom fields instead
     */
    function hasCustomFields()
    {

        //Check to see if there are custom user fields that we should report on, first check the custom_fields array
        $userCustomfields = !empty($GLOBALS['dictionary']['Employee']['custom_fields']);
        if(!$userCustomfields){
            //custom Fields not set, so traverse employee fields to see if any custom fields exist
            foreach ($GLOBALS['dictionary']['Employee']['fields'] as $k=>$v){
                if(!empty($v['source']) && $v['source'] == 'custom_fields'){
                    //custom field has been found, set flag to true and break
                    $userCustomfields = true;
                    break;
                }

            }
        }

        //return result of search for custom fields
        return $userCustomfields;
    }
}

?>
