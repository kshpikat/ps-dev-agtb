<?php
$viewdefs['Emails']['base']['view']['compose'] = array(
    'type'    =>'record',
    'buttons' => array(
        array(
            'type'      => 'button',
            'name'      => 'cancel_button',
            'label'     => 'LBL_CANCEL_BUTTON_LABEL',
            'css_class' => 'btn-invisible btn-link',
        ),
        array(
            'type'    => 'actiondropdown',
            'name'    => 'main_dropdown',
            'buttons' => array(
                array(
                    'name'      => 'send_button',
                    'type'      => 'button',
                    'label'     => 'LBL_SEND_BUTTON_LABEL',
                    'value'     => 'send',
                    'primary'   => true,
                ),
                array(
                    'name'      => 'draft_button',
                    'type'      => 'button',
                    'label'     => 'LBL_SAVE_AS_DRAFT_BUTTON_LABEL',
                    'value'     => 'draft',
                ),
            ),
        ),
        array(
            'name' => 'sidebar_toggle',
            'type' => 'sidebartoggle',
        ),
    ),
    'panels' => array(
        array(
            'name'         => 'panel_body',
            'label'        => 'LBL_PANEL_2',
            'columns'      => 1,
            'labels'       => true,
            'labelsOnTop'  => false,
            'placeholders' => true,
            'fields'       => array(
                array(
                    "name"                => "to_addresses",
                    "type"                => "recipients",
                    "label"               => "LBL_TO_ADDRS",
                    'cell_css_class'      => 'controls-one btn-fit',
                    'label_css_class'     => 'begin-fieldgroup',
                ),
                array(
                    "name"                => "cc_addresses",
                    "type"                => "recipients",
                    "label"               => "LBL_CC",
                    'cell_css_class'      => 'controls-one btn-fit',
                ),
                array(
                    "name"                => "bcc_addresses",
                    "type"                => "recipients",
                    "label"               => "LBL_BCC",
                    'cell_css_class'      => 'controls-one btn-fit',
                ),
                array(
                    'name'                => 'subject',
                    'label'               => 'LBL_SUBJECT',
                    'label_css_class'     => 'end-fieldgroup',
                ),
                array(
                    'name'      => 'actionbar',
                    'type'      => 'compose-actionbar',
                    'buttonSections' => array(
                        array(
                            'name' => 'attachments_dropdown',
                            'type' => 'actiondropdown',
                            'buttons' => array(
                                array(
                                    'name'      => 'upload_new_button',
                                    'type'      => 'button',
                                    'icon'      => 'icon-paper-clip',
                                    'label'     => 'LBL_UPLOAD_ATTACHMENT',
                                    'primary'   => true,
                                ),
                                array(
                                    'name'      => 'attach_sugardoc_button',
                                    'type'      => 'button',
                                    'label'     => 'LBL_ATTACH_SUGAR_DOC',
                                ),
                            ),
                        ),
                        array(
                            'name' => 'other_actions',
                            'css_class' => 'pull-right',
                            'buttons' => array(
                                array(
                                    'name'      => 'signature_button',
                                    'type'      => 'button',
                                    'icon'      => 'icon-edit',
                                    'label'     => 'LBL_EMAIL_SIGNATURES',
                                ),
                                array(
                                    'name'      => 'template_button',
                                    'type'      => 'button',
                                    'icon'      => 'icon-file-alt',
                                    'label'     => 'LBL_EMAIL_TEMPLATES',
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'name'       => 'html_body',
                    'type'       => 'htmleditable_tinymce',
                    'full_width' => true,
                    'tinyConfig' => array(
                        // Location of TinyMCE script
                        'script_url' => 'include/javascript/tiny_mce/tiny_mce.js',

                        // General options
                        'theme' => "advanced",
                        'skin' => "sugar7",
                        'plugins' => "style,searchreplace,print,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras",
                        'entity_encoding' => "raw",

                        // Theme options
                        'theme_advanced_buttons1' => "code,help,separator,bold,italic,underline,strikethrough,separator,bullist,numlist,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,forecolor,backcolor,separator,spellchecker,seperator,formatselect,fontselect,fontsizeselect",
                        'theme_advanced_toolbar_location' => "top",
                        'theme_advanced_toolbar_align' => "left",
                        'theme_advanced_statusbar_location' => "bottom",
                        'theme_advanced_resizing' => false,
                        'schema' => "html5",
                        'template_external_list_url' => "lists/template_list.js",
                        'external_link_list_url' => "lists/link_list.js",
                        'external_image_list_url' => "lists/image_list.js",
                        'media_external_list_url' => "lists/media_list.js",
                        'theme_advanced_path' => false
                    ),
                ),
            ),
        ),
        array(
            'name'         => 'panel_hidden',
            'hide'         => true,
            'columns'      => 1,
            'labelsOnTop'  => false,
            'placeholders' => true,
            'fields'       => array(
                //BEGIN SUGARCRM flav=pro ONLY
                array(
                    "type" => "teamset",
                    "name" => "team_name",
                ),
                //END SUGARCRM flav=pro ONLY
                array (
                    "label" => "LBL_LIST_RELATED_TO",
                    'type'  => 'parent',
                    'name'  => 'parent_name'
                ),
            ),
        ),
    ),
);
