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
 * Login form view.
 *
 * @class View.Views.Portal.LoginView
 * @alias SUGAR.App.view.views.PortalLoginView
 * @extends View.Views.Base.LoginView
 */
({
    plugins: ['ErrorDecoration', 'Tooltip'],

    events: {
        'click [name=login_button]': 'login',
        'click [name=signup_button]': 'signup',
        'keypress': 'handleKeypress'
    },

    /**
     * @inheritdoc
     *
     * Hide `forgot password` tooltip when clicking anywhere outside the link.
     */
    initialize: function(options) {
        var self = this;

        this._super('initialize', [options]);

        $(document).on('click.login', function(event) {
            var $forgotPassword = self.$('#forgot-password'),
                forgotPassword = $forgotPassword.get(0);
            if (!$.contains(forgotPassword, event.target)) {
                app.utils.tooltip.hide(forgotPassword);
            }
        });
    },

    /**
     * Navigate to the `Signup` view.
     */
    signup: function() {
        app.router.navigate('#signup', {trigger: true});
    },

    /**
     * @override
     *
     * There is no need to see if there's any post login setup we need to do
     * unlike in the super class. We simply render.
     */
    postLogin: function() {
        app.$contentEl.show();
    },

    /**
     * @inheritdoc
     *
     * Remove event handler for hiding `forgot password` tooltip.
     */
    _dispose: function() {
        $(document).off('click.login');
        this._super('_dispose');
    }
})
