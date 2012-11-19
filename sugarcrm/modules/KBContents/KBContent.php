<?php
if(!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
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
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2007 SugarCRM, Inc.; All Rights Reserved.
/*********************************************************************************
 * $Id: KBContent.php 20504 2007-02-28 00:18:41 +0000 (Wed, 28 Feb 2007) vineet $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/


// User is used to store Forecast information.
class KBContent extends SugarBean {

	var $id;
    var $kbdocument_body;
	var $created_by;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	var $document_revision_id;
	//BEGIN SUGARCRM flav=pro ONLY
	var $team_id;
	//END SUGARCRM flav=pro ONLY
	var $active_date;
	var $exp_date;	
	var $table_name = "kbcontents";
	var $object_name = "KBContent";
	var $encodeFields = Array ();

	var $new_schema = true;
	var $module_dir = 'KBContents';
	 

    /**
     * This is a depreciated method, please start using __construct() as this method will be removed in a future version
     *
     * @see __construct
     * @depreciated
     */
    public function KBContent()
    {
        $this->__construct();
    }

	public function __construct() {
		parent::__construct();
		$this->setupCustomFields('KBContents'); //parameter is module name
		$this->disable_row_level_security = false;
	}

	function save($check_notify = false) {
		return parent::save($check_notify);
	}
	
	function retrieve($id, $encode = false) {
		$ret = parent::retrieve($id, $encode);
		return $ret;
	}

	function is_authenticated() {
		return $this->authenticated;
	}
	function mark_relationships_deleted($id) {
		//do nothing, this call is here to avoid default delete processing since  
		//delete.php handles deletion of document revisions.
	}
	
	
}
?>