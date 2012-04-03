<?php
//FILE SUGARCRM flav=ent ONLY
if (! defined ( 'sugarEntry' ) || ! sugarEntry)
    die ( 'Not A Valid Entry Point' ) ;
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
 * $Id: additionalDetails.php 13782 2006-06-06 17:58:55Z majed $
 *********************************************************************************/

require_once 'modules/ModuleBuilder/parsers/views/GridLayoutMetaDataParser.php' ;
require_once 'modules/ModuleBuilder/parsers/constants.php' ;

class  PortalGridLayoutMetaDataParser extends GridLayoutMetaDataParser
{

    static $variableMap = array (
    	MB_PORTALEDITVIEW => 'EditView' ,
    	MB_PORTALDETAILVIEW => 'DetailView' ,
    	) ;


    /*
    * Return the layout, padded out with (empty) and (filler) fields ready for display
    */
    public function getLayout ()
    {
//        $viewdefs = array () ;
//        $fielddefs = $this->_fielddefs;
//        $fielddefs [ $this->FILLER [ 'name' ] ] = $this->FILLER ;
//        $fielddefs [ MBConstants::$EMPTY [ 'name' ] ] = MBConstants::$EMPTY ;
//
//        foreach ( $this->_viewdefs [ 'panels' ] as $panelID => $panel )
//        {
//            foreach ( $panel as $rowID => $row )
//            {
//                foreach ( $row as $colID => $fieldname )
//                {
//                    if (isset ($this->_fielddefs [ $fieldname ]))
//                    {
//                        $viewdefs [ $panelID ] [ $rowID ] [ $colID ] = self::_trimFieldDefs( $this->_fielddefs [ $fieldname ] ) ;
//                    }
//                    elseif (isset($this->_originalViewDef [ $fieldname ]) && is_array($this->_originalViewDef [ $fieldname ]))
//                    {
//                        $viewdefs [ $panelID ] [ $rowID ] [ $colID ] = self::_trimFieldDefs( $this->_originalViewDef [ $fieldname ] ) ;
//                    }
//                    else
//                    {
//                        $viewdefs [ $panelID ] [ $rowID ] [ $colID ] = array("name" => $fieldname, "label" => $fieldname);
//                    }
//                }
//            }
//        }
        return $this->_viewdefs ;
    }


    /**
     * helper to pack a row with $cols members of [empty]
     * @param $row
     * @param $cols
     * @return void
     *
     */
    protected function _packRowWithEmpty(&$row, $cols)
    {
        for ($i=0; $i<$cols; $i++) {
            $row[] = MBConstants::$EMPTY;
        }
    }


    /*
     * helper methods for doing field comparisons
     */
    protected function isFiller($field)
    {
        if (is_array($field))  {
            return ($field == MBConstants::$FILLER);
        }

        return ($field == $this->FILLER['name']);
    }

    protected function isEmpty($field)
    {
        if (is_array($field))  {
            return ($field == MBConstants::$EMPTY);
        }

        return ($field == MBConstants::$EMPTY['name']);
    }

    protected function _addCell($field, $colspan)
    {
        if ($colspan > 1 && is_array($field)) {
            $field['displayParams']['colspan'] = $colspan;
        }
        return $field;
    }

    /**
     * here we convert from internal metadata format to file (canonical) metadata
     * @param $panels
     * @param $fielddefs
     * @return array - viewdefs in canonical file format
     */
    protected function _convertToCanonicalForm($panels , $fielddefs)
    {
        $canonicalPanels = array();

        foreach ($panels as $pName => $panel) {
            $fields = array();
            foreach ($panel as $row) {
                $offset = 1; // reset
                $lastField = null; // holder for the field to put in
                foreach ($row as $cell) {

                    // leading empty => should not occur, but assign to next field as colspan
                    $fieldName = isset($cell['name']) ? $cell['name'] : $cell;

                    // empty => get rid of it, and assign to previous field as colspan
                    if ($this->isEmpty($cell)) {
                        $offset++; // count our columns
                        continue;
                    }

                    // dump out the last field we stored and reset column count
                    if ($lastField !== null) {
                        $fields[] = $this->_addCell($lastField,$offset);
                        $offset = 1;
                    }

                    // filler => ''
                    if ($this->isFiller($cell)) {
                        $lastField = '';
                    }
                    else {
                        // field => add the field def.
                        $lastField = $this->getNewRowItem($cell, $fielddefs[$fieldName]);
                    }

                }

                // dump out the last field we stored
                if ($lastField !== null) {
                    $fields[] = $this->_addCell($lastField,$offset);
                }


            }
            $canonicalPanels[] = array('label' => $pName, 'fields' => $fields);
        }
        return $canonicalPanels;
    }

    /**
     * here we convert from file (canonical) metadata => internal metadata format
     * @param $panels
     * @param $fielddefs
     * @return array $internalPanels
     */
    protected function _convertFromCanonicalForm($panels , $fielddefs)
    {
        // canonical form has format:
        // $panels[n]['label'] = label for panel n
        //           ['fields'] = array of fields


        // internally we want:
        // $panels[label for panel] = fields of panel in rows,cols format

        $internalPanels = array();
        foreach ($panels as $n => $panel) {
            $pLabel = !empty($panel['label']) ? $panel['label'] : $n;

            // going from a list of fields to putting them in rows,cols format.
            $internalFieldRows = array();
            $row = array();
            foreach ($panel['fields'] as $field) {
                // try to find the column span of the field. It can range from 1 to max columns of the panel.
                $colspan = isset($field['displayParams']['colspan']) ? $field['displayParams']['colspan'] : 1;
                $colspan = min($colspan, $this->getMaxColumns()); // we can't put in a field wider than the panel.
                $cols_left = $this->getMaxColumns() - count($row);

                if ($cols_left < $colspan) {
                    // add $cols_left of (empty) to $row and put it in
                   $this->_packRowWithEmpty($row, $cols_left);
                   $internalFieldRows[] = $row;
                   $row = array();
                }

                // add field to row + enough (empty) to make it to colspan
                $row[] = empty($field) ? $this->FILLER : $field;
                $this->_packRowWithEmpty($row, $colspan-1);
            }

            // add the last incomplete row if necessary
            if (!empty($row)) {
                $cols_left = $this->getMaxColumns() - count($row);
                // add $cols_left of (empty) to $row and put it in
                $this->_packRowWithEmpty($row, $cols_left);
                $internalFieldRows[] = $row;
            }
            $internalPanels[$pLabel] = $internalFieldRows;
        }

        return $internalPanels;
    }

    /**
     * here we go from POST vars => internal metadata format
     * @param $fielddefs
     */
//    protected function _populateFromRequest(&$fielddefs)
//    {
//
//    }

    /**
     * Returns a list of fields, generally from the original (not customized) viewdefs
     * @param $viewdef
     * @return array array of fields, indexed by field name
     */
    protected function getFieldsFromLayout($viewdef)
    {
        if (isset($viewdef['panels']))
        {
            $panels = $viewdef['panels'];
        } else {
            $panels = $viewdef[self::$variableMap [ $this->_view ] ]['panels'];
        }

        // not canonical form... try parent method
        if (!isset($panels[0]['fields'])) {
            return parent::getFieldsFromLayout($viewdef);
        }

        $out = array();
        foreach ($panels as $panel) {
            foreach($panel['fields'] as $field) {
                $name = (isset($field['name'])) ? $field['name'] : $field; // we either have a name or a bare string
                $out[$name] = $field;
            }
        }
        return $out;
    }

}

?>