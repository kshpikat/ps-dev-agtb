{*
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
*}
{$moduleTitle}
<script type="text/javascript"
        src="{sugar_getjspath file='cache/include/javascript/sugar_grp_yui_widgets.js'}"></script>
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Connectors/tpls/tabs.css'}"/>
<link rel="stylesheet" type="text/css" href="{sugar_getjspath file='modules/Teams/css/custom.css'}"/>

<form name="TBAConfiguration" method="POST">

    <input type="hidden" name="module" value="Administration">
    <input type="hidden" name="action" value="saveTBAConfiguration">

    <span class="error">{$error.main}</span>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="actionsContainer">
        <tr>
            <td>
                <input title="{$APP.LBL_CANCEL_BUTTON_TITLE}"
                       onclick="document.location.href='index.php?module=Administration&action=index'"
                       class="button" type="button" name="cancel" value="{$APP.LBL_CANCEL_BUTTON_LABEL}">
                &nbsp;
                <input title="{$APP.LBL_SAVE_BUTTON_TITLE}"
                       accessKey="{$APP.LBL_SAVE_BUTTON_KEY}"
                       class="button primary"
                       type="button"
                       name="save"
                       value="{$APP.LBL_SAVE_BUTTON_LABEL}"/>
            </td>
        </tr>
    </table>

    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view">
        <tr>
            <td align="left" scope="row" colspan="2" class="left">
                <div class="padding-bottom-20">{$MOD.LBL_TBA_CONFIGURATION_TITLE}</div>
                <div class="padding-bottom-20">{$MOD.LBL_TBA_CONFIGURATION_WARNING_DESC}</div>
            </td>
        </tr>
        <tr>
            <td align="left" scope="row" width="300" class="left">{$MOD.LBL_TBA_CONFIGURATION_LABEL}</td>
            <td scope="row" class="left bg-white">
                <input id="tba_set_enabled" type="checkbox" name="team_based[enable]" value="true"
                       {if $config.enabled}checked="checked"{/if} />
            </td>
        </tr>
    </table>

    <table id="tba_em_block" width="100%" border="0" cellspacing="1" cellpadding="0" class="edit view"
           {if !$config.enabled}style="display: none;"{/if}>
        <tr>
            <th align="left" scope="row"><h4>{$MOD.LBL_TBA_CONFIGURATION_MOD_LABEL}</h4></th>
        </tr>
        <tr>
            <td align="left" class="padding-0">
                <table width="100%" border="0" cellspacing="10" cellpadding="0" class="edit view">
                    <tr>
                    {foreach from=$actionsList key=key item=value}
                        <td class="title {if !$value|in_array:$config.disabled_modules}active{/if}">
                            <div>
                                <input type="checkbox" name="team_based[disabled_modules][]"
                                       data-group="tba_em" value="{$value}" id="tba_em_{$key}"
                                       {if !$value|in_array:$config.disabled_modules}checked="checked"{/if}/>
                                <label for="tba_em_{$key}">{$APP_LIST.moduleList[$value]}</label>
                            </div>
                        </td>
                        {if ($key+1) % 4 eq 0}</tr><tr>{/if}
                    {/foreach}
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    var labelSaving = '{$APP.LBL_SAVING}',
        labelDone = '{$APP.LBL_DONE_BUTTON_LABEL}',
        labelWarning = '{$MOD.LBL_TBA_CONFIGURATION_WARNING}',
        disabledModules = app.config.teamBasedAcl.disabledModules;
    {literal}
    $(document).ready(function() {
        var stateChanged = false;

        $('input[data-group=tba_em]').on('click', function() {
            var $td = $(this).closest('td.title');
            stateChanged = true;
            if ($td.hasClass('active')) {
                $td.removeClass('active');
            } else {
                $td.addClass('active');
            }
        });

        if ($('input#tba_set_enabled').attr('checked') === 'checked') {
            $('#tba_em_block').show();
        } else {
            $('#tba_em_block').hide();
        }

        $('input#tba_set_enabled').on('click', function() {
            stateChanged = true;
            if ($(this).attr('checked') === 'checked') {
                _.each($('input[data-group=tba_em]'), function(item) {
                    if (_.indexOf(disabledModules, $(item).val()) === -1) {
                        $(item).attr('checked', 'checked');
                    }
                });
                $('#tba_em_block').show();
            } else {
                $('#tba_em_block').hide();
            }
        });

        $('input[name=save]').on('click', function() {
            var disabledModules = [],
                isTBEnabled = $('input#tba_set_enabled').attr('checked') === 'checked';

            if (stateChanged) {
                app.alert.show('submit_tba_confirmation', {
                    level: 'confirmation',
                    messages: labelWarning,
                    onConfirm: function() {
                        if (isTBEnabled) {
                            $.each($('input[data-group=tba_em]:not(:checked)'), function(index, item) {
                                disabledModules.push($(item).val());
                            });
                        }

                        ajaxStatus.showStatus(labelSaving);

                        var queryString = SUGAR.util.paramsToUrl({
                                    module: 'Teams',
                                    action: 'savetbaconfiguration',
                                    enabled: isTBEnabled,
                                    disabled_modules: disabledModules,
                                    csrf_token: SUGAR.csrf.form_token
                                }) + 'to_pdf=1';

                        $.ajax({
                            url: 'index.php',
                            data: queryString,
                            type: 'POST',
                            dataType: 'json',
                            timeout: 300000,
                            success: function(response) {
                                ajaxStatus.flashStatus(labelDone);
                                if (response['status'] === true) {
                                    window.location.assign('index.php?module=Administration&action=index');
                                }
                            }
                        });
                    }
                });
            } else {
                window.location.assign('index.php?module=Administration&action=index');
            }
        });
    });
{/literal}
</script>
