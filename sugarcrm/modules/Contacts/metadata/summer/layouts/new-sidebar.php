<?php
require_once('clients/summer/SideBarLayout.php');
$layout = new SideBarLayout();
$layout->push("main", array('view' => 'createhelp'));
$viewdefs['Contacts']['summer']['layout']['new-sidebar'] = $layout->getLayout();
