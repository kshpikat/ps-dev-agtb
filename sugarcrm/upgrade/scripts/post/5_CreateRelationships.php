<?php
/**
 * Create new rels for CE->PRO
 */
class SugarUpgradeCreateRelationships extends UpgradeScript
{
    public $order = 5000;
    public $type = self::UPGRADE_DB;

    public function run()
    {
        if(!($this->from_flavor == 'ce' && $this->toFlavor('pro'))) return;

	    $relForObjects = array('leads'=>'Leads','campaigns'=>'Campaigns','prospects'=>'Prospects');
	    foreach($relForObjects as $relObjName=>$relModName){
	        $guid = create_guid();
	        $result= $this->db->query("SELECT id FROM relationships WHERE relationship_name = '{$relObjName}_team'");
	        $a = $this->db->fetchByAssoc($result);
	        if(!isset($a['id']) && empty($a['id']) ){
	            $qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
	            VALUES ('{$guid}', '{$relObjName}_team','Teams','teams','id','{$relModName}','{$relObjName}','team_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
	            $this->db->query($qRel);
	        }
	    }

	    $guid = create_guid();
	    $result= $this->db->query("SELECT id FROM relationships WHERE relationship_name = 'tracker_monitor_id'");
	    $a = $this->db->fetchByAssoc($result);
	    if(!isset($a['id']) && empty($a['id']) ){
			$qRel = "INSERT INTO relationships (id,relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted)
			VALUES ('{$guid}', 'tracker_monitor_id','TrackerPerfs','tracker_perf','monitor_id','Trackers','tracker','monitor_id',NULL,NULL,NULL,'one-to-many',NULL,NULL,'0','0')";
			$this->db->query($qRel);
	    }
    }
}
