// FILE SUGARCRM flav=ent ONLY
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
describe('VisualPipeline.Layout.ConfigDrawerContentLayout', function() {
    var app;
    var layout;
    var context;
    var options;
    var sandbox;

    beforeEach(function() {
        app = SugarTest.app;
        context = app.context.getContext();
        context.set('model', new Backbone.Model());
        context.prepare();
        options = {
            context: context,
        };

        layout = SugarTest.createLayout('base', 'VisualPipeline', 'config-drawer-content', {}, context, true);
    });

    afterEach(function() {
        sinon.collection.restore();
        layout = null;
        options = null;
    });

    describe('_render', function() {
        beforeEach(function() {
            sinon.collection.stub(layout, '_super', function() {});
            sinon.collection.stub(layout.$el, 'addClass', function() {});
            layout._render();
        });

        afterEach(function() {
            sinon.collection.restore();
        });

        it('should call layout._super with _render', function() {

            expect(layout._super).toHaveBeenCalledWith('_render');
        });

        it('should call layout.$el.addClass with _render', function() {

            expect(layout.$el.addClass).toHaveBeenCalledWith('record-panel');
        });
    });
});
