describe('Base.View.Dashletselect', function() {
    var moduleName = 'Home',
        app, view;

    beforeEach(function() {
        app = SugarTest.app;
        SugarTest.loadComponent('base', 'view', 'list');
        SugarTest.loadComponent('base', 'view', 'filtered-list');
        SugarTest.testMetadata.init();
        SugarTest.testMetadata.set();

        view = SugarTest.createView('base', moduleName, 'dashletselect');
        view.model.set({ dashboard_type: 'dashboard' });
    });
    afterEach(function() {
        view.dispose();
        SugarTest.testMetadata.dispose();
        app.view.reset();
        sinon.collection.restore();
    });

    describe('get available dashlets', function() {
        it('should get all dashlet views that defines Dashlet plugin', function() {
            var customModule = 'RevenueLineItems';
            SugarTest.loadComponent('base', 'view', 'alert');
            SugarTest.loadComponent('base', 'view', 'dashablelist');
            sinon.collection.stub(app.view, 'componentHasPlugin', function() {
                return true;
            }, this);
            sinon.collection.stub(app.metadata, 'getModuleNames', function() {
                return [customModule];
            });
            SugarTest.testMetadata.addViewDefinition('dashablelist', {
                dashlets: [
                    {
                        config: {}
                    }
                ]
            });
            //custom module dashlet
            SugarTest.testMetadata.addViewDefinition('piechart', {
                dashlets: [
                    {
                        config: {}
                    }
                ]
            }, customModule);
            view.loadData();
            var actual = view.collection;
            expect(actual.length).toBe(2);
            expect(actual.at(0).get('type')).toBe('dashablelist');
            expect(actual.at(1).get('type')).toBe('piechart');
            expect(actual.at(1).get('metadata').module).toBe(customModule);
        });

        it('should get all sub dashlets that defines in dashlets array', function() {
            SugarTest.loadComponent('base', 'view', 'alert');
            SugarTest.loadComponent('base', 'view', 'dashablelist');
            sinon.collection.stub(app.view, 'componentHasPlugin', function() {
                return true;
            }, this);
            SugarTest.testMetadata.addViewDefinition('dashablelist', {
                dashlets: [
                    {
                        label: 'first1',
                        config: {}
                    },
                    {
                        label: 'first2',
                        config: {}
                    }
                ]
            });
            view.loadData();
            var actual = view.collection;
            expect(actual.length).toBe(2);
            expect(actual.at(0).get('type')).toBe('dashablelist');
            expect(actual.at(1).get('type')).toBe('dashablelist');
        });

        it('should filter acl access role for module', function() {
            SugarTest.loadComponent('base', 'view', 'alert');
            SugarTest.loadComponent('base', 'view', 'dashablelist');
            sinon.collection.stub(app.view, 'componentHasPlugin', function() {
                return true;
            }, this);
            var noAccessModules = ['Accounts', 'Contacts'];
            sinon.collection.stub(app.acl, 'hasAccess', function(action, module) {
                return !_.contains(noAccessModules, module);
            });
            SugarTest.testMetadata.addViewDefinition('dashablelist', {
                dashlets: [
                    {
                        label: 'first1',
                        config: {}
                    },
                    {
                        label: 'first2',
                        config: {
                            module: 'Contacts'
                        }
                    },
                    {
                        label: 'first3',
                        config: {
                            module: 'Notes'
                        }
                    }
                ]
            });
            view.loadData();
            var actual = view.collection;
            expect(actual.length).toBe(2);
            expect(actual.at(0).get('type')).toBe('dashablelist');
            expect(actual.at(0).get('title')).toBe('first1');
            expect(actual.at(1).get('type')).toBe('dashablelist');
            expect(actual.at(1).get('title')).toBe('first3');
        });

        it('should get all dashlet views that defines Dashlet plugin and are help dashlets', function() {
            SugarTest.loadComponent('base', 'view', 'dashablelist');
            SugarTest.loadComponent('base', 'view', 'help-dashlet');
            sinon.collection.stub(app.view, 'componentHasPlugin', function() {
                return true;
            }, this);
            SugarTest.testMetadata.addViewDefinition('dashablelist', {
                dashlets: [
                    {
                        config: {}
                    }
                ]
            });
            SugarTest.testMetadata.addViewDefinition('help-dashlet', {
                dashlets: [
                    {
                        config: {},
                        filter: {
                            dashboard: 'help-dashboard'
                        }
                    }
                ]
            });
            view.model.set({ dashboard_type: 'help-dashboard' });
            view.loadData();
            var actual = view.collection;
            expect(actual.length).toBe(1);
            expect(actual.at(0).get('type')).toBe('help-dashlet');
        });
    });

    describe('getFilteredList', function() {
        it('should get filtered dashlet list', function() {
            SugarTest.loadComponent('base', 'view', 'alert');
            SugarTest.loadComponent('base', 'view', 'dashablelist');
            SugarTest.testMetadata.addViewDefinition('dashablelist', {
                dashlets: [
                    //Matched module and layout
                    {
                        label: 'first1',
                        config: {},
                        filter: {
                            module: [
                                'Home'
                            ],
                            view: 'records'
                        }
                    },
                    //Mismatched the module (Excluded)
                    {
                        label: 'first2',
                        config: {},
                        filter: {
                            module: [
                                'Accounts',
                                'Contacts'
                            ]
                        }
                    },
                    //Matched module without filtering view
                    {
                        label: 'first3',
                        config: {},
                        filter: {
                            module: [
                                'Home',
                                'Contacts'
                            ]
                        }
                    },
                    //Mismatched the view with matched module (Excluded)
                    {
                        label: 'first4',
                        config: {},
                        filter: {
                            module: [
                                'Home'
                            ],
                            view: 'record'
                        }
                    }
                ]
            });
            var contextStub = sinon.stub(app.controller.context, 'get', function(arg) {
                if (arg === 'module') {
                    return moduleName;
                } else {
                    return 'record';
                }
            });

            view.loadData();
            var actualCollection = view.collection;

            contextStub.restore();

            expect(actualCollection.length).toBe(2);
        });
    });
});
