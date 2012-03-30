/**
 * Manages bean model and collection classes.
 *
 * **DataManager provides:**
 *
 * - Interface to declare bean model and collection classes from metadata.
 * - Factory methods for creating instances of beans and bean collections.
 * - Factory methods for creating instances of bean relations and relation collections.
 * - Custom implementation of <code>Backbone.sync</code> pattern.
 *
 * **Data model metadata**
 *
 * Metadata that describes the data model contains information about module fields and its relationships.
 * From the following sample metadata, data manager would declare two classes: Opportunities and Contacts.
 * <pre><code>
 * var metadata =
 * {
 *   "modules": {
 *     "Opportunities": {
 *        "fields": {
 *            "name": { ... },
 *            ...
 *        },
 *        "relationships": {
 *             "opportunities_contacts": { ... },
 *             ...
 *        }
 *      },
 *      "Contacts": { ... }
 *    }
 * }
 * </code></pre>
 *
 * **Working with beans**
 *
 * <pre><code>
 * // Declare bean classes from metadata payload.
 * // This method should be called at application start-up and whenever the metadata changes.
 * SUGAR.App.dataManager.declareModels(metadata);
 * // You may now create bean instances using factory methods.
 * var opportunity = SUGAR.App.dataManager.createBean("Opportunities", { name: "Cool opportunity" });
 * // You can save a bean using standard Backbone.Model.save method.
 * // The save method will use dataManager's sync method to communicate changes to the remote server.
 * opportunity.save();
 *
 * // Create an empty collection of contacts.
 * var contacts = SUGAR.App.dataManager.createBeanCollection("Contacts");
 * // Fetch a list of contacts
 * contacts.fetch();
 * </code></pre>
 *
 * **Working with relationships**
 *
 * <pre><code>
 * var attrs = {
 *   firstName: "John",
 *   lastName: "Smith",
 *   // relationship field
 *   opportunityRole: "Influencer"
 * }
 * // Create a new instance of a contact related to an existing opportunity
 * var contact = dm.createRelatedBean(opportunity, null, "contacts", attrs);
 * // This will save the contact and create the relationship
 * contact.save(null, { relate: true });
 *
 * // Create an instance of contact collection related to an existing opportunity
 * var contacts = dm.createRelatedCollection(opportunity, "contacts");
 * // This will fetch related contacts
 * contacts.fetch({ relate: true });
 *
 * </code></pre>
 *
 * @class DataManager
 * @alias SUGAR.App.dataManager
 * @singleton
 */
