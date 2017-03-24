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
/**
 * @class View.Fields.Base.Quotes.CurrencyField
 * @alias SUGAR.App.view.fields.BaseQuotesCurrencyField
 * @extends View.Fields.Base.CurrencyField
 */
({
    extendsFrom: 'CurrencyField',

    /**
     * The field's value in Percent
     */
    valuePercent: undefined,

    /**
     * @inheritdoc
     */
    bindDataChange: function() {
        this._super('bindDataChange');

        if (this.name === 'deal_tot' && this.view.name === 'quote-data-grand-totals-header') {
            this.model.on('change:deal_tot_discount_percentage', function() {
                this._updateDiscountPercent();
            }, this);
        }
    },

    /**
     * Updates `this.valuePercent` for the deal_tot field in the quote-data-grand-totals-header view.
     *
     * @private
     */
    _updateDiscountPercent: function() {
        var percent = this.model.get('deal_tot_discount_percentage');

        if (!_.isUndefined(percent)) {
            percent = app.math.mul(percent, 100, app.user.getPreference('decimal_precision'));

            if (app.lang.direction === 'rtl') {
                this.valuePercent = '%' + percent;
            } else {
                this.valuePercent =  percent + '%';
            }

            // re-render after update
            this.render();
        }
    }
});
