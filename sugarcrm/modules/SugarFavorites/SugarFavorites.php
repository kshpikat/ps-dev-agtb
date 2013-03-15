<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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


class SugarFavorites extends Basic
{
	public $new_schema = true;
	public $module_dir = 'SugarFavorites';
	public $object_name = 'SugarFavorites';
	public $table_name = 'sugarfavorites';
	public $importable = false;

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $created_by_link;
    public $modified_user_link;
    public $assigned_user_id;
    public $assigned_user_name;
    public $assigned_user_link;
    public $module;
    public $record_id;
    public $tag;
    public $record_name;
    public $disable_row_level_security = true;

	public static function generateStar(
	    $on,
	    $module,
	    $record
	    )
	{
        return '<div class="star"><div class="'. ($on ? 'on': 'off') .'" onclick="var self=this; parent.SUGAR.App.api.favorite(\''.$module. '\',  \''.$record. '\', $(self).hasClass(\'off\'), { success: function() {$(self).toggleClass(\'on off\');} });">&nbsp;</div></div>';
	}

	public static function generateGUID(
	    $module,
	    $record,
	    $user_id = ''
	    )
	{
	    if(empty($user_id))
	        $user_id = $GLOBALS['current_user']->id;

		return md5($module . $record . $user_id);
	}

	public static function isUserFavorite(
	    $module,
	    $record,
	    $user_id = ''
	    )
	{
		$id = SugarFavorites::generateGUID($module, $record, $user_id);

		$focus = BeanFactory::getBean('SugarFavorites', $id);

		return !empty($focus->id);
	}

	public static function getUserFavoritesByModule($module = '', User $user = null, $orderBy = "", $limit = -1)
	{
	    if ( empty($user) )
	        $where = " sugarfavorites.assigned_user_id = '{$GLOBALS['current_user']->id}' ";
	    else
	        $where = " sugarfavorites.assigned_user_id = '{$user->id}' ";

        if ( !empty($module) ) {
            if ( is_array($module) ) {
                $where .= " AND sugarfavorites.module IN ('" . implode("','",$module) . "')";
            }
            else {
            	$where .= " AND sugarfavorites.module = '$module' ";
            }
        }
        $focus = BeanFactory::getBean('SugarFavorites');
		$response = $focus->get_list($orderBy,$where,0,$limit);

	    return $response['list'];
	}

	public static function getFavoritesByModuleByRecord($module, $id)
	{
		$where = '';
		$orderBy = '';
		$limit = -1;
        if ( !empty($module) ) {
            if ( is_array($module) ) {
                $where .= " sugarfavorites.module IN ('" . implode("','",$module) . "')";
            }
            else {
                $where .= " sugarfavorites.module = '$module' ";
            }
        }

        $where .= " AND sugarfavorites.record_id = '{$id}'";

        $focus = BeanFactory::getBean('SugarFavorites');
		$response = $focus->get_list($orderBy,$where,0,$limit);

	    return $response['list'];
	}

	/**
	 * Use a direct DB Query to retreive only the assigned user id's for a module/record.
	 * @param string $module - module name
	 * @param string $id - guid
	 * @return array $assigned_user_ids - array of assigned user ids
	 */
	public static function getUserIdsForFavoriteRecordByModuleRecord($module, $id) {
		global $db;
		$query = "SELECT assigned_user_id FROM sugarfavorites WHERE module = '$module' AND record_id = '$id' AND deleted = 0";
		$queryResult = $db->query($query);
		$assigned_user_ids = array();
		while($row = $db->fetchByAssoc($queryResult)) {
			$assigned_user_ids[] = $row['assigned_user_id'];
		}
		return $assigned_user_ids;
	}

	public function markRecordDeletedInFavoritesByUser($record_id, $module, $assigned_user_id)
	{
		$query = "UPDATE {$this->table_name} set deleted=1 , module = '{$module}', date_modified = '$date_modified', modified_user_id = NOW() where record_id='{$record_id}' AND assigned_user_id = '{$assigned_user_id}'";
        $this->db->query($query, true, "Error marking favorites deleted: ");
	}

	/**
	 * An easy way to toggle a favorite on and off.
	 * @param string $id
	 * @param int $deleted
	 * @return bool
	 */
	public function toggleExistingFavorite($id, $deleted)
	{
		global $current_user;
		$deleted = (int) $deleted;
		if($deleted != 0 && $deleted != 1) {
			return false;
		}

		$query = "UPDATE {$this->table_name} SET deleted = {$deleted}, created_by = '{$current_user->id}', modified_user_id = '{$current_user->id}', assigned_user_id = '{$current_user->id}' WHERE id = '{$id}'";
		$this->db->query($query, true, "Error marking favorites deleted to {$deleted}: ");
		return true;
	}

    public static function markRecordDeletedInFavorites($record_id, $date_modified, $modified_user_id = "")
    {
        $focus = BeanFactory::getBean('SugarFavorites');
        $focus->mark_records_deleted_in_favorites($record_id, $date_modified, $modified_user_id);
    }

    public function mark_records_deleted_in_favorites($record_id, $date_modified, $modified_user_id = "")
    {
        if (isset($modified_user))
            $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified', modified_user_id = '$modified_user_id' where record_id='$record_id'";
        else
            $query = "UPDATE $this->table_name set deleted=1 , date_modified = '$date_modified' where record_id='$record_id'";

        $this->db->query($query, true, "Error marking favorites deleted: ");
    }

	public function fill_in_additional_list_fields()
	{
	    parent::fill_in_additional_list_fields();

	    $focus = BeanFactory::getBean($this->module);
	    if ( $focus instanceOf SugarBean ) {
	        $focus->retrieve($this->record_id);
	        if ( !empty($focus->id) )
	            $this->record_name = $focus->name;
	    }
	}

    /**
     * Add a Favorites block to the SugarQuery Object to fetch favorites for a specific [default to current] user
     * @param SugarQuery $sugar_query
     * @param bool $joinTo
     * @param string $join_type
     * @param bool|guid $user_id
     * @return string
     */
    public function addToSugarQuery(SugarQuery $sugar_query, $options = array()) {
        $alias = '';
        $user_id = (!isset($options['current_user_id'])) ? $GLOBALS['current_user']->id : $options['current_user_id'];
        $joinTo = (!isset($options['joinTo'])) ? false : $options['joinTo'];
        $joinType = (!isset($options['joinType'])) ? 'INNER' : $options['joinType'];

        if(!$joinTo) {
            if(is_array($sugar_query->from)) {
                list($bean, $alias) = $sugar_query->from;
            }
            else {
                $bean = $sugar_query->from;
                $alias = $bean->getTableName();
            }
        }
        else {
            $linkName = $sugar_query->join[$joinTo]->linkName;

            require_once('data/Link2.php');

            $bean = $sugar_query->from;
            if(is_array($bean)) {
                list($bean, $alias) = $bean;
            }

            $relationship = $bean->field_name_map[$linkName]['name'];

            $link = new Link2($relationship, $bean);

            $bean = BeanFactory::newBean($link->getRelatedModuleName());

            $alias = $joinTo;
        }

        $sfAlias = "sf_" . $bean->getTableName();

        $sugar_query->joinTable(self::getTableName(), array('alias'=>$sfAlias, 'joinType'=>$joinType))
                    ->on()->equals("{$sfAlias}.module", $bean->module_name, $this)
                        ->equalsField("{$sfAlias}.record_id","{$alias}.id", $this)
                    ->equals("{$sfAlias}.assigned_user_id", $user_id)
                    ->equals("{$sfAlias}.deleted", 0);

        return $sfAlias;
    }
}
