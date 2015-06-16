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
(function(app) {
    app.events.on('app:init', function() {
        /**
         * Is built to share knowledge base features among views.
         *
         * - Adds validate tasks to follow dependencies between status, active and expiration dates fields.
         * - Allows to inject KBContentTemplates templates into body fields.
         * - Extends a view with create article and revision functionality.
         */
        app.plugins.register('KBContent', ['view'], {

            events: {
                'click [name=template]': 'launchTemplateDrawer'
            },

            CONTENT_LOCALIZATION: 1,
            CONTENT_REVISION: 2,

            /**
             * Attach events to create localization and revisions.
             *
             * @param {Object} component
             * @param {Object} plugin
             * @return {void}
             */
            onAttach: function(component, plugin) {
                this.on('init', function() {
                    this.context.on('button:create_localization_button:click', this.createLocalization, this);
                    this.context.on('button:create_revision_button:click', this.createRevision, this);
                    this.context.on('button:create_article_button:click', this.createArticle, this);

                    if (this.action == 'list') {
                        this.context.on('list:editrow:fire', _.bind(function(model, view) {
                            this._initValidationHandler(model);
                        }, this));
                    } else {
                        this._initValidationHandler(this.model);
                    }
                });
            },

            /**
             * Handler to create localization.
             * @param {Data.Model} model Parent model.
             */
            createLocalization: function(model) {
                this.createRelatedContent(model, this.CONTENT_LOCALIZATION);
            },

            /**
             * Handler to create revision.
             * @param {Data.Model} model Parent model.
             */
            createRevision: function(model) {
                this.createRelatedContent(model, this.CONTENT_REVISION);
            },

            /**
             * Handler to create a new article.
             * @param {Data.Bean} model A record view model caused creation.
             */
            createArticle: function(model) {
                var link = 'kbcontents',
                    module = 'KBContents',
                    bodyTmpl = app.template.getField('htmleditable_tinymce', 'create-article', module),
                    attrs = {name: model.get('name'), kbdocument_body: bodyTmpl({model: model})},
                    prefill = app.data.createRelatedBean(model, null, link, attrs),
                    relatedFields = app.data.getRelateFields(model.module, link);

                if (!_.isEmpty(relatedFields)) {
                    _.each(relatedFields, function(field) {
                        var parentValue = model.get(field.rname);
                        prefill.set(field.name, parentValue);
                        prefill.set(field.id_name, model.get('id'));
                    }, this);
                }
                app.drawer.open({
                    layout: 'create',
                    context: {
                        create: true,
                        model: prefill,
                        module: module
                    }},
                    function(context, newModel) {}
                );
            },

            /**
             * Creates revision or localization for KB.
             * @param {Data.Bean} parentModel Parent model object.
             * @param {Number} type Type of created content.
             */
            createRelatedContent: function(parentModel, type) {
                var self = this,
                    prefill = app.data.createBean('KBContents');

                parentModel.fetch({
                    success: function() {
                        self._copyRelatedContent(prefill, parentModel);

                        if (type === self.CONTENT_LOCALIZATION) {
                            self._onCreateLocalization(prefill, parentModel);
                        } else {
                            self._onCreateRevision(prefill, parentModel);
                        }
                    },
                    error: function(error) {
                        app.alert.show('server-error', {
                            level: 'error',
                            messages: 'ERR_GENERIC_SERVER_ERROR'
                        });
                    }
                });
            },

            /**
             * Uses standard Model's copy mechanism and adds/removes additional fields, specific to related content.
             * The purpose is to use vardefs' 'duplicate_on_record_copy' only for general copying.
             * @param {Data.Model} prefill New created model.
             * @param {Data.Model} parentModel Parent model.
             * @private
             */
            _copyRelatedContent: function(prefill, parentModel) {
                var removeList = ['id', 'is_external'],
                    addList = [
                        'active_date', 'exp_date', 'attachment_list', 'usefulness_user_vote',
                        'kbsapprover_id', 'kbsapprover_name', 'approved',
                        'kbscase_id', 'kbscase_name',
                        'localizations', 'revisions', 'related_languages',
                        'kbdocument_id', 'kbdocument_name', 'kbdocuments_kbcontents',
                        'kbarticle_id', 'kbarticle_name', 'kbarticles_kbcontents'
                    ];

                prefill.copy(parentModel);

                _.each(removeList, function(field) {
                    prefill.unset(field);
                });
                _.each(addList, function(field) {
                    prefill.set(field, parentModel.get(field));
                });

                prefill.set('status', 'draft');
                prefill.set('assigned_user_id', app.user.get('id'));
                prefill.set('assigned_user_name', app.user.get('full_name'));
            },

            /**
             * Method called on create localization.
             *
             * Setup additional model properties for localization.
             * If no available langs for localizations it shows alert message.
             *
             * @param {Data.Model} prefill New created model.
             * @param {Data.Model} parentModel Parent model.
             * @private
             */
            _onCreateLocalization: function(prefill, parentModel) {

                if (!this.checkCreateLocalization(parentModel)) {
                    app.alert.show('localizations', {
                        level: 'warning',
                        title: app.lang.get('LBL_CANNOT_CREATE_LOCALIZATION', 'KBContents'),
                        autoClose: false
                    });
                    return;
                }
                this.context.createAction = this.CONTENT_LOCALIZATION;
                prefill.set(
                    'related_languages',
                    this.getAvailableLangsForLocalization(parentModel),
                    {silent: true}
                );
                prefill.unset('language', {silent: true});
                prefill.unset('kbarticle_id', {silent: true});

                this._openCreateRelatedDrawer(prefill, parentModel);
            },

            /**
             * Method called on create localization.
             *
             * Setup additional model properties for revision.
             *
             * @param {Data.Model} prefill New created model.
             * @param {Data.Model} parentModel Parent model.
             * @private
             */
            _onCreateRevision: function(prefill, parentModel) {
                this.context.createAction = this.CONTENT_REVISION;
                prefill.set('useful', parentModel.get('useful'));
                prefill.set('notuseful', parentModel.get('notuseful'));
                prefill.set(
                    'related_languages',
                    [parentModel.get('language')],
                    {silent: true}
                );

                this._openCreateRelatedDrawer(prefill, parentModel);
            },

            /**
             * Open drawer for create form.
             * @param {Data.Model} prefill New created model.
             * @param {Data.Model} parentModel Parent model.
             * @private
             */
            _openCreateRelatedDrawer: function(prefill, parentModel) {
                var layoutDef = {
                    layout: 'create',
                    context: {
                        create: true,
                        model: prefill,
                        copiedFromModelId: parentModel.get('id'),
                        parent: this.context,
                        createAction: this.context.createAction
                    }
                };

                if (this.context.loadDrawer == true) {
                    app.drawer.load(layoutDef);
                } else {
                    app.drawer.open(layoutDef, function(context, newModel) {
                        // Just parent - header's create, parent.parent - subpanel's create.
                        var recordViewContext = context.parent.parent || context.parent;
                        parentModel.fetch();
                        recordViewContext.trigger('subpanel:reload', {links: ['revisions', 'localizations']});
                        context.createAction = null;
                        context.loadDrawer = null;
                    });
                }

                prefill.trigger('duplicate:field', parentModel);
            },

            /**
             * Checks if there are available lang for localization.
             *
             * @param {Data.Model} model Parent model.
             * @return {boolean} True on success otherwise false.
             */
            checkCreateLocalization: function(model) {
                var langs = this.getAvailableLangsForLocalization(model),
                    config = app.metadata.getModule('KBContents', 'config');

                if (!langs || !config['languages']) {
                    return true;
                }

                if (!config['languages'] || config['languages'].length == langs.length) {
                    return false;
                }

                return true;
            },

            /**
             * Returns array of langs for that there is localization.
             * @param {Data.Model} model Parent model.
             * @return {Array} Array of langs.
             */
            getAvailableLangsForLocalization: function(model) {
                return model.get('related_languages') || [];
            },

            /**
             * Open the drawer with the KBContentTemplates selection list layout and override the
             * kbdocument_body field with selected template.
             */
            launchTemplateDrawer: function() {
                app.drawer.open({
                        layout: 'selection-list',
                        context: {
                            module: 'KBContentTemplates'
                        }
                    },
                    _.bind(function(model) {
                        if (!model) {
                            return;
                        }
                        var self = this;
                        var template = app.data.createBean('KBContentTemplates', { id: model.id });
                        template.fetch({
                            success: function(template) {
                                if (this.disposed === true) {
                                    return;
                                }
                                var replace = function() {
                                    self.model.set('kbdocument_body', template.get('body'));
                                };
                                if (!self.model.get('kbdocument_body')) {
                                    replace();
                                } else {
                                    app.alert.show('override_confirmation', {
                                        level: 'confirmation',
                                        messages: app.lang.get('LBL_TEMPATE_LOAD_MESSAGE', self.module),
                                        onConfirm: replace
                                    });
                                }
                            },
                            error: function(error) {
                                app.alert.show('template-load-error', {
                                    level: 'error',
                                    messages: app.lang.get('LBL_TEMPLATE_LOAD_ERROR', 'KBContentTemplates')
                                });
                            }
                        });
                    }, this)
                );
            },

            /**
             * Define custom validation tasks.
             *
             * @param {Object} model Bean model.
             */
            _initValidationHandler: function(model) {
                // Copy model for list view records to not replace this.model.
                var _doValidateExpDateFieldPartial = _.partial(this._doValidateExpDateField, model),
                    _doValidateActiveDateFieldPartial = _.partial(this._doValidateActiveDateField, model),
                    _validationCompletePartial = _.partial(this._validationComplete, model);

                // TODO: This needs an API instead. Will be fixed by SC-3369.
                app.error.errorName2Keys['expDateLow'] = 'ERROR_EXP_DATE_LOW';
                app.error.errorName2Keys['activeDateApproveRequired'] = 'ERROR_ACTIVE_DATE_APPROVE_REQUIRED';
                app.error.errorName2Keys['activeDateLow'] = 'ERROR_ACTIVE_DATE_LOW';

                model.addValidationTask('exp_date_publish', _.bind(_doValidateExpDateFieldPartial, this));
                model.addValidationTask('active_date_approve', _.bind(_doValidateActiveDateFieldPartial, this));
                model.on('validation:complete', _validationCompletePartial, this);
            },

            /**
             * Custom validator for the "exp_date" field.
             * Show error when expiration date is lower than publishing.
             *
             * @param {Object} model Bean.
             * @param {Object} fields Hash of field definitions to validate.
             * @param {Object} errors Error validation errors.
             * @param {Function} callback Async.js waterfall callback.
             */
            _doValidateExpDateField: function(model, fields, errors, callback) {
                var fieldName = 'exp_date',
                    expDate = model.get(fieldName),
                    publishingDate = model.get('active_date'),
                    status = model.get('status'),
                    changed = model.changedAttributes(model.getSyncedAttributes());

                if (
                    this._isPublishingStatus(status) &&
                    (!changed.status || !this._isPublishingStatus(changed.status))
                ) {
                    publishingDate = app.date().formatServer(true);
                    model.set('active_date', publishingDate);
                }

                if (status !== 'expired' && expDate && publishingDate && app.date(expDate).isBefore(publishingDate)) {
                    if (!this.getField(fieldName)) {
                        fieldName = 'active_date';
                    }
                    errors[fieldName] = errors[fieldName] || {};
                    errors[fieldName].expDateLow = true;
                }

                callback(null, fields, errors);
            },

            /**
             * Custom validator for the "active_date" field.
             * Approved status requires publishing date.
             *
             * @param {Object} model Bean.
             * @param {Object} fields Hash of field definitions to validate.
             * @param {Object} errors Error validation errors.
             * @param {Function} callback Async.js waterfall callback.
             */
            _doValidateActiveDateField: function(model, fields, errors, callback) {
                var fieldName = 'active_date',
                    status = model.get('status'),
                    publishingDate = model.get(fieldName),
                    pubDateObject = new Date(publishingDate);

                if (status == 'approved') {
                    if (publishingDate && pubDateObject && pubDateObject.getTime() < Date.now()) {
                        errors[fieldName] = errors[fieldName] || {};
                        errors[fieldName].activeDateLow = true;
                        callback(null, fields, errors);
                    } else if (!publishingDate) {
                        app.alert.show('save_without_publish_date_confirmation', {
                            level: 'confirmation',
                            messages: app.lang.get('LBL_SPECIFY_PUBLISH_DATE', 'KBContents'),
                            confirm: {
                                label: app.lang.get('LBL_YES')
                            },
                            cancel: {
                                label: app.lang.get('LBL_NO')
                            },
                            onConfirm: function() {
                                callback(null, fields, errors);
                            },
                            onCancel: _.bind(function() {
                                var field = this.getField(fieldName, model);
                                if (!_.isEmpty(field)) {
                                    var fieldElement = field.getFieldElement();

                                    if (_.isFunction(this.handleFieldError)) {
                                        this.handleFieldError(field, true);
                                    }

                                    if (fieldElement.find('input[data-type=date]').length === 0) {
                                        fieldElement.closest('[data-name=' + fieldName + ']')
                                            .find('.record-edit-link-wrapper')
                                            .click();
                                    }
                                    fieldElement.find('input[data-type=date]').focus();
                                }

                                // enable buttons in recordview
                                if (_.isFunction(this.toggleButtons)) {
                                    this.toggleButtons(true);
                                }

                                // enable save button in recorlist view
                                var inlineSaveButton = this.getField('inline-save', model);
                                if (!_.isEmpty(inlineSaveButton)) {
                                    inlineSaveButton.setDisabled(false);
                                }
                            }, this)
                        });
                    } else {
                        callback(null, fields, errors);
                    }
                } else {
                    callback(null, fields, errors);
                }
            },

            /**
             * Called whenever validation completes.
             * Change publishing and expiration dates to current on manual change.
             *
             * @param {Boolean} isValid
             */
            _validationComplete: function(model, isValid) {
                if (isValid) {
                    var changed = model.changedAttributes(model.getSyncedAttributes());
                    var current = model.get('status');

                    if (current == 'expired') {
                        model.set('exp_date', app.date().formatServer(true));
                    } else if (
                        this._isPublishingStatus(current) &&
                        !(changed.status && this._isPublishingStatus(changed.status))
                    ) {
                        model.set('active_date', app.date().formatServer(true));
                    }
                }
            },

            /**
             * Check if passed status is publishing status.
             *
             * @param {String} status Status field value.
             * @return {Boolean}
             */
            _isPublishingStatus: function(status) {
                return ['published'].indexOf(status) !== -1;
            },

            /**
             * {@inheritDoc}
             * Remove validation on the model.
             */
            onDetach: function() {
                this.model.removeValidationTask('exp_date_publish');
                this.model.removeValidationTask('active_date_approve');
            },

            /**
             * Need additional data while creating new revision/localization.
             * @see View.Views.Base.CreateView::saveAndCreate
             * @override
             */
            saveAndCreate: function() {
                var createAction = this.context.parent.createAction,
                    callback;
                if (!createAction) {
                    Object.getPrototypeOf(this).saveAndCreate.call(this);
                    return;
                }
                switch (createAction) {
                    case this.CONTENT_LOCALIZATION:
                        callback = this.createLocalization;
                        break;
                    case this.CONTENT_REVISION:
                        callback = this.createRevision;
                        break;
                }
                if (callback) {
                    this.initiateSave(_.bind(
                        function() {
                            this.context.loadDrawer = true;
                            if (this.hasSubpanelModels) {
                                // loop through subpanels and call resetCollection on create subpanels
                                _.each(this.context.children, function(child) {
                                    if (child.get('isCreateSubpanel')) {
                                        this.context.trigger('subpanel:resetCollection:' + child.get('link'), true);
                                    }
                                }, this);

                                // reset the hasSubpanelModels flag
                                this.hasSubpanelModels = false;
                            }
                            callback.call(this, this.model);
                        },
                        this
                    ));
                }
            }
        });
    });
})(SUGAR.App);
