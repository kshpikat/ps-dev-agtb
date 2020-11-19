<?php
 // created: 2020-11-11 20:40:19
$dictionary['Contact']['fields']['prof_level_4_c'] = [
    'labelValue' => 'Proficiency Level',
    'dependency' => 'greaterThan(strlen($language_4_c),0)',
    'required_formula' => '',
    'visibility_grid' => '',
    'required' => true,
    'source' => 'custom_fields',
    'name' => 'prof_level_4_c',
    'vname' => 'LBL_PROF_LEVEL',
    'type' => 'enum',
    'massupdate' => true,
    'hidemassupdate' => false,
    'no_default' => false,
    'comments' => '',
    'help' => '',
    'importable' => 'true',
    'duplicate_merge' => 'disabled',
    'duplicate_merge_dom_value' => 0,
    'audited' => true,
    'reportable' => true,
    'unified_search' => false,
    'merge_filter' => 'disabled',
    'pii' => false,
    'calculated' => false,
    'len' => 100,
    'size' => '20',
    'options' => 'gtb_proficiency_list',
    'default' => NULL,
    'id' => '1ca4c924-245e-11eb-bf0e-0242ac140007',
    'custom_module' => 'Contacts',
];
 ?>
