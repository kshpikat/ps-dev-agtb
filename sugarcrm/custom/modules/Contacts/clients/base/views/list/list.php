<?php
$viewdefs['Contacts']['base']['view']['list']['panels'] =
        array (
          0 => 
          array (
            'label' => 'LBL_PANEL_1',
            'fields' => 
            array (
              0 => 
              array (
                'name' => 'name',
                'type' => 'fullname',
                'fields' => 
                array (
                  0 => 'salutation',
                  1 => 'first_name',
                  2 => 'last_name',
                ),
                'link' => true,
                'label' => 'LBL_LIST_NAME',
                'enabled' => true,
                'default' => true,
              ),
              1 => 
              array (
                'name' => 'title',
                'enabled' => true,
                'default' => true,
              ),
              2 => 
              array (
                'name' => 'gtb_cluster_c',
                'label' => 'LBL_GTB_CLUSTER_C',
                'enabled' => true,
                'default' => true,
              ),
              3 => 
              array (
                'name' => 'org_unit_c',
                'label' => 'LBL_ORG_UNIT_C',
                'enabled' => true,
                'default' => true,
              ),
              4 => 
              array (
                'name' => 'function_c',
                'label' => 'LBL_FUNCTION_C',
                'enabled' => true,
                'default' => true,
              ),
              5 => 
              array (
                'name' => 'assigned_user_name',
                'label' => 'LBL_LIST_ASSIGNED_USER',
                'id' => 'ASSIGNED_USER_ID',
                'enabled' => true,
                'default' => true,
              ),
              6 => 
              array (
                'name' => 'date_modified',
                'enabled' => true,
                'default' => true,
              ),
              7 => 
              array (
                'name' => 'date_entered',
                'enabled' => true,
                'default' => true,
                'readonly' => true,
              ),
              8 => 
              array (
                'name' => 'primary_address_country',
                'label' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                'enabled' => true,
                'default' => false,
              ),
              9 => 
              array (
                'name' => 'phone_mobile',
                'label' => 'LBL_MOBILE_PHONE',
                'enabled' => true,
                'default' => false,
              ),
              10 => 
              array (
                'name' => 'lead_source',
                'label' => 'LBL_LEAD_SOURCE',
                'enabled' => true,
                'default' => false,
              ),
              11 => 
              array (
                'name' => 'functional_mobility_c',
                'label' => 'LBL_FUNCTIONAL_MOBILITY_C',
                'enabled' => true,
                'default' => false,
              ),
              12 => 
              array (
                'name' => 'oe_mobility_c',
                'label' => 'LBL_OE_MOBILITY_C',
                'enabled' => true,
                'sortable' => false,
                'default' => false,
              ),
              13 => 
              array (
                'name' => 'career_discussion_c',
                'label' => 'LBL_CAREER_DISCUSSION_C',
                'enabled' => true,
                'default' => false,
              ),
              14 => 
              array (
                'name' => 'email',
                'enabled' => true,
                'default' => false,
              ),
              15 => 
              array (
                'name' => 'availability_c',
                'label' => 'LBL_AVAILABILITY_C',
                'enabled' => true,
                'default' => false,
              ),
            ),
          ),
);
