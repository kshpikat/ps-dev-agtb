<?php
/*********************************************************************************
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2013 SugarCRM Inc.  All rights reserved.
 ********************************************************************************/
require_once 'modules/UpgradeWizard/SidecarUpdate/SidecarGridMetaDataUpgrader.php';

class SidecarMergeGridMetaDataUpgrader extends SidecarGridMetaDataUpgrader
{

    /**
     * Composite views
     * @var array
     */
    protected $mergeViews = array(
        MB_RECORDVIEW => array(
            'detail' => array('detailviewdefs', MB_DETAILVIEW),
            'edit' => array('editviewdefs', MB_EDITVIEW),
        ),
    );

    protected $viewPanels = array(
        MB_RECORDVIEW => array(
            MB_DETAILVIEW => 'LBL_RECORD_BODY',
            MB_EDITVIEW => 'LBL_RECORD_SHOWMORE'
        ),
    );

    /**
     * Sets the necessary legacy field defs for use in converting
     */
    public function setLegacyViewdefs()
    {
        if(empty($this->mergeViews[$this->viewtype])) {
            return;
        }

        $dirname = dirname($this->fullpath);
        // Load all views for this combined view
        foreach($this->mergeViews[$this->viewtype] as $view => $data) {
            unset($module_name);
            list($file, $lViewtype) = $data;
            $filepath = "$dirname/$file.php";
            if(!file_exists($filepath)) {
                continue;
            }

            include $filepath;
            // There is an odd case where custom modules are pathed without the
            // package name prefix but still use it in the module name for the
            // viewdefs. This handles that case. Also sets a prop that lets the
            // rest of the process know that the module is named differently
            if (isset($module_name)) {
                $this->modulename = $module = $module_name;
            } else {
                $module = $this->module;
            }

            $var = $this->variableMap[$this->client][$view];
            if (isset($$var)) {
                $defs = $$var;
                if (isset($this->vardefIndexes[$this->client.$view])) {
                    $index = $this->vardefIndexes[$this->client.$view];
                    $this->legacyViewdefs[$lViewtype] = empty($index) ? $defs[$module] : $defs[$module][$index];
                }
            }
        }
    }

    /**
     * Converts the legacy Grid metadata to Sidecar style
     */
    public function convertLegacyViewDefsToSidecar()
    {
        $this->logUpgradeStatus('Converting ' . $this->client . ' ' . $this->viewtype . ' view defs for ' . $this->module);

        // TODO: if it's a custom module, will throw, we should use template instead
        $parser = ParserFactory::getParser($this->viewtype, $this->module, null, null, $this->client);

        // Go through merge views, add fields added to detail view to base panel
        // and fields added to edit view not in detail view ot hidden panel
        $customFields = array();
        foreach($this->legacyViewdefs as $lViewtype => $data) {
            if(empty($data['panels'])) {
                continue;
            }
            $legacyParser = ParserFactory::getParser($lViewtype, $this->module);

            foreach($legacyParser->getFieldsFromPanels($data['panels']) as $fieldname => $fielddef) {
                if(empty($fieldname) || isset($customFields[$fieldname])) {
                    continue;
                }
                $customFields[$fieldname] = array('data' => $fielddef, 'source' => $lViewtype);
            }
        }

        // Hack: we've moved email1 to email
        if(isset($customFields['email1'])) {
            $customFields['email'] = $customFields['email1'];
            unset($customFields['email1']);
        }

        $origFields = array();
        $origData = $parser->getFieldsFromPanels($parser->convertToCanonicalForm($parser->_viewdefs['panels'], $parser->_fielddefs));
        // Go through existing fields and remove those not in the new data
        foreach($origData as $fname => $fielddef) {
            if(isset($fielddef['type'])) {
                if($fielddef['type'] != 'fieldset') {
                    // special-case fields can be ignored for now
                    continue;
                }
                // fieldsets - iterate over each field
                $setExists = false;
                foreach($fielddef['fields'] as $setfielddef) {
                    if(!is_array($setfielddef)) {
                        $setfname = $setfielddef;
                    } else {
                        // skip werid nameless ones
                        if(empty($setfielddef['name'])) continue;
                        $setfname = $setfielddef['name'];
                    }
                    // if we have one field - we take all set
                    if(isset($customFields[$setfname])) {
                        $setExists = true;
                        break;
                    }
                }
                if($setExists) {
                    // if fields exist, we take all the set as existing fields
                    foreach($fielddef['fields'] as $setfielddef) {
                        if(!is_array($setfielddef)) {
                            $setfname = $setfielddef;
                        } else {
                            // skip werid nameless ones
                            if(empty($setfielddef['name'])) continue;
                            $setfname = $setfielddef['name'];
                        }
                        $origFields[$setfname] = $fielddef;
                    }
                } else {
                    // else we delete the set
                    $parser->removeField($fname);
                }
            } else {
                // if it's a regular field, check against existing field in new data
                if(!isset($customFields[$fname])) {
                    // not there - remove it
                    $parser->removeField($fname);
                } else {
                    // otherwise - keep as existing
                    $origFields[$fname] = $fielddef;
                }
            }
        }

        // now go through new fields and add those not in original data
        foreach($customFields as $fieldname => $data) {
            if(isset($origFields[$fieldname])) {
                // TODO: merge the data such as label, readonly, etc.
                continue;
            } else {
                // TODO: import more data than just name
                $parser->addField(array('name' => $fieldname), $this->viewPanels[$this->viewtype][$data['source']]);
            }
        }

        $newdefs = $parser->_viewdefs;
        $newdefs['panels'] = $parser->convertToCanonicalForm($parser->_viewdefs['panels'] ,$parser->_fielddefs);

        $this->sidecarViewdefs[$this->module][$this->client]['view'][MetaDataFiles::getName($this->viewtype)] = $newdefs;
   }
}