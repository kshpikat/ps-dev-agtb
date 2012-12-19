<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/products/sugar-enterprise-eula.html
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2006 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once('include/MVC/View/views/view.list.php');

class HomeViewList extends ViewList{
 	function ActivitiesViewList(){
 		parent::ViewList();
 		
 	}

 	function display(){
 		global $mod_strings, $export_module, $current_language, $theme, $current_user, $dashletData, $sugar_flavor;
         $this->processMaxPostErrors();
 		include('modules/Home/index.php');
 	}

    function processMaxPostErrors() {
        if($this->checkPostMaxSizeError()){
            $this->errors[] = $GLOBALS['app_strings']['UPLOAD_ERROR_HOME_TEXT'];
            $contentLength = $_SERVER['CONTENT_LENGTH'];

            $maxPostSize = ini_get('post_max_size');
            if (stripos($maxPostSize,"k"))
                $maxPostSize = (int) $maxPostSize * pow(2, 10);
            elseif (stripos($maxPostSize,"m"))
                $maxPostSize = (int) $maxPostSize * pow(2, 20);

            $maxUploadSize = ini_get('upload_max_filesize');
            if (stripos($maxUploadSize,"k"))
                $maxUploadSize = (int) $maxUploadSize * pow(2, 10);
            elseif (stripos($maxUploadSize,"m"))
                $maxUploadSize = (int) $maxUploadSize * pow(2, 20);

            $max_size = min($maxPostSize, $maxUploadSize);
            if ($contentLength > $max_size) {
                $errMessage = string_format($GLOBALS['app_strings']['UPLOAD_MAXIMUM_EXCEEDED'],array($contentLength,  $max_size));
            } else {
                $errMessage =$GLOBALS['app_strings']['UPLOAD_REQUEST_ERROR'];
            }

            $this->errors[] = '* '.$errMessage;
            $this->displayErrors();
        }
    }

}
?>
