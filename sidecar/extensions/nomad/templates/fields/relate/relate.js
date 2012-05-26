(function(app) {

    app.view.fields.RelateField = app.view.Field.extend({
        events: {
            'click input': 'onClick'
        },
        initialize: function(options) {
            app.view.Field.prototype.initialize.call(this, options);

            this.relateLayout = app.view.createLayout({
                name: 'relate'
            });

            var module = app.metadata.getModule(this.view.module).fields[this.name].module;

            var listView = app.view.createView({module: module,
                name: 'list',
                template: app.template.get('list.menu'),
                context: app.context.getContext({module: module}).prepare()
            });
            listView.context.set({view:listView});

            listView.setListItemHelper(Handlebars.helpers.listMenuItem);

            listView.on('menu:item:clicked',function(item){
                this.setValue(item.get('name'));
                this.hideMenu();
            },this);

            var searchboxView = app.view.createView({
                template: app.template.get('list.menu.header'),
                name: 'searchlist',
                context: app.context.getContext({module: module})
            });

            searchboxView.on('menu:cancel:clicked',function(){
                this.hideMenu();
            },this);

            this.relateLayout.addComponent(searchboxView);
            this.relateLayout.addComponent(listView);

        },
        hideMenu:function(){
            this.relateLayout.$el.remove();
            $(app.controller.el).show();
        },
        onClick: function() {
            this.relateLayout.$el.appendTo(document.body);
            this.relateLayout.render();
            this.relateLayout.getComponent('list').context.loadData();

            $(app.controller.el).hide();
        }
    });

})(SUGAR.App);