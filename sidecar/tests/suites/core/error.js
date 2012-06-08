describe("Error module", function() {
    var app;

    beforeEach(function() {
        SugarTest.seedMetadata(true);
        app = SugarTest.app;
        SugarTest.seedFakeServer();
    });

    it("should inject custom http error handlers and should handle http code errors", function() {
        var bean = app.data.createBean("Cases"),
            handled = false, statusCodes;

        // The reason we don't use a spy in this case is because
        // the status codes are copied instead of passed in by
        // by reference, thus the spied function will never be called.
        statusCodes = {
            404: function() {
                handled = true;
            }
        };

        app.error.initialize({statusCodes: statusCodes});

        sinon.spy(app.error, "handleHTTPError");
        SugarTest.server.respondWith([404, {}, ""]);
        bean.save();
        SugarTest.server.respond();
        expect(handled).toBeTruthy();
        expect(app.error.handleHTTPError.called).toBeTruthy();

        app.error.handleHTTPError.restore();
    });

    it("should handle validation errors", function() {
        var bean;

        // Set the length arbitrarily low to force validation error
        fixtures.metadata.modules.Cases.fields.name.len = 1;
        app.data.declareModel("Cases", fixtures.metadata.modules.Cases);
        bean = app.data.createBean("Cases");

        app.error.initialize();
        sinon.spy(app.error, "handleValidationError");

        bean.set({name: "This is a test"});
        bean.save(null, { fieldsToValidate: { name: fixtures.metadata.modules.Cases.fields.name }});

        expect(app.error.handleValidationError.called).toBeTruthy();

        // Restore previous states
        fixtures.metadata.modules.Cases.fields.name.len = 255;
        app.data.declareModel("Cases", fixtures.metadata.modules.Cases);
        app.error.handleValidationError.restore();
    });

    it("overloads window.onerror", function() {
        // Remove on error
        window.onerror = false;

        // Initialize error module
        app.error.overloaded = false;
        app.error.initialize();

        // Check to see if onerror was overloaded
        expect(_.isFunction(window.onerror)).toBeTruthy();
    });

    it("should get error strings", function(){
        var errorKey = "ERROR_TEST";
        var context = "10";
        var string = app.error.getErrorString(errorKey, context);
        expect(string).toEqual("Some error string 10");
    });

    it("should call handleInvalidGrantError callback if available, or, resort to fallback", function() {
        var spyHandleInvalidGrantError, spyFallbackHandler, xhr;
        app.error.handleInvalidGrantError = function() {};
        spyHandleInvalidGrantError = sinon.spy(app.error, 'handleInvalidGrantError');
        xhr = {
            responseText: '{ERROR: "invalid_grant"}',
            status: '400'
        };
        app.error.handleHTTPError(xhr);
        SugarTest.server.respondWith([400, {}, ""]);
        SugarTest.server.respond();
        expect(spyHandleInvalidGrantError.called).toBeTruthy();

        // Now try with it undefined and the fallback should get called
        app.error.handleInvalidGrantError = undefined;
        spyFallbackHandler = sinon.spy(app.error, 'handleStatusCodesFallback');
        app.error.handleHTTPError(xhr);
        SugarTest.server.respond();
        expect(spyHandleInvalidGrantError).not.toHaveBeenCalledTwice();
        expect(spyFallbackHandler.called).toBeTruthy();
        spyFallbackHandler.restore();
    });
    
    it("should call handleInvalidClientError callback if available, or, resort to fallback", function() {
        var spyHandleInvalidClientError, spyFallbackHandler, xhr;
        app.error.handleInvalidClientError = function() {};
        spyHandleInvalidClientError = sinon.spy(app.error, 'handleInvalidClientError');
        xhr = {
            responseText: '{ERROR: "invalid_client"}',
            status: '400'
        };
        app.error.handleHTTPError(xhr);
        SugarTest.server.respondWith([400, {}, ""]);
        SugarTest.server.respond();
        expect(spyHandleInvalidClientError.called).toBeTruthy();

        // Now try with it undefined and the fallback should get called
        app.error.handleInvalidClientError = undefined;
        spyFallbackHandler = sinon.spy(app.error, 'handleStatusCodesFallback');
        app.error.handleHTTPError(xhr);
        SugarTest.server.respond();
        expect(spyHandleInvalidClientError).not.toHaveBeenCalledTwice();
        expect(spyFallbackHandler.called).toBeTruthy();
        spyFallbackHandler.restore();
    });

    
    it("should call handleUnauthorizedError callback on 401 if available, or, resort to fallback", function() {
        var spyHandleUnauthorizedError, spyFallbackHandler, xhr;
        app.error.handleUnauthorizedError = function() {};
        spyHandleUnauthorizedError = sinon.spy(app.error, 'handleUnauthorizedError');
        xhr = {
            status: '401',
            responseText: '{foo:"bar"}'
        };
        app.error.handleHTTPError(xhr);
        SugarTest.server.respondWith([401, {}, ""]);
        SugarTest.server.respond();
        expect(spyHandleUnauthorizedError.called).toBeTruthy();

        // Now try with it undefined and the fallback should get called
        app.error.handleUnauthorizedError = undefined;
        spyFallbackHandler = sinon.spy(app.error, 'handleStatusCodesFallback');
        app.error.handleHTTPError(xhr);
        SugarTest.server.respond();
        expect(spyHandleUnauthorizedError).not.toHaveBeenCalledTwice();
        expect(spyFallbackHandler.called).toBeTruthy();
        spyFallbackHandler.restore();
    });

    it("should call handleForbiddenError callback on 403 if available, or, resort to fallback", function() {
        var spyHandleForbiddenError, spyFallbackHandler, xhr;
        app.error.handleForbiddenError = function() {};
        spyHandleForbiddenError = sinon.spy(app.error, 'handleForbiddenError');
        xhr = {
            status: '403',
            responseText: '{foo:"bar"}'
        };
        app.error.handleHTTPError(xhr);
        SugarTest.server.respondWith([403, {}, ""]);
        SugarTest.server.respond();
        expect(spyHandleForbiddenError.called).toBeTruthy();

        // Now try with it undefined and the fallback should get called
        app.error.handleForbiddenError = undefined;
        spyFallbackHandler = sinon.spy(app.error, 'handleStatusCodesFallback');
        app.error.handleHTTPError(xhr);
        SugarTest.server.respond();
        expect(spyHandleForbiddenError).not.toHaveBeenCalledTwice();
        expect(spyFallbackHandler.called).toBeTruthy();
        spyFallbackHandler.restore();
    });
    

});
