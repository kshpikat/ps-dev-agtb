describe("ConvertLeadLayout", function() {

    beforeEach(function() {
        SugarTest.testMetadata.init();
        SugarTest.loadHandlebarsTemplate('convert', 'layout', 'base', 'Leads');
        SugarTest.loadHandlebarsTemplate('convert-panel', 'view', 'base', 'Leads');
        SugarTest.loadComponent('base', 'layout', 'convert', 'Leads');
        SugarTest.loadComponent('base', 'view', 'convert-panel', 'Leads');
        SugarTest.loadComponent('base', 'view', 'alert');

        SugarTest.testMetadata.addController('base', 'duplicate-list', 'view', createMockDupeView());
        SugarTest.testMetadata.addController('base', 'edit', 'view', createMockRecordView());
        SugarTest.testMetadata.set();
    });

    afterEach(function() {
        SugarTest.testMetadata.dispose();
    });

    var initializeLayout = function() {
        var meta = {
            'modules': [
                {
                    'module': 'Contacts',
                    'required': true,
                    'fieldMapping': {
                        'first_name': 'first_name',
                        'last_name': 'last_name'
                    }
                },
                {
                    'module': 'Accounts',
                    'duplicateCheck': true,
                    'required': true,
                    'fieldMapping': {
                        'name': 'account_name'
                    }
                },
                {
                    'module': 'Opportunities',
                    'duplicateCheck': false,
                    'required': false,
                    'fieldMapping': {
                        'name': 'opportunity_name'
                    },
                    'dependentModules': ['Contacts', 'Accounts']
                }
            ]
        };
        var layout = SugarTest.createLayout('base', 'Leads', 'convert', meta, null, true);

        return layout;
    };

    describe('Initialize', function() {
        var layout;

        beforeEach(function() {
            layout = initializeLayout();
        });

        afterEach(function() {
            delete layout;
        });

        it("should have 3 components on the layout", function() {
            expect(layout._components.length).toEqual(3);
        })

        it("components on the layout should be convert-panels with module metadata", function() {
            var i = 0;

            _.each(layout._components, function(component) {
                expect(component.name).toEqual('convert-panel');
                expect(component.meta.module).toEqual(layout.meta.modules[i++].module);
            });
        });
    });

    describe('Render', function() {
        var layout;

        beforeEach(function() {
            layout = initializeLayout();
        });

        afterEach(function() {
            mockDupesToFind = 2;
            delete layout;
        });

        it("components on the layout each have a duplicate view and create/record view", function() {
            layout.render();
            _.each(layout._components, function(component) {
                expect(component.duplicateView).toBeDefined();
                expect(component.recordView).toBeDefined();
            });
        });

        it("first component is active, other two are not", function() {
            layout.render();
            expect(layout._components[0].$('.accordion-heading').hasClass('active')).toBeTruthy();
            expect(layout._components[1].$('.accordion-heading').hasClass('active')).toBeFalsy();
            expect(layout._components[2].$('.accordion-heading').hasClass('active')).toBeFalsy();
        });

        it("first two components enabled, last is not because of dependency", function() {
            layout.render();
            expect(layout._components[0].$('.accordion-heading').hasClass('enabled')).toBeTruthy();
            expect(layout._components[1].$('.accordion-heading').hasClass('enabled')).toBeTruthy();

            expect(layout._components[2].$('.accordion-heading').hasClass('enabled')).toBeFalsy();
            expect(layout._components[2].$('.accordion-heading').hasClass('disabled')).toBeTruthy();
        });

        it("finish button is disabled", function() {
            layout.render();
            expect(layout.$('[name="lead_convert_finish_button"]').hasClass('disabled')).toBeTruthy();
        });

        it("create views are prepopulated with lead data", function() {
            var last_name = 'mylastname',
                account_name = 'myaccname',
                opportunity_name = 'myoppname'

            layout.model.set('last_name', last_name);
            layout.model.set('account_name', account_name);
            layout.model.set('opportunity_name', opportunity_name);
            layout.render();
            expect(layout._components[0].recordView.model.get("last_name")).toEqual(last_name);
            expect(layout._components[1].recordView.model.get("name")).toEqual(account_name);
            expect(layout._components[2].recordView.model.get("name")).toEqual(opportunity_name);
        });

        it("correct subviews are active", function() {
            layout.render();

            //Contact should have record view active (dupe check not defined, defaults to false)
            expect(layout._components[0].currentState.activeView).toEqual(layout._components[0].RECORD_VIEW);
            //Account should have duplicate view active (dupe check set to true)
            expect(layout._components[1].currentState.activeView).toEqual(layout._components[1].DUPLICATE_VIEW);
            //Opportunity should have record view active (dupe check set to false)
            expect(layout._components[2].currentState.activeView).toEqual(layout._components[2].RECORD_VIEW);
        });

        it("dupe view is skipped if no dupes found", function() {
            mockDupesToFind = 0;
            layout.render();

            expect(layout._components[1].currentState.activeView).toEqual(layout._components[1].RECORD_VIEW);
        });
    });

    describe('Switching Panels', function() {
        var layout, $contactHeader, $accountHeader, $opportunityHeader;

        beforeEach(function() {
            layout = initializeLayout();
            layout.render();
            $contactHeader = layout._components[0].$('.accordion-heading');
            $accountHeader = layout._components[1].$('.accordion-heading');
            $opportunityHeader = layout._components[2].$('.accordion-heading');
        });

        afterEach(function() {
            mockValidationResult = true;
            delete layout;
        });

        it("clicking on the opportunity panel header does nothing (disabled until first two are complete)", function() {
            expect($opportunityHeader.hasClass('disabled')).toBeTruthy(); //disabled before
            $opportunityHeader.click()
            expect($opportunityHeader.hasClass('disabled')).toBeTruthy(); //disabled after
        });

        it("clicking on the account panel header with success validation on contact panel moves activate status to account panel", function() {
            expect($accountHeader.hasClass('active')).toBeFalsy(); //not active before
            $accountHeader.click()
            expect($contactHeader.hasClass('active')).toBeFalsy(); //not active after
            expect($accountHeader.hasClass('active')).toBeTruthy(); //active after
        });

        it("clicking on the account panel header with validation error on contact panel keeps active status on contact panel", function() {
            mockValidationResult = false;
            expect($accountHeader.hasClass('active')).toBeFalsy(); //not active before
            $accountHeader.click()
            expect($contactHeader.hasClass('active')).toBeTruthy(); //still active after
            expect($accountHeader.hasClass('active')).toBeFalsy(); //not active after
        });

        it("completing contact panel and account panel ready for validation activates opportunity panel", function() {
            $accountHeader.click(); //complete contact panel by navigating to account
            $accountHeader.find('.show-record').click(); //switching to record mode puts panel in dirty state, ready for validation
            expect($opportunityHeader.hasClass('enabled')).toBeTruthy(); //now opportunity is enabled
        });

        it("completing required panels enables finish button", function() {
            $accountHeader.click(); //complete contact panel by navigating to account
            $accountHeader.find('.show-record').click(); //switching to record mode puts panel in dirty state, ready for validation
            $opportunityHeader.click(); //complete account panel by navigating to opportunity
            expect(layout.$('[name="lead_convert_finish_button"]').hasClass('enabled')).toBeTruthy();
        });
    });

    describe('Switching SubViews', function() {
        var layout;

        beforeEach(function() {
            layout = initializeLayout();
            layout.render();
        });

        afterEach(function() {
            delete layout;
        });

        it("clicking on ignore duplicates switches to create/record view and back", function() {
            layout._components[1].$('.accordion-heading').click(); //go to account panel
            expect(layout._components[1].currentState.activeView).toEqual(layout._components[1].DUPLICATE_VIEW);
            layout._components[1].$('.show-record').click();
            expect(layout._components[1].currentState.activeView).toEqual(layout._components[1].RECORD_VIEW);
            layout._components[1].$('.show-duplicate').click();
            expect(layout._components[1].currentState.activeView).toEqual(layout._components[1].DUPLICATE_VIEW);
        });
    });

    describe('Finishing Convert Lead', function() {
        var layout,
            showAlertStub,
            last_name = 'mylastname',
            account_name = 'myaccname',
            opportunity_name = 'myoppname';

        beforeEach(function() {
            layout = initializeLayout();
            layout.model.set('last_name', last_name);
            layout.model.set('account_name', account_name);
            layout.model.set('opportunity_name', opportunity_name);
            layout.render();
            showAlertStub = sinon.stub(SugarTest.app.alert, 'show', $.noop());
        });

        afterEach(function() {
            showAlertStub.restore();
            delete layout;
        });

        var getMockCreateConvertModel = function(expectedModel) {
            return function () {
                var convertModel = Backbone.Model.extend({
                    sync:function (method, model) {
                        expect(JSON.stringify(model)).toEqual(expectedModel);
                    }
                });

                return new convertModel();
            }
        };

        it("clicking on finish after completing all panels bundles up models from each panel and calls the API", function() {
            sinon.stub(layout, 'createConvertModel', getMockCreateConvertModel(
                '{"modules":{"Contacts":{"last_name":"'+last_name+'"},"Accounts":{"name":"'+account_name+'"},"Opportunities":{"name":"'+opportunity_name+'"}}}'
            ));

            layout._components[1].$('.accordion-heading').click(); //click Account to complete Contact
            layout._components[1].$('.accordion-heading').find('.show-record').click();
            layout._components[2].$('.accordion-heading').click(); //click Opportunity to complete Account
            layout.$('[name="lead_convert_finish_button"]').click(); //click finish to complete Opportunity
        });

        it("clicking on finish when optional panels have not been completed should not pass the optional model to API", function() {
            sinon.stub(layout, 'createConvertModel', getMockCreateConvertModel(
                '{"modules":{"Contacts":{"last_name":"'+last_name+'"},"Accounts":{"name":"'+account_name+'"}}}'
            ));

            layout._components[1].$('.accordion-heading').click(); //click Account to complete Contact
            layout._components[1].$('.accordion-heading').find('.show-record').click();
            layout.$('[name="lead_convert_finish_button"]').click(); //click Finish to complete Account
        });
    });

    var mockDupesToFind = 2;
    var mockDupes = [
        {'id': '123', 'name': 'abc'},
        {'id': '456', 'name': 'def'}
    ];

    var createMockDupeView = function() {
        return {
            'render': function() {
                if (this.collection.length > 0) {debugger;}
            },
            'loadData': function() {
                var mockDupesFound = [];
                for (i = 0; i < mockDupesToFind; i++) {
                    mockDupesFound.push(mockDupes[i]);
                }
                this.collection.reset(mockDupesFound);
            }
        };
    };

    var mockValidationResult = true;
    var createMockRecordView = function() {
        return {
            'render': function() {
                _.extend(this.model,
                    {
                        'isValid': function() {
                            return mockValidationResult;
                        }
                    }
                );
            }
        };
    };
});
