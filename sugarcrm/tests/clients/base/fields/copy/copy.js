describe('copy field', function() {

    describe('edit view', function() {
        var app;
        var model;
        var field;

        beforeEach(function() {

            app = SugarTest.app;
            app.view.Field.prototype._renderHtml = function() {
            };

            model = new Backbone.Model({
                int: 1234567890,
                float: 1234567.89,
                name: 'Lórem ipsum dolor sit àmêt, ut úsu ómnés tatión imperdiet.',
                subject: 'Eum quem êrror éligéndi ne, eum ex mundi détracto?',
                description: 'No nobis laboràmus nec, pri id tritáni indoçtum.\n' +
                    'Ením euripidis eu usu. Êt dicô legímus eos, pêr tantas desêruisse effiçíàntur ut.\n' +
                    'Quôdsi ópõrteãt seá eu, êt luçilíús gloriàtur vis. Usu vivêndô appêteré gubergren éx, usu illud mollis insólens çu, êt usú solêt volúptua.',
                address_street: '1 Foo Way',
                address_city: 'Castro Valley',
                address_state: 'CA',
                address_postalcode: '94546',
                address_country: 'USA'
            });

            var fieldDef = {
                mapping: {
                    'address_street': 'name',
                    'float': 'address_street',
                    'int': 'float',
                    'address_postalcode': 'int',
                    'subject': 'address_postalcode',
                    'description': 'subject'
                }
            };
            field = SugarTest.createField('base', 'copy_from_master', 'copy', 'edit', fieldDef);
            sinon.stub(field, '_loadTemplate', function() {
                this.template = function() {
                    return '<input type="checkbox" />';
                };
            });
        });

        afterEach(function() {
            app.cache.cutAll();
            app.view.reset();
            delete Handlebars.templates;
            model = null;
            field._loadTemplate.restore();
            field = null;
        });

        it('should initialize all the private properties correctly', function() {

            field._initialValues = 'initial values must be initialized';
            field._fields = 'fields must be initialized';

            field.initialize(field.options);

            expect(field._initialValues).toEqual({});
            expect(field._fields).toEqual({});
        });

        it('should be able to copy values from and to any type when checked', function() {

            var prev = model.clone();

            field.model = model;
            field.render();
            field._renderHtml();

            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });
            field.$('input[type=checkbox]').attr('checked', true).trigger('click');
            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(source));
            });
        });

        it('should make target fields disabled if exists', function() {

            var stub = sinon.stub(field, 'getField', function() {
                return SugarTest.createField('base', 'dummy', 'base', 'edit');
            });

            var disabled = sinon.spy(field, 'setDisabled');

            field.$('input[type=checkbox]').attr('checked', true).trigger('click');
            expect(disabled.calledOnce);

            stub.restore();
            disabled.restore();
        });

        it('should be able to restore values after unchecked', function() {

            var prev = model.clone();

            field.model = model;
            field.render();
            field._renderHtml();

            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });
            field.$('input[type=checkbox]').attr('checked', true).trigger('click');
            field.$('input[type=checkbox]').attr('checked', false).trigger('click');
            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });
        });

        it('should be able to restore module changed values after unchecked', function() {

            var prev = model.clone();

            field.model = model;
            field.render();
            field._renderHtml();

            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });

            var name = 'Edited name to be restored';
            field.model.set('name', name);
            expect(field.model.get('name')).toEqual(name);

            field.$('input[type=checkbox]').attr('checked', true).trigger('click');
            field.$('input[type=checkbox]').attr('checked', false).trigger('click');

            _.each(field.def.mapping, function(target, source) {
                if (target === 'name') {
                    return;
                }
                expect(field.model.get(target)).toEqual(prev.get(target));
            });

            expect(field.model.get('name')).toEqual(name);
        });

        it('should have sync enabled by default', function() {
            expect(field.def.sync).toBeTruthy();
        });

        it('should be able to keep values in sync', function() {

            var prev = model.clone();

            field.model = model;
            field.render();
            field._renderHtml();

            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });

            field.$('input[type=checkbox]').attr('checked', true).trigger('click');

            var value = 'Edited float value to sync with `address_street` and `name` fields';
            field.model.set('float', value);
            expect(field.model.get('float')).toEqual(value);
            expect(field.model.get('address_street')).toEqual(value);
            expect(field.model.get('name')).toEqual(value);
        });

        it('should be able to copy values without keep it in sync', function() {

            var prev = model.clone();

            field.def.sync = false;
            expect(field.def.sync).toBeFalsy();

            field.model = model;
            field.render();
            field._renderHtml();

            _.each(field.def.mapping, function(target, source) {
                expect(field.model.get(target)).toEqual(prev.get(target));
            });

            field.$('input[type=checkbox]').attr('checked', true).trigger('click');

            var value = 'Edited float value to sync with `address_street` and `name` fields';
            field.model.set('float', value);
            expect(field.model.get('float')).toEqual(value);

            _.each(field.def.mapping, function(target, source) {
                if (target === 'float') {
                    return;
                }
                expect(field.model.get(target)).toEqual(prev.get(source));
            });
        });
    });
});
