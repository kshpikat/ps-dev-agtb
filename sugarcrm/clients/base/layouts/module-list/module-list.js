/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement ("MSA"), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright (C) 2004-2014 SugarCRM Inc. All rights reserved.
 */
({
    className: 'module-list',
    plugins: ['Dropdown'],
    events: {
        'click .actionLink' : 'handleMenuEvent',
        'click a[data-route]': 'handleRouteEvent'
    },

    /**
     * The catalog of modules linked to their menus (short and long).
     *
     * The menu element is to the partial created at {@link #_placeComponent}
     * method.
     *
     * @property {Object} A hash of module name with each short and long menus:
     * <pre><code>
     *     {
     *         'Home': {long: el1, short: el2},
     *         'Accounts': {long: el3, short: el4},
     *         //...
     *     }
     * </code></pre>
     *
     * @protected
     */
    _catalog: {},

    /**
     * The cached `[data-action=more-modules]` since this view can be quite
     * big.
     *
     * @property {jQuery} The jQuery element pointing to our
     * `[data-action=more-modules]` element.
     *
     * @protected
     */
    _$moreModulesDD: undefined,

    handleRouteEvent: function (event) {
        var currentFragment,
            currentTarget = this.$(event.currentTarget),
            route = currentTarget.data('route');

        if (route) {
            if ((!_.isUndefined(event.button) && event.button !== 0) || event.ctrlKey || event.metaKey) {
                event.stopPropagation();
                window.open(route, '_blank');
                // FIXME remove this hack once the drawer doesn't popup even after stopPropagation() is called.
//                return false;
            }
            event.preventDefault();
            currentFragment = Backbone.history.getFragment();
            if (('#' + currentFragment) === route) {
                app.router.refresh();
            } else {
                app.router.navigate(route, {trigger: true});
            }
        }
    },

    handleMenuEvent:function (evt) {
        var $currentTarget = this.$(evt.currentTarget);
        if ($currentTarget.data('event')) {
            var module = $currentTarget.closest('li.dropdown').data('module');
            app.events.trigger($currentTarget.data('event'), module, evt);
        }
    },

    initialize: function(options) {

        app.events.on('app:sync:complete', this._resetMenu, this);
        app.events.on('app:view:change', this.handleViewChange, this);

        this._super('initialize', [options]);

        if (this.layout) {
            this.layout.on('view:resize', this.resize, this);
        }

        // FIXME we need to refactor this file to support defaultSettings
        // FIXME we need to support partials for hbs files (each module should
        // be able to make their own overrides on partials)
        // FIXME we need to support menu placeholders on the metadata for recent
        // and favorites if we don't support override of partials on each module

        // not using `hide_dashboard_bwc` form, because we shouldn't give this
        // feature by default - need confirmation from PMs.
        if (app.config.enableLegacyDashboards && app.config.enableLegacyDashboards === true) {
            this.dashboardBwcLink = app.bwc.buildRoute('Home', null, 'bwc_dashboard');
        }

    },

    handleViewChange: function() {
        this._setActiveModule(app.controller.context.get('module'));
        this.layout.trigger('header:update:route');
    },

    /**
     * Populates recently created dashboards on open menu.
     */
    populateDashboards:function () {
        var self = this,
            sync = function(method, model, options) {
                options       = app.data.parseOptionsForSync(method, model, options);
                var callbacks = app.data.getSyncCallbacks(method, model, options);
                app.api.records(method, this.apiModule, model.attributes, options.params, callbacks);
            },
            Dashboard = app.Bean.extend({
                sync: sync,
                apiModule: 'Dashboards',
                module: 'Home'
            }),
            DashboardCollection = app.BeanCollection.extend({
                sync: sync,
                apiModule: 'Dashboards',
                module: 'Home',
                model: Dashboard
            });
        var dashCollection = new DashboardCollection();
        dashCollection.fetch({
            //Don't show alerts for this request
            showAlerts: false,
            success: function(data) {
                var pattern = /^(LBL|TPL|NTC|MSG)_(_|[a-zA-Z0-9])*$/;
                _.each(dashCollection.models, function(model) {
                    if (pattern.test(model.get('name'))) {
                        model.set('name', app.lang.get(model.get('name'), dashCollection.module || null));
                    }
                });
                var recentsTemplate = app.template.getLayout('module-list.recents');
                self.$('[data-module=Home] .dashboardContainer').html(recentsTemplate(dashCollection));
            }
        });
    },

    /**
     * @inheritDoc
     *
     * If it is a `module-menu` component, we wrap it with our `list` template
     * and place it before the `more-modules` drop down or inside the drop down
     * if we are handling a short version of the menu.
     * The short version is always hidden, since it will be toggled on the
     * first resize call (when it overflows the existing width).
     *
     * @param {View.View/View.Layout} component View or layout component.
     * @protected
     */
    _placeComponent: function(component) {

        if (component.name !== 'module-menu') {
            this.$el.append(component.el);
            return;
        }

        var tpl = app.template.getLayout(this.name + '.list', component.module) ||
            app.template.getLayout(this.name + '.list'),
            $content = $(tpl({module: component.module})).append(component.el);

        // initialize catalog if isn't initialized
        this._catalog[component.module] = this._catalog[component.module] || {};

        if (component.meta && component.meta.short) {
            // FIXME remove the hide() when we fix the CSS
            $content.addClass('hidden').hide();
            this._catalog[component.module].short = $content;
            this._$moreModulesDD.find('[data-container="overflow"]').append($content);
        } else {
            this._catalog[component.module].long = $content;
            this.$('[data-action="more-modules"]').before($content);
        }
    },

    /**
     * Resets the menu based on new metadata information.
     *
     * It resets components, catalog and template (html).
     *
     * @protected
     */
    _resetMenu: function() {

        this._components = [];
        this._catalog = {};
        this.$el.html(this.template(this, this.options));

        // cache the more-dropdown now
        this._$moreModulesDD = this.$('[data-action="more-modules"]');

        this._addDefaultMenus();
        this._setActiveModule(app.controller.context.get('module'));
        this.render();
    },

    /**
     * Adds all default menu views as components in both full and short
     * version.
     *
     * This will set the menu as sticky to diferentiate from the others that
     * are added based on navigation/reference only.
     *
     * @private
     */
    _addDefaultMenus: function() {

        var moduleList = app.metadata.getModuleNames({filter: 'display_tab', access: 'read'});

        _.each(moduleList, function(module) {
            this._addMenu(module, true);
        }, this);
    },

    /**
     * Adds a menu as a component. Sticky menus aren't added to `more-modules`
     * list.
     *
     * @param {String} module The module
     * @param {Boolean} [sticky=false] Set to `true` if this is a menu that is
     *   part of user preferences.
     * @private
     */
    _addMenu: function(module, sticky) {

        var def = {
            view: {
                name: 'module-menu',
                sticky: sticky
            }
        };
        this.addComponent(this.createComponentFromDef(def, null, module), def);

        if (!sticky) {
            return;
        }

        def = {
            view: {
                name: 'module-menu',
                short: true
            }
        };
        this.addComponent(this.createComponentFromDef(def, null, module), def);
    },

    /**
     * Resize the module list to the specified width and move the extra module
     * names to the `more-modules` drop down.
     *
     * We first clone the module list, make adjustments, and then replace.
     *
     * @param {Number} width The width that we have available.
     */
    resize: function(width) {
        if (width <= 0 || _.isEmpty(this._components)) {
            return;
        }

        //TODO: ie Compatible, scrollable dropdown for low-res. window
        //TODO: Theme Compatible, Filtered switching menu
        //TODO: User preferences maximum menu count

        // FIXME we need to cache more jQuery searches because everytime we
        // change the module we can retrigger a new resize
        var $moduleListClone = this.$('[data-container=module-list]'),
            $dropdown = this._$moreModulesDD.find('[data-container=overflow]');

        if ($moduleListClone.outerWidth(true) >= width) {
            this.removeModulesFromList($moduleListClone, width);
        } else {
            this.addModulesToList($moduleListClone, width);
        }
        this._$moreModulesDD.toggleClass('hidden', $dropdown.children().length === 0);
    },

    /**
     * Move modules from the dropdown to the list to fit the specified width
     * @param $modules
     * @param width
     */
    addModulesToList: function($modules, width) {

        var $dropdown = this._$moreModulesDD.find('[data-container=overflow]'),
            $toHide = $dropdown.children('li').not('.hidden').first(),
            currentWidth = $modules.outerWidth(true);

        while (currentWidth < width && $toHide.length > 0) {

            this.toggleModule($toHide.data('module'), true);

            $toHide = $dropdown.children('li').not('.hidden').first();

            currentWidth = $modules.outerWidth(true);
        }

        if (currentWidth >= width) {
            this.toggleModule($toHide.data('module'), false);
        }
    },

    /**
     * Move modules from the list to the dropdown to fit the specified width
     * @param $modules
     * @param width
     */
    removeModulesFromList: function($modules, width) {

        var $toHide = this._$moreModulesDD.prev();

        while ($modules.outerWidth(true) >= width && $toHide.length > 0) {

            if (!this.isRemovableModule($toHide.data('module'))) {
                $toHide = $toHide.prev();
                continue;
            }

            this.toggleModule($toHide.data('module'), false);

            $toHide = $toHide.prev();
        }
    },

    /**
     * Toggle module menu given. This will make sure it will be always in sync.
     *
     * We decided to assume that the `more-modules` drop down is the master of
     * the information to keep in sync.
     *
     * If we don't have a short menu version (on `more-modules` drop down),
     * it means that we don't need to keep it in sync and just show/hide based
     * on the module name. Think at this as a cached menu until we get another
     * `app:sync:complete` event.
     *
     * @param {String} module The module you want to turn on/off.
     * @param {Boolean} [state] `true` to show it on mega menu, `false`
     *   otherwise. If no state given, will toggle.
     *
     * @chainable
     */
    toggleModule: function(module, state) {

        // cache version only
        if (!this._catalog[module].short) {
            state = !_.isUndefined(state) ? !state : undefined;
            var newState = this._catalog[module].long.toggleClass('hidden', state).hasClass('hidden');
            this._catalog[module].long.toggle(!newState);
            return this;
        }

        // keep it in sync
        var newState = this._catalog[module].short.toggleClass('hidden', state).hasClass('hidden');
        this._catalog[module].long.toggleClass('hidden', !newState);

        // FIXME hide() because there is a css problem
        this._catalog[module].long.toggle(!!newState);
        this._catalog[module].short.toggle(!newState);

        return this;
    },

    /**
     * Sets the module given as active and shown in the mega nav bar.
     *
     * This waits for the full `this._components` to be set first. If we fail
     * to do that, we will see the current module context as the first menu.
     *
     * @param {String} module the Module to set as Active on the menu.
     *
     * @protected
     * @chainable
     */
    _setActiveModule: function(module) {

        if (_.isEmpty(this._components)) {
            // wait until we have the mega menu in place
            return this;
        }

        this.$('[data-container=module-list]').children('.active').removeClass('active');

        if (!this._catalog[module]) {
            this._addMenu(module, false);
        }

        this._catalog[module].long.addClass('active');
        this.toggleModule(module, true);

        return this;
    },

    /**
     * Returns `true` if a certain module can be removed from the main nav bar,
     * `false` otherwise.
     *
     * Currently we can't remove the Home module (sugar cube) neither the
     * current active module.
     *
     * @param {String} module The module to check.
     *
     * @return {Boolean} `true` if the module is safe to be removed.
     */
    isRemovableModule: function(module) {
        return !(module === 'Home' || this.isActiveModule(module));
    },

    /**
     * Returns `true` when the module is active in main nav bar, `false`
     * otherwise.
     *
     * This is normally based on the `App.controller.context` current module
     * and then sets a fallback mechanism to determine which module it is,
     * that you can see described in {@link #_setActiveModule}.
     *
     * @param {String} module The module to check.
     *
     * @return {Boolean} `true` if the module is safe to be removed.
     */
    isActiveModule: function(module) {
        return this._catalog[module].long.hasClass('active');
    }

})
