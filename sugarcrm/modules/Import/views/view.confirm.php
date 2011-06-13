<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Enterprise Subscription
 * Agreement ("License") which can be viewed at
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
 * by SugarCRM are Copyright (C) 2004-2007 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: view.step1.php 31561 2008-02-04 18:41:10Z jmertic $
 * Description: view handler for step 1 of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
require_once('include/MVC/View/SugarView.php');
require_once('modules/Import/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');

require_once('include/upload_file.php');

class ImportViewConfirm extends SugarView
{
    const SAMPLE_ROW_SIZE = 3;

 	/**
     * @see SugarView::getMenu()
     */
    public function getMenu(
        $module = null
        )
    {
        global $mod_strings, $current_language;
        
        if ( empty($module) )
            $module = $_REQUEST['import_module'];
        
        $old_mod_strings = $mod_strings;
        $mod_strings = return_module_language($current_language, $module);
        $returnMenu = parent::getMenu($module);
        $mod_strings = $old_mod_strings;
        
        return $returnMenu;
    }
    
 	/**
     * @see SugarView::_getModuleTab()
     */
 	protected function _getModuleTab()
    {
        global $app_list_strings, $moduleTabMap;
        
 		// Need to figure out what tab this module belongs to, most modules have their own tabs, but there are exceptions.
        if ( !empty($_REQUEST['module_tab']) )
            return $_REQUEST['module_tab'];
        elseif ( isset($moduleTabMap[$_REQUEST['import_module']]) )
            return $moduleTabMap[$_REQUEST['import_module']];
        // Default anonymous pages to be under Home
        elseif ( !isset($app_list_strings['moduleList'][$_REQUEST['import_module']]) )
            return 'Home';
        else
            return $_REQUEST['import_module'];
 	}
 	
 	/**
	 * @see SugarView::_getModuleTitleParams()
	 */
	protected function _getModuleTitleParams($browserTitle = false)
	{
	    global $mod_strings, $app_list_strings;
	    
	    $iconPath = $this->getModuleTitleIconPath($this->module);
	    $returnArray = array();
	    if (!empty($iconPath) && !$browserTitle) {
	        $returnArray[] = "<a href='index.php?module={$_REQUEST['import_module']}&action=index'><img src='{$iconPath}' alt='{$app_list_strings['moduleList'][$_REQUEST['import_module']]}' title='{$app_list_strings['moduleList'][$_REQUEST['import_module']]}' align='absmiddle'></a>";
    	}
    	else {
    	    $returnArray[] = $app_list_strings['moduleList'][$_REQUEST['import_module']];
    	}
	    $returnArray[] = "<a href='index.php?module=Import&action=Step1&import_module={$_REQUEST['import_module']}'>".$mod_strings['LBL_MODULE_NAME']."</a>";
	    $returnArray[] = $mod_strings['LBL_CONFIRM_TITLE'];
    	
	    return $returnArray;
    }
    
 	/** 
     * @see SugarView::display()
     */
 	public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        global $sugar_config, $locale;


        $this->ss->assign("IMPORT_MODULE", $_REQUEST['import_module']);
        $this->ss->assign("TYPE",( !empty($_REQUEST['type']) ? $_REQUEST['type'] : "import" ));
        $this->ss->assign("SOURCE", $_REQUEST['source']);
        $this->ss->assign("SOURCE_ID", $_REQUEST['source_id']);
        $this->ss->assign("MODULE_TITLE", $this->getModuleTitle());
        $has_header = ( isset( $_REQUEST['has_header']) ? 1 : 0 );
        $sugar_config['import_max_records_per_file'] = ( empty($sugar_config['import_max_records_per_file']) ? 1000 : $sugar_config['import_max_records_per_file'] );

        // Clear out this user's last import
        $seedUsersLastImport = new UsersLastImport();
        $seedUsersLastImport->mark_deleted_by_user_id($current_user->id);
        ImportCacheFiles::clearCacheFiles();

        $this->ss->assign("CUSTOM_DELIMITER", ( !empty($_REQUEST['custom_delimiter']) ? $_REQUEST['custom_delimiter'] : "," ));
        $this->ss->assign("CUSTOM_ENCLOSURE", ( !empty($_REQUEST['custom_enclosure']) ? $_REQUEST['custom_enclosure'] : "" ));

        // handle uploaded file
        $uploadFile = new UploadFile('userfile');
        if (isset($_FILES['userfile']) && $uploadFile->confirm_upload())
        {

            //file extension should be set to csv ONLY
            $uploadedFileExtension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
            if(empty($uploadedFileExtension) || $uploadedFileExtension != 'csv' ){
                //if the extension is not .csv then return error message
                $this->_showImportError($mod_strings['LBL_IMPORT_ERROR_MIME_TYPE'],$_REQUEST['import_module'],'Step2');
                return;
            }

            $uploadFile->final_move('IMPORT_'.$this->bean->object_name.'_'.$current_user->id);
            $uploadFileName = $uploadFile->get_upload_path('IMPORT_'.$this->bean->object_name.'_'.$current_user->id);
        }
        else {
            $this->_showImportError($mod_strings['LBL_IMPORT_MODULE_ERROR_NO_UPLOAD'],$_REQUEST['import_module'],'Step2');
            return;
        }

