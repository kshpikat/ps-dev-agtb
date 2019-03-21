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

describe('Base.Layout.Togglepanel', function() {

    var app, layout, getModuleStub;

    beforeEach(function() {
        app = SugarTest.app;
        getModuleStub = sinon.stub(app.metadata, 'getModule', function(module) {
            return {activityStreamEnabled:true};
        });

        SugarTest.loadFile('../modules/Dashboards/clients/base/routes', 'routes', 'js', function(d) {
            eval(d);
            app.routing.start();
        });
    });

    afterEach(function() {
        getModuleStub.restore();
        app.cache.cutAll();
        app.view.reset();
        app.router.stop();
        Handlebars.templates = {};
        layout.dispose();
        layout = null;
    });

    describe('Toggle Panel', function() {
        var oLastState;
        beforeEach(function() {
            var meta = {
            }
            oLastState = app.user.lastState;
            app.user.lastState = {
                key: function() {},
                get: function() {},
                set: function() {},
                register: function() {}
            };
            var stub = sinon.stub(app.user.lastState);
            layout = SugarTest.createLayout('base', 'Accounts', 'togglepanel', meta);
        });
        afterEach(function() {
            app.user.lastState = oLastState;
        });
        it('should initialize', function() {
            var processToggleSpy = sinon.stub(layout, 'processToggles', function() {
            });
            var options = {};
            layout.initialize(options);
            expect(layout.componentsList).toEqual({});
            expect(processToggleSpy).toHaveBeenCalled();
        });
        it('should process toggles', function() {
            var options = {};
            var meta = {
                'availableToggles': [
                    {
                        'name': 'test1',
                        'label': 'test1',
                        'icon': 'icon1'
                    },
                    {
                        'name': 'test2',
                        'label': 'test2',
                        'icon': 'icon2'
                    },
                    {
                        'name': 'test3',
                        'label': 'test3',
                        'icon': 'icon3',
                        'css_class': 'testClass',
                        'disabled': true
                    }
                ],
                'components': {
                    'c1': {
                        'view': 'test1'
                    },
                    'c2': {
                        'layout': 'test2'
                    },
                    'c3': {
                        'layout': {
                            'type': 'test3'
                        }
                    }
                }
            }
            options.meta = meta;
            layout.initialize(options);
            expect(layout.toggles).toEqual([
                {
                    class: 'icon1',
                    title: 'test1',
                    toggle: 'test1',
                    disabled: false
                },
                {
                    class: 'icon2',
                    title: 'test2',
                    toggle: 'test2',
                    disabled: false
                },
                {
                    class: 'icon3',
                    title: 'test3',
                    toggle: 'test3',
                    css_class: 'testClass',
                    disabled: true
                }
            ]);
        });
        it('should add toggle components to the togglable component lists', function() {
            var mockComponent = app.view.createView({type: 'test1', name: 'test1'});
            layout.options.meta.availableToggles = [
                {
                    'name': 'test1',
                    'label': 'test1',
                    'icon': 'icon1'
                }
            ];
            layout._placeComponent(mockComponent);

            expect(layout.componentsList[mockComponent.name]).toEqual(mockComponent);

            mockComponent.dispose();
        });

        describe('getNonToggleComponents', function() {
            it('should only return components that cannot be toggled', function() {
                var actual;
                var nonTogglable = app.view.createView({type: 'base'});
                var togglable = app.view.createView({type: 'base'});

                layout._components = [nonTogglable, togglable];
                layout.componentsList = [togglable];

                actual = layout.getNonToggleComponents();

                expect(actual.length).toBe(1);
                expect(actual[0].cid).toBe(nonTogglable.cid);

                nonTogglable.dispose();
                togglable.dispose();
            });
        });

        describe('toggleView', function() {
            var evt = {
                currentTarget: 'testTarget'
            };
            var lastStateSetStub;
            beforeEach(function() {
                sinon.collection.stub(app.router,'navigate', function() {});
                sinon.collection.stub(layout,'showComponent', function() {});
                sinon.collection.stub(layout,'_toggleAria', function() {});
            });
            afterEach(function() {
                sinon.collection.restore();
            });
            describe('when the data.route is not pipeline', function() {
                it('should set last state with last state key and data.route', function() {
                    sinon.collection.stub(layout, '$', function() {
                        return {
                            data: function() {
                                return {
                                    route: ''
                                };
                            },
                            hasClass: function() {
                                return false;
                            }
                        };
                    });
                    app.user.lastState.set.restore();
                    sinon.collection.stub(app.user.lastState, 'set');
                    layout.toggleView(evt);
                    expect(app.user.lastState.set).toHaveBeenCalled();
                });
            });

            describe('when the data.route is pipeline', function() {
                it('should route to Opportunities/pipelines records view', function() {
                    sinon.collection.stub(layout, '$', function() {
                        return {
                            data: function() {
                                return {
                                    route: 'pipeline'
                                };
                            },
                            hasClass: function() {
                                return false;
                            }
                        };
                    });
                    layout.toggleView(evt);
                    expect(app.router.navigate).toHaveBeenCalledWith(layout.module + '/pipeline', {trigger: true});
                });
            });
        });
    });
});
