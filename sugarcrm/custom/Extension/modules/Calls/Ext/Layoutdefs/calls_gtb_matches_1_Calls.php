<?php
 // created: 2020-11-16 20:23:47
$layout_defs["Calls"]["subpanel_setup"]['calls_gtb_matches_1'] = array (
  'order' => 100,
  'module' => 'gtb_matches',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_CALLS_GTB_MATCHES_1_FROM_GTB_MATCHES_TITLE',
  'get_subpanel_data' => 'calls_gtb_matches_1',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
