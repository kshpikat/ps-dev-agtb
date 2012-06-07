(function(app) {

    app.view.views.EditView = app.view.View.extend({
        events: {
            "click #saveRecord": "saveRecord",
            "click #backRecord": "cancel"
        },
        initialize: function (options) {
            app.view.View.prototype.initialize.call(this, options);
            this.backupModel();
        },
        _renderSelf: function () {
            _.each(this.meta.panels, function (panel, panelIndex) {
                _.each(panel.fields, function (field, fieldIndex) {
                    if (field.name.indexOf("email") == 0) field.type = "email_temp";
                });
            });
            app.view.View.prototype._renderSelf.call(this);
        },
        saveRecord: function () {
            var source = this;
            app.alert.show('save_process', {level: 'general', messages: 'Saving...', autoClose: true});

            var model = this.context.get("model"),
                module = model.module;

            model.save(null, {
                relate: !!this.context.get('link'),
                fieldsToValidate: this.getFields(),
                success: function (model, resp) {
                    app.alert.dismiss('save_process');
                    app.alert.show('save_success', {level: 'success', messages: 'Saved successfully.', autoClose: true});
                    var depth = parseInt(source.context.get("depth")) || 1;
                    app.router.go(-depth);
                },
                error: function (model, resp, options) {
                    app.alert.dismiss('save_process');
                    app.alert.show('save_error', {level: 'error', messages: 'Save error!', autoClose: true});
                }
            });
        },
        cancel: function (e) {
            this.restoreModel();
            var depth = parseInt(this.context.get("depth")) || 1;
            app.router.go(-depth);
        },
        backupModel: function () {
            var serializedModel = JSON.stringify(this.model.attributes);
            this._modelBackup = JSON.parse(serializedModel);
        },
        restoreModel: function () {
            this.model.set(this._modelBackup);
        }
    });

})(SUGAR.App);