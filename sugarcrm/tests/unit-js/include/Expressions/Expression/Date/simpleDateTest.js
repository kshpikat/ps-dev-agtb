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
describe("Simple Date Expression Functions", function () {
    var app, dm, sinonSandbox, meta, model;

    var getSLContext = function (modelOrCollection, context) {
        var isCollection = (modelOrCollection instanceof dm.beanCollection);
        var model = isCollection ? new modelOrCollection.model() : modelOrCollection;
        context = context || app.context.getContext({
            url: "someurl",
            module: model.module,
            model: model
        });
        var view = SugarTest.createComponent("View", {
            context: context,
            type: "edit",
            module: model.module
        });
        return new SUGAR.expressions.SidecarExpressionContext(view, model, isCollection ? modelOrCollection : false);
    };

    beforeEach(function () {
        sinonSandbox = sinon.sandbox.create();
        SugarTest.seedMetadata();
        app = SugarTest.app;
        meta = SugarTest.loadFixture("revenue-line-item-metadata");
        app.metadata.set(meta);
        dm = app.data;
        dm.reset();
        dm.declareModels();
        model = dm.createBean("RevenueLineItems", SugarTest.loadFixture("rli"));
    });

    afterEach(function () {
        sinonSandbox.restore();
    });

    describe("Add Days Expression Function", function () {
        it("should add/subtract certain number of days from original date", function () {
            var dateString = new SUGAR.expressions.StringLiteralExpression(['1/1/2010']);
            var date = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            var num = new SUGAR.expressions.ConstantExpression([5]);
            var res = new SUGAR.expressions.AddDaysExpression([date, num], getSLContext(model));
            expect(res.evaluate()).toBe('2010-01-06');
            num = new SUGAR.expressions.ConstantExpression([-5]);
            res = new SUGAR.expressions.AddDaysExpression([date, num], getSLContext(model));
            expect(res.evaluate()).toBe('2009-12-27');
        });
    });

    describe("Day of Week Expression Function", function () {
        it("should return index for day of the week", function () {
            var dateString = new SUGAR.expressions.StringLiteralExpression(['7/19/2018']);
            var date = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            var res = new SUGAR.expressions.DayOfWeekExpression([date], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe(4);
        });
    });

    describe("Month of Year Expression Function", function () {
        it("should return index for Month of Year", function () {
            var dateString = new SUGAR.expressions.StringLiteralExpression(['7/19/2018']);
            var date = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            var res = new SUGAR.expressions.MonthOfYearExpression([date], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe(7);
        });
    });

    describe("Days Until Expression Function", function () {
        it("returns number of days from now until the specified date.", function () {
            var days, date, last, day, month, year, dateString, dateExpr, res;
            days = 7;
            date = new Date();
            last = new Date(date.getTime() + (days * 24 * 60 * 60 * 1000));
            day =last.getDate();
            month=last.getMonth()+1;
            year=last.getFullYear();
            dateString = new SUGAR.expressions.StringLiteralExpression([`${month}/${day}/${year}`]);
            dateExpr = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            res = new SUGAR.expressions.DaysUntilExpression([dateExpr], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe(days);
            last = new Date(date.getTime() - (days * 24 * 60 * 60 * 1000));
            day =last.getDate();
            month=last.getMonth()+1;
            year=last.getFullYear();
            dateString = new SUGAR.expressions.StringLiteralExpression([`${month}/${day}/${year}`]);
            dateExpr = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            res = new SUGAR.expressions.DaysUntilExpression([dateExpr], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe(days*-1);
        });
    });

    describe("Hours Until Expression Function", function () {
        it("returns number of hours from now until the specified date.", function () {
            var days, date, last, day, month, year, dateString, dateExpr, res;
            days = 7;
            date = new Date();
            last = new Date(date.getTime() + (days * 24 * 60 * 60 * 1000));
            day =last.getDate();
            month=last.getMonth()+1;
            year=last.getFullYear();
            dateString = new SUGAR.expressions.StringLiteralExpression([`${month}/${day}/${year}`]);
            dateExpr = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            res = new SUGAR.expressions.HoursUntilExpression([dateExpr], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe((days*24)-date.getHours()-1);
            last = new Date(date.getTime() - (days * 24 * 60 * 60 * 1000));
            day =last.getDate();
            month=last.getMonth()+1;
            year=last.getFullYear();
            dateString = new SUGAR.expressions.StringLiteralExpression([`${month}/${day}/${year}`]);
            dateExpr = new SUGAR.expressions.DefineDateExpression([dateString], getSLContext(model));
            res = new SUGAR.expressions.HoursUntilExpression([dateExpr], getSLContext(model));
            expect(parseFloat(res.evaluate())).toBe((days*-24) - date.getHours() );
        });
    });

    describe("Define Date Expression Function", function () {
        it("defines the date", function () {
            var date = new SUGAR.expressions.StringLiteralExpression(['01/01/2010']);
            var defDate = new SUGAR.expressions.DefineDateExpression([date], getSLContext(model));
            var expectDate = new Date('01/01/2010');
            expect(defDate.evaluate()).toEqual(expectDate);
        });
    });

    describe("Define Now Expression Function", function () {
        it("returns time expression of right now", function () {
            var today = new SUGAR.expressions.NowExpression([], getSLContext(model));
            var dateVar = new Date();
            var year = dateVar.getFullYear();
            var month = ("0" + (dateVar.getMonth()+1)).slice(-2);
            var date = ("0" + dateVar.getDate()).slice(-2);
            var todaysDate = `${year}-${month}-${date} ${dateVar.getHours()}:${dateVar.getMinutes()}:00`;

            expect(today.evaluate()).toEqual(todaysDate);
            // pending();
        });
    });

    describe("Today Expression Function", function () {
        it("returns today's date", function () {
            var today = new SUGAR.expressions.TodayExpression([], getSLContext(model));
            var dateVar = new Date();
            var year = dateVar.getFullYear();
            var month = ("0" + (dateVar.getMonth()+1)).slice(-2);
            var date = ("0" + dateVar.getDate()).slice(-2);
            var todaysDate = `${year}-${month}-${date}`;

            expect(today.evaluate()).toEqual(todaysDate);
        });
    });

    describe("Timestampe Expression Function", function () {
        it("returns the passed in datetime string as a unix timestamp", function () {
            var dateString = new SUGAR.expressions.StringLiteralExpression(['01/01/2010']);
            var today = new SUGAR.expressions.TimestampExpression([dateString], getSLContext(model));
            var tempDate = new Date('01/01/2010');
            expect(today.evaluate()).toEqual(parseFloat(tempDate.getTime()/1000));
        });
    });
});
