<?php
 // created: 2020-11-16 20:24:36
$layout_defs["gtb_matches"]["subpanel_setup"]['meetings_gtb_matches_1'] = array (
  'order' => 100,
  'module' => 'Meetings',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_MEETINGS_GTB_MATCHES_1_FROM_MEETINGS_TITLE',
  'get_subpanel_data' => 'meetings_gtb_matches_1',
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