(function(app) {

    // Bean class cache
    var _models = {};
    // Bean collection class cache
    var _collections = {};

    var _serverProxy;
    var _dataManager = {

        /**
         * Reference to the base bean model class. Defaults to {@link Bean}.
         * @property {Bean}
         */
        beanModel: app.Bean,
        /**
         * Reference to the base bean collection class. Defaults to {@link BeanCollection}.
         * @property {BeanCollection}
         */
        beanCollection: app.BeanCollection,

        /**
         * Initializes data manager.
         * @method
         */
        init: function() {
            _serverProxy = app.api;
            Backbone.sync = this.sync;
        },

        /**
         * Resets class declarations.
         * @param {String} module(optional) module name. If not specified, resets models of all modules.
         * @method
         */
        reset: function(module) {
            if (module) {
                delete _models[module];
                delete _collections[module];
            }
            else {
                _models = {};
                _collections = {};
            }
        },

        /**
         * Declares bean model and collection classes for a given module.
         * @param {String} moduleName module name.
         * @param module module metadata object.
         * @method
         */
        declareModel: function(moduleName, module) {
            this.reset(moduleName);

            var fields = module.fields;
            var relationships = module.relationships;
            var defaults = null;

            _.each(_.values(fields), function(field) {
                if (!_.isUndefined(field["default"])) {
                    if (defaults === null) {
                        defaults = {};
                    }
                    defaults[field.name] = field["default"];
                }
            });

            var model = this.beanModel.extend({
                defaults: defaults,
                /**
                 * TODO: Documentation required
                 * @member Bean
                 * @property {Object}
                 *
                 */
                sugarFields: {},
                /**
                 * Module name.
                 * @member Bean
                 * @property {String}
                 */
                module: moduleName,
                /**
                 * Vardefs metadata.
                 * @member Bean
                 * @property {Object}
                 */
                fields: fields,
                /**
                 * Relationships metadata.
                 * @member Bean
                 * @property {Object}
                 */
                relationships: relationships
            });

            _collections[moduleName] = this.beanCollection.extend({
                model: model,
                /**
                 * Module name.
                 * @member BeanCollection
                 * @property {String}
                 */
                module: moduleName,
                /**
                 * Pagination offset.
                 * @member BeanCollection
                 * @property {Number}
                 */
                offset: 0
            });

            _models[moduleName] = model;
        },

        /**
         * Declares bean models and collections classes for each module definition.
         * @param metadata metadata hash in which keys are module names and values are module definitions.
         */
        declareModels: function(metadata) {
            this.reset();
            _.each(metadata.modules, function(module, name) {
                this.declareModel(name, module);
            }, this);
        },

        /**
         * Creates instance of a bean.
         * <pre>
         * // Create an account bean. The account's name property will be set to "Acme".
         * var account = SUGAR.App.dataManager.createBean("Accounts", { name: "Acme" });
         *
         * // Create a team set bean with a given ID
         * var teamSet = SUGAR.App.dataManager.createBean("TeamSets", { id: "xyz" });
         * </pre>
         * @param {String} module Sugar module name.
         * @param attrs(optional) initial values of bean attributes, which will be set on the model.
         * @return {Bean} A new instance of bean model.
         */
        createBean: function(module, attrs) {
            return new _models[module](attrs);
        },

        /**
         * Creates instance of a bean collection.
         * <pre><code>
         * // Create an empty collection of account beans.
         * var accounts = SUGAR.App.dataManager.createBeanCollection("Accounts");
         * </code></pre>
         * @param {String} module Sugar module name.
         * @param {Bean[]} models(optional) initial array or collection of models.
         * @param {Object} options(optional) options hash.
         * @return {BeanCollection} A new instance of bean collection.
         */
        createBeanCollection: function(module, models, options) {
            return new _collections[module](models, options);
        },

        /**
         * Creates an instance of related {@link Bean} or updates an existing bean with link information.
         *
         * <pre><code>
         * // Create a new contact related to the given opportunity.
         * var contact = SUGAR.App.dataManager.createRelatedBean(opportunity, "1", "contacts", {
         *    "first_name": "John",
         *    "last_name": "Smith",
         *    "contact_role": "Decision Maker"
         * });
         * contact.save();
         * </code></pre>
         *
         * @param {Bean} bean1 instance of the first bean
         * @param {Bean/String} beanOrId2 instance or ID of the second bean. A new instance is created if this parameter is <code>null</code>
         * @param {String} link relationship link name
         * @param {Object} attrs(optional) bean attributes hash
         * @return {Bean} a new instance of the related bean or existing bean instance updated with relationship link information.
         */
        createRelatedBean: function(bean1, beanOrId2, link, attrs) {
            var name = bean1.fields[link].relationship;
            var relationship = bean1.relationships[name];
            var relatedModule = bean1.module == relationship.lhs_module ? relationship.rhs_module : relationship.lhs_module;

            attrs = attrs || {};
            if (_.isString(beanOrId2)) {
                attrs.id = beanOrId2;
                beanOrId2 = this.createBean(relatedModule, attrs);
            }
            else if (_.isNull(beanOrId2)) {
                beanOrId2 = this.createBean(relatedModule, attrs);
            }
            else {
                beanOrId2.set(attrs);
            }

            /**
             * Relationship link information.
             *
             * <pre>
             * {
             *   name: link name,
             *   bean: reference to the related bean
             * }
             * </pre>
             *
             * @member Bean
             */
            beanOrId2.link = {
                name: link,
                bean: bean1
            };

            return beanOrId2;
        },

        /**
         * Creates a new instance of related beans collection.
         *
         * <pre><code>
         * // Create contacts collection for an existing opportunity.
         * var contact = SUGAR.App.dataManager.createRelatedCollection(opportunity, "contacts");
         * contacts.fetch({ relate: true });
         * </code></pre>
         *
         * @param {Bean} bean the related beans are linked to the specified bean
         * @param {String} link relationship link name
         * @return {BeanCollection} a new instance of the bean collection
         */
        createRelatedCollection: function(bean, link) {
            var name = bean.fields[link].relationship;
            var relationship = bean.relationships[name];
            var relatedModule = relationship.lhs_module == bean.module ? relationship.rhs_module : relationship.lhs_module;
            return this.createBeanCollection(relatedModule, undefined, {
                /**
                 * Link information.
                 *
                 * <pre>
                 * {
                 *   name: link name,
                 *   bean: reference to the related bean
                 * }
                 * </pre>
                 *
                 * @member BeanCollection
                 */
                link: {
                    name: link,
                    bean: bean
                }
            });
        },

        /**
         * Custom implementation of <code>Backbone.sync</code> pattern. Syncs models with remote server using Sugar.Api lib.
         * @param {String} method the CRUD method (<code>"create", "read", "update", or "delete"</code>)
         * @param {Bean/BeanCollection} model the model to be saved (or collection to be read)
         * @param options(optional) standard Backbone options as well as Sugar specific options
         */
        sync: function(method, model, options) {
            app.logger.trace('remote-sync-' + (options.relate ? 'relate-' : '') + method + ": " + model);

            options = options || {};
            options.params = options.params || {};

            if (options.fields) {
                options.params.fields = options.fields.join(",");
            }

            if ((method == "read") && (model instanceof app.BeanCollection)) {
                if (options.offset && options.offset !== 0) {
                    options.params.offset = options.offset;
                }

                if (app.config && app.config.maxQueryResult) {
                    options.params.maxresult = app.config.maxQueryResult;
                }
            }

            var success = function(data) {
                if (options.success) {
                    if ((method == "read") && (model instanceof app.BeanCollection)) {
                        if (data.next_offset) {
                            model.offset = data.next_offset;
                            model.page = model.getPageNumber();
                        }
                        // TODO: Hack to overcome wrong response format of get-relationships request until fixed
                        data = data.records ? data.records : data;
                    }
                    else if ((options.relate === true) && (method != "read")) {
                        // The response for create/update/delete relationship contains updated beans
                        if (model.link.bean) model.link.bean.set(data.bean);
                        data = data.relatedBean;
                        // Attributes will be set automatically for create/update but not for delete
                        if (method == "delete") model.set(data);
                    }

                    options.success(data);
                }
            };

            var callbacks = {
                success: success,
                error: options.error
            };

            if (options.relate === true) {
                // Related data is an object should contain:
                // - related bean (including relationship fields) in case of create method
                // - just relationship fields in case of update method
                // - null for read/delete method
                var relatedData = null;
                if (method == "create" || method == "update") {
                    // TODO: Figure out how to extract relationship fields for update method
                    // We shouldn't pass bean fields in update request but just the relationship fields
                    // On the other hand passing all fields shouldn't break the server
                    relatedData = model.attributes;
                }

                _serverProxy.relationships(
                    method,
                    model.link.bean.module,
                    {
                        id: model.link.bean.id,
                        link: model.link.name,
                        relatedId: model.id,
                        related: relatedData
                    },
                    options.params,
                    callbacks
                );
            }
            else {
                _serverProxy.beans(
                    method,
                    model.module,
                    model.attributes,
                    options.params,
                    callbacks
                );
            }

        }
    };

    app.augment("dataManager", _dataManager, false);

})(SUGAR.App);

