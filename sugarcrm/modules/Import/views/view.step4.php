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
 * $Id: view.step4.php 31561 2008-02-04 18:41:10Z jmertic $
 * Description: view handler for step 4 of the import process
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 ********************************************************************************/
 
require_once('include/MVC/View/SugarView.php');
require_once('modules/Import/ImportFile.php');
require_once('modules/Import/ImportFileSplitter.php');
require_once('modules/Import/ImportCacheFiles.php');
require_once('modules/Import/ImportFieldSanitize.php');
require_once('modules/Import/ImportDuplicateCheck.php');

class ImportViewStep4 extends SugarView 
{
    private $currentStep;

    /**
     * @var ImportFieldSanitizer
     */
    private $ifs;

    /**
     * @var Currency
     */
    private $defaultUserCurrency;

    public function __construct($bean = null, $view_object_map = array())
    {
        parent::__construct($bean, $view_object_map);
        $this->currentStep = isset($_REQUEST['current_step']) ? ($_REQUEST['current_step'] + 1) : 1;

        // set the default locale settings
        $this->ifs = $this->getFieldSanitizer();

        //Get the default user currency
        $this->defaultUserCurrency = new Currency();
        $this->defaultUserCurrency->retrieve('-99');
    }
    /** 
     * @see SugarView::display()
     */
 	public function display()
    {
        global $sugar_config;
        
        // Increase the max_execution_time since this step can take awhile
        ini_set("max_execution_time", max($sugar_config['import_max_execution_time'],3600));
        
        // stop the tracker
        TrackerManager::getInstance()->pause();
        
        // use our own error handler
        set_error_handler(array('ImportViewStep4','handleImportErrors'),E_ALL);
        
        global $mod_strings, $app_strings, $current_user, $import_bean_map;
        global $app_list_strings, $timedate;
        
        $update_only = ( isset($_REQUEST['import_type']) && $_REQUEST['import_type'] == 'update' );
        $firstrow    = unserialize(base64_decode($_REQUEST['firstrow']));

        // loop through all request variables
        $importColumns = $this->getImportColumns();

        // Check to be sure we are getting an import file that is in the right place
        if ( realpath(dirname($_REQUEST['tmp_file']).'/') != realpath($sugar_config['upload_dir']) )
            trigger_error($mod_strings['LBL_CANNOT_OPEN'],E_USER_ERROR);
        
        // Open the import file
        $importFile = new ImportFile($_REQUEST['tmp_file'],$_REQUEST['custom_delimiter'],html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES));
        
        if ( !$importFile->fileExists() )
            trigger_error($mod_strings['LBL_CANNOT_OPEN'],E_USER_ERROR);

        
        $fieldDefs = $this->bean->getFieldDefinitions();
        
