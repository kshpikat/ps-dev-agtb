<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

$mod_strings = array(
    'LBL_LOADING' => 'Loading' /*for 508 compliance fix*/,
    'LBL_HIDEOPTIONS' => 'Hide Options' /*for 508 compliance fix*/,
    'LBL_DELETE' => 'Delete' /*for 508 compliance fix*/,
    'LBL_POWERED_BY_SUGAR' => 'Powered By SugarCRM' /*for 508 compliance fix*/,
// BEGIN SUGARCRM flav=ent ONLY
    'LBL_ROLE' => 'Role',
// END SUGARCRM flav=ent ONLY
'help'=>array(
    'package'=>array(
            'create'=>'Provide a <b>Name</b> for the package. The name must start with a letter and may only consist of letters, numbers, and underscores. No spaces or other special characters may be used. (Example: HR_Management)<br/><br/> You can provide <b>Author</b> and <b>Description</b> information for package. <br/><br/>Click <b>Save</b> to create the package.',
            'modify'=>'The properties and possible actions for the <b>Package</b> appear here.<br><br>You can modify the <b>Name</b>, <b>Author</b> and <b>Description</b> of the package, as well as view and customize all of the modules contained within the package.<br><br>Click <b>New Module</b> to create a module for the package.<br><br>If the package contains at least one module, you can <b>Publish</b> and <b>Deploy</b> the package, as well as <b>Export</b> the customizations made in the package.',
            'name'=>'This is the <b>Name</b> of the current package. <br/><br/>The name must start with a letter and may only consist of letters, numbers, and underscores. No spaces or other special characters may be used. (Example: HR_Management)',
            'author'=>'This is the <b>Author</b> that is displayed during installation as the name of the entity that created the package.<br><br>The Author could be either an individual or a company.',
            'description'=>'This is the <b>Description</b> of the package that is displayed during installation.',
            'publishbtn'=>'Click <b>Publish</b> to save all entered data and to create a .zip file that is an installable version of the package.<br><br>Use <b>Module Loader</b> to upload the .zip file and install the package.',
            'deploybtn'=>'Click <b>Deploy</b> to save all entered data and to install the package, including all modules, in the current instance.',
            'duplicatebtn'=>'Click <b>Duplicate</b> to copy the contents of the package into a new package and to display the new package. <br/><br/>For the new package, a new name will be generated automatically by appending a number to the end of the name of the package used to create the new one. You can rename the new package by entering a new <b>Name</b> and clicking <b>Save</b>.',
            'exportbtn'=>'Click <b>Export</b> to create a .zip file containing the customizations made in the package.<br><br> The generated file is not an installable version of the package.<br><br>Use <b>Module Loader</b> to import the .zip file and to have the package, including customizations, appear in Module Builder.',
            'deletebtn'=>'Click <b>Delete</b> to delete this package and all files related to this package.',
            'savebtn'=>'Click <b>Save</b> to save all entered data related to the package.',
            'existing_module'=>'Click the <b>Module</b> icon to edit the properties and customize the fields, relationships and layouts associated with the module.',
            'new_module'=>'Click <b>New Module</b> to create a new module for this package.',
            'key'=>'This 5-letter, alphanumeric <b>Key</b> will be used to prefix all directories, class names and database tables for all of the modules in the current package.<br><br>The key is used in an effort to achieve table name uniqueness.',
            'readme'=>'Click to add <b>Readme</b> text for this package.<br><br>The Readme will be available at the time of installation.',

),
    'main'=>array(

    ),
    'module'=>array(
        'create'=>'Provide a <b>Name</b> for the module. The <b>Label</b> that you provide will appear in the navigation tab. <br/><br/>Choose to display a navigation tab for the module by checking the <b>Navigation Tab</b> checkbox.<br/><br/>Check the <b>Team Security</b> checkbox to have a Team selection field within the module records. <br/><br/>Then choose the type of module you would like to create. <br/><br/>Select a template type. Each template contains a specific set of fields, as well as pre-defined layouts, to use as a basis for your module. <br/><br/>Click <b>Save</b> to create the module.',
        'modify'=>'You can change the module properties or customize the <b>Fields</b>, <b>Relationships</b> and <b>Layouts</b> related to the module.',
        'importable'=>'Checking the <b>Importable</b> checkbox will enable importing for this module.<br><br>A link to the Import Wizard will appear in the Shortcuts panel in the module.  The Import Wizard facilitates importing of data from external sources into the custom module.',
        'team_security'=>'Checking the <b>Team Security</b> checkbox will enable team security for this module.  <br/><br/>If team security is enabled, the Team selection field will appear within the records in the module ',
        'reportable'=>'Checking this box will allow this module to have reports run against it.',
        'assignable'=>'Checking this box will allow a record in this module to be assigned to a selected user.',
        'has_tab'=>'Checking <b>Navigation Tab</b> will provide a navigation tab for the module.',
        'acl'=>'Checking this box will enable Access Controls on this module, including Field Level Security.',
        'studio'=>'Checking this box will allow administrators to customize this module within Studio.',
        'audit'=>'Checking this box will enable Auditing for this module. Changes to certain fields will be recorded so that administrators can review the change history.',
        'viewfieldsbtn'=>'Click <b>View Fields</b> to view the fields associated with the module and to create and edit custom fields.',
        'viewrelsbtn'=>'Click <b>View Relationships</b> to view the relationships associated with this module and to create new relationships.',
        'viewlayoutsbtn'=>'Click <b>View Layouts</b> to view the layouts for the module and to customize the field arrangement within the layouts.',
        'viewmobilelayoutsbtn' => 'Click <b>View Mobile Layouts</b> to view the mobile layouts for the module and to customize the field arrangement within the layouts.',
        'duplicatebtn'=>'Click <b>Duplicate</b> to copy the properties of the module into a new module and to display the new module. <br/><br/>For the new module, a new name will be generated automatically by appending a number to the end of the name of the module used to create the new one.',
        'deletebtn'=>'Click <b>Delete</b> to delete this module.',
        'name'=>'This is the <b>Name</b> of the current module.<br/><br/>The name must be alphanumeric and must start with a letter and contain no spaces. (Example: HR_Management)',
        'label'=>'This is the <b>Label</b> that will appear in the navigation tab for the module. ',
        'savebtn'=>'Click <b>Save</b> to save all entered data related to the module.',
        'type_basic'=>'The <b>Basic</b> template type provides basic fields, such as the Name, Assigned to, Team, Date Created and Description fields.',
        'type_company'=>'The <b>Company</b> template type provides organization-specific fields, such as Company Name, Industry and Billing Address.<br/><br/>Use this template to create modules that are similar to the standard Accounts module.',
        'type_issue'=>'The <b>Issue</b> template type provides case- and bug-specific fields, such as Number, Status, Priority and Description.<br/><br/>Use this template to create modules that are similar to the standard Cases and Bug Tracker modules.',
        'type_person'=>'The <b>Person</b> template type provides individual-specific fields, such as Salutation, Title, Name, Address and Phone Number.<br/><br/>Use this template to create modules that are similar to the standard Contacts and Leads modules.',
        'type_sale'=>'The <b>Sale</b> template type provides opportunity specific fields, such as Lead Source, Stage, Amount and Probability. <br/><br/>Use this template to create modules that are similar to the standard Opportunities module.',
        'type_file'=>'The <b>File</b> template provides Document specific fields, such as File Name, Document type, and Publish Date.<br><br>Use this template to create modules that are similar to the standard Documents module.',

    ),
    'dropdowns'=>array(
        'default' => 'All of the <b>Dropdowns</b> for the application are listed here.<br><br>The dropdowns can be used for dropdown fields in any module.<br><br>To make changes to an existing dropdown, click on the dropdown name.<br><br>Click <b>Add Dropdown</b> to create a new dropdown.',
        'editdropdown'=>'Dropdown lists can be used for standard or custom dropdown fields in any module.<br><br>Provide a <b>Name</b> for the dropdown list.<br><br>If any language packs are installed in the application, you can select the <b>Language</b> to use for the list items.<br><br>In the <b>Item Name</b> field, provide a name for the option in the dropdown list.  This name will not appear in the dropdown list that is visible to users.<br><br>In the <b>Display Label</b> field, provide a label that will be visible to users.<br><br>After providing the item name and display label, click <b>Add</b> to add the item to the dropdown list.<br><br>To reorder the items in the list, drag and drop items into the desired positions.<br><br>To edit the display label of an item, click the <b>Edit icon</b>, and enter a new label. To delete an item from the dropdown list, click the <b>Delete icon</b>.<br><br>To undo a change made to a display label, click <b>Undo</b>.  To redo a change that was undone, click <b>Redo</b>.<br><br>Click <b>Save</b> to save the dropdown list.',

    ),
    'subPanelEditor'=>array(
        'modify'	=> 'All of the fields that can be displayed in the <b>Subpanel</b> appear here.<br><br>The <b>Default</b> column contains the fields that are displayed in the Subpanel.<br/><br/>The <b>Hidden</b> column contains fields that can be added to the Default column.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        'savebtn'	=> 'Click <b>Save & Deploy</b> to save changes you made and to make them active within the module.',
        'historyBtn'=> 'Click <b>View History</b> to view and restore a previously saved layout from the history.',
        'historyRestoreDefaultLayout'=> 'Click <b>Restore Default Layout</b> to restore a view to its original layout.',
        'Hidden' 	=> '<b>Hidden</b> fields do not appear in the subpanel.',
        'Default'	=> '<b>Default</b> fields appear in the subpanel.',

    ),
    'listViewEditor'=>array(
        'modify'	=> 'All of the fields that can be displayed in the <b>ListView</b> appear here.<br><br>The <b>Default</b> column contains the fields that are displayed in the ListView by default.<br/><br/>The <b>Available</b> column contains fields that a user can select in the Search to create a custom ListView. <br/><br/>The <b>Hidden</b> column contains fields that can be added to the Default or Available column.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        'savebtn'	=> 'Click <b>Save & Deploy</b> to save changes you made and to make them active within the module.',
        'historyBtn'=> 'Click <b>View History</b> to view and restore a previously saved layout from the history.<br><br><b>Restore</b> within <b>View History</b> restores the field placement within previously saved layouts. To change field labels, click the Edit icon next to each field.',
        'historyRestoreDefaultLayout'=> 'Click <b>Restore Default Layout</b> to restore a view to its original layout.<br><br><b>Restore Default Layout</b> only restores the field placement within the original layout. To change field labels, click the Edit icon next to each field.',
        'Hidden' 	=> '<b>Hidden</b> fields not currently available for users to see in ListViews.',
        'Available' => '<b>Available</b> fields are not shown by default, but can be added to ListViews by users.',
        'Default'	=> '<b>Default</b> fields appear in ListViews that are not customized by users.'
    ),
    'popupListViewEditor'=>array(
        'modify'	=> 'All of the fields that can be displayed in the <b>ListView</b> appear here.<br><br>The <b>Default</b> column contains the fields that are displayed in the ListView by default.<br/><br/>The <b>Hidden</b> column contains fields that can be added to the Default or Available column.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        'savebtn'	=> 'Click <b>Save & Deploy</b> to save changes you made and to make them active within the module.',
        'historyBtn'=> 'Click <b>View History</b> to view and restore a previously saved layout from the history.<br><br><b>Restore</b> within <b>View History</b> restores the field placement within previously saved layouts. To change field labels, click the Edit icon next to each field.',
        'historyRestoreDefaultLayout'=> 'Click <b>Restore Default Layout</b> to restore a view to its original layout.<br><br><b>Restore Default Layout</b> only restores the field placement within the original layout. To change field labels, click the Edit icon next to each field.',
        'Hidden' 	=> '<b>Hidden</b> fields not currently available for users to see in ListViews.',
        'Default'	=> '<b>Default</b> fields appear in ListViews that are not customized by users.'
    ),
    'searchViewEditor'=>array(
        'modify'	=> 'All of the fields that can be displayed in the <b>Search</b> form appear here.<br><br>The <b>Default</b> column contains the fields that will be displayed in the Search form.<br/><br/>The <b>Hidden</b> column contains fields available for you as an admin to add to the Search form.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    . '<br/><br/>This configuration applies to popup search layout in legacy modules only.',
        'savebtn'	=> 'Clicking <b>Save & Deploy</b> will save all changes and make them active',
        'Hidden' 	=> '<b>Hidden</b> fields do not appear in the Search.',
        'historyBtn'=> 'Click <b>View History</b> to view and restore a previously saved layout from the history.<br><br><b>Restore</b> within <b>View History</b> restores the field placement within previously saved layouts. To change field labels, click the Edit icon next to each field.',
        'historyRestoreDefaultLayout'=> 'Click <b>Restore Default Layout</b> to restore a view to its original layout.<br><br><b>Restore Default Layout</b> only restores the field placement within the original layout. To change field labels, click the Edit icon next to each field.',
        'Default'	=> '<b>Default</b> fields appear in the Search.'
    ),
    'layoutEditor'=>array(
        'defaultdetailview'=>'The <b>Layout</b> area contains the fields that are currently displayed within the <b>DetailView</b>.<br/><br/>The <b>Toolbox</b> contains the <b>Recycle Bin</b> and the fields and layout elements that can be added to the layout.<br><br>Make changes to the layout by dragging and dropping elements and fields between the <b>Toolbox</b> and the <b>Layout</b> and within the layout itself.<br><br>To remove a field from the layout, drag the field to the <b>Recycle Bin</b>. The field will then be available in the Toolbox to add to the layout.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        'defaultquickcreate'=>'The <b>Layout</b> area contains the fields that are currently displayed within the <b>QuickCreate</b> form.<br><br>The QuickCreate form appears in the subpanels for the module when the Create button is clicked.<br/><br/>The <b>Toolbox</b> contains the <b>Recycle Bin</b> and the fields and layout elements that can be added to the layout.<br><br>Make changes to the layout by dragging and dropping elements and fields between the <b>Toolbox</b> and the <b>Layout</b> and within the layout itself.<br><br>To remove a field from the layout, drag the field to the <b>Recycle Bin</b>. The field will then be available in the Toolbox to add to the layout.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        //this defualt will be used for edit view
        'default'	=> 'The <b>Layout</b> area contains the fields that are currently displayed within the <b>EditView</b>.<br/><br/>The <b>Toolbox</b> contains the <b>Recycle Bin</b> and the fields and layout elements that can be added to the layout.<br><br>Make changes to the layout by dragging and dropping elements and fields between the <b>Toolbox</b> and the <b>Layout</b> and within the layout itself.<br><br>To remove a field from the layout, drag the field to the <b>Recycle Bin</b>. The field will then be available in the Toolbox to add to the layout.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        //this defualt will be used for edit view
        'defaultrecordview'   => 'The <b>Layout</b> area contains the fields that are currently displayed within the <b>Record View</b>.<br/><br/>The <b>Toolbox</b> contains the <b>Recycle Bin</b> and the fields and layout elements that can be added to the layout.<br><br>Make changes to the layout by dragging and dropping elements and fields between the <b>Toolbox</b> and the <b>Layout</b> and within the layout itself.<br><br>To remove a field from the layout, drag the field to the <b>Recycle Bin</b>. The field will then be available in the Toolbox to add to the layout.'
    . '<br/><br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_dependent.png"/>Indicates a Dependent field that may or may not be visible based on the value of a formula.<br/><!--not_in_theme!--><img src="themes/default/images/SugarLogic/icon_calculated.png" /> Indicates a Calculated field whose value will be automatically determined based on a formula.'
    ,
        'saveBtn'	=> 'Click <b>Save</b> to preserve the changes you made to the layout since the last time you saved it.<br><br>The changes will not be displayed in the module until you Deploy the saved changes.',
        'historyBtn'=> 'Click <b>View History</b> to view and restore a previously saved layout from the history.<br><br><b>Restore</b> within <b>View History</b> restores the field placement within previously saved layouts. To change field labels, click the Edit icon next to each field.',
        'historyRestoreDefaultLayout'=> 'Click <b>Restore Default Layout</b> to restore a view to its original layout.<br><br><b>Restore Default Layout</b> only restores the field placement within the original layout. To change field labels, click the Edit icon next to each field.',
        'publishBtn'=> 'Click <b>Save & Deploy</b> to save all changes you made to the layout since the last time you saved it, and to make the changes active in the module.<br><br>The layout will immediately be displayed in the module.',
        'toolbox'	=> 'The <b>Toolbox</b> contains the <b>Recycle Bin</b>, additional layout elements and the set of available fields to add to the layout.<br/><br/>The layout elements and fields in the Toolbox can be dragged and dropped into the layout, and the layout elements and fields can be dragged and dropped from the layout into the Toolbox.<br><br>The layout elements are <b>Panels</b> and <b>Rows</b>. Adding a new row or a new panel to the layout provides additional locations in the layout for fields.<br/><br/>Drag and drop any of the fields in the Toolbox or layout onto a occupied field position to swap the locations of the two fields.<br/><br/>The <b>Filler</b> field creates blank space in the layout where it is placed.',
        'panels'	=> 'The <b>Layout</b> area provides a view of how the layout will appear within the module when the changes made to the layout are deployed.<br/><br/>You can reposition fields, rows and panels by dragging and dropping them in the desired location.<br/><br/>Remove elements by dragging and dropping them in the <b>Recycle Bin</b> in the Toolbox, or add new elements and fields by dragging them from the <b>Toolbox</b>s and dropping them in the desired location in the layout.',
        'delete'	=> 'Drag and drop any element here to remove it from the layout',
        'property'	=> 'Edit the <b>Label</b> displayed for this field.<br><br><b>Width</b> provide a width value in pixels for Sidecar modules and as a percentage of the table width for backward compatible modules.',
    ),
    'fieldsEditor'=>array(
        'default'	=> 'The <b>Fields</b> that are available for the module are listed here by Field Name.<br><br>Custom fields created for the module appear above the fields that are available for the module by default.<br><br>To edit a field, click the <b>Field Name</b>.<br/><br/>To create a new field, click <b>Add Field</b>.',
        'mbDefault'=>'The <b>Fields</b> that are available for the module are listed here by Field Name.<br><br>To configure the properties for a field, click the Field Name.<br><br>To create a new field, click <b>Add Field</b>. The label along with the other properties of the new field can be edited after creation by clicking the Field Name.<br><br>After the module is deployed, the new fields created in Module Builder are regarded as standard fields in the deployed module in Studio.',
        'addField'	=> 'Select a <b>Data Type</b> for the new field. The type you select determines what kind of characters can be entered for the field. For example, only numbers that are integers may be entered into fields that are of the Integer data type.<br><br> Provide a <b>Name</b> for the field.  The name must be alphanumeric and must not contain any spaces. Underscores are valid.<br><br> The <b>Display Label</b> is the label that will appear for the fields in the module layouts.  The <b>System Label</b> is used to refer to the field in the code.<br><br> Depending on the data type selected for the field, some or all of the following properties can be set for the field:<br><br> <b>Help Text</b> appears temporarily while a user hovers over the field and can be used to prompt the user for the type of input desired.<br><br> <b>Comment Text</b> is only seen within Studio &/or Module Builder, and can be used to describe the field for administrators.<br><br> <b>Default Value</b> will appear in the field.  Users can enter a new value in the field or use the default value.<br><br> Select the <b>Mass Update</b> checkbox in order to be able to use the Mass Update feature for the field.<br><br>The <b>Max Size</b> value determines the maximum number of characters that can be entered in the field.<br><br> Select the <b>Required Field</b> checkbox in order to make the field required. A value must be provided for the field in order to be able to save a record containing the field.<br><br> Select the <b>Reportable</b> checkbox in order to allow the field to be used for filters and for displaying data in Reports.<br><br> Select the <b>Audit</b> checkbox in order to be able to track changes to the field in the Change Log.<br><br>Select an option in the <b>Importable</b> field to allow, disallow or require the field to be imported into in the Import Wizard.<br><br>Select an option in the <b>Duplicate Merge</b> field to enable or disable the Merge Duplicates and Find Duplicates features.<br><br>Additional properties can be set for certain data types.',
        'editField' => 'The properties of this field can be customized.<br><br>Click <b>Clone</b> to create a new field with the same properties.',
        'mbeditField' => 'The <b>Display Label</b> of a template field can be customized. The other properties of the field can not be customized.<br><br>Click <b>Clone</b> to create a new field with the same properties.<br><br>To remove a template field so that it does not display in the module, remove the field from the appropriate <b>Layouts</b>.'

    ),
    'exportcustom'=>array(
        'exportHelp'=>'Export customizations made in Studio by creating packages that can be uploaded into another Sugar instance through the <b>Module Loader</b>.<br><br>  First, provide a <b>Package Name</b>.  You can provide <b>Author</b> and <b>Description</b> information for package as well.<br><br>Select the module(s) that contain the customizations you wish to export. Only modules containing customizations will appear for you to select.<br><br>Then click <b>Export</b> to create a .zip file for the package containing the customizations.',
        'exportCustomBtn'=>'Click <b>Export</b> to create a .zip file for the package containing the customizations that you wish to export.',
        'name'=>'This is the <b>Name</b> of the package. This name will be displayed during installation.',
        'author'=>'This is the <b>Author</b> that is displayed during installation as the name of the entity that created the package. The Author can be either an individual or a company.',
        'description'=>'This is the <b>Description</b> of the package that is displayed during installation.',
    ),
    'studioWizard'=>array(
        'mainHelp' 	=> 'Welcome to the <b>Developer Tools</b> area. <br/><br/>Use the tools within this area to create and manage standard and custom modules and fields.',
        'studioBtn'	=> 'Use <b>Studio</b> to customize deployed modules.',
        'mbBtn'		=> 'Use <b>Module Builder</b> to create new modules.',
        'sugarPortalBtn' => 'Use <b>Sugar Portal Editor</b> to manage and customize the Sugar Portal.',
        'dropDownEditorBtn' => 'Use <b>Dropdown Editor</b> to add and edit global dropdowns for dropdown fields.',
        'appBtn' 	=> 'Application mode is where you can customize various properties of the program, such as how many TPS reports are displayed on the homepage',
        'backBtn'	=> 'Return to the previous step.',
        'studioHelp'=> 'Use <b>Studio</b> to determine what and how information is displayed in the modules.',
        'studioBCHelp' => ' indicates the module is a backward compatible module',
        'moduleBtn'	=> 'Click to edit this module.',
        'moduleHelp'=> 'The components that you can customize for the module appear here.<br><br>Click an icon to select the component to edit.',
        'fieldsBtn'	=> 'Create and customize <b>Fields</b> to store information in the module.',
        'labelsBtn' => 'Edit the <b>Labels</b> that display for the fields and other titles in the module.'	,
        'relationshipsBtn' => 'Add new or view existing <b>Relationships</b> for the module.' ,
        'layoutsBtn'=> 'Customize the module <b>Layouts</b>.  The layouts are the different views of the module contaning fields.<br><br>You can determine which fields appear and how they are organized in each layout.',
        'subpanelBtn'=> 'Determine which fields appear in the <b>Subpanels</b> in the module.',
        'portalBtn' =>'Customize the module <b>Layouts</b> that appear in the <b>Sugar Portal</b>.',
        'layoutsHelp'=> 'The module <b>Layouts</b> that can be customized appear here.<br><br>The layouts display fields and field data.<br><br>Click an icon to select the layout to edit.',
        'subpanelHelp'=> 'The <b>Subpanels</b> in the module that can be customized appear here.<br><br>Click an icon to select the module to edit.',
        'newPackage'=>'Click <b>New Package</b> to create a new package.',
        'exportBtn' => 'Click <b>Export Customizations</b> to create and download a package containing customizations made in Studio for specific modules.',
        'mbHelp'    => 'Use <b>Module Builder</b> to create packages containing custom modules based on standard or custom objects.',
        'viewBtnEditView' => 'Customize the module\'s <b>EditView</b> layout.<br><br>The EditView is the form containing input fields for capturing user-entered data.',
        'viewBtnDetailView' => 'Customize the module\'s <b>DetailView</b> layout.<br><br>The DetailView displays user-entered field data.',
        'viewBtnDashlet' => 'Customize the module\'s <b>Sugar Dashlet</b>, including the Sugar Dashlet\'s ListView and Search.<br><br>The Sugar Dashlet will be available to add to the pages in the Home module.',
        'viewBtnListView' => 'Customize the module\'s <b>ListView</b> layout.<br><br>The Search results appear in the ListView.',
        'searchBtn' => 'Customize the module\'s <b>Search</b> layouts.<br><br>Determine what fields can be used to filter records that appear in the ListView.',
        'viewBtnQuickCreate' =>  'Customize the module\'s <b>QuickCreate</b> layout.<br><br>The QuickCreate form appears in subpanels and in the Emails module.',

        'searchHelp'=> 'The <b>Search</b> forms that can be customized appear here.<br><br>Search forms contain fields for filtering records.<br><br>Click an icon to select the search layout to edit.',
        'dashletHelp' =>'The <b>Sugar Dashlet</b> layouts that can be customized appear here.<br><br>The Sugar Dashlet will be available to add to the pages in the Home module.',
        'DashletListViewBtn' =>'The <b>Sugar Dashlet ListView</b> displays records based on the Sugar Dashlet search filters.',
        'DashletSearchViewBtn' =>'The <b>Sugar Dashlet Search</b> filters records for the Sugar Dashlet listview.',
        'popupHelp' =>'The <b>Popup</b> layouts that can be customized appear here.<br>',
        'PopupListViewBtn' => '<b>Popup ListView</b> layout is used to view a list of records when selecting one or more records to relate to the current record.',
        'PopupSearchViewBtn' => '<b>Popup Search</b> layout allows users to search for records to relate to a current record and appears above the popup listview in the same window. Legacy modules use this layout for popup searching while Sidecar modules use the Search layoutâs configuration.',
        'BasicSearchBtn' => 'Customize the <b>Basic Search</b> form that appears in the Basic Search tab in the Search area for the module.',
        'AdvancedSearchBtn' => 'Customize the <b>Advanced Search</b> form that appears in the Advanced Search tab in the Search area for the module.',
        'portalHelp' => 'Manage and customize the <b>Sugar Portal</b>.',
        'SPUploadCSS' => 'Upload a <b>Style Sheet</b> for the Sugar Portal.',
        'SPSync' => '<b>Sync</b> customizations to the Sugar Portal instance.',
        'Layouts' => 'Customize the <b>Layouts</b> of the Sugar Portal modules.',
        'portalLayoutHelp' => 'The modules within the Sugar Portal appear in this area.<br><br>Select a module to edit the <b>Layouts</b>.',
        'relationshipsHelp' => 'All of the <b>Relationships</b> that exist between the module and other deployed modules appear here.<br><br>The relationship <b>Name</b> is the system-generated name for the relationship.<br><br>The <b>Primary Module</b> is the module that owns the relationships.  For example, all of the properties of the relationships for which the Accounts module is the primary module are stored in the Accounts database tables.<br><br>The <b>Type</b> is the type of relationship exists between the Primary module and the <b>Related Module</b>.<br><br>Click a column title to sort by the column.<br><br>Click a row in the relationship table to view the properties associated with the relationship.<br><br>Click <b>Add Relationship</b> to create a new relationship.<br><br>Relationships can be created between any two deployed modules.',
        'relationshipHelp'=>'<b>Relationships</b> can be created between the module and another deployed module.<br><br> Relationships are visually expressed through subpanels and relate fields in the module records.<br><br>Select one of the following relationship <b>Types</b> for the module:<br><br> <b>One-to-One</b> - Both modules\' records will contain relate fields.<br><br> <b>One-to-Many</b> - The Primary Module\'s record will contain a subpanel, and the Related Module\'s record will contain a relate field.<br><br> <b>Many-to-Many</b> - Both modules\' records will display subpanels.<br><br> Select the <b>Related Module</b> for the relationship. <br><br>If the relationship type involves subpanels, select the subpanel view for the appropriate modules.<br><br> Click <b>Save</b> to create the relationship.',
        'convertLeadHelp' => "Here you can add modules to the convert layout screen and modify the settings of existing ones.<br/><br/>
        <b>Ordering:</b><br/>
        Contacts, Accounts, and Opportunities must maintain their order. You can re-order any other module by dragging its row in the table.<br/><br/>
        <b>Dependency:</b><br/>
        If Opportunities is included, Accounts must either be required or removed from the convert layout.<br/><br/>
        <b>Module:</b> The name of the module.<br/><br/>
        <b>Required:</b> Required modules must be created or selected before the lead can be converted.<br/><br/>
        <b>Copy Data:</b> If checked, fields from the lead will be copied to fields with the same name in the newly created records.<br/><br/>
        <b>Delete:</b> Remove this module from the convert layout.<br/><br/>
        ",
        'editDropDownBtn' => 'Edit a global Dropdown',
        'addDropDownBtn' => 'Add a new global Dropdown',
    ),
    'fieldsHelp'=>array(
        'default'=>'The <b>Fields</b> in the module are listed here by Field Name.<br><br>The module template includes a pre-determined set of fields.<br><br>To create a new field, click <b>Add Field</b>.<br><br>To edit a field, click the <b>Field Name</b>.<br/><br/>After the module is deployed, the new fields created in Module Builder, along with the template fields, are regarded as standard fields in Studio.',
    ),
    'relationshipsHelp'=>array(
        'default'=>'The <b>Relationships</b> that have been created between the module and other modules appear here.<br><br>The relationship <b>Name</b> is the system-generated name for the relationship.<br><br>The <b>Primary Module</b> is the module that owns the relationships. The relationship properties are stored in the database tables belonging to the primary module.<br><br>The <b>Type</b> is the type of relationship exists between the Primary module and the <b>Related Module</b>.<br><br>Click a column title to sort by the column.<br><br>Click a row in the relationship table to view and edit the properties associated with the relationship.<br><br>Click <b>Add Relationship</b> to create a new relationship.',
        'addrelbtn'=>'mouse over help for add relationship..',
        'addRelationship'=>'<b>Relationships</b> can be created between the module and another custom module or a deployed module.<br><br> Relationships are visually expressed through subpanels and relate fields in the module records.<br><br>Select one of the following relationship <b>Types</b> for the module:<br><br> <b>One-to-One</b> - Both modules\' records will contain relate fields.<br><br> <b>One-to-Many</b> - The Primary Module\'s record will contain a subpanel, and the Related Module\'s record will contain a relate field.<br><br> <b>Many-to-Many</b> - Both modules\' records will display subpanels.<br><br> Select the <b>Related Module</b> for the relationship. <br><br>If the relationship type involves subpanels, select the subpanel view for the appropriate modules.<br><br> Click <b>Save</b> to create the relationship.',
    ),
    'labelsHelp'=>array(
        'default'=> 'The <b>Labels</b> for the fields and other titles in the module can be changed.<br><br>Edit the label by clicking within the field, entering a new label and clicking <b>Save</b>.<br><br>If any language packs are installed in the application, you can select the <b>Language</b> to use for the labels.',
        'saveBtn'=>'Click <b>Save</b> to save all changes.',
        'publishBtn'=>'Click <b>Save & Deploy</b> to save all changes and make them active.',
    ),
    'portalSync'=>array(
        'default' => 'Enter the <b>Sugar Portal URL</b> of the portal instance to update, and click <b>Go</b>.<br><br>Then enter a valid Sugar user name and password, and then click <b>Begin Sync</b>.<br><br>The customizations made to the Sugar Portal <b>Layouts</b>, along with the <b>Style Sheet</b> if one was uploaded, will be transferred to specified the portal instance.',
    ),
    'portalConfig'=>array(
           'default' => '',
       ),
    'portalStyle'=>array(
        'default' => 'You can customize the look of the Sugar Portal by using a style sheet.<br><br>Select a <b>Style Sheet</b> to upload.<br><br>The style sheet will be implemented in the Sugar Portal the next time a sync is performed.',
    ),
),

'assistantHelp'=>array(
    'package'=>array(
            //custom begin
            'nopackages'=>'To get started on a project, click <b>New Package</b> to create a new package to house your custom module(s). <br/><br/>Each package can contain one or more modules.<br/><br/>For instance, you might want to create a package containing one custom module that is related to the standard Accounts module. Or, you might want to create a package containing several new modules that work together as a project and that are related to each other and to other modules already in the application.',
            'somepackages'=>'A <b>package</b> acts as a container for custom modules, all of which are part of one project. The package can contain one or more custom <b>modules</b> that can be related to each other or to other modules in the application.<br/><br/>After creating a package for your project, you can create modules for the package right away, or you can return to the Module Builder at a later time to complete the project.<br><br>When the project is complete, you can <b>Deploy</b> the package to install the custom modules within the application.',
            'afterSave'=>'Your new package should contain at least one module. You can create one or more custom modules for the package.<br/><br/>Click <b>New Module</b> to create a custom module for this package.<br/><br/> After creating at least one module, you can publish or deploy the package to make it available for your instance and/or other users\' instances.<br/><br/> To deploy the package in one step within your Sugar instance, click <b>Deploy</b>.<br><br>Click <b>Publish</b> to save the package as a .zip file. After the .zip file is saved to your system, use the <b>Module Loader</b> to upload and install the package within your Sugar instance.  <br/><br/>You can distribute the file to other users to upload and install within their own Sugar instances.',
            'create'=>'A <b>package</b> acts as a container for custom modules, all of which are part of one project. The package can contain one or more custom <b>modules</b> that can be related to each other or to other modules in the application.<br/><br/>After creating a package for your project, you can create modules for the package right away, or you can return to the Module Builder at a later time to complete the project.',
            ),
    'main'=>array(
        'welcome'=>'Use the <b>Developer Tools</b> to create and manage standard and custom modules and fields. <br/><br/>To manage modules in the application, click <b>Studio</b>. <br/><br/>To create custom modules, click <b>Module Builder</b>.',
        'studioWelcome'=>'All of the currently installed modules, including standard and module-loaded objects, are customizable within Studio.'
    ),
    'module'=>array(
        'somemodules'=>"Since the current package contains at least one module, you can <b>Deploy</b> the modules in the package within your Sugar instance or <b>Publish</b> the package to be installed in the current Sugar instance or another instance using the <b>Module Loader</b>.<br/><br/>To install the package directly within your Sugar instance, click <b>Deploy</b>.<br><br>To create a .zip file for the package that can be loaded and installed within the current Sugar instance and other instances using the <b>Module Loader</b>, click <b>Publish</b>.<br/><br/> You can build the modules for this package in stages, and publish or deploy when you are ready to do so. <br/><br/>After publishing or deploying a package, you can make changes to the package properties and customize the modules further.  Then re-publish or re-deploy the package to apply the changes." ,
        'editView'=> 'Here you can edit the existing fields. You can remove any of the existing fields or add available fields in the left panel.',
        'create'=>'When choosing the type of <b>Type</b> of module that you wish to create, keep in mind the types of fields you would like to have within the module. <br/><br/>Each module template contains a set of fields pertaining to the type of module described by the title.<br/><br/><b>Basic</b> - Provides basic fields that appear in standard modules, such as the Name, Assigned to, Team, Date Created and Description fields.<br/><br/> <b>Company</b> - Provides organization-specific fields, such as Company Name, Industry and Billing Address.  Use this template to create modules that are similar to the standard Accounts module.<br/><br/> <b>Person</b> - Provides individual-specific fields, such as Salutation, Title, Name, Address and Phone Number.  Use this template to create modules that are similar to the standard Contacts and Leads modules.<br/><br/><b>Issue</b> - Provides case- and bug-specific fields, such as Number, Status, Priority and Description.  Use this template to create modules that are similar to the standard Cases and Bug Tracker modules.<br/><br/>Note: After you create the module, you can edit the labels of the fields provided by the template, as well as create custom fields to add to the module layouts.',
        'afterSave'=>'Customize the module to suit your needs by editing and creating fields, establishing relationships with other modules and arranging the fields within the layouts.<br/><br/>To view the template fields and manage custom fields within the module, click <b>View Fields</b>.<br/><br/>To create and manage relationships between the module and other modules, whether modules already in the application or other custom modules within the same package, click <b>View Relationships</b>.<br/><br/>To edit the module layouts, click <b>View Layouts</b>. You can change the Detail View, Edit View and List View layouts for the module just as you would for modules already in the application within Studio.<br/><br/> To create a module with the same properties as the current module, click <b>Duplicate</b>.  You can further customize the new module.',
        'viewfields'=>'The fields in the module can be customized to suit your needs.<br/><br/>You can not delete standard fields, but you can remove them from the appropriate layouts within the Layouts pages. <br/><br/>You can quickly create new fields that have similar properties to existing fields by clicking <b>Clone</b> in the <b>Properties</b> form.  Enter any new properties, and then click <b>Save</b>.<br/><br/>It is recommended that you set all of the properties for the standard fields and custom fields before you publish and install the package containing the custom module.',
        'viewrelationships'=>'You can create many-to-many relationships between the current module and other modules in the package, and/or between the current module and modules already installed in the application.<br><br> To create one-to-many and one-to-one relationships, create <b>Relate</b> and <b>Flex Relate</b> fields for the modules.',
        'viewlayouts'=>'You can control what fields are available for capturing data within the <b>Edit View</b>.  You can also control what data displays within the <b>Detail View</b>.  The views do not have to match. <br/><br/>The Quick Create form is displayed when the <b>Create</b> is clicked in a module subpanel. By default, the <b>Quick Create</b> form layout is the same as the default <b>Edit View</b> layout. You can customize the Quick Create form so that it contains less and/or different fields than the Edit View layout. <br><br>You can determine the module security using Layout customization along with <b>Role Management</b>.<br><br>',
        'existingModule' =>'After creating and customizing this module, you can create additional modules or return to the package to <b>Publish</b> or <b>Deploy</b> the package.<br><br>To create additional modules, click <b>Duplicate</b> to create a module with the same properties as the current module, or navigate back to the package, and click <b>New Module</b>.<br><br> If you are ready to <b>Publish</b> or <b>Deploy</b> the package containing this module, navigate back to the package to perform these functions. You can publish and deploy packages containing at least one module.',
        'labels'=> 'The labels of the standard fields as well as custom fields can be changed.  Changing field labels will not affect the data stored in the fields.',
    ),
    'listViewEditor'=>array(
        'modify'	=> 'There are three columns displayed to the left. The "Default" column contains the fields that are displayed in a list view by default, the "Available" column contains fields that a user can choose to use for creating a custom list view, and the "Hidden" column contains fields available for you as an admin to either add to the default or Available columns for use by users but are currently disabled.',
        'savebtn'	=> 'Clicking <b>Save</b> will save all changes and make them active.',
        'Hidden' 	=> 'Hidden fields are fields that are not currently available to users for use in list views.',
        'Available' => 'Available fields are fields that are not shown by default, but can be enabled by users.',
        'Default'	=> 'Default fields are displayed to users who have not created custom list view settings.'
    ),

    'searchViewEditor'=>array(
        'modify'	=> 'There are two columns displayed to the left. The "Default" column contains the fields that will be displayed in the search view, and the "Hidden" column contains fields available for you as an admin to add to the view.',
        'savebtn'	=> 'Clicking <b>Save & Deploy</b> will save all changes and make them active.',
        'Hidden' 	=> 'Hidden fields are fields that will not be shown in the search view.',
        'Default'	=> 'Default fields will be shown in the search view.'
    ),
    'layoutEditor'=>array(
        'default'	=> 'There are two columns displayed to the left. The right-hand column, labeled Current Layout or Layout Preview, is where you change the module layout. The left-hand column, entitled Toolbox, contains useful elements and tools for use when editing the layout. <br/><br/>If the layout area is titled Current Layout then you are working on a copy of the layout currently used by the module for display.<br/><br/>If it is titled Layout Preview then you are working on a copy created earlier by a click on the Save button, that might have already been changed from the version seen by users of this module.',
        'saveBtn'	=> 'Clicking this button saves the layout so that you can preserve your changes. When you return to this module you will start from this changed layout. Your layout however will not be seen by users of the module until you click the Save and Publish button.',
        'publishBtn'=> 'Click this button to deploy the layout. This means that this layout will immediately be seen by users of this module.',
        'toolbox'	=> 'The toolbox contains a variety of useful features for editing layouts, including a trash area, a set of additional elements and a set of available fields. Any of these can be dragged and dropped onto the layout.',
        'panels'	=> 'This area shows how your layout will look to users of this module when it is depolyed.<br/><br/>You can reposition elements such as fields, rows and panels by dragging and dropping them; delete elements by dragging and dropping them on the trash area in the toolbox, or add new elements by dragging them from the toolbox and dropping them on to the layout in the desired position.'
    ),
    'dropdownEditor'=>array(
        'default'	=> 'There are two columns displayed to the left. The right-hand column, labeled Current Layout or Layout Preview, is where you change the module layout. The left-hand column, entitled Toolbox, contains useful elements and tools for use when editing the layout. <br/><br/>If the layout area is titled Current Layout then you are working on a copy of the layout currently used by the module for display.<br/><br/>If it is titled Layout Preview then you are working on a copy created earlier by a click on the Save button, that might have already been changed from the version seen by users of this module.',
        'dropdownaddbtn'=> 'Clicking this button adds a new item to the dropdown.',

    ),
    'exportcustom'=>array(
        'exportHelp'=>'Customizations made in Studio within this instance can be packaged and deployed in another instance.  <br><br>Provide a <b>Package Name</b>.  You can provide <b>Author</b> and <b>Description</b> information for package.<br><br>Select the module(s) that contain the customizations to export. (Only modules containing customizations will appear for you to select.)<br><br>Click <b>Export</b> to create a .zip file for the package containing the customizations.  The .zip file can be uploaded in another instance through <b>Module Loader</b>.',
        'exportCustomBtn'=>'Click <b>Export</b> to create a .zip file for the package containing the customizations that you wish to export.
',
        'name'=>'The <b>Name</b> of the package will be displayed in Module Loader after the package is uploaded for installation in Studio.',
        'author'=>'The <b>Author</b> is the name of the entity that created the package. The Author can be either an individual or a company.<br><br>The Author will be displayed in Module Loader after the package is uploaded for installation in Studio.
',
        'description'=>'The <b>Description</b> of the package will be displayed in Module Loader after the package is uploaded for installation in Studio.',
    ),
    'studioWizard'=>array(
        'mainHelp' 	=> 'Welcome to the <b>Developer Tools</b1> area. <br/><br/>Use the tools within this area to create and manage standard and custom modules and fields.',
        'studioBtn'	=> 'Use <b>Studio</b> to customize installed modules by changing the field arrangement, selecting what fields are available and creating custom data fields.',
        'mbBtn'		=> 'Use <b>Module Builder</b> to create new modules.',
        'appBtn' 	=> 'Use Application mode to customize various properties of the program, such as how many TPS reports are displayed on the homepage',
        'backBtn'	=> 'Return to the previous step.',
        'studioHelp'=> 'Use <b>Studio</b> to customize installed modules.',
        'moduleBtn'	=> 'Click to edit this module.',
        'moduleHelp'=> 'Select the module component that you would like to edit',
        'fieldsBtn'	=> 'Edit what information is stored in the module by controlling the <b>Fields</b> in the module.<br/><br/>You can edit and create custom fields here.',
        'layoutsBtn'=> 'Customize the <b>Layouts</b> of the Edit, Detail, List and search views.',
        'subpanelBtn'=> 'Edit what information is shown in this modules subpanels.',
        'layoutsHelp'=> 'Select a <b>Layout to edit</b>.<br/<br/>To change the layout that contains data fields for entering data, click <b>Edit View</b>.<br/><br/>To change the layout that displays the data entered into the fields in the Edit View, click <b>Detail View</b>.<br/><br/>To change the columns which appear in the default list, click <b>List View</b>.<br/><br/>To change the Basic and Advanced search form layouts, click <b>Search</b>.',
        'subpanelHelp'=> 'Select a <b>Subpanel</b> to edit.',
        'searchHelp' => 'Select a <b>Search</b> layout to edit.',
        'labelsBtn'	=> 'Edit the <b>Labels</b> to display for values in this module.',
        'newPackage'=>'Click <b>New Package</b> to create a new package.',
        'mbHelp'    => '<b>Welcome to Module Builder.</b><br/><br/>Use <b>Module Builder</b> to create packages containing custom modules based on standard or custom objects. <br/><br/>To begin, click <b>New Package</b> to create a new package, or select a package to edit.<br/><br/> A <b>package</b> acts as a container for custom modules, all of which are part of one project. The package can contain one or more custom modules that can be related to each other or to modules in the application. <br/><br/>Examples: You might want to create a package containing one custom module that is related to the standard Accounts module. Or, you might want to create a package containing several new modules that work together as a project and that are related to each other and to modules in the application.',
        'exportBtn' => 'Click <b>Export Customizations</b> to create a package containing customizations made in Studio for specific modules.',
    ),

),
//HOME
'LBL_HOME_EDIT_DROPDOWNS'=>'Dropdown Editor',

//ASSISTANT
'LBL_AS_SHOW' => 'Show Assistant in future.',
'LBL_AS_IGNORE' => 'Ignore Assistant in future.',
'LBL_AS_SAYS' => 'Assistant Says:',

//STUDIO2
'LBL_MODULEBUILDER'=>'Module Builder',
'LBL_STUDIO' => 'Studio',
'LBL_DROPDOWNEDITOR' => 'Dropdown Editor',
'LBL_EDIT_DROPDOWN'=>'Edit Dropdown',
'LBL_DEVELOPER_TOOLS' => 'Developer Tools',
'LBL_SUGARPORTAL' => 'Sugar Portal Editor',
'LBL_SYNCPORTAL' => 'Sync Portal',
'LBL_PACKAGE_LIST' => 'Package List',
'LBL_HOME' => 'Home',
'LBL_NONE'=>'-None-',
'LBL_DEPLOYE_COMPLETE'=>'Deploy complete',
'LBL_DEPLOY_FAILED'   =>'An error has occurred during deploy process, your package may not have installed correctly',
'LBL_ADD_FIELDS'=>'Add Custom Fields',
'LBL_AVAILABLE_SUBPANELS'=>'Available Subpanels',
'LBL_ADVANCED'=>'Advanced',
'LBL_ADVANCED_SEARCH'=>'Advanced Search',
'LBL_BASIC'=>'Basic',
'LBL_BASIC_SEARCH'=>'Basic Search',
'LBL_CURRENT_LAYOUT'=>'Layout',
'LBL_CURRENCY' => 'Currency',
'LBL_CUSTOM' => 'Custom',
'LBL_DASHLET'=>'Sugar Dashlet',
'LBL_DASHLETLISTVIEW'=>'Sugar Dashlet ListView',
'LBL_DASHLETSEARCH'=>'Sugar Dashlet Search',
'LBL_POPUP'=>'PopupView',
'LBL_POPUPLIST'=>'Popup ListView',
'LBL_POPUPLISTVIEW'=>'Popup ListView',
'LBL_POPUPSEARCH'=>'Popup Search',
'LBL_DASHLETSEARCHVIEW'=>'Sugar Dashlet Search',
'LBL_DISPLAY_HTML'=>'Display HTML Code',
'LBL_DETAILVIEW'=>'DetailView',
'LBL_DROP_HERE' => '[Drop Here]',
'LBL_EDIT'=>'Edit',
'LBL_EDIT_LAYOUT'=>'Edit Layout',
'LBL_EDIT_ROWS'=>'Edit Rows',
'LBL_EDIT_COLUMNS'=>'Edit Columns',
'LBL_EDIT_LABELS'=>'Edit Labels',
'LBL_EDIT_PORTAL'=>'Edit Portal for ',
'LBL_EDIT_FIELDS'=>'Edit Fields',
'LBL_EDITVIEW'=>'EditView',
'LBL_FILTER_SEARCH' => "Search",
'LBL_FILLER'=>'(filler)',
'LBL_FIELDS'=>'Fields',
'LBL_FAILED_TO_SAVE' => 'Failed To Save',
'LBL_FAILED_PUBLISHED' => 'Failed to Publish',
'LBL_HOMEPAGE_PREFIX' => 'My',
'LBL_LAYOUT_PREVIEW'=>'Layout Preview',
'LBL_LAYOUTS'=>'Layouts',
'LBL_LISTVIEW'=>'List View',
'LBL_RECORDVIEW'=>'Record View',
'LBL_RECORDDASHLETVIEW'=>'Record View Dashlet',
'LBL_PREVIEWVIEW'=>'Preview View',
'LBL_MODULE_TITLE' => 'Studio',
'LBL_NEW_PACKAGE' => 'New Package',
'LBL_NEW_PANEL'=>'New Panel',
'LBL_NEW_ROW'=>'New Row',
'LBL_PACKAGE_DELETED'=>'Package Deleted',
'LBL_PUBLISHING' => 'Publishing ...',
'LBL_PUBLISHED' => 'Published',
'LBL_SELECT_FILE'=> 'Select File',
'LBL_SAVE_LAYOUT'=> 'Save Layout',
'LBL_SELECT_A_SUBPANEL' => 'Select a Subpanel',
'LBL_SELECT_SUBPANEL' => 'Select Subpanel',
'LBL_SUBPANELS' => 'Subpanels',
'LBL_SUBPANEL' => 'Subpanel',
'LBL_SUBPANEL_TITLE' => 'Title:',
'LBL_SEARCH_FORMS' => 'Search',
'LBL_STAGING_AREA' => 'Staging Area (drag and drop items here)',
'LBL_SUGAR_FIELDS_STAGE' => 'Sugar Fields (click items to add to staging area)',
'LBL_SUGAR_BIN_STAGE' => 'Sugar Bin (click items to add to staging area)',
'LBL_TOOLBOX' => 'Toolbox',
'LBL_VIEW_SUGAR_FIELDS' => 'View Sugar Fields',
'LBL_VIEW_SUGAR_BIN' => 'View Sugar Bin',
'LBL_QUICKCREATE' => 'QuickCreate',
'LBL_EDIT_DROPDOWNS' => 'Edit a Global Dropdown',
'LBL_ADD_DROPDOWN' => 'Add a new Global Dropdown',
'LBL_BLANK' => '-blank-',
'LBL_TAB_ORDER' => 'Tab Order',
'LBL_TAB_PANELS' => 'Enable tabs',
'LBL_TAB_PANELS_HELP' => 'When tabs are enabled, use the "type" dropdown box<br />for each section to define how it will be displayed (tab or panel)',
'LBL_TABDEF_TYPE' => 'Display Type',
'LBL_TABDEF_TYPE_HELP' => 'Select how this section should be displayed. This option only has effect if you have enabled tabs on this view.',
'LBL_TABDEF_TYPE_OPTION_TAB' => 'Tab',
'LBL_TABDEF_TYPE_OPTION_PANEL' => 'Panel',
'LBL_TABDEF_TYPE_OPTION_HELP' => 'Select Panel to have this panel display within the view of the layout. Select Tab to have this panel displayed within a separate tab within the layout. When Tab is specified for a panel, subsequent panels set to display as Panel will be displayed within the tab. <br/>A new Tab will be started for the next panel for which Tab is selected. If Tab is selected for a panel below the first panel, the first panel will necessarily be a Tab.',
'LBL_TABDEF_COLLAPSE' => 'Collapse',
'LBL_TABDEF_COLLAPSE_HELP' => 'Select to make the default state of this panel collapsed.',
'LBL_DROPDOWN_TITLE_NAME' => 'Name',
'LBL_DROPDOWN_LANGUAGE' => 'Language',
'LBL_DROPDOWN_ITEMS' => 'List Items',
'LBL_DROPDOWN_ITEM_NAME' => 'Item Name',
'LBL_DROPDOWN_ITEM_LABEL' => 'Display Label',
'LBL_SYNC_TO_DETAILVIEW' => 'Sync to DetailView',
'LBL_SYNC_TO_DETAILVIEW_HELP' => 'Select this option to sync this EditView layout to the corresponding DetailView layout. Fields and field placement in the EditView<br>will be sync\'d and saved to the DetailView automatically upon clicking Save or Save & Deploy in the EditView. <br>Layout changes will not be able to be made in the DetailView.',
'LBL_SYNC_TO_DETAILVIEW_NOTICE' => 'This DetailView is sync\'d with the corresponding EditView.<br> Fields and field placement in this DetailView reflect the fields and field placement in the EditView.<br> Changes to the DetailView cannot be saved or deployed within this page. Make changes or un-sync the layouts in the EditView. ',
// BEGIN SUGARCRM flav=ent ONLY
'LBL_COPY_FROM' => 'Copy from',
// END SUGARCRM flav=ent ONLY
'LBL_COPY_FROM_EDITVIEW' => 'Copy from EditView',
'LBL_DROPDOWN_BLANK_WARNING' => 'Values are required for both the Item Name and the Display Label. To add a blank item, click Add without entering any values for the Item Name and the Display Label.',
'LBL_DROPDOWN_KEY_EXISTS' => 'Key already exists in list',
// BEGIN SUGARCRM flav=ent ONLY
'LBL_DROPDOWN_LIST_EMPTY' => 'The list must contain at least one enabled item',
// END SUGARCRM flav=ent ONLY
'LBL_NO_SAVE_ACTION' => 'Could not find the save action for this view.',
'LBL_BADLY_FORMED_DOCUMENT' => 'Studio2:establishLocation: badly formed document',
// @TODO: Remove this lang string and uncomment out the string below once studio
// supports removing combo fields if a member field is on the layout already.
'LBL_INDICATES_COMBO_FIELD' => '** Indicates a combination field. A combination field is a collection of individual fields. For example, "Address" is a combination field that contains "Street address", "City", "Zip Code","State" and "Country".<br><br>Doubleclick a combination field to see which fields it contains.',
'LBL_COMBO_FIELD_CONTAINS' => 'contains:',

'LBL_WIRELESSLAYOUTS'=>'Mobile Layouts',
'LBL_WIRELESSEDITVIEW'=>'Mobile EditView',
'LBL_WIRELESSDETAILVIEW'=>'Mobile DetailView',
'LBL_WIRELESSLISTVIEW'=>'Mobile ListView',
'LBL_WIRELESSSEARCH'=>'Mobile Search',

'LBL_BTN_ADD_DEPENDENCY'=>'Add Dependency',
'LBL_BTN_EDIT_FORMULA'=>'Edit Formula',
'LBL_DEPENDENCY' => 'Dependency',
'LBL_DEPENDANT' => 'Dependant',
'LBL_CALCULATED' => 'Calculated Value',
'LBL_READ_ONLY' => 'Read Only',
'LBL_FORMULA_BUILDER' => 'Formula Builder',
'LBL_FORMULA_INVALID' => 'Invalid Formula',
'LBL_FORMULA_TYPE' => 'The formula must be of type ',
'LBL_NO_FIELDS' => 'No Fields Found',
'LBL_NO_FUNCS' => 'No Functions Found',
'LBL_SEARCH_FUNCS' => 'Search Functions...',
'LBL_SEARCH_FIELDS' => 'Search Fields...',
'LBL_FORMULA' => 'Formula',
'LBL_DYNAMIC_VALUES_CHECKBOX' => 'Dependent',
'LBL_DEPENDENT_DROPDOWN_HELP' => 'Drag options from the list on the left of available options in the dependent dropdown to the lists on the right to make those options available when the parent option is selected. If no items are under a parent option, when the parent option is selected, the dependent dropdown will not be displayed.',
'LBL_AVAILABLE_OPTIONS' => 'Available Options',
'LBL_PARENT_DROPDOWN' => 'Parent Dropdown',
'LBL_VISIBILITY_EDITOR' => 'Visibility Editor',
'LBL_ROLLUP' => 'Rollup',
'LBL_RELATED_FIELD' => 'Related Field',
'LBL_CONFIG_PORTAL_LOGOMARK_URL'=> 'URL to custom logomark image. The recommended logomark dimensions are 22 x 22 pixels. Any image uploaded that is larger in either direction will be scaled to these max dimensions.',
'LBL_CONFIG_PORTAL_LOGO_URL'=> 'URL to custom logo image. The recommended logo width is 200 pixels. Any image uploaded that is larger in either direction will be scaled to these max dimensions. This logo will be used on the login screen. If no image is uploaded, the logomark will be used.',
'LBL_PORTAL_ROLE_DESC' => 'Do not delete this role. Customer Self-Service Portal Role is a system-generated role created during the Sugar Portal activation process. Use Access controls within this Role to enable and/or disable Bugs, Cases or Knowledge Base modules in Sugar Portal. Do not modify any other access controls for this role to avoid unknown and unpredictable system behavior. In case of accidental deletion of this role, recreate it by disabling and enabling Sugar Portal.',

//RELATIONSHIPS
'LBL_MODULE' => 'Module',
'LBL_LHS_MODULE'=>'Primary Module',
'LBL_CUSTOM_RELATIONSHIPS' => '* relationship created in Studio',
'LBL_RELATIONSHIPS'=>'Relationships',
'LBL_RELATIONSHIP_EDIT' => 'Edit Relationship',
'LBL_REL_NAME' => 'Name',
'LBL_REL_LABEL' => 'Label',
'LBL_REL_TYPE' => 'Type',
'LBL_RHS_MODULE'=>'Related Module',
'LBL_NO_RELS' => 'No Relationships',
'LBL_RELATIONSHIP_ROLE_ENTRIES'=>'Optional Condition' ,
'LBL_RELATIONSHIP_ROLE_COLUMN'=>'Column',
'LBL_RELATIONSHIP_ROLE_VALUE'=>'Value',
'LBL_SUBPANEL_FROM'=>'Subpanel from',
'LBL_RELATIONSHIP_ONLY'=>'No visible elements will be created for this relationship as there is a pre-existing visible relationship between these two modules.',
'LBL_ONETOONE' => 'One to One',
'LBL_ONETOMANY' => 'One to Many',
'LBL_MANYTOONE' => 'Many to One',
'LBL_MANYTOMANY' => 'Many to Many',

//STUDIO QUESTIONS
'LBL_QUESTION_FUNCTION' => 'Select a function or component.',
'LBL_QUESTION_MODULE1' => 'Select a module.',
'LBL_QUESTION_EDIT' => 'Select a module to edit.',
'LBL_QUESTION_LAYOUT' => 'Select a layout to edit.',
'LBL_QUESTION_SUBPANEL' => 'Select a subpanel to edit.',
'LBL_QUESTION_SEARCH' => 'Select a search layout to edit.',
'LBL_QUESTION_MODULE' => 'Select a module component to edit.',
'LBL_QUESTION_PACKAGE' => 'Select a package to edit, or create a new package.',
'LBL_QUESTION_EDITOR' => 'Select a tool.',
'LBL_QUESTION_DROPDOWN' => 'Select a dropdown to edit, or create a new dropdown.',
'LBL_QUESTION_DASHLET' => 'Select a dashlet layout to edit.',
'LBL_QUESTION_POPUP' => 'Select a popup layout to edit.',
//CUSTOM FIELDS
'LBL_RELATE_TO'=>'Relate To',
'LBL_NAME'=>'Name',
'LBL_LABELS'=>'Labels',
'LBL_MASS_UPDATE'=>'Mass Update',
'LBL_AUDITED'=>'Audit',
'LBL_CUSTOM_MODULE'=>'Module',
'LBL_DEFAULT_VALUE'=>'Default Value',
'LBL_REQUIRED'=>'Required',
'LBL_DATA_TYPE'=>'Type',
'LBL_HCUSTOM'=>'CUSTOM',
'LBL_HDEFAULT'=>'DEFAULT',
'LBL_LANGUAGE'=>'Language:',
'LBL_CUSTOM_FIELDS' => '* field created in Studio',

//SECTION
'LBL_SECTION_EDLABELS' => 'Edit Labels',
'LBL_SECTION_PACKAGES' => 'Packages',
'LBL_SECTION_PACKAGE' => 'Package',
'LBL_SECTION_MODULES' => 'Modules',
'LBL_SECTION_PORTAL' => 'Portal',
'LBL_SECTION_DROPDOWNS' => 'Dropdowns',
'LBL_SECTION_PROPERTIES' => 'Properties',
'LBL_SECTION_DROPDOWNED' => 'Edit Dropdown',
'LBL_SECTION_HELP' => 'Help',
'LBL_SECTION_ACTION' => 'Action',
'LBL_SECTION_MAIN' => 'Main',
'LBL_SECTION_EDPANELLABEL' => 'Edit Panel Label',
'LBL_SECTION_FIELDEDITOR' => 'Edit Field',
'LBL_SECTION_DEPLOY' => 'Deploy',
'LBL_SECTION_MODULE' => 'Module',
'LBL_SECTION_VISIBILITY_EDITOR'=>'Edit Visibility',
//WIZARDS

//LIST VIEW EDITOR
'LBL_DEFAULT'=>'Default',
'LBL_HIDDEN'=>'Hidden',
'LBL_AVAILABLE'=>'Available',
'LBL_LISTVIEW_DESCRIPTION'=>'There are three columns displayed below. The <b>Default</b> column contains fields that are displayed in a list view by default.  The <b>Additional</b> column contains fields that a user can choose to use for creating a custom view.  The <b>Available</b> column displays fields availabe for you as an admin to add to the Default or Additional columns for use by users.',
'LBL_LISTVIEW_EDIT'=>'List View Editor',

//Manager Backups History
'LBL_MB_PREVIEW'=>'Preview',
'LBL_MB_RESTORE'=>'Restore',
'LBL_MB_DELETE'=>'Delete',
'LBL_MB_COMPARE'=>'Compare',
'LBL_MB_DEFAULT_LAYOUT'=>'Default Layout',

//END WIZARDS

//BUTTONS
'LBL_BTN_ADD'=>'Add',
'LBL_BTN_SAVE'=>'Save',
'LBL_BTN_SAVE_CHANGES'=>'Save Changes',
'LBL_BTN_DONT_SAVE'=>'Discard Changes',
'LBL_BTN_CANCEL'=>'Cancel',
'LBL_BTN_CLOSE'=>'Close',
'LBL_BTN_SAVEPUBLISH'=>'Save & Deploy',
'LBL_BTN_NEXT'=>'Next',
'LBL_BTN_BACK'=>'Back',
'LBL_BTN_CLONE'=>'Clone',
// BEGIN SUGARCRM flav=ent ONLY
'LBL_BTN_COPY' => 'Copy',
'LBL_BTN_COPY_FROM' => 'Copy from…',
// END SUGARCRM flav=ent ONLY
'LBL_BTN_ADDCOLS'=>'Add Columns',
'LBL_BTN_ADDROWS'=>'Add Rows',
'LBL_BTN_ADDFIELD'=>'Add Field',
'LBL_BTN_ADDDROPDOWN'=>'Add Dropdown',
'LBL_BTN_SORT_ASCENDING'=>'Sort Ascending',
'LBL_BTN_SORT_DESCENDING'=>'Sort Descending',
'LBL_BTN_EDLABELS'=>'Edit Labels',
'LBL_BTN_UNDO'=>'Undo',
'LBL_BTN_REDO'=>'Redo',
'LBL_BTN_ADDCUSTOMFIELD'=>'Add Custom Field',
'LBL_BTN_EXPORT'=>'Export Customizations',
'LBL_BTN_DUPLICATE'=>'Duplicate',
'LBL_BTN_PUBLISH'=>'Publish',
'LBL_BTN_DEPLOY'=>'Deploy',
'LBL_BTN_EXP'=>'Export',
'LBL_BTN_DELETE'=>'Delete',
'LBL_BTN_VIEW_LAYOUTS'=>'View Layouts',
'LBL_BTN_VIEW_MOBILE_LAYOUTS'=>'View Mobile Layouts',
'LBL_BTN_VIEW_FIELDS'=>'View Fields',
'LBL_BTN_VIEW_RELATIONSHIPS'=>'View Relationships',
'LBL_BTN_ADD_RELATIONSHIP'=>'Add Relationship',
'LBL_BTN_RENAME_MODULE' => 'Change Module Name',
'LBL_BTN_INSERT'=>'Insert',
//TABS

//ERRORS
'ERROR_ALREADY_EXISTS'=> 'Error: Field Already Exists',
'ERROR_INVALID_KEY_VALUE'=> "Error: Invalid Key Value: [']",
'ERROR_NO_HISTORY' => 'No history files found',
'ERROR_MINIMUM_FIELDS' => 'The layout must contain at least one field',
'ERROR_GENERIC_TITLE' => 'An error has occurred',
'ERROR_REQUIRED_FIELDS' => 'Are you sure you wish to continue? The following required fields are missing from the layout:  ',
'ERROR_ARE_YOU_SURE' => 'Are you sure you wish to continue?',

'ERROR_CALCULATED_MOBILE_FIELDS' => 'The following field(s) have calculated values which will not be re-calculated in real time in the SugarCRM Mobile Edit View:',
'ERROR_CALCULATED_PORTAL_FIELDS' => 'The following field(s) have calculated values which will not be re-calculated in real time in the SugarCRM Portal Edit View:',

//SUGAR PORTAL
    'LBL_PORTAL_DISABLED_MODULES' => 'The following module(s) are disabled:',
    'LBL_PORTAL_ENABLE_MODULES' => 'If you would like to enable them in the portal please enable them <a id="configure_tabs" target="_blank" href="./index.php?module=Administration&amp;action=ConfigureTabs">here</a>.',
    'LBL_PORTAL_CONFIGURE' => 'Configure Portal',
    'LBL_PORTAL_ENABLE_PORTAL' => 'Enable portal',
    'LBL_PORTAL_ENABLE_SEARCH' => 'Enable search before opening a case',
    'LBL_PORTAL_ALLOW_CLOSE_CASE' => 'Allow portal users to close case',
    'LBL_PORTAL_THEME' => 'Theme Portal',
    'LBL_PORTAL_ENABLE' => 'Enable',
    'LBL_PORTAL_SITE_URL' => 'Your portal site is available at:',
    'LBL_PORTAL_APP_NAME' => 'Application Name',
    'LBL_PORTAL_LOGOMARK_URL' => 'Logomark URL',
    'LBL_PORTAL_LOGOMARK_PREVIEW' => 'Logomark Preview',
    'LBL_PORTAL_LOGO_URL' => 'Logo URL',
    'LBL_PORTAL_LOGO_PREVIEW' => 'Logo Preview',
    'LBL_PORTAL_CONTACT_PHONE' => 'Phone',
    'LBL_PORTAL_CONTACT_EMAIL' => 'Email',
    'LBL_PORTAL_CONTACT_EMAIL_INVALID' => 'Must enter a valid email address',
    'LBL_PORTAL_CONTACT_URL' => 'URL',
    'LBL_PORTAL_CONTACT_INFO_ERROR' => 'At least one method of contact must be specified',
    'LBL_PORTAL_LIST_NUMBER' => 'Number of records to display on list',
    'LBL_PORTAL_DETAIL_NUMBER' => 'Number of fields to display on Detail View',
    'LBL_PORTAL_SEARCH_RESULT_NUMBER' => 'Number of results to display on Global Search',
    'LBL_PORTAL_DEFAULT_ASSIGN_USER' => 'Default assigned for new portal registrations',
    'LBL_PORTAL_MODULES' => 'Portal modules',
    'LBL_CONFIG_PORTAL_CONTACT_INFO' => 'Portal Contact Information',
    'LBL_CONFIG_PORTAL_CONTACT_INFO_HELP' => 'Configure the contact information that is presented to Portal users who require additional assistance with their account. At least one option must be configured.',
    'LBL_CONFIG_PORTAL_MODULES_HELP' => 'Drag and drop the names of the Portal modules to set them to be displayed or hidden in the Portal\'s top navigation bar. To control Portal user access to modules, use <a href="?module=ACLRoles&action=index">Role Management.</a>',
    'LBL_CONFIG_PORTAL_MODULES_DISPLAYED' => 'Displayed Modules',
    'LBL_CONFIG_PORTAL_MODULES_HIDDEN' => 'Hidden Modules',

'LBL_PORTAL'=>'Portal',
'LBL_PORTAL_LAYOUTS'=>'Portal Layouts',
'LBL_SYNCP_WELCOME'=>'Please enter the URL of the portal instance you wish to update.',
'LBL_SP_UPLOADSTYLE'=>'Select a style sheet to upload from your computer.<br> The style sheet will be implemented in the Sugar Portal the next time you perform a sync.',
'LBL_SP_UPLOADED'=> 'Uploaded',
'ERROR_SP_UPLOADED'=>'Please be sure that you are uploading a css style sheet.',
'LBL_SP_PREVIEW'=>'Here is a preview of what the Sugar Portal will look like using the style sheet.',
'LBL_PORTALSITE'=>'Sugar Portal URL: ',
'LBL_PORTAL_GO'=>'Go',
'LBL_UP_STYLE_SHEET'=>'Upload Style Sheet',
'LBL_QUESTION_SUGAR_PORTAL' => 'Select a Sugar Portal layout to edit.',
'LBL_QUESTION_PORTAL' => 'Select a portal layout to edit.',
'LBL_SUGAR_PORTAL'=>'Sugar Portal Editor',
'LBL_USER_SELECT' => '-- Select --',

//PORTAL PREVIEW
'LBL_CASES'=>'Cases',
'LBL_NEWSLETTERS'=>'Newsletters',
'LBL_BUG_TRACKER'=>'Bug Tracker',
'LBL_MY_ACCOUNT'=>'My Account',
'LBL_LOGOUT'=>'Logout',
'LBL_CREATE_NEW'=>'Create New',
'LBL_LOW'=>'Low',
'LBL_MEDIUM'=>'Medium',
'LBL_HIGH'=>'High',
'LBL_NUMBER'=>'Number:',
'LBL_PRIORITY'=>'Priority:',
'LBL_SUBJECT'=>'Subject',

//PACKAGE AND MODULE BUILDER
'LBL_PACKAGE_NAME'=>'Package Name:',
'LBL_MODULE_NAME'=>'Module Name:',
'LBL_MODULE_NAME_SINGULAR' => 'Singular Module Name:',
'LBL_AUTHOR'=>'Author:',
'LBL_DESCRIPTION'=>'Description:',
'LBL_KEY'=>'Key:',
'LBL_ADD_README'=>' Readme',
'LBL_MODULES'=>'Modules:',
'LBL_LAST_MODIFIED'=>'Last Modified:',
'LBL_NEW_MODULE'=>'New Module',
'LBL_LABEL'=>'Plural Label',
'LBL_LABEL_TITLE'=>'Label',
'LBL_SINGULAR_LABEL' => 'Singular Label',
'LBL_WIDTH'=>'Width',
'LBL_PACKAGE'=>'Package:',
'LBL_TYPE'=>'Type:',
'LBL_TEAM_SECURITY'=>'Team Security',
'LBL_ASSIGNABLE'=>'Assignable',
'LBL_PERSON'=>'Person',
'LBL_COMPANY'=>'Company',
'LBL_ISSUE'=>'Issue',
'LBL_SALE'=>'Sale',
'LBL_FILE'=>'File',
'LBL_NAV_TAB'=>'Navigation Tab',
'LBL_CREATE'=>'Create',
'LBL_LIST'=>'List',
'LBL_VIEW'=>'View',
'LBL_LIST_VIEW'=>'List View',
'LBL_HISTORY'=>'View History',
'LBL_RESTORE_DEFAULT_LAYOUT'=>'Restore Default Layout',
'LBL_ACTIVITIES'=>'Activity Stream',
'LBL_SEARCH'=>'Search',
'LBL_NEW'=>'New',
'LBL_TYPE_BASIC'=>'basic',
'LBL_TYPE_COMPANY'=>'company',
'LBL_TYPE_PERSON'=>'person',
'LBL_TYPE_ISSUE'=>'issue',
'LBL_TYPE_SALE'=>'sale',
'LBL_TYPE_FILE'=>'file',
'LBL_RSUB'=>'This is the subpanel that will be displayed in your module',
'LBL_MSUB'=>'This is the subpanel that your module provides to the related module for display',
'LBL_MB_IMPORTABLE'=>'Allow Imports',

// VISIBILITY EDITOR
'LBL_VE_VISIBLE'=>'visible',
'LBL_VE_HIDDEN'=>'hidden',
'LBL_PACKAGE_WAS_DELETED'=>'[[package]] was deleted',

//EXPORT CUSTOMS
'LBL_EC_TITLE'=>'Export Customizations',
'LBL_EC_NAME'=>'Package Name:',
'LBL_EC_AUTHOR'=>'Author:',
'LBL_EC_DESCRIPTION'=>'Description:',
'LBL_EC_KEY'=>'Key:',
'LBL_EC_CHECKERROR'=>'Please select a module.',
'LBL_EC_CUSTOMFIELD'=>'customized field(s)',
'LBL_EC_CUSTOMLAYOUT'=>'customized layout(s)',
'LBL_EC_CUSTOMDROPDOWN' => 'customized dropdown(s)',
'LBL_EC_NOCUSTOM'=>'No modules have been customized.',
'LBL_EC_EXPORTBTN'=>'Export',
'LBL_MODULE_DEPLOYED' => 'Module has been deployed.',
'LBL_UNDEFINED' => 'undefined',
'LBL_EC_CUSTOMLABEL'=>'customized label(s)',

//AJAX STATUS
'LBL_AJAX_FAILED_DATA' => 'Failed to retrieve data',
'LBL_AJAX_TIME_DEPENDENT' => 'A time dependent action is in progress. Please wait and try again in a few seconds.',
'LBL_AJAX_LOADING' => 'Loading...',
'LBL_AJAX_DELETING' => 'Deleting...',
'LBL_AJAX_BUILDPROGRESS' => 'Build In Progress...',
'LBL_AJAX_DEPLOYPROGRESS' => 'Deploy In Progress...',
'LBL_AJAX_FIELD_EXISTS' =>'The field name you entered already exists. Please enter a new field name.',
//JS
'LBL_JS_REMOVE_PACKAGE' => 'Are you sure you wish to remove this package? This will permanently delete all files associated with this package.',
'LBL_JS_REMOVE_MODULE' => 'Are you sure you wish to remove this module? This will permanently delete all files associated with this module.',
'LBL_JS_DEPLOY_PACKAGE' => 'Any customizations that you made in Studio will be overwritten when this module is re-deployed. Are you sure you want to proceed?',

'LBL_DEPLOY_IN_PROGRESS' => 'Deploying Package',
'LBL_JS_VALIDATE_NAME'=>'Name - Must start with a letter and may only consist of letters, numbers, and underscores. No spaces or other special characters may be used.',
'LBL_JS_VALIDATE_PACKAGE_KEY'=>'Package Key already exists',
'LBL_JS_VALIDATE_PACKAGE_NAME'=>'Package Name already exists',
'LBL_JS_PACKAGE_NAME'=>'Package Name - Must start with a letter and may only consist of letters, numbers, and underscores. No spaces or other special characters may be used.',
'LBL_JS_VALIDATE_KEY_WITH_SPACE'=>'Key - Must be alphanumeric and begin with a letter.',
'LBL_JS_VALIDATE_KEY'=>'Key - Must be alphanumeric, begin with a letter and contain no spaces.',
'LBL_JS_VALIDATE_LABEL'=>'Please enter a label that will be used as the Display Name for this module',
'LBL_JS_VALIDATE_TYPE'=>'Please select the type of module you wish to build from the list above',
'LBL_JS_VALIDATE_REL_NAME'=>'Name - Must be alphanumeric with no spaces',
'LBL_JS_VALIDATE_REL_LABEL'=>'Label - please add a label that will be displayed above the subpanel',

// Dropdown lists
'LBL_JS_DELETE_REQUIRED_DDL_ITEM' => 'Are you sure you wish to delete this required dropdown list item? This may affect the functionality of your application.',

// Specific dropdown list should be:
// LBL_JS_DELETE_REQUIRED_DDL_ITEM_(UPPERCASE_DDL_NAME)
'LBL_JS_DELETE_REQUIRED_DDL_ITEM_SALES_STAGE_DOM' => 'Are you sure you wish to delete this dropdown list item? Deleting the Closed Won or Closed Lost stages will cause the Forecasting module to not work properly',

// Specific list items should be:
// LBL_JS_DELETE_REQUIRED_DDL_ITEM_(UPPERCASE_ITEM_NAME)
// Item name should have all special characters removed and spaces converted to
// underscores
//BEGIN SUGARCRM flav=ent ONLY
'LBL_JS_DELETE_REQUIRED_DDL_ITEM_NEW' => 'Are you sure you wish to delete the New sales status? Deleting this status will cause the Opportunities module Revenue Line Item workflow to not work properly.',
'LBL_JS_DELETE_REQUIRED_DDL_ITEM_IN_PROGRESS' => 'Are you sure you wish to delete the In Progress sales status? Deleting this status will cause the Opportunities module Revenue Line Item workflow to not work properly.',
//END SUGARCRM flav=ent ONLY
'LBL_JS_DELETE_REQUIRED_DDL_ITEM_CLOSED_WON' => 'Are you sure you wish to delete the Closed Won sales stage? Deleting this stage will cause the Forecasting module to not work properly',
'LBL_JS_DELETE_REQUIRED_DDL_ITEM_CLOSED_LOST' => 'Are you sure you wish to delete the Closed Lost sales stage? Deleting this stage will cause the Forecasting module to not work properly',

//CONFIRM
'LBL_CONFIRM_FIELD_DELETE'=>'Deleting this custom field will delete both the custom field and all the data related to the custom field in the database. The field will be no longer appear in any module layouts.'
        . ' If the field is involved in a formula to calculate values for any fields, the formula will no longer work.'
        . '\n\nThe field will no longer be available to use in Reports; this change will be in effect after logging out and logging back in to the application. Any reports containing the field will need to be updated in order to be able to be run.'
        . '\n\nDo you wish to continue?',
'LBL_CONFIRM_RELATIONSHIP_DELETE'=>'Are you sure you wish to delete this relationship?<br>Note: This operation may not complete for several minutes.',
'LBL_CONFIRM_RELATIONSHIP_DEPLOY'=>'This will make this relationship permanent. Are you sure you wish to deploy this relationship?',
'LBL_CONFIRM_DONT_SAVE' => 'Changes have been made since you last saved, would you like to save?',
'LBL_CONFIRM_DONT_SAVE_TITLE' => 'Save Changes?',
'LBL_CONFIRM_LOWER_LENGTH' => 'Data may be truncated and this cannot be undone, are you sure you wish to continue?',

//POPUP HELP
'LBL_POPHELP_FIELD_DATA_TYPE'=>'Select the appropriate data type based on the type of data that will be entered into the field.',
'LBL_POPHELP_FTS_FIELD_CONFIG' => 'Configure the field to be full text searchable.',
'LBL_POPHELP_FTS_FIELD_BOOST' => 'Boosting is the process of enhancing the relevancy of a record\\\'s fields.<br />Fields with a higher boost level will be given greater weight when the search is performed. When a search is performed, matching records containing fields with a greater weight will be appear higher in the search results.<br />The default value is 1.0 which stands for a neutral boost. To apply a positive boost any float value higher than 1 is accepted. For a negative boost use values lower than 1. For example a value of 1.35 will positively boost a field by 135%. Using a value of 0.60 will apply a negative boost.<br />Note that in previous versions it was required to perform a full text search reindex. This is no longer required.',
'LBL_POPHELP_IMPORTABLE'=>'<b>Yes</b>: The field will be included in an import operation.<br><b>No</b>: The field will not be included in an import.<br><b>Required</b>: A value for the field must be provided in any import.',
'LBL_POPHELP_PII'=>'This field will be automatically marked for audit and available in the Personal Info view.<br>Personal Information fields can also be permanently erased when the record is related to a Data Privacy erasure request.<br>Erasure is done via the Data Privacy module and can be executed by admins or users in the Data Privacy Manager role.',
'LBL_POPHELP_IMAGE_WIDTH'=>'Enter a number for Width, as measured in pixels.<br> The uploaded image will be scaled to this Width.',
'LBL_POPHELP_IMAGE_HEIGHT'=>'Enter a number for the Height, as measured in pixels.<br> The uploaded image will be scaled to this Height.',
'LBL_POPHELP_DUPLICATE_MERGE'=>'<b>Enabled</b>: The field will appear in the Merge Duplicates feature, but will not be available to use for the filter conditions in the Find Duplicates feature.<br><b>Disabled</b>: The field will not appear in the Merge Duplicates feature, and will not be available to use for the filter conditions in the Find Duplicates feature.'
. '<br><b>In Filter</b>: The field will appear in the Merge Duplicates feature, and will also be available in the Find Duplicates feature.<br><b>Filter Only</b>: The field will not appear in the Merge Duplicates feature, but will be available in the Find Duplicates feature.<br><b>Default Selected Filter</b>: The field will be used for a filter condition by default in the Find Duplicates page, and will also appear in the Merge Duplicates feature.'
,
'LBL_POPHELP_CALCULATED'=>"Create a formula to determine the value in this field.<br>"
   . "Workflow definitions containing an action that are set to update this field will no longer execute the action.<br>"
   . "Fields using formulas will not be calculated in real-time in "
   //BEGIN SUGARCRM flav=ent ONLY
   . "the Sugar Self-Service Portal or "
   //END SUGARCRM flav=ent ONLY
   . "Mobile EditView layouts.",

'LBL_POPHELP_DEPENDENT'=>"Create a formula to determine whether this field is visible in layouts.<br/>"
        . "Dependent fields will follow the dependency formula in the browser-based mobile view, <br/>"
        . "but will not follow the formula in the native applications, such as Sugar Mobile for iPhone. <br/>"
        . "They will not follow the formula in the Sugar Self-Service Portal.",
'LBL_POPHELP_REQUIRED'=>"Create a formula to determine whether this field is required in layouts.<br/>"
    . "Required fields will follow the formula in the browser-based mobile view, <br/>"
    . "but will not follow the formula in the native applications, such as Sugar Mobile for iPhone. <br/>"
    . "They will not follow the formula in the Sugar Self-Service Portal.",
'LBL_POPHELP_GLOBAL_SEARCH'=>'Select to use this field when searching for records using the Global Search on this module.',
//Revert Module labels
'LBL_RESET' => 'Reset',
'LBL_RESET_MODULE' => 'Reset Module',
'LBL_REMOVE_CUSTOM' => 'Remove Customizations',
'LBL_CLEAR_RELATIONSHIPS' => 'Clear Relationships',
'LBL_RESET_LABELS' => 'Reset Labels',
'LBL_RESET_LAYOUTS' => 'Reset Layouts',
'LBL_REMOVE_FIELDS' => 'Remove Custom Fields',
'LBL_CLEAR_EXTENSIONS' => 'Clear Extensions',

'LBL_HISTORY_TIMESTAMP' => 'TimeStamp',
'LBL_HISTORY_TITLE' => ' history',

'fieldTypes' => array(
                'varchar'=>'TextField',
                'int'=>'Integer',
                'float'=>'Float',
                'bool'=>'Checkbox',
                'enum'=>'DropDown',
                'multienum' => 'MultiSelect',
                'date'=>'Date',
                'phone' => 'Phone',
                'currency' => 'Currency',
                'html' => 'HTML',
                'radioenum' => 'Radio',
                'relate' => 'Relate',
                'address' => 'Address',
                'text' => 'TextArea',
                'url' => 'URL',
                'iframe' => 'IFrame',
                'image' => 'Image',
                'encrypt'=>'Encrypt',
                'datetimecombo' =>'Datetime',
                'decimal'=>'Decimal',
                'autoincrement' => 'AutoIncrement',
),
'labelTypes' => array(
    "" => "Frequently used labels",
    "all" => "All Labels",
),

'parent' => 'Flex Relate',

'LBL_ILLEGAL_FIELD_VALUE' =>"Drop down key cannot contain quotes.",
'LBL_CONFIRM_SAVE_DROPDOWN' =>"You are selecting this item for removal from the dropdown list. Any dropdown fields using this list with this item as a value will no longer display the value, and the value will no longer be able to be selected from the dropdown fields. Are you sure you want to continue?",
'LBL_POPHELP_VALIDATE_US_PHONE'=>"Select to validate this field for the entry of a 10-digit<br>" .
                                 "phone number, with allowance for the country code 1, and<br>" .
                                 "to apply a U.S. format to the phone number when the record<br>" .
                                 "is saved. The following format will be applied: (xxx) xxx-xxxx.",
'LBL_ALL_MODULES'=>'All Modules',
'LBL_RELATED_FIELD_ID_NAME_LABEL' => '{0} (related {1} ID)',
// BEGIN SUGARCRM flav=ent ONLY
'LBL_HEADER_COPY_FROM_LAYOUT' => 'Copy from layout',
// END SUGARCRM flav=ent ONLY
);
