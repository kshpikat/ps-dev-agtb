<?php

require_once('clients/summer/SideBarLayout.php');

$layout = new SideBarLayout();
$layout->push('main', array('layout'=>'list'));
$layout->push('side', array('layout'=>'list-sidebar'));
$viewdefs['summer']['layout']['records'] = $layout->getLayout();
