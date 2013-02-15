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

describe("forecast editableCurrency field", function () {
    var field, fieldDef, context, model, app, getModuleStub;

    beforeEach(function () {
        app = SugarTest.app;
        context = app.context.getContext();

        app.user = SugarTest.app.user;
        app.user.setPreference('decimal_precision', 2);
        app.user.setPreference('decimal_separator', '.');
        app.user.setPreference('number_grouping_separator', ',');

        SugarTest.loadFile("../sidecar/src/utils", "utils", "js", function (d) {
            return eval(d);
        });

        context.set({"selectedUser": {'id': app.user.get('id')}});
        getModuleStub = sinon.stub(app.metadata, "getModule", function (module, type) {
            return {
                sales_stage_won: ["Closed Won"],
                sales_stage_lost: ["Closed Lost"]
            };
        });
        model = new Backbone.Model({"sales_stage": 'test_sales_stage', 'editableCurrency': '50.50'});

        fieldDef = {
            "name": "editableCurrency",
            "type": "editableCurrency",
            "view": "detail"
        };
        SugarTest.loadComponent('base', 'field', 'currency');
        field = SugarTest.createField("../modules/Forecasts/clients/base", "editableCurrency", "editableCurrency", "detail", fieldDef, "Forecasts", model, context);
    });

    afterEach(function () {
        getModuleStub.restore();
        delete field;
        delete context;
        delete model;
    });

    describe("event should fire", function () {
        var stubs = [];

        afterEach(function () {
            _.each(stubs, function (stub) {
                stub.restore();
            });

            stubs = [];
        });

        xit("onClick when clicked", function () {
            stubs.push(sinon.stub(field, "onClick", function () {
            }));

            field.$el.html('<span class="editable"></span>');
        });
    });

    describe("isEditable", function () {
        it("should be false with same user and configured excluded sales stage", function () {
            // restore for this one test
            getModuleStub.restore();
            getModuleStub = sinon.stub(app.metadata, "getModule", function (module, type) {
                return {
                    sales_stage_won: ["test_sales_stage"],
                    sales_stage_lost: ["Closed Lost"]
                };
            });

            field.checkIfCanEdit();
            expect(field.isEditable()).toBeFalsy();
        });
        it("should be true with same user and no configured excluded sales stage", function () {
            expect(field.isEditable()).toBeTruthy();
        });

        it("should be false with different user and no configured excluded sales stage", function () {
            field.context.set({"selectedUser": {"id": "doh"}});
            field.checkIfCanEdit();
            expect(field.isEditable()).toBeFalsy();
        });
    });

    describe("parsePercentage", function () {
        beforeEach(function () {
            field.value = model.get('editableCurrency');
        });
        afterEach(function () {
            field.value = '';
        });
        it("should return model value if not a percentage", function () {
            expect(field.parsePercentage(field.value)).toEqual(field.value);
        });
        it("should return a 75 when percentage is +50%", function () {
            expect(field.parsePercentage("+50%")).toEqual("75.75");
        });
        it("should return a 25 when percentage is -50%", function () {
            expect(field.parsePercentage("-50%")).toEqual("25.25");
        });
        it("should return a 25 when percentage is 50%", function () {
            expect(field.parsePercentage("50%")).toEqual("25.25");
        });
        it("should return 53 with percentage is +5%", function () {
            expect(field.parsePercentage("+5%")).toEqual("53.03");
        });
    });

    describe("compareValuesLocale", function () {
        it("should return true when identical", function () {
            expect(field.compareValuesLocale("1200.00", "1200.00")).toBeTruthy();
        });
        it("should return true when decimal ommitted", function () {
            expect(field.compareValuesLocale("1200.00", "1200")).toBeTruthy();
        });
        it("should return true when comma is present", function () {
            expect(field.compareValuesLocale("1,200.00", "1200.00")).toBeTruthy();
        });
        it("should return false when not equal", function () {
            expect(field.compareValuesLocale("1200.00", "1200.01")).toBeFalsy();
        });
    });

    describe("isValid", function () {
        it("should return true when value is valid", function () {
            expect(field.isValid("1200.00")).toBeTruthy();
        });
        it("should return false when value empty", function () {
            expect(field.isValid("")).toBeFalsy();
        });
        it("should return false when value is whitespace", function () {
            expect(field.isValid(" ")).toBeFalsy();
        });
        it("should return false when value is invalid", function () {
            expect(field.isValid("abcd")).toBeFalsy();
        });
    });

    describe("compareValuesLocale", function () {
        it("should return true when value is equal and in different locale than model", function () {
            app.user.setPreference('decimal_separator', ',');
            app.user.setPreference('number_grouping_separator', '.');
            expect(field.compareValuesLocale('125.000,00', '125000.00')).toBeTruthy();
        });
    });

    describe("test trigger events", function () {
        beforeEach(function () {
            sinon.spy(field.context, "trigger");
            field.bindDataChange();
        });
        afterEach(function () {
            field.context.trigger.restore();
        });
        it("should not fire error event if is not error state", function () {
            field.isErrorState = false;
            field.context.trigger('field:editable:open');
            expect(field.context.trigger).not.toHaveBeenCalledWith('field:editable:error');
        });
        it("should fire error event if is error state", function () {
            field.isErrorState = true;
            field.context.trigger('field:editable:open');
            expect(field.context.trigger).toHaveBeenCalledWith('field:editable:error');
        });
    });

});
