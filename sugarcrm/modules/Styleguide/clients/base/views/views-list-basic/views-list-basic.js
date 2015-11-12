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
({
    plugins: ['Prettify'],

    initialize: function(options) {
        this._super('initialize', [options]);
        this.request = this.context.get('request');
console.log('request: ', this.request);
    },

    _render: function() {
        this._super('_render');

        this.example = app.view.createView({
                context: this.context,
                type: 'list',
                name: 'list',
                module: 'Styleguide',
                layout: this.layout,
                model: this.layout.model,
                readonly: true
            });

        this.$('#example_view').append(this.example.el);
        this.example.render();

console.log('this.example: ', this.example);
    }
})
