//FILE SUGARCRM flav=pro ONLY
/********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
if (!(fixtures)) {
    var fixtures = {};
}
// Make this play nice if fixtures has already been defined for other tests
// so we dont overwrite data
if (!_.has(fixtures, 'metadata')) {
    fixtures.metadata = {};
}
fixtures.metadata.currencies = {
    "-99": {
        id: '-99',
        symbol: "$",
        conversion_rate: "1.0",
        iso4217: "USD"
    },
    //Because obviously everyone loves 1970's Jackson5 hits
    "abc123": {
        id: 'abc123',
        symbol: "€",
        conversion_rate: "0.9",
        iso4217: "EUR"
    }
}
describe("RevenueLineItems.Base.View.Record", function() {
    var app, view, options;

    beforeEach(function() {
        options = {
            meta: {
                panels: [
                    {
                        fields: [
                            {
                                name: "commit_stage"
                            }
                        ]
                    }
                ]
            }
        };

        app = SugarTest.app;
        SugarTest.seedMetadata(true, './fixtures');
        app.user.setPreference('decimal_precision', 2);
        SugarTest.loadComponent('base', 'view', 'record');

        view = SugarTest.createView('base', 'RevenueLineItems', 'record', options.meta, null, true);
    });

    describe("initialization", function() {
        beforeEach(function() {
            sinon.stub(app.view.views.BaseRecordView.prototype, "initialize");

            sinon.stub(app.metadata, "getModule", function() {
                return {
                    is_setup: true,
                    buckets_dom: "commit_stage_binary_dom"
                }
            })
            sinon.stub(view, "_parsePanelFields");

        });

        afterEach(function() {
            view._parsePanelFields.restore();
            app.metadata.getModule.restore();
            app.view.views.BaseRecordView.prototype.initialize.restore();
        });
    });

    describe("_parsePanelFields method", function() {
        var panels;
        beforeEach(function() {
            panels = [
                {
                    fields: [
                        {
                            name: "commit_stage"
                        }
                    ]
                }
            ];
        });

        afterEach(function() {
            panels = undefined;
        });

        it("should replace commit_stage with a spacer", function() {
            sinon.stub(app.metadata, "getModule", function() {
                return {
                    is_setup: false
                }
            });
            view._parsePanelFields(panels);
            expect(panels[0].fields).toEqual([
                { name: 'spacer', span: 6, readonly: true }
            ]);
            app.metadata.getModule.restore();
        });
    });

    describe('_handleDuplicateBefore', function() {
        var new_model;
        beforeEach(function() {
            new_model = new Backbone.Model();
        });

        afterEach(function() {
            new_model = undefined;
        });

        it('should unset quote_id and quote_name', function() {
            new_model.set({quote_id: '123', quote_name: 'name'});

            view._handleDuplicateBefore(new_model);

            expect(new_model.attributes.quote_id).toBeUndefined();
            expect(new_model.attributes.quote_name).toBeUndefined();
        });
    });

})
