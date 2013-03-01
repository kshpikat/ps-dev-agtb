/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Master Subscription
 * Agreement (""License"") which can be viewed at
 * http://www.sugarcrm.com/crm/master-subscription-agreement
 * By installing or using this file, You have unconditionally agreed to the
 * terms and conditions of the License, and You may not use this file except in
 * compliance with the License.  Under the terms of the license, You shall not,
 * among other things: 1) sublicense, resell, rent, lease, redistribute, assign
 * or otherwise transfer Your rights to the Software, and 2) use the Software
 * for timesharing or service bureau purposes such as hosting the Software for
 * commercial gain and/or for the benefit of a third party.  Use of the Software
 * may be subject to applicable fees and any use of the Software without first
 * paying applicable fees is strictly prohibited.  You do not have the right to
 * remove SugarCRM copyrights from the source code or user interface.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the ""Powered by SugarCRM"" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.
 *
 * Your Warranty, Limitations of liability and Indemnity are expressly stated
 * in the License.  Please refer to the License for the specific language
 * governing these rights and limitations under the License.  Portions created
 * by SugarCRM are Copyright (C) 2004-2012 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/**
 *
 * Create a modal popup that renders popup layout container
 * @precondition layout metadata must contain a modal layout among the components.
 * array(
 *      'layout' => 'modal',
 *      'showEvent' => [event name] //corresponding trigger name (a single string or array of strings)
 *      ),
 * @trigger [event name] Create popup modal window and draws specified type of layout
 *      @params Parameters - [Object] {
 *              span - [int] size of modal[1-12]
 *              options - (Optional) 3rd party options goes here
 *
 *              components - [Array] list of either views or layouts (optional for single layout)
 *                           i.e. [ {view: ... } , {layout: ..., context: ... }, ...]
 *
 *                  context should be within the each component metadata.
 *                  context - [Object] configured context attributes
 *                        i.e. { module:..., link:..., modelId:... }
 *                        {
 *                            module - [String] Module name (i.e. Accounts, Contacts, etc) (optional),
 *                            link - [String] related module name (optional),
 *                            modelId - [String] model ID (optional)
 *                        }
 *      }
 *
 *      @params callback - [function(model)] - called by trigger "modal:callback" with correponded model
 *
 * @trigger "modal:callback" Executes binded callback function with the updated model as parameter
 *      @params model - object Backbone model that relates to the current job
 *
 * @trigger "modal:close" Close popup modal and release layout for popup
 *
 * How to Use:
 * in the view widget
 *     this.layout.trigger([event name], ...)
 * in the field widget
 *     this.view.layout.trigger([event name], ...)
 */
({
    baseComponents: [
        { 'view' : 'modal-header' }
    ],
    initialize: function(options, skipModalJsCheck) {
        var self = this,
            showEvent = options.meta.showEvent;

        if (!skipModalJsCheck) {
            if(!_.isFunction(this.$el.modal)) {
                app.logger.error("Unable to load modal.js: Needs bootstrap modal plugin.");
            }
        }

        this.metaComponents = options.meta.components;
        options.meta.components = this.baseComponents;
        if (options.meta.before){
            _.each(options.meta.before, function(callback, event){
                self.before(event, callback);
            });
        }
        app.view.Layout.prototype.initialize.call(this, options);
        options.meta.components = this.metaComponents; //revert components metadata back to original
        if (showEvent) {
            if(_.isArray(showEvent)) {
                //Bind the multiple event handler names
                _.each(showEvent, function(evt, index) {
                    self._bindShowEvent(evt);
                });
            } else {
                self._bindShowEvent(showEvent);
            }
        }
    },
    _bindShowEvent : function(event, delegate){
        var self = this;
        if (_.isObject(event))
        {
            delegate = event.delegate;
            event = event.event;
        }
        if (delegate){
            self.layout.events = self.layout.events || {};
            self.layout.events[event] = function(params, callback){self.show(params, callback)};
            self.layout.delegateEvents();
        } else {
            self.layout.on(event, function(params, callback){self.show(params, callback);}, self);
        }
    },
    getBodyComponents: function() {
        return _.rest(this._components, this._initComponentSize);
    },
    _placeComponent: function(comp, def) {
        if(this.$('.modal:first').length == 0) {
            this.$el.append(
                $('<div>', {'class' : 'modal hide'}).append(
                    this.$body
                )
            );
        }

        if(def.bodyComponent) {
            if(_.isUndefined(this.$body)) {
                this.$body = $('<div>', {'class' : 'modal-body'});
                this.$('.modal:first').append(this.$body);
            }
            this.$body.append(comp.el);
        } else {
            this.$('.modal:first').append(comp.el);
        }
    },

    /**
     *
     * @param params
     * @param callback
     * @private
     */
    _buildComponentsBeforeShow : function(params, callback) {
        var self = this,
            params = params || {},
            buttons = params.buttons || [],
            message = params.message || '',
            components = (params.components || this.metaComponents || []),
            title = (params.title || this.meta.title) + '';
        if(message && components.length == 0) {
            this.confirmMessage = message;
            components.push({view: 'modal-confirm'});
        }
        //stops for empty component elements
        if(components.length == 0) {
            app.logger.error("Unable to display modal dialog: no components or message");
            return false;
        }

        //set title and buttons for modal-header
        var header_view = self.getComponent('modal-header');
        if(header_view) {
            header_view.setTitle(title);
            header_view.setButton(buttons);
        }

        //if previous modal-body exists, remove it.
        if(self._initComponentSize) {
            for(var i = 0; i < self._components.length; i++) {
                self._components[self._components.length - 1].$el.remove();
                self.removeComponent(self._components.length - 1);
            }
        } else {
            self._initComponentSize = self._components.length;
        }

        this._addComponentsFromDef(components);

        self.context.off("modal:callback");
        self.context.on("modal:callback", function(model) {
            callback(model);
            self.hide();
        },self);
        self.context.off("modal:close");
        self.context.on("modal:close", self.hide, self);


    },

    show: function(params, callback) {
        if (!this.triggerBefore("show") || this.disposed) return false;
        var self = this;
        if (params.before){
            _.each(params.before, function(callback, event){
                self.offBefore(event);
                self.before(event, callback);
            });
        }

        if (this._buildComponentsBeforeShow(params, callback) === false)
            return false;
        this.loadData();
        this.render();
        var width = params ? params.width : null,
            options = params ? params.options || {} : {},
            modal_container = this.$(".modal:first");

        //Clean out previous span css class
        modal_container.attr("style", "");
        if(_.isNumber(width)) {
            modal_container.width(width);
            modal_container.css('marginLeft', -(width/2) + 'px');
        }
        if(!_.isFunction(modal_container.modal)) {
            app.logger.error("Unable to load modal.js: Needs bootstrap modal plugin.");
            return false;
        }

        modal_container.modal(_.extend({keyboard:false, backdrop:'static'}, options.modal));
        modal_container.modal('show');

        this.trigger("show");
        return true;
    },
    hide: function(event) {
        if (!this.triggerBefore("hide")) return false;
        //restore back to the scroll position at the top
        var modal_container = this.$(".modal:first");
        modal_container.scrollTop(0);

        if(!_.isFunction(modal_container.modal)) {
            app.logger.error("Unable to load modal.js: Needs bootstrap modal plugin.");
            return false;
        }
        modal_container.modal('hide');
        this.trigger("hide");
        return true;
    }
})
