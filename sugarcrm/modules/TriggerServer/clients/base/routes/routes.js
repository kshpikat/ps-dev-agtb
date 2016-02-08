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

(function (app) {
    app.events.on('router:init', function () {
        var routes = [
            {
                name: "TriggerServer",
                route: "TriggerServer",
                callback: function () {
                    app.router.navigate('#Home', {trigger: true, replace: true});
                }
            },
            {
                name: "TriggerServerConfig",
                route: "TriggerServer/config",
                callback: function () {
                    app.controller.loadView({
                        layout: 'config-drawer',
                        module: 'TriggerServer',
                        skipFetch: true
                    });
                }
            }
        ];

        app.router.addRoutes(routes);
    });
})(SUGAR.App);
