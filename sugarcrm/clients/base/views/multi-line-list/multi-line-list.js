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
 * MultiLineList supports more than one line of data per Model row. User can group
 * relevant data into the same column of the data-table.
 *
 * The view metadata of each field columns uses subfields property to determine the
 * actual data being shown. Each subfields entry contains field data.
 *
 * Example:
 * array(
 *     'fields' => array(
 *         array(
 *             'user' => 'user',
 *             'label' => 'LBL_USER',
 *             'width' => 'xlarge',
 *             'subfields' => array(
 *                 array(
 *                     'name' => 'user_name',
 *                     'label' => 'LBL_USER_NAME',
 *                     'enable' => true,
 *                     'default' => true,
 *                 ),
 *                 array(
 *                     'name' => 'user_id',
 *                     'label' => 'LBL_USER_ID',
 *                     'enable' => true,
 *                     'default' => true,
 *                 ),
 *             ),
 *         ),
 *     ),
 * )
 *
 * @class View.Views.Base.MultiLineListView
 * @alias SUGAR.App.view.views.BaseMultiLineListView
 * @extends View.Views.Base.ListView
 */
({
    extendsFrom: 'ListView',
    className: 'multi-line-list-view',
    drawerModelId: null,
    sideDrawer: null,

    /**
     * Event handlers for left row actions.
     */
    contextEvents: {
        'list:openrow:fire': 'openClicked',
        'list:editrow:fire': 'editClicked',
        'list:copyrow:fire': 'copyClicked'
    },

    /**
     * @override
     */
    initialize: function(options) {
        var defaultMeta = app.metadata.getView(null, 'multi-line-list') || {};
        var listViewMeta = app.metadata.getView(options.module, 'multi-line-list') || {};
        options.meta = _.extend({}, defaultMeta, listViewMeta, options.meta || {});

        this._setCollectionOption(options);

        this._super('initialize', [options]);

        // Set fields on context to forcefully load these fields
        var fields = this._extractFieldNames(this.meta);
        this.context.set('fields', fields);

        this.leftColumns = [];
        this.addActions(this.meta);

        var leftColumnsEvents = {};
        //add an event delegate for left action dropdown buttons onclick events
        if (this.leftColumns.length) {
            leftColumnsEvents = {
                'hidden.bs.dropdown .actions': 'updateDropdownDirection',
                'shown.bs.dropdown .actions': 'updateDropdownDirection',
            };
        }
        this.events = _.extend({}, this.events, leftColumnsEvents, {
            'click .multi-line-row': 'handleRowClick',
        });
    },

    /**
     * Get fields names from metadata
     *
     * @param {Object} meta Metadata containing fields on view
     * @return {Array} Array of fields
     * @private
     */
    _extractFieldNames: function(meta) {
        var fields = [];
        _.each(meta.panels, function(panel) {
            var panelFields = panel.fields;
            _.each(panelFields, function(fieldDefs) {
                var subFields = fieldDefs.subfields;
                fields = _.union(fields, _.pluck(subFields, 'name'));
            }, this);
        }, this);

        return fields;
    },

    /**
     * Set collection option and filterDef
     *
     * @param {Object} options object for the view
     */
    _setCollectionOption: function(options) {
        var collection = options.context.get('collection');
        if (!collection) {
            collection = app.data.createBeanCollection(options.module);
            options.context.set({collection: collection});
        }
        var meta = options.meta || {};
        if (meta.collectionOptions) {
            collection.setOption(meta.collectionOptions);
        }
        if (meta.filterDef) {
            collection.filterDef = meta.filterDef;
        }
    },

    /**
     * Trigger action when a model row is clicked
     *
     * @param {Object} event Click event that triggers the function
     */
    handleRowClick: function(event) {
        var $el = this.$(event.target);

        // ignore event triggered by dorpdown-toggle or any action dropdown is open
        if (this.isDropdownToggle($el) || this.isActionsDropdownOpen()) {
            return;
        };

        var modelId = $el.closest('.multi-line-row').data('id');
        var model = this.collection.get(modelId);
        var sideDrawer = this._getSideDrawer();
        if (sideDrawer) {
            if (!sideDrawer.isOpen()) {
                sideDrawer.open({
                    layout: 'row-model-data',
                    context: {
                        model: model,
                        module: model.get('_module'),
                        layout: 'multi-line'
                    }
                });
                this.drawerModelId = modelId;
            } else if (this.drawerModelId !== modelId) {
                var rowModelDataLayout = sideDrawer.getComponent('row-model-data');
                if (rowModelDataLayout && rowModelDataLayout.setRowModel(model)) {
                    this.drawerModelId = modelId;
                }
            }
        }
    },

    /**
     * Get side drawer.
     * @return {Object} The side drawer.
     * @private
     */
    _getSideDrawer: function() {
        if (!this.sideDrawer) {
            this.sideDrawer = this.layout.getComponent('side-drawer');
        }
        return this.sideDrawer;
    },

    /**
     * Open record view in edit mode when 'edit in new tab' is clicked.
     *
     * @param {Backbone.Model} model Selected row's model.
     */
    editClicked: function(model) {
        var route = app.router.buildRoute(model.module, model.id, 'edit');
        window.open('#' + route, '_blank');
    },

    /**
     * Open record view when 'open in new tab' is clicked.
     *
     * @param {Backbone.Model} model Selected row's model.
     */
    openClicked: function(model) {
        var route = app.router.buildRoute(model.module, model.id);
        window.open('#' + route, '_blank');
    },

    /**
     * Copy record url to clipboard when 'copy url' is clicked.
     *
     * This function is adaped from: https://gist.github.com/Chalarangelo/4ff1e8c0ec03d9294628efbae49216db
     * @param {Backbone.Model} model Selected row's model.
     */
    copyClicked: function(model) {
        var route = app.router.buildRoute(model.module, model.id);
        var el = document.createElement('textarea');
        el.value = app.utils.getSiteUrl() + '#' + route;
        el.setAttribute('readonly', '');
        el.style.position = 'absolute';
        el.style.left = '-9999px';
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    },

    /**
     * Add rowactions to left column
     *
     * @param {Object} meta View metadata
     */
    addActions: function(meta) {
        if (meta && _.isObject(meta.rowactions)) {
            var _generateMeta = function(label, cssClass, buttons) {
                return {
                    'type': 'fieldset',
                    'css_class': 'overflow-visible',
                    'fields': [
                        {
                            'type': 'rowactions',
                            'no_default_action': true,
                            'label': label || '',
                            'css_class': cssClass,
                            'buttons': buttons || []
                        }
                    ],
                };
            };
            var def = meta.rowactions;
            this.leftColumns.push(_generateMeta(def.label, def.css_class, def.actions));
        }
    },

    /**
     * Check if any rowaction dropdown-menu is open
     *
     * @return {boolean} dropdown-menu open or not
     */
    isActionsDropdownOpen: function() {
        return !!this.$('.fieldset.actions.list.btn-group.open').length;
    },

    /**
     * Check if the event is triggered from dropdown-toggle
     *
     * @param {jQuery} $el element that trigger the event
     * @return {boolean} element is dropdown-toggle or not
     */
    isDropdownToggle: function($el) {
        return $el.hasClass('dropdown-toggle') || $el.parent().hasClass('dropdown-toggle');
    },

    /**
     * Update CSS class of dropdown-menu based on its vertical position
     *
     * @param {Event} event Shown/Hidden event
     */
    updateDropdownDirection: function(event) {
        var $buttonGroup = this.$(event.currentTarget).first();
        var windowHeight = $(window).height() - 65; // height of window less padding
        var menuHeight = $buttonGroup.height() + $buttonGroup.children('ul').first().height();
        if (windowHeight < $buttonGroup.offset().top + menuHeight) {
            $buttonGroup.toggleClass('dropup');
        }
    },
})
