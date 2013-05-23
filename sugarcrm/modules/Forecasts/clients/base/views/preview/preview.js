/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ("Company") that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */

({
    /**
     * Who is my parent
     */
    extendsFrom: 'PreviewView',

    /**
     * Track the original model passed in from the worksheet, this is needed becuase of how the base preview works
     */
    originalModel: undefined,

    /**
     * Override _renderPreview to pull in the parent_type and parent_id when we are running a fetch
     *
     * @param model
     * @param collection
     * @param fetch
     * @param previewId
     * @private
     */
    _renderPreview: function(model, collection, fetch, previewId){
        var self = this;

        // If there are drawers there could be multiple previews, make sure we are only rendering preview for active drawer
        if(app.drawer && !app.drawer.isActive(this.$el)){
            return;  //This preview isn't on the active layout
        }

        // Close preview if we are already displaying this model
        if(this.originalModel && model && (this.originalModel.get("id") == model.get("id") && previewId == this.previewId)) {
            // Remove the decoration of the highlighted row
            app.events.trigger("list:preview:decorate", false);
            // Close the preview panel
            app.events.trigger('preview:close');
            return;
        }

        if (model) {
            // Get the corresponding detail view meta for said module.
            // this.meta needs to be set before this.getFieldNames is executed.
            this.meta = app.metadata.getView(model.get('parent_type') || model.get('_module'), 'record') || {};
            this.meta = this._previewifyMetadata(this.meta);
        }

        if (fetch) {
            var mdl = app.data.createBean(model.get('parent_type'), {'id' : model.get('parent_id')});
            this.originalModel = model;
            mdl.fetch({
                //Show alerts for this request
                showAlerts: true,
                success: function(model) {
                    self.renderPreview(model, collection);
                }
            });
        } else {
            this.renderPreview(model, collection);
        }

        this.previewId = previewId;
    },

    /**
     * Show previous and next buttons groups on the view.
     *
     * This gets called everytime the collection gets updated. It also depends
     * if we have a current model or layout.
     *
     * TODO we should check if we have the preview open instead of doing a bunch
     * of if statements.
     */
    showPreviousNextBtnGroup: function () {
        if (!this.model || !this.layout || !this.collection) {
            return;
        }
        var collection = this.collection;
        if (!collection.size()) {
            this.layout.hideNextPrevious = true;
        }
        // use the originalModel if one is defined, if not fall back to the basic model
        var model = this.originalModel || this.model;
        var recordIndex = collection.indexOf(collection.get(model.id));
        this.layout.previous = collection.models[recordIndex-1] ? collection.models[recordIndex-1] : undefined;
        this.layout.next = collection.models[recordIndex+1] ? collection.models[recordIndex+1] : undefined;
        this.layout.hideNextPrevious = _.isUndefined(this.layout.previous) && _.isUndefined(this.layout.next);

        // Need to rerender the preview header
        this.layout.trigger("preview:pagination:update");
    },

    /**
     * Renders the preview dialog with the data from the current model and collection
     * @param model Model for the object to preview
     * @param newCollection Collection of related objects to the current model
     */
    renderPreview: function(model, newCollection) {
        if(newCollection) {
            this.collection.reset(newCollection.models);
        }

        if (model) {
            this.model = app.data.createBean(model.module, model.toJSON());

            app.view.View.prototype._render.call(this);

            // TODO: Remove when pagination on activity streams is fixed.
            if (this.previewModule && this.previewModule === "Activities") {
                this.layout.hideNextPrevious = true;
                this.layout.trigger("preview:pagination:update");
            }
            // Open the preview panel
            app.events.trigger("preview:open",this);
            // Highlight the row
            // use the original model when going to the list:preview:decorate event
            app.events.trigger("list:preview:decorate", this.originalModel, this);
            if(!this.$el.is(":visible")) {
                this.context.trigger("openSidebar",this);
            }
        }
    },

    /**
     * Switches preview to left/right model in collection.
     * @param {String} data direction Direction that we are switching to, either 'left' or 'right'.
     * @param index Optional current index in list
     * @param id Optional
     * @param module Optional
     */
    switchPreview: function(data, index, id, module) {
        var self = this,
            currModule = module || this.model.module,
            currID = id || this.model.get("postId") || this.model.get("id"),
            // use the originalModel vs the model
            currIndex = index || _.indexOf(this.collection.models, this.collection.get(this.originalModel.get('id')));

        if( this.switching || this.collection.models.length < 2) {
            // We're currently switching previews or we don't have enough models, so ignore any pagination click events.
            return;
        }
        this.switching = true;
        // get the parent_id from the specific module
        if( data.direction === "left" && (currID === _.first(this.collection.models).get("parent_id")) ||
            data.direction === "right" && (currID === _.last(this.collection.models).get("parent_id")) ) {
            this.switching = false;
            return;
        }
        else {
            // We can increment/decrement
            data.direction === "left" ? currIndex -= 1 : currIndex += 1;

            // If there is no target_id, we don't have access to that activity record
            // The other condition ensures we're previewing from activity stream items.
            if( _.isUndefined(this.collection.models[currIndex].get("target_id")) &&
                this.collection.models[currIndex].get("activity_data") ) {

                currID = this.collection.models[currIndex].id;
                this.switching = false;
                this.switchPreview(data, currIndex, currID, currModule);
            }
            else {
                var targetModule = this.collection.models[currIndex].get("target_module") || currModule,
                    moduleMeta = app.metadata.getModule(targetModule);

                // Some activity stream items aren't previewable - e.g. no detail views
                // for "Meetings" module.
                if( moduleMeta && _.isUndefined(moduleMeta.views.detail) ) {
                    currID = this.collection.models[currIndex].id;
                    this.switching = false;
                    this.switchPreview(data, currIndex, currID, currModule);
                }
                else {
                    this.model = app.data.createBean(targetModule);

                    if( _.isUndefined(this.collection.models[currIndex].get("target_id")) ) {
                        // get the parent_id
                        this.model.set("id", this.collection.models[currIndex].get("parent_id"));
                    }
                    else
                    {
                        this.model.set("postId", this.collection.models[currIndex].get("id"));
                        this.model.set("id", this.collection.models[currIndex].get("target_id"));
                    }
                    this.originalModel = this.collection.models[currIndex];
                    this.model.fetch({
                        //Show alerts for this request
                        showAlerts: true,
                        success: function(model) {
                            model.set("_module", targetModule);
                            self.model = null;
                            //Reset the preview
                            app.events.trigger("preview:render", model, null, false);
                            self.switching = false;
                        }
                    });
                }
            }
        }
    }
})
