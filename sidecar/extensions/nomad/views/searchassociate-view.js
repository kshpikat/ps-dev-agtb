(function(app) {

    app.view.views.SearchassociateView = app.view.views.SearchlistView.extend({
        initialize: function(options) {
            app.view.views.SearchlistView.prototype.initialize.call(this, options);
            this.template = app.template.get("list.associate.header"); //todo:remove it later
        },
        onClickMenuSave:function(e){
            e.preventDefault();
            this.listView.save();
        },
        onClickMenuCancel:function(e){
            e.preventDefault();
        }
    });

})(SUGAR.App);
