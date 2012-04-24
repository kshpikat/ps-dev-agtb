<?php
$viewdefs['Cases']['portal']['view']['list'] = array(
    'buttons' =>
    array(
        0 =>
        array(
            'name' => 'show_more_button',
            'type' => 'button',
            'label' => 'Show More',
            'class' => 'loading wide',
            'events' =>
            array(
                'click' => 'function(){ var self = this;this.context.state.collection.paginate({add:true, success:function(){console.log("in paginate success");window.scrollTo(0,document.body.scrollHeight);}});}',
            ),
        ),
        1 =>
        array(
            'name' => 'test_editable',
            'type' => 'clickToEdit',
            'label' => 'click to edit',
            'class' => 'cte',
        ),
    ),
    'listNav' =>
    array(
        0 =>
        array(
            'name' => 'show_more_button_back',
            'type' => 'navelement',
            'icon' => 'icon-plus',
            'label' => ' ',
            'route' =>
            array(
                'action' => 'create',
                'module' => 'Cases',
            ),
        ),
        1 =>
        array(
            'name' => 'show_more_button_back',
            'type' => 'navelement',
            'icon' => 'icon-chevron-left',
            'label' => ' ',
            'events' =>
            array(
                'click' => 'function(){ var self = this;this.context.state.collection.paginate({page:-1, success:function(){console.log("in paginate success");}});}',
            ),
        ),
        2 =>
        array(
            'name' => 'show_more_button_forward',
            'type' => 'navelement',
            'icon' => 'icon-chevron-right',
            'label' => ' ',
            'events' =>
            array(
                'click' => 'function(){ var self = this;console.log(this); this.context.state.collection.paginate({success:function(){console.log("in paginate success");}});}',
            ),
        ),
    ),
    'panels' =>
    array(
        0 =>
        array(
            'label' => 'LBL_PANEL_1',
            'fields' =>
            array(
                0 =>
                array(
                    'name' => 'case_number',
                    'label' => 'ID',
                    'class' => 'foo',
                    'default' => true,
                    'enabled' => true,
                    'sorting' => true,
                    'width' =>  8
                ),
                1 =>
                array(
                    'name' => 'name',
                    'label' => 'Title',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                    'sorting' => true,
                    'width' => 49
                ),
                2 =>
                array(
                    'name' => 'status',
                    'label' => 'Status',
                    'default' => true,
                    'enabled' => true,
                    'sorting' => true,
                    'width' => 13
                ),
                3 =>
                array(
                    'name' => 'date_modified',
                    'label' => 'Modifed Date',
                    'default' => true,
                    'enabled' => true,
                    'sorting' => true,
                    'width' => 17
                ),
                4 =>
                array(
                    'type' => 'actionslink',
                    'label' => '',
                    'width' => 9,
                    'sorting' => false
                ),
            ),
        ),
    ),
);