        while ( $row = $importFile->getNextRow() )
        {
            $focus = clone $this->bean;
            $focus->unPopulateDefaultValues();
            $focus->save_from_post = false;
            $focus->team_id = null;
            $this->ifs->createdBeans = array();

            $do_save = true;

            for ( $fieldNum = 0; $fieldNum < $_REQUEST['columncount']; $fieldNum++ )
            {
                // loop if this column isn't set
                if ( !isset($importColumns[$fieldNum]) )
                    continue;

                // get this field's properties
                $field           = $importColumns[$fieldNum];
                $fieldDef        = $focus->getFieldDefinition($field);
                $fieldTranslated = translate((isset($fieldDef['vname'])?$fieldDef['vname']:$fieldDef['name']),
                    $_REQUEST['module'])." (".$fieldDef['name'].")";

                // Bug 37241 - Don't re-import over a field we already set during the importing of another field
                if ( !empty($focus->$field) )
                    continue;


                //DETERMINE WHETHER OR NOT $fieldDef['name'] IS DATE_MODIFIED AND SET A VAR, USE DOWN BELOW

                // translate strings
                global $locale;
                if(empty($locale))
                {
                    $locale = new Localization();
                }
                if ( isset($row[$fieldNum]) )
                    $rowValue = $locale->translateCharset(strip_tags(trim($row[$fieldNum])),$_REQUEST['importlocale_charset'],$sugar_config['default_charset']);
                else
                    $rowValue = '';

                // If there is an default value then use it instead
                if ( !empty($_REQUEST[$field]) )
                {
                    $defaultRowValue = $this->populateDefaultMapValue($field, $_REQUEST[$field], $fieldDef);

                    //BEGIN SUGARCRM flav=pro ONLY
                    if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset' && empty($rowValue))
                    {
                        require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                        $sugar_field = new SugarFieldTeamset('Teamset');
                        $rowValue = implode(', ',$sugar_field->getTeamsFromRequest($field));
                    }
                    //END SUGARCRM flav=pro ONLY

                    if( empty($rowValue))
                    {
                        $rowValue = $defaultRowValue;
                        unset($defaultRowValue);
                    }
                }

                // Bug 22705 - Don't update the First Name or Last Name value if Full Name is set
                if ( in_array($field, array('first_name','last_name')) && !empty($focus->full_name) )
                    continue;

                // loop if this value has not been set
                if ( !isset($rowValue) )
                    continue;

                // If the field is required and blank then error out
                if ( array_key_exists($field,$focus->get_import_required_fields()) && empty($rowValue) && $rowValue!='0')
                {
                    $importFile->writeError( $mod_strings['LBL_REQUIRED_VALUE'],$fieldTranslated,'NULL');
                    $do_save = false;
                }

                // Handle the special case "Sync to Outlook"
                if ( $focus->object_name == "Contacts" && $field == 'sync_contact' )
                {
                    $bad_names = array();
                    $returnValue = $this->ifs->synctooutlook($rowValue,$fieldDef,$bad_names);
                    // try the default value on fail
                    if ( !$returnValue && !empty($defaultRowValue) )
                        $returnValue = $this->ifs->synctooutlook($defaultRowValue, $fieldDef, $bad_names);
                    if ( !$returnValue )
                    {
                        $importFile->writeError($mod_strings['LBL_ERROR_SYNC_USERS'], $fieldTranslated, explode(",",$bad_names));
                        $do_save = 0;
                    }
                }

                // Handle email1 and email2 fields ( these don't have the type of email )
                if ( $field == 'email1' || $field == 'email2' )
                {
                    $returnValue = $this->ifs->email($rowValue, $fieldDef, $focus);
                    // try the default value on fail
                    if ( !$returnValue && !empty($defaultRowValue) )
                        $returnValue = $this->ifs->email( $defaultRowValue, $fieldDef);
                    if ( $returnValue === FALSE )
                    {
                        $do_save=0;
                        $importFile->writeError( $mod_strings['LBL_ERROR_INVALID_EMAIL'], $fieldTranslated, $rowValue);
                    }
                    else
                    {
                        $rowValue = $returnValue;
                        // check for current opt_out and invalid email settings for this email address
                        // if we find any, set them now
                        $emailres = $focus->db->query( "SELECT opt_out, invalid_email FROM email_addresses WHERE email_address = '".$focus->db->quote($rowValue)."'");
                        if ( $emailrow = $focus->db->fetchByAssoc($emailres) )
                        {
                            $focus->email_opt_out = $emailrow['opt_out'];
                            $focus->invalid_email = $emailrow['invalid_email'];
                        }
                    }
                }

                // Handle splitting Full Name into First and Last Name parts
                if ( $field == 'full_name' && !empty($rowValue) )
                {
                    $this->ifs->fullname($rowValue,$fieldDef,$focus);
                }

                // to maintain 451 compatiblity
                if(!isset($fieldDef['module']) && $fieldDef['type']=='relate')
                    $fieldDef['module'] = ucfirst($fieldDef['table']);

                if(isset($fieldDef['custom_type']) && !empty($fieldDef['custom_type']))
                    $fieldDef['type'] = $fieldDef['custom_type'];

                // If the field is empty then there is no need to check the data
                if( !empty($rowValue) )
                {
                    switch ($fieldDef['type'])
                    {
                        case 'enum':
                        case 'multienum':
                            if ( isset($fieldDef['type']) && $fieldDef['type'] == "multienum" )
                                $returnValue = $this->ifs->multienum($rowValue,$fieldDef);
                            else
                                $returnValue = $this->ifs->enum($rowValue,$fieldDef);
                            // try the default value on fail
                            if ( !$returnValue && !empty($defaultRowValue) )
                                if ( isset($fieldDef['type']) && $fieldDef['type'] == "multienum" )
                                    $returnValue = $this->ifs->multienum($defaultRowValue,$fieldDef);
                                else
                                    $returnValue = $this->ifs->enum($defaultRowValue,$fieldDef);
                            if ( $returnValue === FALSE )
                            {
                                $importFile->writeError($mod_strings['LBL_ERROR_NOT_IN_ENUM'] . implode(",",$app_list_strings[$fieldDef['options']]), $fieldTranslated,$rowValue);
                                $do_save = 0;
                            }
                            else
                                $rowValue = $returnValue;

                            break;
                        case 'relate':
                        case 'parent':
                            $returnValue = $this->ifs->relate($rowValue, $fieldDef, $focus, empty($defaultRowValue));
                            if (!$returnValue && !empty($defaultRowValue))
                                $returnValue = $this->ifs->relate($defaultRowValue,$fieldDef, $focus);
                            // Bug 33623 - Set the id value found from the above method call as an importColumn
                            if ($returnValue !== false)
                                $importColumns[] = $fieldDef['id_name'];
                            break;
                        case 'teamset':
                            $returnValue = $this->ifs->teamset($rowValue,$fieldDef,$focus);
                            $importColumns[] = 'team_set_id';
                            $importColumns[] = 'team_id';
                            break;
                        case 'fullname':
                            break;
                        default:
                            $fieldtype = $fieldDef['type'];
                            $returnValue = $this->ifs->$fieldtype($rowValue, $fieldDef, $focus);
                            // try the default value on fail
                            if ( !$returnValue && !empty($defaultRowValue) )
                                $returnValue = $this->ifs->$fieldtype($defaultRowValue,$fieldDef, $focus);
                            if ( !$returnValue ) {
                                $do_save=0;
                                $importFile->writeError($mod_strings['LBL_ERROR_INVALID_'.strtoupper($fieldDef['type'])],$fieldTranslated,$rowValue,$focus);
                            }
                            else {
                                $rowValue = $returnValue;
                            }
                    }
                }
                $focus->$field = $rowValue;
                unset($defaultRowValue);
            }

            // Now try to validate flex relate fields
            if ( isset($focus->field_defs['parent_name']) && isset($focus->parent_name) && ($focus->field_defs['parent_name']['type'] == 'parent') )
            {
                // populate values from the picker widget if the import file doesn't have them
                $parent_idField = $focus->field_defs['parent_name']['id_name'];
                if ( empty($focus->$parent_idField) && !empty($_REQUEST[$parent_idField]) )
                    $focus->$parent_idField = $_REQUEST[$parent_idField];

                $parent_typeField = $focus->field_defs['parent_name']['type_name'];

                if ( empty($focus->$parent_typeField) && !empty($_REQUEST[$parent_typeField]) )
                    $focus->$parent_typeField = $_REQUEST[$parent_typeField];
                // now validate it
                $returnValue = $this->ifs->parent($focus->parent_name,$focus->field_defs['parent_name'],$focus, empty($_REQUEST['parent_name']));
                if ( !$returnValue && !empty($_REQUEST['parent_name']) )
                    $returnValue = $this->ifs->parent( $_REQUEST['parent_name'],$focus->field_defs['parent_name'], $focus);
            }

            // check to see that the indexes being entered are unique.
            if (isset($_REQUEST['enabled_dupes']) && $_REQUEST['enabled_dupes'] != "")
            {
                $toDecode = html_entity_decode  ($_REQUEST['enabled_dupes'], ENT_QUOTES);
                $enabled_dupes = json_decode($toDecode);
                $idc = new ImportDuplicateCheck($focus);

                if ( $idc->isADuplicateRecord($enabled_dupes) )
                {
                    $importFile->markRowAsDuplicate();
                    $this->_undoCreatedBeans($this->ifs->createdBeans);
                    continue;
                }
            }

            // if the id was specified
            $newRecord = true;
            if ( !empty($focus->id) )
            {
                $focus->id = $this->_convertId($focus->id);

                // check if it already exists
                $query = "SELECT * FROM {$focus->table_name} WHERE id='".$focus->db->quote($focus->id)."'";
                $result = $focus->db->query($query)
                            or sugar_die("Error selecting sugarbean: ");

                $dbrow = $focus->db->fetchByAssoc($result);

                if (isset ($dbrow['id']) && $dbrow['id'] != -1)
                {
                    // if it exists but was deleted, just remove it
                    if (isset ($dbrow['deleted']) && $dbrow['deleted'] == 1 && $update_only==false)
                    {
                        $this->removeDeletedBean($focus);
                        $focus->new_with_id = true;
                    }
                    else
                    {
                        if( !$update_only )
                        {
                            $importFile->writeError($mod_strings['LBL_ID_EXISTS_ALREADY'],'ID',$focus->id);
                            $this->_undoCreatedBeans($this->ifs->createdBeans);
                            continue;
                        }

                        $clonedBean = $this->cloneExistingBean($focus, $importColumns);
                        if($clonedBean === FALSE)
                        {
                            $importFile->writeError($mod_strings['LBL_RECORD_CANNOT_BE_UPDATED'],'ID',$focus->id);
                            $this->_undoCreatedBeans($this->ifs->createdBeans);
                            continue;
                        }
                        else
                        {
                            $focus = $clonedBean;
                            $newRecord = FALSE;
                        }
                    }
                }
                else
                {
                    $focus->new_with_id = true;
                }
            }

            if ($do_save)
            {
                $this->saveImportBean($focus, $newRecord);
                // Update the created/updated counter
                $importFile->markRowAsImported($newRecord);
            }
            else
                $this->_undoCreatedBeans($this->ifs->createdBeans);

            unset($defaultRowValue);
        }
        //End while loop


