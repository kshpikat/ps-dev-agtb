{*

/**
 * LICENSE: The contents of this file are subject to the SugarCRM Professional
 * End User License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You
 * may not use this file except in compliance with the License.  Under the
 * terms of the license, You shall not, among other things: 1) sublicense,
 * resell, rent, lease, redistribute, assign or otherwise transfer Your
 * rights to the Software, and 2) use the Software for timesharing or service
 * bureau purposes such as hosting the Software for commercial gain and/or for
 * the benefit of a third party.  Use of the Software may be subject to
 * applicable fees and any use of the Software without first paying applicable
 * fees is strictly prohibited.  You do not have the right to remove SugarCRM
 * copyrights from the source code or user interface.
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
 * by SugarCRM are Copyright (C) 2006 SugarCRM, Inc.; All Rights Reserved.
 */

// $Id: DashletGenericConfigure.tpl 48260 2009-06-09 14:34:52Z jmertic $

*}


<div>
<form action='index.php' id='configure_{$id}' method='post' onSubmit='SUGAR.mySugar.setChooser(); return SUGAR.dashlets.postForm("configure_{$id}", SUGAR.mySugar.uncoverPage);'>
<input type='hidden' name='id' value='{$id}'>
<input type='hidden' name='module' value='Home'>
<input type='hidden' name='action' value='ConfigureDashlet'>
<input type='hidden' name='configure' value='true'>
<input type='hidden' name='to_pdf' value='true'>
<input type='hidden' id='displayColumnsDef' name='displayColumnsDef' value=''>
<input type='hidden' id='hideTabsDef' name='hideTabsDef' value=''>
<input type='hidden' id='dashletType' name='dashletType' value='' />

<table cellpadding="0" cellspacing="0" border="0" width="100%" class="edit view">
	<tr>
        <td scope='row'colspan='4' align='left'>
        	<h2>{$strings.general}</h2>
        </td>
    </tr>
    <tr>
	    <td scope='row'>
		    {$strings.title}
        </td>
        <td colspan='3'>
            <input type='text' name='dashletTitle' value='{$dashletTitle}'>
        </td>
	</tr>
    <tr>
	    <td scope='row'>
		    {$strings.displayRows}
        </td>
        <td{if !$isRefreshable} colspan='3'{/if}>
            <select name='displayRows'>
				{html_options values=$displayRowOptions output=$displayRowOptions selected=$displayRowSelect}
           	</select>
        </td>
        {if $isRefreshable}
        <td scope='row'>
		    {$strings.autoRefresh}
        </td>
        <td>
            <select name='autoRefresh'>
				{html_options options=$autoRefreshOptions selected=$autoRefreshSelect}
           	</select>
        </td>
        {/if}
    </tr>
    <tr>
        <td colspan='4' align='center'>
        	<table border='0' cellpadding='0' cellspacing='0'>
        	<tr><td>
			    {$columnChooser}
		    </td>
		    </tr></table>
	    </td>    
	</tr>
	<tr>
        <td scope='row'colspan='4' align='left'>
	        <br>
        	<h2>{$strings.filters}</h2>
        </td>
    </tr>
    {if $showMyItemsOnly}
    <tr>
	    <td scope='row'>
            {$strings.myItems}
        </td>
        <td>
            <input type='checkbox' {if $myItemsOnly == 'true'}checked{/if} name='myItemsOnly' value='true'>
        </td>
    </tr>
    {/if}
    <tr>
    {foreach name=searchIteration from=$searchFields key=name item=params}
        <td scope='row' valign='top'>
            {$params.label}
        </td>
        <td valign='top' style='padding-bottom: 5px'>
            {$params.input}
        </td>
        {if ($smarty.foreach.searchIteration.iteration is even) and $smarty.foreach.searchIteration.iteration != $smarty.foreach.searchIteration.last}
        </tr><tr>
        {/if}
    {/foreach}
    </tr>
    <tr>
	    <td colspan='4' align='right'>
	        <input type='submit' class='button' value='{$strings.save}'>
	        <input type='submit' class='button' value='{$strings.clear}' onclick='SUGAR.searchForm.clear_form(this.form);return false;'>
	    </td>    
	</tr>
</table>
</form>
</div>