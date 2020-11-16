<?php
// created: 2020-11-16 20:24:22
$dictionary["tasks_gtb_matches_1"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'tasks_gtb_matches_1' => 
    array (
      'lhs_module' => 'Tasks',
      'lhs_table' => 'tasks',
      'lhs_key' => 'id',
      'rhs_module' => 'gtb_matches',
      'rhs_table' => 'gtb_matches',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'tasks_gtb_matches_1_c',
      'join_key_lhs' => 'tasks_gtb_matches_1tasks_ida',
      'join_key_rhs' => 'tasks_gtb_matches_1gtb_matches_idb',
    ),
  ),
  'table' => 'tasks_gtb_matches_1_c',
  'fields' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'id',
    ),
    'date_modified' => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    'deleted' => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'default' => 0,
    ),
    'tasks_gtb_matches_1tasks_ida' => 
    array (
      'name' => 'tasks_gtb_matches_1tasks_ida',
      'type' => 'id',
    ),
    'tasks_gtb_matches_1gtb_matches_idb' => 
    array (
      'name' => 'tasks_gtb_matches_1gtb_matches_idb',
      'type' => 'id',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'idx_tasks_gtb_matches_1_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_tasks_gtb_matches_1_ida1_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'tasks_gtb_matches_1tasks_ida',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_tasks_gtb_matches_1_idb2_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'tasks_gtb_matches_1gtb_matches_idb',
        1 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'tasks_gtb_matches_1_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'tasks_gtb_matches_1tasks_ida',
        1 => 'tasks_gtb_matches_1gtb_matches_idb',
      ),
    ),
  ),
);