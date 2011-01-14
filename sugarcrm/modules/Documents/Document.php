<?php
if(!defined('sugarEntry') || !sugarEntry)
	die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
/*********************************************************************************
 * $Id: Document.php 23743 2007-06-19 01:21:46Z clee $
 * Description: TODO:  To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/
require_once ('include/upload_file.php');


// User is used to store Forecast information.
class Document extends SugarBean {

	var $id;
	var $document_name;
	var $description;
	var $category_id;
	var $subcategory_id;
	var $status_id;
	var $status;
	var $created_by;
	var $date_entered;
	var $date_modified;
	var $modified_user_id;
	//BEGIN SUGARCRM flav=pro ONLY
	var $team_id;
	//END SUGARCRM flav=pro ONLY
	var $active_date;
	var $exp_date;
	var $document_revision_id;
	var $filename;
	var $doc_type;

	var $img_name;
	var $img_name_bare;
	var $related_doc_id;
	var $related_doc_name;
	var $related_doc_rev_id;
	var $related_doc_rev_number;
	var $is_template;
	var $template_type;

	//additional fields.
	var $revision;
	var $last_rev_create_date;
	var $last_rev_created_by;
	var $last_rev_created_name;
	var $file_url;
	var $file_url_noimage;

	var $table_name = "documents";
	var $object_name = "Document";
	var $user_preferences;

	var $encodeFields = Array ();

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array ('revision');
	
	var $new_schema = true;
	var $module_dir = 'Documents';
	
	var $save_file;

	var $relationship_fields = Array(
		'contract_id'=>'contracts',
	 );
	  

	function Document() {
		parent :: SugarBean();
		$this->setupCustomFields('Documents'); //parameter is module name
		$this->disable_row_level_security = false;
	}

	function save($check_notify = false) {
		
		if (empty($this->doc_type)) {
			$this->doc_type = 'Sugar';
		}
		
        if (!empty($_FILES['filename_file']))
        {
            if (empty($this->id)) { 
                $this->id = create_guid();
                $this->new_with_id = true;
            }
            $Revision = new DocumentRevision();
            //save revision.
            $Revision->change_log = translate('DEF_CREATE_LOG','Documents');
            $Revision->revision = $this->revision;
            $Revision->document_id = $this->id;
            $Revision->filename = $this->filename;
            $Revision->file_ext = $this->file_ext;
            $Revision->file_mime_type = $this->file_mime_type;
            $Revision->doc_type = $this->doc_type;
            $Revision->doc_id = $this->doc_id;
            $Revision->doc_url = $this->doc_url;
            $Revision->doc_direct_url = $this->doc_direct_url;
            $Revision->save();
			
            //Move file saved during populatefrompost to match the revision id rather than document id
            rename(UploadFile :: get_url($this->filename, $this->id), UploadFile :: get_url($this->filename, $Revision->id));
            //update document with latest revision id
            $this->process_save_dates=false; //make sure that conversion does not happen again.
            $this->document_revision_id = $Revision->id;	
        }
		
        if (empty($this->id) || $this->new_with_id)
		{
            //set relationship field values if contract_id is passed (via subpanel create)
            if (!empty($_POST['contract_id'])) {
                $save_revision['document_revision_id']=$this->document_revision_id;	
                $this->load_relationship('contracts');
                $this->contracts->add($_POST['contract_id'],$save_revision);
            }
            
            if ((isset($_POST['load_signed_id']) and !empty($_POST['load_signed_id']))) {
                $query="update linked_documents set deleted=1 where id='".$_POST['load_signed_id']."'";
                $this->db->query($query);
            }
        }
        
		return parent :: save($check_notify);
	}
	function get_summary_text() {
		return "$this->document_name";
	}

	function is_authenticated() {
		return $this->authenticated;
	}

	function fill_in_additional_list_fields() {
		$this->fill_in_additional_detail_fields();
	}

	function fill_in_additional_detail_fields() {
		global $theme;
		global $current_language;
		global $timedate;
		global $locale;

		parent::fill_in_additional_detail_fields();
		
		$mod_strings = return_module_language($current_language, 'Documents');

		$query = "SELECT users.first_name AS first_name, users.last_name AS last_name, document_revisions.date_entered AS rev_date, document_revisions.filename AS filename, document_revisions.revision AS revision, document_revisions.file_ext AS file_ext FROM users, document_revisions WHERE users.id = document_revisions.created_by AND document_revisions.id = '$this->document_revision_id'";
		$result = $this->db->query($query);
		$row = $this->db->fetchByAssoc($result);

		//popuplate filename
        if(isset($row['filename']))$this->filename = $row['filename'];
        //$this->latest_revision = $row['revision'];
        if(isset($row['revision']))$this->revision = $row['revision'];
        
		//populate the file url. 
		//image is selected based on the extension name <ext>_icon_inline, extension is stored in document_revisions.
		//if file is not found then default image file will be used.
		global $img_name;
		global $img_name_bare;
		if (!empty ($row['file_ext'])) {
			$img_name = SugarThemeRegistry::current()->getImageURL(strtolower($row['file_ext'])."_image_inline.gif");
			$img_name_bare = strtolower($row['file_ext'])."_image_inline";
		}

		//set default file name.
		if (!empty ($img_name) && file_exists($img_name)) {
			$img_name = $img_name_bare;
		} else {
			$img_name = "def_image_inline"; //todo change the default image.						
		}
		if($this->ACLAccess('DetailView')){
			$file_url = "<a href='index.php?entryPoint=download&id=".basename(UploadFile :: get_url($this->filename, $this->document_revision_id))."&type=Documents' target='_blank'>".SugarThemeRegistry::current()->getImage($img_name, 'alt="'.$mod_strings['LBL_LIST_VIEW_DOCUMENT'].'"  border="0"')."</a>";

			if(!empty($this->doc_type) && $this->doc_type != 'Sugar' && !empty($this->doc_url))
                $file_url= "<a href='".$this->doc_url."' target='_blank'>".SugarThemeRegistry::current()->getImage($this->doc_type.'_image_inline', 'alt="'.$mod_strings['LBL_LIST_VIEW_DOCUMENT'].'"  border="0"',null,null,'.png')."</a>";
    		$this->file_url = $file_url;
    		$this->file_url_noimage = basename(UploadFile :: get_url($this->filename, $this->document_revision_id));
		}else{
            $this->file_url = "";
            $this->file_url_noimage = "";
		}
		
		//get last_rev_by user name.
		if (!empty ($row)) {
			$this->last_rev_created_name = $locale->getLocaleFormattedName($row['first_name'], $row['last_name']);

			$this->last_rev_create_date = $timedate->to_display_date_time($row['rev_date']);
		}
		
		global $app_list_strings;
	    if(!empty($this->status_id)) {
	       //_pp($this->status_id);
	       $this->status = $app_list_strings['document_status_dom'][$this->status_id];
	    }
        $this->related_doc_name = Document::get_document_name($this->related_doc_id);
        $this->related_doc_rev_number = DocumentRevision::get_document_revision_name($this->related_doc_rev_id);
        $this->save_file = basename($this->file_url_noimage);
        
	}

	function list_view_parse_additional_sections(& $list_form, $xTemplateSection) {
		return $list_form;
	}

    function create_export_query(&$order_by, &$where, $relate_link_join='')
    {
        $custom_join = $this->custom_fields->getJOIN(true, true,$where);
		if($custom_join)
				$custom_join['join'] .= $relate_link_join;
		$query = "SELECT
						documents.*";
		if($custom_join){
			$query .=  $custom_join['select'];
		}
		$query .= " FROM documents ";
		if($custom_join){
			$query .=  $custom_join['join'];
		}

		$where_auto = " documents.deleted = 0";

		if ($where != "")
			$query .= " WHERE $where AND ".$where_auto;
		else
			$query .= " WHERE ".$where_auto;

		if ($order_by != "")
			$query .= " ORDER BY $order_by";
		else
			$query .= " ORDER BY documents.document_name";

		return $query;
	}

	function get_list_view_data() {
		global $current_language;
		$app_list_strings = return_app_list_strings_language($current_language);

		$document_fields = $this->get_list_view_array();

        $this->fill_in_additional_list_fields();


		$document_fields['FILENAME'] = $this->filename;
		$document_fields['FILE_URL'] = $this->file_url;
		$document_fields['FILE_URL_NOIMAGE'] = $this->file_url_noimage;
		$document_fields['LAST_REV_CREATED_BY'] = $this->last_rev_created_name;
		$document_fields['CATEGORY_ID'] = empty ($this->category_id) ? "" : $app_list_strings['document_category_dom'][$this->category_id];
		$document_fields['SUBCATEGORY_ID'] = empty ($this->subcategory_id) ? "" : $app_list_strings['document_subcategory_dom'][$this->subcategory_id];
        $document_fields['NAME'] = $this->document_name;
		$document_fields['DOCUMENT_NAME_JAVASCRIPT'] = $GLOBALS['db']->helper->escape_quote($document_fields['DOCUMENT_NAME']);
		return $document_fields;
	}
	function mark_relationships_deleted($id) {
		//do nothing, this call is here to avoid default delete processing since  
		//delete.php handles deletion of document revisions.
	}

	function bean_implements($interface) {
		switch ($interface) {
			case 'ACL' :
				return true;
		}
		return false;
	}
	
	//static function.
	function get_document_name($doc_id){
		if (empty($doc_id)) return null;
		
		$db = DBManagerFactory::getInstance();				
		$query="select document_name from documents where id='$doc_id'";
		$result=$db->query($query);
		if (!empty($result)) {
			$row=$db->fetchByAssoc($result);
			if (!empty($row)) {
				return $row['document_name'];
			}
		}
		return null;
	}
}

require_once('modules/Documents/DocumentExternalApiDropDown.php');

