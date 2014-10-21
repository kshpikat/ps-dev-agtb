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
/**
 * @class View.Fields.Base.Meetings.ClosebuttonField
 * @alias SUGAR.App.view.fields.BaseMeetingsClosebuttonField
 * @extends View.Fields.Base.ClosebuttonField
 */
({
    extendsFrom: 'ClosebuttonField',

    /**
     * Status indicating that the meeting is closed or complete.
     *
     * @type {String}
     */
    closedStatus: 'Held',

    /**
     * @inheritdoc
     */
    showSuccessMessage: function() {
        app.alert.show('close_meeting_success', {
            level: 'success',
            autoClose: true,
            title: app.lang.get('LBL_MEETING_CLOSE_SUCCESS', this.module)
        });
    }
})
