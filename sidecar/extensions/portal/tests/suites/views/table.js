describe("Table View", function() {
    var app, TableView;
        
    beforeEach(function() {
        var controller;
        //SugarTest.app.config.env = "dev"; // so I can see app.data ;=)
        controller = SugarTest.loadFile('../../../../../sugarcrm/clients/base/views/table', 'table', 'js', function(d){ return d;});
        SugarTest.seedMetadata(true);
        app = SugarTest.app;
        TableView = app.view.declareComponent('view', 'Table', null, controller);
    });
    
    it("should set order by", function() {
        var event, x;
        var context = app.context.getContext();
        var collection = {
            orderBy: {
                field: "",
                direction: ""
            },
            fetch: function() {
                return true;
            }
        };
        var options = {
            context: context,
            id: "1",
            template: "asdf"
        };

        context.set({collection: collection});
        var view = new app.view.views.TableView(options); 

        view.$el.html('<div id="test" data-fieldname="bob"></div>');

        x = view.$el.children('#test');
        event = {target: x};
        view.setOrderBy(event);

        expect(collection.orderBy.direction).toEqual('desc');
        expect(collection.orderBy.field).toEqual('bob');
        view.setOrderBy(event);

        expect(collection.orderBy.direction).toEqual('asc');
        expect(collection.orderBy.field).toEqual('bob');
    });
});