        // save mapping if requested
        if ( isset($_REQUEST['save_map_as']) && $_REQUEST['save_map_as'] != '' )
        {
            $this->saveMappingFile($importColumns, $focus);
        }
        
        $importFile->writeStatus();
    }

    protected function cloneExistingBean($focus, $importColumns)
    {
        $existing_focus = clone $this->bean;
        if ( !( $existing_focus->retrieve($focus->id) instanceOf SugarBean ) )
        {
            return FALSE;
        }
        else
        {
            $newData = $focus->toArray();
            foreach ( $newData as $focus_key => $focus_value )
                if ( in_array($focus_key,$importColumns) )
                    $existing_focus->$focus_key = $focus_value;

            return $existing_focus;
        }
    }

    protected function removeDeletedBean($focus)
    {
        global $mod_strings;

        $query2 = "DELETE FROM {$focus->table_name} WHERE id='".$focus->db->quote($focus->id)."'";
        $result2 = $focus->db->query($query2) or sugar_die($mod_strings['LBL_ERROR_DELETING_RECORD']." ".$focus->id);
        if ($focus->hasCustomFields())
        {
            $query3 = "DELETE FROM {$focus->table_name}_cstm WHERE id_c='".$focus->db->quote($focus->id)."'";
            $result2 = $focus->db->query($query3);
        }
    }


    protected function saveImportBean($focus, $newRecord)
    {
        global $timedate, $current_user;

        // Populate in any default values to the bean
        $focus->populateDefaultValues();

        if ( !isset($focus->assigned_user_id) || $focus->assigned_user_id == '' && $newRecord )
        {
            $focus->assigned_user_id = $current_user->id;
        }
        //BEGIN SUGARCRM flav=pro ONLY
        if ( !isset($focus->team_id) || $focus->team_id == '' && $newRecord )
        {
            $focus->team_id = $current_user->default_team;
        }
        //END SUGARCRM flav=pro ONLY
        /*
        * Bug 34854: Added all conditions besides the empty check on date modified.
        */
        if ( ( !empty($focus->new_with_id) && !empty($focus->date_modified) ) ||
             ( empty($focus->new_with_id) && $timedate->to_db($focus->date_modified) != $timedate->to_db($timedate->to_display_date_time($focus->fetched_row['date_modified'])) )
        )
            $focus->update_date_modified = false;

        $focus->optimistic_lock = false;
        if ( $focus->object_name == "Contacts" && isset($focus->sync_contact) )
        {
            //copy the potential sync list to another varible
            $list_of_users=$focus->sync_contact;
            //and set it to false for the save
            $focus->sync_contact=false;
        }
        else if($focus->object_name == "User" && !empty($current_user) && $focus->is_admin && !is_admin($current_user) && is_admin_for_module($current_user, 'Users')) {
            sugar_die($GLOBALS['mod_strings']['ERR_IMPORT_SYSTEM_ADMININSTRATOR']);
        }
        //bug# 40260 setting it true as the module in focus is involved in an import
        $focus->in_import=true;
        // call any logic needed for the module preSave
        $focus->beforeImportSave();

        $focus->save(false);

        // call any logic needed for the module postSave
        $focus->afterImportSave();

        if ( $focus->object_name == "Contacts" && isset($list_of_users) )
            $focus->process_sync_to_outlook($list_of_users);

        // Add ID to User's Last Import records
        if ( $newRecord )
            ImportFile::writeRowToLastImport( $_REQUEST['import_module'],($focus->object_name == 'Case' ? 'aCase' : $focus->object_name),$focus->id);

    }


    protected function saveMappingFile($importColumns, $focus)
    {
        $mappingValsArr = $importColumns;
        $mapping_file = new ImportMap();
        if ( isset($_REQUEST['has_header']) && $_REQUEST['has_header'] == 'on')
        {
            $header_to_field = array ();
            foreach ($importColumns as $pos => $field_name)
            {
                if (isset($firstrow[$pos]) && isset($field_name))
                {
                    $header_to_field[$firstrow[$pos]] = $field_name;
                }
            }

            $mappingValsArr = $header_to_field;
        }
        //get array of values to save for duplicate and locale settings
        $advMapping = $this->retrieveAdvancedMapping();

        //merge with mappingVals array
        if(!empty($advMapping) && is_array($advMapping))
        {
            $mappingValsArr = array_merge($mappingValsArr,$advMapping);
        }

        //set mapping
        $mapping_file->setMapping($mappingValsArr);

        // save default fields
        $defaultValues = array();
        for ( $i = 0; $i < $_REQUEST['columncount']; $i++ )
        {
            if (isset($importColumns[$i]) && !empty($_REQUEST[$importColumns[$i]]))
            {
                $field = $importColumns[$i];
                $fieldDef = $focus->getFieldDefinition($field);
                if(!empty($fieldDef['custom_type']) && $fieldDef['custom_type'] == 'teamset')
                {
                    require_once('include/SugarFields/Fields/Teamset/SugarFieldTeamset.php');
                    $sugar_field = new SugarFieldTeamset('Teamset');
                    $teams = $sugar_field->getTeamsFromRequest($field);
                    if(isset($_REQUEST['primary_team_name_collection']))
                    {
                        $primary_index = $_REQUEST['primary_team_name_collection'];
                    }

                    //If primary_index was selected, ensure that the first Array entry is the primary team
                    if(isset($primary_index))
                    {
                        $count = 0;
                        $new_teams = array();
                        foreach($teams as $id=>$name)
                        {
                            if($primary_index == $count++)
                            {
                                $new_teams[$id] = $name;
                                unset($teams[$id]);
                                break;
                            }
                        }

                        foreach($teams as $id=>$name)
                        {
                            $new_teams[$id] = $name;
                        }
                        $teams = $new_teams;
                    } //if

                    $json = getJSONobj();
                    $defaultValues[$field] = $json->encode($teams);
                }
                else
                {
                    $defaultValues[$field] = $_REQUEST[$importColumns[$i]];
                }
            }
        }
        $mapping_file->setDefaultValues($defaultValues);
        $result = $mapping_file->save( $current_user->id,  $_REQUEST['save_map_as'], $_REQUEST['import_module'], $_REQUEST['source'],
            ( isset($_REQUEST['has_header']) && $_REQUEST['has_header'] == 'on'), $_REQUEST['custom_delimiter'], html_entity_decode($_REQUEST['custom_enclosure'],ENT_QUOTES)
        );
    }


    protected function populateDefaultMapValue($field, $fieldValue, $fieldDef)
    {
        global $timedate, $current_user;
        
        if ( is_array($fieldValue) )
            $defaultRowValue = encodeMultienumValue($fieldValue);
        else
            $defaultRowValue = $_REQUEST[$field];
        // translate default values to the date/time format for the import file
        if( $fieldDef['type'] == 'date' && $this->ifs->dateformat != $timedate->get_date_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->dateformat, $timedate->get_date_format());

        if( $fieldDef['type'] == 'time' && $this->ifs->timeformat != $timedate->get_time_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->timeformat, $timedate->get_time_format());

        if( ($fieldDef['type'] == 'datetime' || $fieldDef['type'] == 'datetimecombo') && $this->ifs->dateformat.' '.$this->ifs->timeformat != $timedate->get_date_time_format() )
            $defaultRowValue = $timedate->swap_formats($defaultRowValue, $this->ifs->dateformat.' '.$this->ifs->timeformat,$timedate->get_date_time_format());

        if ( in_array($fieldDef['type'],array('currency','float','int','num')) && $this->ifs->num_grp_sep != $current_user->getPreference('num_grp_sep') )
            $defaultRowValue = str_replace($current_user->getPreference('num_grp_sep'), $this->ifs->num_grp_sep,$defaultRowValue);

        if ( in_array($fieldDef['type'],array('currency','float')) && $this->ifs->dec_sep != $current_user->getPreference('dec_sep') )
            $defaultRowValue = str_replace($current_user->getPreference('dec_sep'), $this->ifs->dec_sep,$defaultRowValue);

        $user_currency_symbol = $this->defaultUserCurrency->symbol;
        if ( $fieldDef['type'] == 'currency' && $this->ifs->currency_symbol != $user_currency_symbol )
            $defaultRowValue = str_replace($user_currency_symbol, $this->ifs->currency_symbol,$defaultRowValue);

        return $defaultRowValue;
    }
    protected function getImportColumns()
    {
        $importable_fields = $this->bean->get_importable_fields();
        $importColumns = array();
        foreach ($_REQUEST as $name => $value)
        {
            // only look for var names that start with "fieldNum"
            if (strncasecmp($name, "colnum_", 7) != 0)
                continue;

            // pull out the column position for this field name
            $pos = substr($name, 7);

            if ( isset($importable_fields[$value]) )
            {
                // now mark that we've seen this field
                $importColumns[$pos] = $value;
            }
        }

        return $importColumns;
    }

    protected function getFieldSanitizer()
    {
        $ifs = new ImportFieldSanitize();
        $ifs->dateformat = $_REQUEST['importlocale_dateformat'];
        $ifs->timeformat = $_REQUEST['importlocale_timeformat'];
        $ifs->timezone = $_REQUEST['importlocale_timezone'];
        $currency = new Currency();
        $currency->retrieve($_REQUEST['importlocale_currency']);
        $ifs->currency_symbol = $currency->symbol;
        $ifs->default_currency_significant_digits = $_REQUEST['importlocale_default_currency_significant_digits'];
        $ifs->num_grp_sep  = $_REQUEST['importlocale_num_grp_sep'];
        $ifs->dec_sep = $_REQUEST['importlocale_dec_sep'];
        $ifs->default_locale_name_format  = $_REQUEST['importlocale_default_locale_name_format'];

        return $ifs;
    }
    /**
     * If a bean save is not done for some reason, this method will undo any of the beans that were created
     *
     * @param array $ids ids of user_last_import records created
     */
    protected function _undoCreatedBeans( array $ids )
    {
        $focus = new UsersLastImport();
        foreach ($ids as $id)
            $focus->undoById($id);
    }
    
    /**
     * clean id's when being imported
     *
     * @param  string $string
     * @return string
     */
    protected function _convertId($string)
    {
        return preg_replace_callback(
            '|[^A-Za-z0-9\-]|',
            create_function(
            // single quotes are essential here,
            // or alternative escape all $ as \$
            '$matches',
            'return ord($matches[0]);'
                 ) ,
            $string);
    }
    
    /**
     * Replaces PHP error handler in Step4
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param string $errline
     */
     public static function handleImportErrors($errno, $errstr, $errfile, $errline)
     {
        if ( !defined('E_DEPRECATED') )
            define('E_DEPRECATED','8192');
        if ( !defined('E_USER_DEPRECATED') )
            define('E_USER_DEPRECATED','16384');

        // check to see if current reporting level should be included based upon error_reporting() setting, if not
        // then just return
        if ( !(error_reporting() & $errno) )
            return true;

        switch ($errno)
        {
            case E_USER_ERROR:
                echo "ERROR: [$errno] $errstr on line $errline in file $errfile<br />\n";
                exit(1);
                break;
            case E_USER_WARNING:
            case E_WARNING:
                echo "WARNING: [$errno] $errstr on line $errline in file $errfile<br />\n";
                break;
            case E_USER_NOTICE:
            case E_NOTICE:
                echo "NOTICE: [$errno] $errstr on line $errline in file $errfile<br />\n";
                break;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                // don't worry about these
                //echo "STRICT ERROR: [$errno] $errstr on line $errline in file $errfile<br />\n";
                break;
            default:
                echo "Unknown error type: [$errno] $errstr on line $errline in file $errfile<br />\n";
                break;
        }

        return true;
    }



    public function retrieveAdvancedMapping()
    {
        $advancedMappingSettings = array();

        //harvest the dupe index settings
        if( isset($_REQUEST['enabled_dupes']) )
        {
            $toDecode = html_entity_decode  ($_REQUEST['enabled_dupes'], ENT_QUOTES);
            $dupe_ind = json_decode($toDecode);

            foreach($dupe_ind as $dupe)
            {
                $advancedMappingSettings['dupe_'.$dupe] = $dupe;
            }
        }

        foreach($_REQUEST as $rk=>$rv)
        {
            //harvest the import locale settings
            if(strpos($rk,'portlocale_')>0)
            {
                $advancedMappingSettings[$rk] = $rv;
            }

        }
        return $advancedMappingSettings;
    }
    
}
