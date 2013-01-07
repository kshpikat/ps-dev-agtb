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

describe("forecast editableInt field", function () {
    var field, fieldDef, context, model;

    beforeEach(function () {
        var app = SugarTest.app;
        context = app.context.getContext();

        app.user = SugarTest.app.user;
        app.user.setPreference('decimal_precision', 2);

        SugarTest.loadFile("../sidecar/src/utils", "utils", "js", function (d) {
            return eval(d);
        });

        context.forecasts = new Backbone.Model({"selectedUser" : {'id' : app.user.get('id')}});
        context.forecasts.config = new Backbone.Model({"sales_stage_won" : [], "sales_stage_lost" : []});

        model = new Backbone.Model({"sales_stage" : 'test_sales_stage'});

        fieldDef = {
            "name": "editableInt",
            "type": "editableInt",
            "view": "detail",
            "maxValue": 100,
            "minValue": 0
        };
        SugarTest.loadComponent('base', 'field', 'int');
        field = SugarTest.createField("../modules/Forecasts/clients/base", "editableInt", "editableInt", "detail", fieldDef, "Forecasts", model, context);
    });

    afterEach(function() {
        delete field;
        delete context;
        delete model;
    });

    describe("event should fire", function() {
        var stubs = [];

        afterEach(function(){
            _.each(stubs, function(stub){
                stub.restore();
            });

            stubs = [];
        });

        xit("onClick when clicked", function() {
            stubs.push(sinon.stub(field, "onClick", function(){}));

            field.$el.html('<span class="editable"></span>');
        });
    });

    describe("isEditable", function() {
        it("should be false with same user and configured excluded sales stage", function() {
            field.context.forecasts.config.set('sales_stage_won', ["test_sales_stage"]);
            field.checkIfCanEdit();
            expect(field.isEditable()).toBeFalsy();
        });
        it("should be true with same user and no configured excluded sales stage", function() {
            expect(field.isEditable()).toBeTruthy();
        });

        it("should be false with different user and no configured excluded sales stage", function() {
            field.context.forecasts.set({"selectedUser" : {"id" : "doh"}});
            field.checkIfCanEdit();
            expect(field.isEditable()).toBeFalsy();
        });
    });

    describe("parsePercentage", function() {
        afterEach(function() {
            field.value = "";
        });
        it("should return model value if not a percentage", function() {
            field.value = "50";
            expect(field.parsePercentage(field.value)).toEqual(field.value);
        });
        it("should return a 75 when percentage is +50%", function() {
            field.value = "50";
            expect(field.parsePercentage("+50%")).toEqual(75);
        });
        it("should return a 25 when percentage is -50%", function() {
            field.value = "50";
            expect(field.parsePercentage("-50%")).toEqual(25);
        });
        it("should return a 25 when percentage is 50%", function() {
            field.value = "50";
            expect(field.parsePercentage("50%")).toEqual(25);
        });
        it("should return 53 with percentage is +5%", function() {
            field.value = "50";
            expect(field.parsePercentage("+5%")).toEqual(53);
        });
    });

    describe("isValid", function() {
        it("should return true when value is valid int", function() {
            expect(field.isValid("55")).toBeTruthy();
        });
        it("should return error when value empty", function() {
            expect(_.isObject(field.isValid(""))).toBeTruthy();
        });
        it("should return error when value is whitespace", function() {
            expect(_.isObject(field.isValid(" "))).toBeTruthy();
        });
        it("should return error when value is invalid chars", function() {
            expect(_.isObject(field.isValid("abcd"))).toBeTruthy();
        });
        it("should return error when value is invalid decimal", function() {
            expect(_.isObject(field.isValid("12.34"))).toBeTruthy();
        });
        it("should return error when value is invalid range low", function() {
            expect(_.isObject(field.isValid("-44"))).toBeTruthy();
        });
        it("should return error when value is invalid range high", function() {
            expect(_.isObject(field.isValid("109"))).toBeTruthy();
        });
    });

});
