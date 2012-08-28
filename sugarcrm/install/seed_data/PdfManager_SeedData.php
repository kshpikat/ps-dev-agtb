<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
//FILE SUGARCRM flav=pro ONLY

require_once 'include/Sugarpdf/sugarpdf_config.php';
global $mod_strings, $installer_mod_strings;

include_once('modules/PdfManager/PdfManagerHelper.php');
$templatesArray = PdfManagerHelper::getPublishedTemplatesForModule('Quotes');

if (empty($templatesArray)) {

    $modStringSrc = return_module_language($GLOBALS['current_language'], 'PdfManager');
    
    $logoUrl = './themes/default/images/pdf_logo.jpg';
if (defined(PDF_HEADER_LOGO)) {
        $logoUrl = K_PATH_CUSTOM_IMAGES.PDF_HEADER_LOGO;
    $imsize = @getimagesize($logo);
    if ($imsize === FALSE) {
        // encode spaces on filename
            $logoUrl = str_replace(' ', '%20', $logo);
        $imsize = @getimagesize($logo);
        if ($imsize === FALSE) {
                $logoUrl = K_PATH_IMAGES.PDF_HEADER_LOGO;
        }
    }
        $logoUrl = './' . $logoUrl;
}

    $ss = new Sugar_Smarty();
    $ss->assign('logoUrl', $logoUrl);
    $ss->assign('MOD', $modStringSrc);
    $pdfTemplate = new PdfManager();
    $pdfTemplate->base_module = 'Quotes';
    $pdfTemplate->name = $modStringSrc['LBL_TPL_QUOTE_NAME'];
    $pdfTemplate->description = $modStringSrc['LBL_TPL_QUOTE_DESCRIPTION'];
    $pdfTemplate->body_html = to_html($ss->fetch('modules/PdfManager/tpls/templateQuote.tpl'));
    $pdfTemplate->template_name = $modStringSrc['LBL_TPL_QUOTE_TEMPLATE_NAME'];;
    $pdfTemplate->author = PDF_AUTHOR;
    $pdfTemplate->title = PDF_TITLE;
    $pdfTemplate->subject = PDF_SUBJECT;
    $pdfTemplate->keywords = PDF_KEYWORDS;
    $pdfTemplate->published = 'yes';
    $pdfTemplate->deleted = 0;
    $pdfTemplate->team_id = 1;
    $pdfTemplate->save();

    $pdfTemplate = new PdfManager();
    $pdfTemplate->base_module = 'Quotes';
    $pdfTemplate->name = $modStringSrc['LBL_TPL_INVOICE_NAME'];
    $pdfTemplate->description = $modStringSrc['LBL_TPL_INVOICE_DESCRIPTION'];
    $pdfTemplate->body_html = to_html($ss->fetch('modules/PdfManager/tpls/templateInvoice.tpl'));
    $pdfTemplate->template_name = $modStringSrc['LBL_TPL_INVOICE_TEMPLATE_NAME'];;
    $pdfTemplate->author = PDF_AUTHOR;
    $pdfTemplate->title = PDF_TITLE;
    $pdfTemplate->subject = PDF_SUBJECT;
    $pdfTemplate->keywords = PDF_KEYWORDS;
    $pdfTemplate->published = 'yes';
    $pdfTemplate->deleted = 0;
    $pdfTemplate->team_id = 1;
    $pdfTemplate->save();
}

