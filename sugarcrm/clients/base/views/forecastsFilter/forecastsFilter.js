/**
 * View that displays a list of models pulled from the context's collection.
 * @class View.Views.FilterView
 * @alias SUGAR.App.layout.FilterView
 * @extends View.View
 */
({

    bindDataChange: function() {
        var self = this,
            model = self.layout.getModel('filters');

        model.on('change', function() {
            self.buildDropdowns(this);
        });
    },

    buildDropdowns: function(model) {
        var self = this;
        self.$el.empty();
        _.each(model.attributes, function(data, key) {
            var chosen = app.view.createField({
                    def: {
                        name: key,
                        type: 'enum'
                    },
                    view: self
                }),
                $chosenPlaceholder = $(chosen.getPlaceholder().toString());

            self.$el.append($chosenPlaceholder);

            chosen.options.viewName = 'edit';
            chosen.label = model[key].get('label');
            chosen.def.options = model[key].get('options');
            chosen.setElement($chosenPlaceholder);
            chosen.render();

            $chosenPlaceholder.find('select').chosen().on('change', function(event, data) {
                model[key].set('value', data.selected);
            });
        });
    }

})
