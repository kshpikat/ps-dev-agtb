describe("Theme Roller View", function() {

    var app, view;

    beforeEach(function() {
        SugarTest.seedMetadata(true);
        app = SugarTest.app;
        var context = app.context.getContext();
        view = SugarTest.createView("base","Cases", "themeroller", null, context);
    });
    
    afterEach(function() {
        app.cache.cutAll();
        app.view.reset();
        delete Handlebars.templates;
        view = null;
    });

    it("should get input values and store them in the context", function() {
        $('<input>').attr({type:"text", name:"a", value:"aaaa"}).appendTo(view.$el);
        $('<input>').attr({type:"text", name:"b", value:"bbbb"}).appendTo(view.$el);
        $('<input>').attr({type:"text", name:"c", value:"cccc"}).appendTo(view.$el);
        $('<input>').attr({type:"text", name:"d", value:"dddd"}).appendTo(view.$el);
        $('<input>').attr({type:"text", name:"e", value:"eeee"}).addClass("bgvar").appendTo(view.$el);

        view.previewTheme();
        expect(view.context.get('colors')).toEqual({
            a: "aaaa",
            b: "bbbb",
            c: "cccc",
            d: "dddd",
            e: '"eeee"'
        });
    });

    it("should make right api call", function() {
        var url = app.api.buildURL('theme', '', {}, {});
        $('<input>').attr({type:"text", name:"a", value:"aaaa"}).appendTo(view.$el);


        //Describe loadTheme
        var themeApiSpy = sinon.stub(app.api, "call");
        var showMessageSpy = sinon.stub(view, "showMessage");
        view.loadTheme();
        expect(themeApiSpy).toHaveBeenCalledWith("read", url, {platform: "base", themeName: "default"});

        //Describe saveTheme
        view.saveTheme();
        expect(themeApiSpy).toHaveBeenCalledWith("create", url, {a: "aaaa", platform: "base", themeName: "default"});

        //Describe resetTheme
        view.resetTheme();
        expect(themeApiSpy).toHaveBeenCalledWith("create", url, {reset: true, platform: "base", themeName: "default"});

        //Restore stubs
        themeApiSpy.restore();
        showMessageSpy.restore();
    });


    it("should parse less vars and add an @ to relate variables", function() {
        view.lessVars = {
            rel: [
                {"name": "TheVar", value: "@TheRelatedVar"}
            ]
        };
        view.parseLessVars();
        expect(view.lessVars.rel[0].relname).toEqual("TheRelatedVar");
    });
});
