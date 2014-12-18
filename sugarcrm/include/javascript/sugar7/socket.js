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
(function(app) {
    app.augment("socket", _.extend({}, Backbone.Events), false);

    app.events.on('app:init', function() {

        if (_.isUndefined(app.config.websockets) ||
            _.isUndefined(app.config.websockets.client) ||
            _.isUndefined(app.config.websockets.client.url)) {
            return;
        }
        
        var socket = io(app.config.websockets.client.url, {
            autoConnect: false
        });

        var connect = function() {
            socket.emit('OAuthToken', {
                'siteUrl': app.config.siteUrl,
                'serverUrl': app.config.serverUrl,
                'publicSecret': app.config.websockets.publicSecret,
                'token': app.api.getOAuthToken()
            });
        };

        socket.on('connect', connect);
        app.events.on('app:login:success', connect);
        app.events.on('app:logout', connect);

        socket.on('message', _.bind(function(data) {
            app.socket.trigger(data.message, data.args);
        }, this));

        socket.open();
    });
})(SUGAR.App);
