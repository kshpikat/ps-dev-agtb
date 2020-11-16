<?php
// created: 2020-11-16 20:23:47
$dictionary["calls_gtb_matches_1"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'calls_gtb_matches_1' => 
    array (
      'lhs_module' => 'Calls',
      'lhs_table' => 'calls',
      'lhs_key' => 'id',
      'rhs_module' => 'gtb_matches',
      'rhs_table' => 'gtb_matches',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'calls_gtb_matches_1_c',
      'join_key_lhs' => 'calls_gtb_matches_1calls_ida',
      'join_key_rhs' => 'calls_gtb_matches_1gtb_matches_idb',
    ),
  ),
  'table' => 'calls_gtb_matches_1_c',
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
    'calls_gtb_matches_1calls_ida' => 
    array (
      'name' => 'calls_gtb_matches_1calls_ida',
      'type' => 'id',
    ),
    'calls_gtb_matches_1gtb_matches_idb' => 
    array (
      'name' => 'calls_gtb_matches_1gtb_matches_idb',
      'type' => 'id',
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'idx_calls_gtb_matches_1_pk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'idx_calls_gtb_matches_1_ida1_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'calls_gtb_matches_1calls_ida',
        1 => 'deleted',
      ),
    ),
    2 => 
    array (
      'name' => 'idx_calls_gtb_matches_1_idb2_deleted',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'calls_gtb_matches_1gtb_matches_idb',
        1 => 'deleted',
      ),
    ),
    3 => 
    array (
      'name' => 'calls_gtb_matches_1_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'calls_gtb_matches_1calls_ida',
        1 => 'calls_gtb_matches_1gtb_matches_idb',
      ),
    ),
  ),
);