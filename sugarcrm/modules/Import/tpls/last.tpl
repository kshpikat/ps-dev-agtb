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
*}

{literal}
<style>
div.resultsTable {
    overflow: auto;
    width: 1056px;
    padding-top: 20px;
    position: relative;
}
</style>
{/literal}

<h2>
	<p>{$MOD.LBL_SUMMARY}</p>
</h2>
<br/>
<span style="font-size: 14px">
{if $createdCount > 0}
<b>{$createdCount}</b>&nbsp;{$MOD.LBL_SUCCESSFULLY_IMPORTED}<br />
{/if}
{if $updatedCount > 0}
<b>{$updatedCount}</b>&nbsp;{$MOD.LBL_UPDATE_SUCCESSFULLY}<br />
{/if}
{if $errorCount > 0}
<b>{$errorCount}</b>&nbsp;{$MOD.LBL_RECORDS_SKIPPED_DUE_TO_ERROR}<br />
{/if}
{if $dupeCount > 0}
<b>{$dupeCount}</b>&nbsp;{$MOD.LBL_DUPLICATES}<br />
{/if}
</span>
<form name="importlast" id="importlast" method="POST" action="index.php">
<input type="hidden" name="module" value="Import">
<input type="hidden" name="action" value="Undo">
<input type="hidden" name="has_header" value="{$smarty.request.has_header}">
<input type="hidden" name="import_module" value="{$IMPORT_MODULE}">

<br />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="left" style="padding-bottom: 2px;">
        {if $showUndoButton}
            <input title="{$MOD.LBL_UNDO_LAST_IMPORT}" accessKey="" class="button"
                type="submit" name="undo" id="undo" value="  {$MOD.LBL_UNDO_LAST_IMPORT}  ">
        {/if}
        <input title="{$MOD.LBL_IMPORT_MORE}" accessKey="" class="button" type="submit" name="importmore" id="importmore" value="  {$MOD.LBL_IMPORT_MORE}  ">
        <input title="{$MOD.LBL_FINISHED}{$MODULENAME}" accessKey="" class="button" type="submit" name="finished" id="finished" value="  {$MOD.LBL_IMPORT_COMPLETE}  ">
            <!--//BEGIN SUGARCRM flav!=sales ONLY -->
            {$PROSPECTLISTBUTTON}
            <!--//END SUGARCRM flav!=sales ONLY -->
        </td>
    </tr>
</table>
</form>

<br/>
    
<table width="100%" id="tabListContainerTable" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td nowrap id="tabListContainerTD">
            <div id="tabListContainer" class="yui-module yui-scroll">
                <div class="yui-hd">
                    <span class="yui-scroll-controls" style="visibility: none">
                        <a title="scroll left" class="yui-scrollup"><em>scroll left</em></a>
                        <a title="scroll right" class="yui-scrolldown"><em>scroll right</em></a>
                    </span>
                </div>
                <div class="yui-bd">
                    <ul class="subpanelTablist" id="tabList">
                        <li id="pageNumIW_0" class="active" >
                            <a id="pageNumIW_0_anchor" class="current" href="javascript:SUGAR.IV.togglePages('0');">
                            <span id="pageNum_0_input_span" style="display:none;">
                            <input type="hidden" id="pageNum_0_name_hidden_input" value="{$pageData.pageTitle}"/>
                            <input type="text" id="pageNum_0_name_input" value="Testing" size="10"/>
                            </span>
                            <span id="pageNum_0_link_span" class="tabText">
                            <span id="pageNum_0_title_text">{$MOD.LBL_CREATED_TAB}</span>
                            </span>
                            </a>
                        </li>
                        <li id="pageNumIW_1" >
                            <a id="pageNumIW_1_anchor" class="" href="javascript:SUGAR.IV.togglePages('1');">
                            <span id="pageNum_1_input_span" style="display:none;">
                            <input type="hidden" id="pageNum_1_name_hidden_input" value="{$pageData.pageTitle}"/>
                            <input type="text" id="pageNum_1_name_input" value="Testing" size="10"/>
                            </span>
                            <span id="pageNum_1_link_span" class="tabText">
                            <span id="pageNum_1_title_text">{$MOD.LBL_DUPLICATE_TAB}</span>
                            </span>
                            </a>
                        </li>
                        <li id="pageNumIW_2" >
                            <a id="pageNumIW_2_anchor" class="" href="javascript:SUGAR.IV.togglePages('2');">
                            <span id="pageNum_2_input_span" style="display:none;">
                            <input type="hidden" id="pageNum_2_name_hidden_input" value="{$pageData.pageTitle}"/>
                            <input type="text" id="pageNum_2_name_input" value="Testing" size="10" />
                            </span>
                            <span id="pageNum_2_link_span" class="tabText">
                            <span id="pageNum_2_title_text">{$MOD.LBL_ERROR_TAB}</span>
                            </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="addPage" style="visibility: hidden">
                <a href='javascript:void(0)' id="add_page"></a>
            </div>
        </td>
        <td nowrap id="dashletCtrlsTD">
            <div id="dashletCtrls" style="margin:0;padding:0;"></div>
        </td>
    </tr>
</table>

<div style='width:100%'>
    <div id="pageNumIW_0_div">{$RESULTS_TABLE}</div>
    <div id="pageNumIW_1_div" style="display:none;" ><br/>
        {if $dupeCount > 0}
            <a href ="{$dupeFile}" target='_blank'>{$MOD.LNK_DUPLICATE_LIST}</a><br />
        {/if}
        <br/>
        {$MOD.LBL_DUP_HELP}
        <div id="dup_table" class="resultsTable">
            {$DUP_TABLE}
        </div>
    </div>
    <div id="pageNumIW_2_div" style="display: none;" ><br/>
        {$MOD.LBL_ERROR_HELP}
        {if $errorCount > 0}
            <br/><br/>
            <a href="{$errorFile}" target='_blank'>{$MOD.LNK_ERROR_LIST}</a><br />
            <a href ="{$errorrecordsFile}" target='_blank'>{$MOD.LNK_RECORDS_SKIPPED_DUE_TO_ERROR}</a><br />
        {/if}
        <div id="errors_table" class="resultsTable">
            {$ERROR_TABLE}
        </div>
    </div>
</div>

<!--//BEGIN SUGARCRM flav!=sales ONLY -->
{if $PROSPECTLISTBUTTON != ''}
<form name="DetailView">
    <input type="hidden" name="module" value="Prospects">
    <input type="hidden" name="record" value="id">
</form>
{/if}
<!--//END SUGARCRM flav!=sales ONLY -->