        $this->ss->assign("FILE_NAME", $uploadFileName);

        // Now parse the file and look for errors
        $importFile = new ImportFile( $uploadFileName, $_REQUEST['custom_delimiter'], html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES), FALSE);

        if ( !$importFile->fileExists() ) {
            $this->_showImportError($mod_strings['LBL_CANNOT_OPEN'],$_REQUEST['import_module'],'Step2');
            return;
        }

         //Check if we will exceed the maximum number of records allowed per import.
         $maxRecordsExceeded = FALSE;
         $maxRecordsWarningMessg = "";
         $lineCount = $importFile->getNumberOfLinesInfile();
         $maxLineCount = isset($sugar_config['import_max_records_total_limit'] ) ? $sugar_config['import_max_records_total_limit'] : 5000;
         if( !empty($maxLineCount) && ($lineCount > $maxLineCount) )
         {
             $maxRecordsExceeded = TRUE;
             $maxRecordsWarningMessg = string_format($mod_strings['LBL_IMPORT_ERROR_MAX_REC_LIMIT_REACHED'], array($lineCount, $maxLineCount) );
         }

        //Retrieve a sample set of data
        $rows = array();
        for($i=0; $i < self::SAMPLE_ROW_SIZE; $i++)
        {
            $rows[] = $importFile->getNextRow();
        }

        $charset_for_import = $importFile->autoDetectCharacterSet();

        // Charset
        $charsetOptions = get_select_options_with_id( $locale->getCharsetSelect(), $charset_for_import);//wdong,  bug 25927, here we should use the charset testing results from above.
        $this->ss->assign('CHARSETOPTIONS', $charsetOptions);

        $this->ss->assign("SAMPLE_ROWS",$rows);
        $this->ss->assign("JAVASCRIPT", $this->_getJS($maxRecordsExceeded, $maxRecordsWarningMessg ));
        $this->ss->display('modules/Import/tpls/confirm.tpl');
    }
    
    /**
     * Returns JS used in this view
     */
    private function _getJS($maxRecordsExceeded, $maxRecordsWarningMessg )
    {
        global $mod_strings;
        $maxRecordsExceededJS = $maxRecordsExceeded?"true":"false";
        return <<<EOJAVASCRIPT
<script type="text/javascript">

document.getElementById('goback').onclick = function(){
    document.getElementById('importconfirm').action.value = 'Step2';
    return true;
}

document.getElementById('gonext').onclick = function(){
    document.getElementById('importconfirm').action.value = 'Step3';
    return true;
}

document.getElementById('custom_enclosure').onchange = function()
{
    document.getElementById('importconfirm').custom_enclosure_other.style.display = ( this.value == 'other' ? '' : 'none' );
}

YAHOO.util.Event.onDOMReady(function(){
    if($maxRecordsExceededJS)
    {
        var contImport = confirm('$maxRecordsWarningMessg');
        if(!contImport)
        {
            var module = document.getElementById('importconfirm').import_module.value;
            var source = document.getElementById('importconfirm').source.value;
            var returnUrl = "index.php?module=Import&action=Step2&import_module=" + module + "&source=" + source;
            document.location.href = returnUrl;
        }
    }

    function refreshDataTable(e)
    {
        var callback = {
          success: function(o) {
            document.getElementById('confirm_table').innerHTML = o.responseText;
          },
          failure: function(o) {},
        };

        var importFile = document.getElementById('importconfirm').file_name.value;
        var fieldDelimeter = document.getElementById('custom_delimiter').value;
        var fieldQualifier = document.getElementById('custom_enclosure').value;
        if(fieldQualifier == 'other' && this.id == 'custom_enclosure')
        {
            return;
        }
        else if( fieldQualifier == 'other' )
        {
            fieldQualifier = document.getElementById('custom_enclosure_other').value;
        }

        var url = 'index.php?action=RefreshMapping&module=Import&importFile=' + importFile
                    + '&delim=' + fieldDelimeter + '&qualif=' + fieldQualifier;

        YAHOO.util.Connect.asyncRequest('GET', url, callback);
    }
    YAHOO.util.Event.addListener("custom_delimiter", "change", refreshDataTable);
    YAHOO.util.Event.addListener("custom_enclosure", "change", refreshDataTable);
    YAHOO.util.Event.addListener("custom_enclosure_other", "change", refreshDataTable);

});
</script>

EOJAVASCRIPT;
    }
}

?>
