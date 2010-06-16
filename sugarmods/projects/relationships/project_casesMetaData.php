<?php
// adding project-to-cases relationship
$dictionary['projects_cases'] = array (
    'table' => 'projects_cases',
    'fields' => array (
        array('name' => 'id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'case_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'project_id', 'type' => 'varchar', 'len' => '36'),
        array('name' => 'date_modified', 'type' => 'datetime'),
        array('name' => 'deleted', 'type' => 'bool', 'len' => '1', 'default' => '0', 'required' => true),
    ),
    'indices' => array (
        array('name' => 'projects_cases_pk', 'type' =>'primary', 'fields'=>array('id')),
        array('name' => 'idx_proj_case_proj', 'type' =>'index', 'fields'=>array('project_id')),
        array('name' => 'idx_proj_case_case', 'type' =>'index', 'fields'=>array('case_id')),
        array('name' => 'projects_cases_alt', 'type'=>'alternate_key', 'fields'=>array('project_id','case_id')),
    ),
    'relationships' => array (
        'projects_cases' => array(
            'lhs_module' => 'Project',
            'lhs_table' => 'project',
            'lhs_key' => 'id',
            'rhs_module' => 'Cases',
            'rhs_table' => 'cases',
            'rhs_key' => 'id',
            'relationship_type' => 'many-to-many',
            'join_table' => 'projects_cases',
            'join_key_lhs' => 'project_id',
            'join_key_rhs' => 'case_id',
        ),
    ),
);
?>
