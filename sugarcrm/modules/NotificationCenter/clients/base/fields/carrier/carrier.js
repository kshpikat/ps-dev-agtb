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
 * @class View.Fields.Base.NotificationCenterCarrierField
 * @alias SUGAR.App.view.fields.BaseNotificationCenterCarrierField
 * @extends View.Fields.Base.BaseField
 */
({
    fieldTag: 'input[data-type=carrier]',

    /**
     * Config model carriers.
     */
    carriers: {},

    /**
     * Globally configured carriers. Only for user mode.
     */
    carriersGlobal: null,

    /**
     * @inheritDoc
     */
    initialize: function(options) {
        this._super('initialize', [options]);
        if (this.model.get('configMode') === 'user') {
            this.carriers = this.model.get('personal')['carriers'];
            this.carriersGlobal = this.model.get('global')['carriers'];
        } else {
            this.carriers = this.model.get('carriers');
        }

        this.model.on('reset:all', this.render, this);
        this.events = this.events || {};
        _.extend(this.events, {
            'click .configure': 'showConfiguration'
        });

    },

    /**
     * Open configuration for carrier in drawer.
     */
    showConfiguration: function () {
        var self = this;
        app.drawer.open({
            layout: this.def.config.configLayout,
            context: {
                create: true,
                module: this.name,
            }
        }, _.bind(this.onConfigClosed, this));
    },

    /**
     * Handle cloging carrier configuration.
     *
     * @param (boolean) isSaved is saved carrier configuration
     */
    onConfigClosed: function (isSaved) {
        var carriers = this.model.get('carriers');
        carriers[this.name].isConfigured = carriers[this.name].isConfigured || isSaved;
        this.model.set('carriers', carriers);
        this.render();
    },

    /**
     * @inheritDoc
     */
    format: function(value) {
        var globalConfig;
        if (this.carriersGlobal) {
            globalConfig = this.carriersGlobal[this.def.name];
            if (globalConfig.configurable) {
                this.def.isGloballyEnabled = globalConfig.status && globalConfig.isConfigured;
            } else {
                this.def.isGloballyEnabled = globalConfig.status;
            }
        }

        if ('global' === this.model.get('configMode')) {
            this.def.config = this.model.get('carriers')[this.name];
        }
        return this.carriers[this.def.name].status;
    },

    /**
     * @inheritDoc
     */
    bindDomChange: function() {
        var $el = this.$(this.fieldTag + '[name=' + this.def.name + ']');
        $el.on('change', _.bind(function() {
            var modifiedCarriers = _.clone(this.carriers);
            modifiedCarriers[this.def.name].status = $el.prop('checked');

            this.model.set('carriers', modifiedCarriers);

            var eventName = (this.model.get('configMode') === 'user') ?
                'change:personal:carrier:' + this.def.name :
                'change:carrier:' + this.def.name;
            this.model.trigger(eventName);
        }, this));
    }
})

