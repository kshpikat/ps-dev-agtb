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
 * (i) the "Powered by SugarCRM" logo and 
 * (ii) the SugarCRM copyright notice 
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer 
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.  
 ********************************************************************************/
/*********************************************************************************
 * $Id: view.edit.php 
 * Description: This file is used to override the default Meta-data EditView behavior
 * to provide customization specific to the Contacts module.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

require_once('include/MVC/View/views/view.detail.php');

class DCEActionsViewDetail extends ViewDetail {
   
 	function DCEctionsViewDetail(){
 		parent::ViewDetail();
 	}
 	
 	function preDisplay(){
        parent::preDisplay();
        $logs_array=explode("\n", $this->bean->logs);
        // show first 100 lines of log and create a link to download the full log
        if(count($logs_array)>100){
            $logs_array=array_slice($logs_array, 0, 100);
            $this->bean->logs="<span class='error'>[".translate('LBL_LOGS_DETAIL','DCEActions')."]</span>\n\n";
            $this->bean->logs.=implode("\n", $logs_array);
            $this->bean->logs.="\n\n <a href='index.php?module=DCEActions&action=downloadLogs&record={$this->bean->id}'><b>Download the complete log</b></a>";
        }
 	}
}

?>