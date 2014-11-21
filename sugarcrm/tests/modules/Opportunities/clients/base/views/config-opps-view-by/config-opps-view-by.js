/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
//FILE SUGARCRM flav=ent ONLY
describe('Opportunities.View.ConfigOppsViewBy', function() {
    var app,
        view,
        options,
        meta,
        context;

    beforeEach(function() {
        app = SugarTest.app;
        context = app.context.getContext();

        var cfgModel = new Backbone.Model({
            opps_view_by: 'Opportunities'
        });

        context.set({
            model: cfgModel,
            module: 'Opportunities'
        });

        meta = {
            label: 'testLabel',
            panels: [{
                fields: []
            }]
        };

        options = {
            meta: meta,
            context: context
        };

        sinon.collection.stub(app.metadata, 'getModule', function() {
            return {
                is_setup: false
            }
        });

        sinon.collection.stub(app.template, 'getView', function(view) {
            return function() {
                return view;
            };
        });

        // load the parent config-panel view
        SugarTest.loadComponent('base', 'view', 'config-panel');
        view = SugarTest.createView('base', 'Opportunities', 'config-opps-view-by', meta, null, true);
    });

    afterEach(function() {
        sinon.collection.restore();
        view = null;
    });

    describe('initialize()', function() {
        it('should set currentOppsViewBySetting', function() {
            view.initialize(options);
            expect(view.currentOppsViewBySetting).toEqual('Opportunities');
        });
    });

    describe('bindDataChange()', function() {
        beforeEach(function() {
            sinon.collection.stub(view, 'showRollupOptions');
        });
        it('should call displayWarningAlert if isForecastsSetup and change is different than current', function() {
            sinon.collection.stub(view, 'displayWarningAlert', function() {});
            view.isForecastsSetup = true;
            view.currentOppsViewBySetting = 'Opportunities';
            view.bindDataChange();
            view.model.set('opps_view_by', 'RevenueLineItems');
            expect(view.displayWarningAlert).toHaveBeenCalled();
            expect(view.showRollupOptions).toHaveBeenCalled();
        });

        it('should not call displayWarningAlert if isForecastsSetup == false', function() {
            sinon.collection.stub(view, 'displayWarningAlert', function() {});
            view.isForecastsSetup = false;
            view.currentOppsViewBySetting = 'Opportunities';
            view.bindDataChange();
            view.model.set('opps_view_by', 'RevenueLineItems');
            expect(view.displayWarningAlert).not.toHaveBeenCalled();
            expect(view.showRollupOptions).toHaveBeenCalled();
        });

        it('should not call displayWarningAlert if changing to the same as current', function() {
            sinon.collection.stub(view, 'displayWarningAlert', function() {});
            view.isForecastsSetup = true;
            view.currentOppsViewBySetting = 'RevenueLineItems';
            view.bindDataChange();
            view.model.set('opps_view_by', 'RevenueLineItems');
            expect(view.displayWarningAlert).not.toHaveBeenCalled();
            expect(view.showRollupOptions).toHaveBeenCalled();
        });
    });

    describe('_render()', function() {
        beforeEach(function() {
            sinon.collection.stub(view, 'showRollupOptions');
        });
        it('should call showRollupOptions()', function() {
            view._render();
            expect(view.showRollupOptions).toHaveBeenCalled();
        });
    });

    describe('_updateTitleValues()', function() {
        beforeEach(function() {
            sinon.collection.stub(view, 'showRollupOptions');
            sinon.collection.stub(app.lang, 'getAppListStrings', function() {
                return {
                    'testValue': 'Test Value'
                }
            });
        });
        it('should set this.titleSelectedValues', function() {
            view.model.set('opps_view_by', 'testValue');
            view._updateTitleValues();
            expect(view.titleSelectedValues).toBe('Test Value');
        });
    });
});
