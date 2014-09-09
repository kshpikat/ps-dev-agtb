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
 * @class View.Views.Base.HeaderpaneView
 * @alias SUGAR.App.view.views.BaseHeaderpaneView
 * @extends View.View
 */
({
    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);

        if (this.meta && this.meta.title) {
            this.title = this.meta.title;
        }

        this.context.on('headerpane:title', function(title) {
            this.title = title;
            if (!this.disposed) this.render();
        }, this);

        //shortcut keys
        app.shortcuts.register('Headerpane:Cancel', ['esc','ctrl+alt+l'], function() {
            var $cancelButton = this.$('a[name=cancel_button]'),
                $closeButton = this.$('a[name=close]');

            if ($cancelButton.is(':visible') && !$cancelButton.hasClass('disabled')) {
                $cancelButton.click();
            } else if ($closeButton.is(':visible') && !$closeButton.hasClass('disabled')) {
                $closeButton.click();
            }
        }, this, true);
        app.shortcuts.register('Headerpane:Save', ['ctrl+s','ctrl+alt+a'], function() {
            var $saveButton = this.$('a[name=save_button]');
            if ($saveButton.is(':visible') && !$saveButton.hasClass('disabled')) {
                $saveButton.click();
            }
        }, this, true);
    },

    /**
     * @inheritDoc
     */
    _renderHtml: function() {
        /**
         * The title being rendered in the headerpane.
         *
         * @type {string}
         */
        this.title = this.formatTitle(this.title || this.module);

        this._super('_renderHtml');
    },

    /**
     * Formats the title before being rendered.
     *
     * @param {string} title The unformatted title.
     * @return {string} The formatted title.
     */
    formatTitle: function(title) {
        return app.lang.get(title, this.module);
    }
})
