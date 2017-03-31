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
// jscs:disable
    var DecisionTable = function(options) {
        Element.call(this, {id: options.id});
        this.base_module = null;
        this.hitType = null;
        this.dom = null;
        this.proxy = null;
        this.conditions = null;
        this.conclusions = null;
        this.decisionRows = null;
        this.rows = null;
        this.width = null;
        this.onAddColumn = null;
        this.onRemoveColumn = null;
        this.onAddRow = null;
        this.onRemoveRow = null;
        this.onChange = null;
        this.onDirty = null;
        this.showDirtyIndicator = null;
        this.isDirty = null;
        this.conditionFields = [];
        this.conditionCombos = {};
        this.conditionFieldsReady = false;
        this.conclusionFields = [];
        this.conclusionCombos = {};
        this.conclusionFieldsReady = false;
        this.correctlyBuilt = false;
        this.globalCBControl = null;
        this.globalDDSelector = null;
        this.moduleFieldSeparator = "|||";
        this._currencies = [];
        this._dateFormat = null;
        this._timeFormat = null;
        this._isApplyingColumnScrolling = null;
        this.invalidFieldAlertKey = 'DecisionTableInvalidField';
        DecisionTable.prototype.initObject.call(this, options || {});
    };

    DecisionTable.prototype = new Element();

    DecisionTable.prototype.type = 'DecisionTable';

    DecisionTable.LANG_MODULE = 'pmse_Business_Rules';

    DecisionTable.prototype.initObject = function(options) {
        var defaults = {
            proxy: new SugarProxy(),
            restClient: null,
            base_module: "",
            type: 'multiple',
            width: 'auto',
            rows: 0,
            container: null,
            columns: {
                conditions: [],
                conclusions: []
            },
            ruleset: [],
            onAddColumn: null,
            onRemoveColumn: null,
            onChange: null,
            showDirtyIndicator: true,
            currencies: [],
            dateFormat: "YYYY-MM-DD",
            timeFormat: "H:i",
        }, that = this;

        $.extend(true, defaults, options);

        this.dom = {};
        this.conclusions = [];
        this.conditions = [];
        this.decisionRows = 0;
        this.onAddColumn = defaults.onAddColumn;
        this.onRemoveColumn = defaults.onRemoveColumn;
        this.onChange = defaults.onChange;
        this.rows = parseInt(defaults.rows, 10);

        this.setCurrencies(defaults.currencies)
            .setDateFormat(defaults.dateFormat)
            .setTimeFormat(defaults.timeFormat)
            .setProxy(defaults.proxy/*, defaults.restClient*/)
            .setBaseModule(defaults.base_module)
            .setHitType(defaults.type)
            .setWidth(defaults.width)
            .setShowDirtyIndicator(defaults.showDirtyIndicator);

        //this.getHTML();
        if(defaults.container) {
            $(defaults.container).append(this.getHTML());

            if(!this.isDOMNodeInsertedSupported) {
                this.updateDimensions();
            }
        }

        this.auxConclutions = defaults.columns.conclusions;
        this.auxConditions = defaults.columns.conditions;
        this.rules = defaults.ruleset;

        this.globalCBControl = new ExpressionControl({
            matchOwnerWidth: false,
            width: 250,
            allowInput: true,
            itemContainerHeight: 70,
            dateFormat: this._dateFormat,
            timeFormat: this._timeFormat,
            appendTo: jQuery("#businessrulecontainer").get(0),
            currencies: this._currencies
        });

        this.globalDDSelector = new DropdownSelector({
            matchOwnerWidth: true
        });

        this.getFields();
    };

    DecisionTable.prototype.setCurrencies = function (currencies) {
        this._currencies = currencies;
        if (this.globalCBControl) {
            this.globalCBControl.setCurrencies(this._currencies);
        }
        return this;
    };

    DecisionTable.prototype.setDateFormat = function (dateFormat) {
        if (this.globalCBControl) {
            this.globalCBControl.setDateFormat(dateFormat);
        }
        this._dateFormat = dateFormat;
        return this;
    };

    DecisionTable.prototype.setTimeFormat = function (timeFormat) {
        if (this.globalCBControl) {
            this.globalCBControl.setDateFormat(timeFormat);
        }
        this._timeFormat = timeFormat;
        return this;
    };

    DecisionTable.prototype.setShowDirtyIndicator = function (show) {
        this.showDirtyIndicator = !!show;
        return this;
    };

    DecisionTable.prototype.getIsDirty = function () {
        return this.isDirty;
    };

    DecisionTable.prototype.setIsDirty = function(dirty, silence) {
        this.isDirty = dirty;
        if (!silence) {
            if(typeof this.onDirty === 'function') {
                this.onDirty.call(this, dirty);
            }
        }
        return this;
    };

    DecisionTable.prototype.onChangeVariableHandler = function() {
        var that = this;
        return function(newVal, oldVal) {
            var valid, cell = this.getHTML(),
                index = $(cell.parentElement).find(cell.tagName.toLowerCase()).index(cell);
            if(this.variableMode === 'condition') {
                valid = that.validateColumn(index, 0);
            } else {
                valid = that.validateColumn(index, 1);
            }

            that.setIsDirty(true);

            if(typeof that.onChange === 'function') {
                that.onChange.call(that, {
                    object: this,
                    newVal: newVal,
                    oldVal: oldVal
                }, valid);
            }
        };
    };

    DecisionTable.prototype.onChangeValueHandler = function() {
        var that = this;
        return function(valueObject, newVal, oldVal) {
            var row, cell, index, indexColumn, isEvaluationVariable, valid;

            isEvaluationVariable = valueObject instanceof DecisionTableValueEvaluation;
            cell = isEvaluationVariable ? valueObject.getHTML()[0] : valueObject.getHTML();
            row = cell.parentElement;
            indexColumn = $(cell.parentElement).find("td").index(cell) / (isEvaluationVariable ? 2 : 1);
            index = $(row.parentElement).find("tr").index(row);

            /*valid = valueObject.isValid();*/

            //if(valid.valid) {
            valid = that.validateColumn(indexColumn, isEvaluationVariable ? 0 : 1);
            if(valid.valid) {
                valid = that.validateRow(index);
            }
            /* } else {
             valid.location = (isEvaluationVariable ? 'Condition' : 'Conclusion') + " # " + (indexColumn + 1) + " - row # " + (index + 1);
             }*/
            that.setIsDirty(true);
            if(typeof that.onChange === 'function') {
                that.onChange.call(that, {
                    object: valueObject,
                    newVal: newVal,
                    oldVal: oldVal
                }, valid);
            }
        };
    };

    DecisionTable.prototype.removeAllConclusions = function() {
        while(this.conclusions.length) {
            this.conclusions[0].remove(true);
        }

        return this;
    };

    DecisionTable.prototype.removeAllConditions = function() {
        while(this.conditions.length) {
            this.conditions[0].remove(true);
        }
        return this;
    };

    DecisionTable.prototype.setConditions = function(conditions) {
        var i;
        this.removeAllConditions();
        for(i = 0; i < conditions.length; i+=1) {
            this.addCondition(conditions[i]);
        }
        return this;
    };

    DecisionTable.prototype.setConclusions = function(conclusions) {
        var i;
        this.removeAllConclusions();
        for(i = 0; i < conclusions.length; i+=1) {
            this.addConclusion(!conclusions[i], this.base_module + this.moduleFieldSeparator + conclusions[i]);
        }
        return this;
    };

    /**
     * Scan through the fields list in the current rule set for any invalid fields
     * Toggle save button states and error alert
     * @param {boolean} whether to show error alert
     */
    DecisionTable.prototype.validateFields = function(showAlert) {
        var scanArray = function(input) {
            var i;
            for(i = 0; i < input.length; i++) {
                if(!input[i].fieldValid && (input[i].field != '' || input[i].module != '')) {
                    return false;
                }
            }
            return true;
        };
        var valid = scanArray(this.conditions);
        if (valid) {
            valid = scanArray(this.conclusions);
        }
        if (valid) {
            $(".btn-primary[name='project_save_button']").removeClass("disabled");
            $(".btn-primary[name='project_finish_button']").removeClass("disabled");
            App.alert.dismiss(this.invalidFieldAlertKey);
        } else {
            $(".btn-primary[name='project_save_button']").addClass("disabled");
            $(".btn-primary[name='project_finish_button']").addClass("disabled");
            if (showAlert) {
                App.alert.show(this.invalidFieldAlertKey, {
                    level: "error",
                    messages: translate('LBL_PMSE_MESSAGE_REQUIRED_FIELDS_BUSINESSRULES', DecisionTable.LANG_MODULE)
                });
            }
        }
    };

    /**
     * Utility function to construct a module field concatenation to be used
     * mostly as an identifier
     * @param {string} module name
     * @param {string} field name
     * @return {string} concatenation
     */
    DecisionTable.prototype.getModuleFieldConcat = function(module, field) {
        return module + this.moduleFieldSeparator + field;
    };

    DecisionTable.prototype.setRuleset = function(ruleset) {
        var i, j,
            condition_column_helper = {},
            conclusion_column_helper = {},
            aux,
            conditions, conclusions, auxKey;

        //fill the column helper for conditions
        for(i = 0; i < this.conditions.length; i+=1) {
            if(!condition_column_helper[this.conditions[i].select.value]) {
                condition_column_helper[this.conditions[i].select.value] = [i];
            } else {
                condition_column_helper[this.conditions[i].select.value].push(i);
            }
        }

        conclusion_column_helper.result = 0;
        for(i = 1; i < this.conclusions.length; i+=1) {
            conclusion_column_helper[this.conclusions[i].select.value] = i
        }

        for(i = 0; i < ruleset.length; i+=1) {
            conditions = ruleset[i].conditions;
            aux = {};
            for(j = 0; j < conditions.length; j+=1) {
                auxKey = this.getModuleFieldConcat(conditions[j].variable_module, conditions[j].variable_name);
                if(typeof aux[auxKey] === 'undefined') {
                    aux[auxKey] = -1;
                }
                aux[auxKey] +=1;
                if(typeof condition_column_helper[auxKey] !== 'undefined') {
                    this.conditions[condition_column_helper[auxKey][aux[auxKey]]].addValue(conditions[j].value, conditions[j].condition);
                }
            }

            conclusions = ruleset[i].conclusions;
            for(j = 0; j < conclusions.length; j+=1) {
                auxKey = (conclusions[j].conclusion_type === "return" ? "result" : this.getModuleFieldConcat(this.base_module, conclusions[j].conclusion_value));
                if(typeof conclusion_column_helper[auxKey] !== 'undefined') {
                    this.conclusions[conclusion_column_helper[auxKey]].addValue(conclusions[j].value);
                }
            }

            this.addDecisionRow();
        }

        this.validateFields(true);
        this.correctlyBuilt = true;
        this.updateDimensions();
        return this;
    };

    DecisionTable.prototype.isDOMNodeInsertedSupported = function() {
        var div = this.createHTMLElement('div'), supported = false;
        div.addEventListener('DOMNodeInserted', function() { supported = true; });
        div.appendChild(div.cloneNode());

        return supported;
    };

    DecisionTable.prototype.setRows = function(rows) {
        this.rows = parseInt(rows, 10);
        return this.updateDimensions();
    };

    DecisionTable.prototype.setWidth = function(w) {
        this.width = w;
        return this.updateDimensions();
    };

    DecisionTable.prototype.updateDimensions = function() {
        if(!this.html) {
            return this;
        }
        var w, w_cond, w_conc, index_w;//, header = $(this.dom.hitTypeLabel.parentElement);
        //console.log("Header: ", header);

        //this.dom.nameLabel.style.display = 'none';

        if(this.width !== 'auto') {
            index_w = $(this.dom.indexTableContainer).outerWidth() + 4;
            w = (this.width - index_w) / (this.conditions.length + this.conclusions.length);
            w_cond = $(this.dom.conditionsTable).css("width", "").outerWidth();
            w_conc = $(this.dom.conclusionsTable).css("width", "").outerWidth();
            w = w_cond + w_conc;
            w_cond = Math.floor(w_cond / w * (this.width - index_w));
            w_conc = this.width - index_w - w_cond;
        } else {
            $(this.dom.conditionsHeader.parentElement).css("width", "").find('th').css("width", "");
            $(this.dom.conclusionsTable).css("width", "");
            $(this.dom.conclusionsHeader.parentElement).css("width", "").find('th').css("width", "");
        }

        this.dom.conditionsTableContainer.style.width = this.dom.conditionsHeaderContainer.style.width = this.width !== 'auto' ? w_cond + "px" : "auto";
        this.dom.conclusionsTableContainer.style.width = this.dom.conclusionsHeaderContainer.style.width = this.width !== 'auto' ? w_conc + "px" : "auto";

        if(this.decisionRows && this.rows) {
            w = $(this.dom.conditionsTable).find("tr").outerHeight();
            this.dom.indexTableContainer.style.height = this.dom.conditionsTableContainer.style.height = this.dom.conclusionsTableContainer.style.height = ((w * this.rows) + 10 + this.rows) + "px";
        } else {
            this.dom.indexTableContainer.style.height = this.dom.conditionsTableContainer.style.height = this.dom.conclusionsTableContainer.style.height = "auto";
        }

        w = $(this.dom.conditionsTable).outerWidth();
        if(w < $(this.dom.conditionsTableContainer).width() && this.width !== 'auto') {
            this.dom.conditionsTable.style.width = "100%";
            w = $(this.dom.conditionsTable).outerWidth();
            w = Math.ceil(w/2) * 2;
        }
        $(this.dom.conditionsHeader.parentElement).css("width", w + "px");
        w = Math.floor(w / this.conditions.length);
        $(this.dom.conditionsHeader).find('th').css("width", w + "px");

        w = $(this.dom.conclusionsTable).outerWidth();
        if(w < $(this.dom.conclusionsTableContainer).width() && this.width !== 'auto') {
            this.dom.conclusionsTable.style.width = "100%";
            w = $(this.dom.conclusionsTable).outerWidth();
            w = Math.ceil(w/2) * 2;
        }
        $(this.dom.conclusionsHeader.parentElement).css("width", w + "px");
        w = Math.floor(w / this.conclusions.length);
        $(this.dom.conclusionsHeader).find("th").css("width", w + "px");

        //w_cond = $(this.dom.hitTypeLabel);
        //w_conc = header.find('.decision-table-module');
        //index = $(this.dom.dirtyIndicator);
        //w = header.width();
        //w -= ( w_cond.innerWidth() + parseInt(w_cond.css("margin-left"))
        //+ parseInt(w_cond.css("margin-right")) + w_conc.innerWidth()
        //+ parseInt(w_conc.css("margin-left")) + parseInt(w_conc.css("margin-right"))
        //+ parseInt($(this.dom.nameLabel).css("margin-right")) + parseInt($(this.dom.nameLabel).css("margin-left"))
        //+ index.width() + parseInt(index.css("margin-left")) + parseInt(index.css("margin-right")));
        //this.dom.nameLabel.style.maxWidth = (w - 25) + 'px';
        //this.dom.nameLabel.style.display = '';

        return this;
    };

    DecisionTable.prototype.createRemoveButton = function() {
        //var input = this.createHTMLElement('input');
        var minusNode = this.createHTMLElement('span');
        minusNode.className = 'fa fa-minus decision-table-remove';
        //minusNode.innerHTML = '&nbsp;';
        //input.tabIndex = 0;
        //input.type = 'text';
        //input.className = 'decision-table-remove';
        //input.readOnly = true;
        //input.value = '-';
        //input.appendChild(minusNode);
        //input.style.width = '15px';

        return minusNode;
    };

    DecisionTable.prototype.addDecisionRow = function () {
        var row = this.createHTMLElement('tr'), i, aux;

        if(!(this.conditions.length && this.conclusions.length)) {
            return this;
        }

        for(i = 0; i < this.conditions.length; i+=1) {
            if(!this.conditions[i].values[this.decisionRows]) {
                this.conditions[i].addValue();
            }
            aux = this.conditions[i].getValueHTML(this.conditions[i].values.length - 1);
            row.appendChild(aux[0]);
            row.appendChild(aux[1]);
        }
        this.dom.conditionsTable.appendChild(row);

        row = row.cloneNode(false);
        for(i = 0; i < this.conclusions.length; i+=1) {
            if(!this.conclusions[i].values[this.decisionRows]) {
                this.conclusions[i].addValue();
            }
            row.appendChild(this.conclusions[i].getValueHTML(this.conclusions[i].values.length - 1));
        }
        this.dom.conclusionsTable.appendChild(row);

        row = row.cloneNode(false);
        aux = this.createRemoveButton();
        this.decisionRows+=1;
        i = this.createHTMLElement("td");
        i.appendChild(aux);
        row.appendChild(i);
        this.dom.indexTable.appendChild(row);

        return this;
    };

    DecisionTable.prototype.removeRowWithoutConfirmation = function (index) {
        for(i = 0; i < this.conclusions.length; i+=1) {
            this.conclusions[i].removeValue(index);
        }

        for(i = 0; i < this.conditions.length; i+=1) {
            this.conditions[i].removeValue(index);
        }

        $(this.dom.indexTable).find('tr:eq(' + index + ')').remove();
        $(this.dom.conditionsTable).find('tr:eq(' + index + ')').remove();
        $(this.dom.conclusionsTable).find('tr:eq(' + index + ')').remove();

        this.decisionRows --;
        this.setIsDirty(true);

        valid = this.validateColumn();

        if(typeof this.onChange === 'function') {
            this.onChange.call(this, {}, valid);
        }

        if(typeof this.onRemoveRow === 'function') {
            this.onRemoveRow.call(this);
        }

        return this;
    };

    DecisionTable.prototype.removeDecisionRow = function(index) {
        var i,
            ask = false,
            self = this;

        if(this.decisionRows === 1) {
            App.alert.show('mininal-error', {
                level: 'warning',
                messages: translate('LBL_PMSE_MESSAGE_LABEL_MIN_ROWS', DecisionTable.LANG_MODULE),
                autoClose: true
            });
            return this;
        }

        //Check if there are conditions or conditions filled
        for(i = 0; i < this.conditions.length; i+=1) {
            if(this.conditions[i].values[index].filledValue()) {
                ask = true;
                break;
            }
        }
        if (!ask) {
            for(i = 0; i < this.conclusions.length; i+=1) {
                if(this.conclusions[i].values[index].filledValue()) {
                    ask = true;
                    break;
                }
            }
        }
        if (ask) {
            App.alert.show('message-config-delete-row', {
                level: 'confirmation',
                messages: translate('LBL_PMSE_MESSAGE_LABEL_DELETE_ROW', DecisionTable.LANG_MODULE),
                onConfirm: function() {
                    return self.removeRowWithoutConfirmation(index);
                },
                onCancel: function() {
                    return this;
                }
            });
        } else {
            return this.removeRowWithoutConfirmation(index);
        }
    };

    DecisionTable.prototype.parseFieldsData = function(data, self) {
        var i, j, fields, combos, module, result = {success : false};
        if (data && data.success) {
            fields = [];
            combos = {};
            for (i = 0; i < data.result.length; i += 1) {
                module = data.result[i];
                for (j = 0; j < module.fields.length; j += 1) {
                    fields.push({
                        label: module.fields[j].text,
                        value: module.fields[j].value,
                        type: module.fields[j].type,
                        moduleText: module.text,
                        moduleValue: module.value
                    });
                    //Maybe backend shouldn't send the optionItem field if doesn't apply to the field.
                    if (module.fields[j].optionItem !== "none") {
                        combos[module.value + self.moduleFieldSeparator + module.fields[j].value] = module.fields[j].optionItem;
                    } else if (module.fields[j].type === 'Checkbox') {
                        combos[module.value + self.moduleFieldSeparator + module.fields[j].value] = {
                            checked: translate('LBL_PMSE_DROP_DOWN_CHECKED', 'pmse_Business_Rules'),
                            unchecked: translate('LBL_PMSE_DROP_DOWN_UNCHECKED', 'pmse_Business_Rules')
                        };
                    }
                }
            }
            result.fields = fields;
            result.combos = combos;
            result.success = true;
        }
        return result;
    };

    DecisionTable.prototype.finishGetFields = function(defaultValue, self) {
        self.setConditions(self.auxConditions);
        self.setConclusions(self.auxConclutions);
        self.setRuleset(self.rules);
        if(!self.conditions.length) {
            self.addCondition(defaultValue);
        }
        if(!self.conclusions.length) {
            self.addConclusion(true);
        }
        if(!self.decisionRows) {
            self.addDecisionRow();
        }

        self.updateDimensions();

        App.alert.dismiss('upload');
        self.setIsDirty(false);
    };

    DecisionTable.prototype.getConditionFields = function(defaultValue) {
        var self = this;
        this.proxy.setUrl('ProcessBusinessRules/fields/conditions');
        this.proxy.getData({base_module: this.base_module, call_type: 'BRR'}, {
            success: function(data) {
                var result = self.parseFieldsData(data, self);
                if (result.success) {
                    self.conditionFields = result.fields;
                    self.conditionCombos = result.combos;
                    self.conditionFieldsReady = true;
                    if (self.conclusionFieldsReady) {
                        self.finishGetFields(defaultValue, self);
                    }
                }
            }
        });
    };

    DecisionTable.prototype.getConclusionFields = function(defaultValue) {
        var self = this;
        this.proxy.setUrl('ProcessBusinessRules/fields/conclusions');
        this.proxy.getData({base_module: this.base_module, call_type: 'BR'}, {
            success: function(data) {
                var result = self.parseFieldsData(data, self);
                if (result.success) {
                    self.conclusionFields = result.fields;
                    self.conclusionCombos = result.combos;
                    self.conclusionFieldsReady = true;
                    if (self.conditionFieldsReady) {
                        self.finishGetFields(defaultValue, self);
                    }
                }
            }
        });
    };

    DecisionTable.prototype.getFields = function(defaultValue) {
        if (!this.conditionFieldsReady || !this.conclusionFieldsReady) {
            App.alert.show('upload', {level: 'process', title: 'LBL_LOADING', autoclose: false});
            if (!this.conditionFieldsReady) {
                this.getConditionFields(defaultValue);
            }
            if (!this.conclusionFieldsReady) {
                this.getConclusionFields(defaultValue);
            }
        }
    };

    DecisionTable.prototype.setProxy = function(proxy/*, restClient*/) {
        this.proxy = proxy;
        return this;
    };

    DecisionTable.prototype.setBaseModule = function(base_module) {
        this.base_module = base_module;
        return this;
    };

    DecisionTable.prototype.setHitType = function(hitType) {
        this.hitType = hitType;
        return this;
    };

    DecisionTable.prototype.onBeforeVariableOpenPanelHandler = function () {
        var that = this;
        return function (column, decisionTableValue) {
            var decisionTable = that,
                headerContainer = decisionTableValue instanceof DecisionTableValueEvaluation ? decisionTable.dom.conditionsHeaderContainer
                    : decisionTable.dom.conclusionsHeaderContainer,
                tableContainer = decisionTableValue instanceof DecisionTableValueEvaluation ? decisionTable.dom.conditionsTableContainer
                    : decisionTable.dom.conclusionsTableContainer,
                headerPosition = getRelativePosition(column.html, headerContainer),
                headerWidth = $(column.html).innerWidth();

            that.globalCBControl.setAlignWithOwner("left");
            if (headerPosition.left < 0) {
                that._isApplyingColumnScrolling = true;
                tableContainer.scrollLeft += headerPosition.left;
            } else if (headerPosition.left + headerWidth > $(headerContainer).innerWidth()) {
                that.globalCBControl.setAlignWithOwner("right");
                that._isApplyingColumnScrolling = true;
                tableContainer.scrollLeft = headerPosition.left + headerWidth + headerContainer.scrollLeft
                    - $(headerContainer).innerWidth();
            }
            if (getRelativePosition(this.html, decisionTable.html).left + that.globalCBControl.width
                > $(decisionTable.html).outerWidth()) {
                that.globalCBControl.setAlignWithOwner("right");
            }
        };
    }

    DecisionTable.prototype.onRemoveVariableHandler = function(array) {
        var that = this, variablesArray = array, valid;
        return function() {
            var x;
            for(var i = 0; i < variablesArray.length; i+=1) {
                if(variablesArray[i] === this) {
                    x = variablesArray[i];
                    variablesArray.splice(i, 1);
                }
            }
            that.updateDimensions();
            valid = that.validateRow();
            if(typeof that.onRemoveColumn === 'function') {
                that.onRemoveColumn.call(this, x);
            }
            that.setIsDirty(true);
            if(typeof that.onChange === 'function') {
                that.onChange.call(that, {}, valid);
            }
        };
    };


    DecisionTable.prototype.addCondition = function(defaultValue) {

        var condition = new DecisionTableVariable({
            parent: this,
            field: defaultValue || null,
            fields: this.conditionFields,
            combos: this.conditionCombos,
            inputFields: this.conditionFields
        }), i, html;

        condition.onBeforeValueOpenPanel = this.onBeforeVariableOpenPanelHandler();
        condition.onRemove = this.onRemoveVariableHandler(this.conditions);
        condition.onChangeValue = this.onChangeValueHandler();
        condition.onChange = this.onChangeVariableHandler();
        this.conditions.push(condition);
        if(this.html) {
            this.dom.conditionsHeader.appendChild(condition.getHTML());
        }

        this.proxy.uid = this.base_module || "";

        for(i = 0; i < this.decisionRows; i+=1) {
            condition.addValue();
            html = condition.getValueHTML(i);
            $(this.dom.conditionsTable).find("tr:eq(" + i + ")").append(html[0]).append(html[1]);
        }

        this.updateDimensions();
        this.setIsDirty(true);

        if(typeof this.onAddColumn === 'function') {
            this.onAddColumn.call(this, condition);
        }

        return this;
    };

    DecisionTable.prototype.addConclusion = function (returnType, defaultValue) {
        var conclusion = new DecisionTableVariable({
            isReturnType: returnType,
            variableMode: "conclusion",
            fields: this.conclusionFields,
            combos: this.conclusionCombos,
            inputFields: this.conditionFields,
            field: defaultValue,
            parent: this
        }), i;

        conclusion.onBeforeValueOpenPanel = this.onBeforeVariableOpenPanelHandler();
        conclusion.onRemove = this.onRemoveVariableHandler(this.conclusions);
        conclusion.onChangeValue = this.onChangeValueHandler();
        conclusion.onChange = this.onChangeVariableHandler();
        this.conclusions.push(conclusion);
        if(this.html) {
            this.dom.conclusionsHeader.appendChild(conclusion.getHTML());
        }

        for(i = 0; i < this.decisionRows; i+=1) {
            conclusion.addValue();
            this.dom.conclusionsTable.childNodes[i].appendChild(conclusion.getValueHTML(i));
        }

        this.updateDimensions();
        this.setIsDirty(true);
        if(typeof this.onAddColumn === 'function') {
            this.onAddColumn.call(this, conclusion);
        }

        return this;
    };

    DecisionTable.prototype.canBeRemoved = function(obj) {
        var res = false;
        if(obj.parent === this) {
            if(obj.variableMode === 'condition') {
                res = this.conditions.length > 1;
                if(!res) {
                    App.alert.show('mininal-column-error', {
                        level: 'warning',
                        messages: translate('LBL_PMSE_MESSAGE_LABEL_MIN_CONDITIONS_COLS', DecisionTable.LANG_MODULE),
                        autoClose: true
                    });
                }
            } else if (obj.variableMode === 'conclusion') {
                res = this.conclusions.length > 1;
                if(!res) {
                    App.alert.show('mininal-column-error', {
                        level: 'warning',
                        messages: translate('LBL_PMSE_MESSAGE_LABEL_MIN_CONCLUSIONS_COLS', DecisionTable.LANG_MODULE),
                        autoClose: true
                    });
                }
            }
        }
        return res;
    };

    DecisionTable.prototype.createHTML = function() {
        if(this.html) {
            return this.html;
        }

        var table, row, cell, header, body, textContainer, subtable, button, i, span;

        header = this.createHTMLElement('thead');

        plusNode = this.createHTMLElement('span');
        plusNode.className = 'fa fa-plus';
        plusNode2 = this.createHTMLElement('span');
        plusNode2.className = 'fa fa-plus';

        //create the table subheaders
        row = this.createHTMLElement('tr');
        cell = this.createHTMLElement('th');
        row.appendChild(cell);
        cell = this.createHTMLElement('th');
        button = this.createHTMLElement('button');
        button.appendChild(plusNode);
        button.className = 'decision-table-add-button';
        button.title = translate('LBL_PMSE_TOOLTIP_ADD_CONDITION', DecisionTable.LANG_MODULE);
        this.dom.addConditionButton = button;
        textContainer = this.createHTMLElement('span');
        textContainer.appendChild(document.createTextNode(translate('LBL_PMSE_LABEL_CONDITIONS', DecisionTable.LANG_MODULE)));
        textContainer.appendChild(button);
        cell.appendChild(textContainer);
        cell.className = 'decision-table-separator-border';
        row.appendChild(cell);
        cell = cell.cloneNode(false);
        button = button.cloneNode(true);
        button.title = translate('LBL_PMSE_TOOLTIP_ADD_CONCLUSION', DecisionTable.LANG_MODULE);
        this.dom.addConclusionButton = button;
        textContainer = textContainer.cloneNode(false);
        textContainer.appendChild(document.createTextNode(translate('LBL_PMSE_LABEL_CONCLUSIONS', DecisionTable.LANG_MODULE)));
        textContainer.appendChild(button);
        cell.appendChild(textContainer);
        row.appendChild(cell);
        header.appendChild(row);

        //create the body and the body header
        row = this.createHTMLElement("tr");
        cell = this.createHTMLElement('th');
        textContainer = this.createHTMLElement('button');
        textContainer.appendChild(plusNode2);
        textContainer.title = translate('LBL_PMSE_TOOLTIP_ADD_ROW', DecisionTable.LANG_MODULE);
        textContainer.className = 'decision-table-add-row';
        cell.appendChild(textContainer);
        row.appendChild(cell);
        cell = this.createHTMLElement('th');
        textContainer = this.createHTMLElement('div');
        textContainer.className = 'decision-table-conditions-header';
        this.dom.conditionsHeaderContainer = textContainer;
        subtable = this.createHTMLElement('table');
        subtable.appendChild(row.cloneNode(false));
        textContainer.appendChild(subtable);
        this.dom.conditionsHeader = subtable.childNodes[0];
        cell.className = 'decision-table-separator-border';
        cell.appendChild(textContainer);
        row.appendChild(cell);
        cell = cell.cloneNode(true);
        this.dom.conclusionsHeaderContainer = cell.childNodes[0];
        this.dom.conclusionsHeaderContainer.className = "decision-table-conclusions-header";
        this.dom.conclusionsHeader = this.dom.conclusionsHeaderContainer.childNodes[0].childNodes[0];
        row.appendChild(cell);
        body = this.createHTMLElement('tbody');
        body.appendChild(row);

        //create the cells in body that will contain the tables for data
        row = this.createHTMLElement('tr');
        cell = this.createHTMLElement('td');
        textContainer = textContainer.cloneNode(false);
        textContainer.className = 'decision-table-container';
        this.dom.indexTableContainer = textContainer;
        subtable = subtable.cloneNode(false);
        subtable.className = 'decision-table-index';
        this.dom.indexTable = subtable;
        textContainer.appendChild(subtable);
        cell.appendChild(textContainer);
        row.appendChild(cell);
        cell = cell.cloneNode(true);
        this.dom.conditionsTable = (this.dom.conditionsTableContainer = cell.childNodes[0]).childNodes[0];
        this.dom.conditionsTable.className = 'decision-table-conditions';
        cell.className = 'decision-table-separator-border';
        row.appendChild(cell);
        cell = cell.cloneNode(true);
        cell.className = "";
        this.dom.conclusionsTable = (this.dom.conclusionsTableContainer = cell.childNodes[0]).childNodes[0];
        //$(this.dom.conclusionsTableContainer).addClass("decision-table-scroll-x");
        this.dom.conclusionsTable.className = 'decision-table-conclusions';
        row.appendChild(cell);
        body.appendChild(row);

        //create the table and append the header and body
        table = this.createHTMLElement('table');
        table.className = "decision-table";
        table.appendChild(header);
        table.appendChild(body);

        this.html = table;

        for(i = 0; i < this.conditions.length; i+=1) {
            this.dom.conditionsHeader.appendChild(this.conditions[i].getHTML());
        }

        for(i = 0; i < this.conclusions.length; i+=1) {
            this.dom.conclusionsHeader.appendChild(this.conclusions[i].getHTML());
        }

        this.setShowDirtyIndicator(this.showDirtyIndicator);

        this.attachListeners();

        return this.html;
    };

    DecisionTable.prototype.attachListeners = function() {
        var that = this;
        $(this.dom.conditionsTableContainer).on('scroll', function(){
            if (that._isApplyingColumnScrolling) {
                that._isApplyingColumnScrolling = false;
            } else {
                that.globalCBControl.close();
                that.globalDDSelector.close();
            }
            that.dom.conditionsHeaderContainer.scrollLeft = this.scrollLeft;
            that.dom.conclusionsTableContainer.scrollTop = this.scrollTop;
        });

        $(this.dom.conditionsHeaderContainer).on('scroll', function() {
            that.dom.conditionsTableContainer.scrollLeft = this.scrollLeft;
        });

        $(this.dom.conclusionsHeaderContainer).on('scroll', function () {
            that.dom.conclusionsTableContainer.scrollLeft = this.scrollLeft;
        });

        $(this.dom.conclusionsTableContainer).on('scroll', function(){
            if (that._isApplyingColumnScrolling) {
                that._isApplyingColumnScrolling = false;
            } else {
                that.globalCBControl.close();
                that.globalDDSelector.close();
            }
            that.dom.conclusionsHeaderContainer.scrollLeft = this.scrollLeft;
            that.dom.indexTableContainer.scrollTop = that.dom.conditionsTableContainer.scrollTop = this.scrollTop;
        });

        $(this.dom.indexTableContainer).on('scroll', function() {
            that.dom.conditionsTableContainer.scrollTop = that.dom.conclusionsTableContainer.scrollTop = this.scrollTop;
        });

        $(this.dom.addConclusionButton).on('click', function() {
            that.addConclusion();
        });

        $(this.dom.addConditionButton).on('click', function() {
            that.addCondition();
        });

    //    $(this.dom.indexTable).on('click', 'span', function() {
    //        that.removeDecisionRow($(that.dom.indexTable).find("span").index(this));
    //    });
        $(this.dom.indexTable).on('click', 'span.decision-table-remove', function() {
            that.removeDecisionRow($(that.dom.indexTable).find("span.decision-table-remove").index(this));
        });

        $(this.dom.conditionsTable).on('keydown', 'td', function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                    e.preventDefault();
                    $(that.conclusions[0].getValueHTML(index)).find("span").focus();
                } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                    e.preventDefault();
                    $(that.dom.indexTable).find("span").eq(index).focus();
                }
            }
        });

        $(this.dom.indexTable).on("keydown", "td", function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if(!e.shiftKey) {
                    e.preventDefault();
                    $(that.conditions[0].getValueHTML(index)[0]).find("span").focus();
                } else if(index > 0){
                    e.preventDefault();
                    $(that.conclusions[that.conclusions.length - 1].getValueHTML(index - 1)).find("span").focus();
                }
            }
        });

        $(this.dom.conclusionsTable).on("keydown", "td", function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if($(row).find("td:last").get(0) === this && !e.shiftKey && index < that.decisionRows - 1) {
                    e.preventDefault();
                    $(that.dom.indexTable).find("span").eq(index + 1).focus();
                } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                    e.preventDefault();
                    $(that.conditions[that.conditions.length - 1].getValueHTML(index)[1]).find("span").focus();
                }
            }
        });

        $(this.dom.conditionsTable).on('keydown', 'td', function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                    e.preventDefault();
                    $(that.conclusions[0].getValueHTML(index)).find("span").focus();
                } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                    e.preventDefault();
                    $(that.dom.indexTable).find("button").eq(index).focus();
                }
            }
        });

        $(this.dom.indexTable).on('keydown', 'td', function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if($(row).find("td:last").get(0) === this && !e.shiftKey) {
                    e.preventDefault();
                    $(that.conditions[0].getValueHTML(index)[0]).find("span").focus();
                } else if($(row).find("td:first").get(0) === this && e.shiftKey && index > 0){
                    e.preventDefault();
                    $(that.conclusions[that.conclusions.length - 1].getValueHTML(index - 1)).find('span').focus();
                }
            }
        });

        $(this.dom.conclusionsTable).on('keydown', 'td', function(e) {
            var index, row = this.parentElement;
            if(e.keyCode === 9) {
                index = $(row.parentElement).find("tr").index(row);
                if($(row).find("td:last").get(0) === this && !e.shiftKey && index < that.decisionRows - 1) {
                    e.preventDefault();
                    $(that.dom.indexTable).find("button").eq(index + 1).focus();
                } else if($(row).find("td:first").get(0) === this && e.shiftKey) {
                    e.preventDefault();
                    $(that.conditions[that.conditions.length - 1].getValueHTML(index)[1]).find("span").focus();
                }
            }
        });

        $(this.html).find('.decision-table-add-row').on('click', function() {
            that.addDecisionRow();
        });

        $(this.dom.conditionsTable).add(this.dom.conclusionsTable).add(this.dom.indexTable).on("focus", "td", function() {
            var row = this.parentElement, index;
            $(that.html).find("tr.cell-edit").removeClass("cell-edit");
            index = $(row.parentElement).find("tr").index(row);
            $(that.dom.indexTable.childNodes[index]).add(that.dom.conditionsTable.childNodes[index]).add(that.dom.conclusionsTable.childNodes[index]).addClass("cell-edit");
        }).on("blur", "select, input", function(){
            //$(that.html).find("tr.cell-edit").removeClass("cell-edit");
        });

        $(document).bind('DOMNodeInserted', function(e) {
            if(e.target === that.html) {
                that.updateDimensions();
            }
        });

        return this;
    };

    DecisionTable.prototype.validateConclusions = function() {
        var i, obj = {};

        for(i = 0; i < this.conclusions.length; i+=1) {
            if(!this.conclusions[i].isReturnType && this.conclusions[i].field && this.conclusions[i].getFilledValuesNum()) {
                if(!obj[this.conclusions[i].field]) {
                    obj[this.conclusions[i].field] = true;
                } else {
                    $(this.conclusions[i].getHTML()).addClass('error');
                    return {
                        valid: false,
                        location: "Conclusion # " + (i + 1),
                        message: translate('LBL_PMSE_BUSINESSRULES_ERROR_CONCLUSIONVARDUPLICATED', DecisionTable.LANG_MODULE)
                    }
                }
            }
            $(this.conclusions[i].getHTML()).removeClass('error');
        }

        return {valid: true};
    };

    DecisionTable.prototype.validateRow = function(index) {
        var start = 0, limit = this.decisionRows,
            rowHasConclusions, rowHasConditions, i, j, defaultRulesets = 0;

        if(typeof index === 'number') {
            start = index;
            limit = index + 1;
        }

        for(i = start; i < limit; i+=1) {
            rowHasConditions = false;
            rowHasConclusions = false;
            //validate if the row has return value conclusion if there are any condition
            for(j = 0; j < this.conditions.length; j+=1) {
                if(this.conditions[j].values[i].filledValue()) {
                    rowHasConditions = true;
                    break;
                }
            }

            if(rowHasConditions) {
                if(!this.conclusions[0].values[i].filledValue()) {
                    $(this.conclusions[0].values[i].getHTML()).addClass("error");
                    return {
                        valid: false,
                        message: translate('LBL_PMSE_MESSAGE_LABEL_EMPTY_RETURN_VALUE', DecisionTable.LANG_MODULE),
                        location: "row # " + (i + 1)
                    };
                } else {
                    rowHasConclusions = true;
                }
            }
            $(this.conclusions[0].values[i].getHTML()).removeClass("error");

            if(!rowHasConclusions) {
                for(j = 0; j < this.conclusions.length; j+=1) {
                    if(this.conclusions[j].values[i].filledValue()) {
                        rowHasConclusions = true;
                        break;
                    }
                }
            }
            if(rowHasConclusions && !rowHasConditions) {
                defaultRulesets += 1;
                if(defaultRulesets > 1) {
                    $(this.dom.conditionsTable).find('tr').eq(i).addClass('error');
                    return {
                        valid: false,
                        message: translate('LBL_PMSE_BUSINESSRULES_ERROR_EMPTYROW', DecisionTable.LANG_MODULE),
                        location: 'row # ' + (i + 1)
                    };
                }
            }
            $(this.dom.conditionsTable).find('tr').eq(i).removeClass('error');
        }

        return {valid: true};
    };

    DecisionTable.prototype.validateColumn = function(index, type) {
        var valid, i, j, variables = [
            {
                type: "condition",
                collection: this.conditions
            }, {
                type: "conclusion",
                collection: this.conclusions
            }
        ];

        $(this.dom.conditionsTable).find('tr').removeClass('error');

        if(typeof index === 'number' && typeof type === 'number') {
            valid = variables[type].collection[index].isValid();
            if(!valid.valid) {
                return {
                    valid: false,
                    message: valid.message,
                    location: variables[type].type + " # " + (index + 1) + (!isNaN(valid.index) ? " - row " + (valid.index + 1) : "")
                };
            }
        } else {
            for(j = 0; j < variables.length; j+=1) {
                for(i = 0; i < variables[j].collection.length; i+=1) {
                    valid = variables[j].collection[i].isValid();
                    if(!valid.valid) {
                        return {
                            valid: false,
                            message: valid.message,
                            location: variables[j].type + " # " + (i + 1) + (!isNaN(valid.index) ? " - row " + (valid.index + 1) : "")
                        };
                    }
                }
            }
        }

        return {valid: true};
    };

    DecisionTable.prototype.isValid = function() {
        var valid;

        if(!this.correctlyBuilt) {
            return {
                valid: false,
                message: translate('LBL_PMSE_BUSINESSRULES_ERROR_INCORRECT_BUILD', DecisionTable.LANG_MODULE)
            };
        }

        valid = this.validateColumn();

        if(!valid.valid) {
            return valid;
        }
        valid = this.validateRow();
        if(!valid.valid) {
            return valid;
        }

        return this.validateConclusions();
    };

    DecisionTable.prototype.getJSON = function() {
        var json = {
            base_module: this.base_module,
            type: this.hitType,
            columns: {
                conditions: [],
                conclusions: []
            },
            ruleset: []
        }, ruleset, conditions, conclusions, i, j, obj, defaultRuleSets = 0, auxKey;

        if(!this.isValid().valid) {
            return null;
        }

        //Add the conditions columns evaluating duplications
        obj = {};
        for(j = 0; j < this.decisionRows; j+=1) {
            for(i = 0; i < this.conditions.length; i+=1) {
                if(this.conditions[i].field && this.conditions[i].values[j].getValue().length) {
                    auxKey = this.conditions[i].module + this.moduleFieldSeparator + this.conditions[i].field;
                    if(!obj[auxKey]) {
                        obj[auxKey] = {
                            max: 0,
                            current: 0
                        };
                    }
                    obj[auxKey].current += 1;
                    if(obj[auxKey].current > obj[auxKey].max) {
                        obj[auxKey].max = obj[auxKey].current;
                    }
                }
            }
            for(i in obj) {
                obj[i].current = 0;
            }
        }
        for(i = 0; i < this.conditions.length; i+=1) {
            auxKey = this.conditions[i].module + this.moduleFieldSeparator + this.conditions[i].field;
            if(obj[auxKey]) {
                for(j = 0; j < obj[auxKey].max; j+=1) {
                    json.columns.conditions.push({
                        module: this.conditions[i].module,
                        field: this.conditions[i].field
                    });
                }
                delete obj[auxKey];
            }
        }


        for(i = 0; i < this.conclusions.length; i+=1) {
            if(this.conclusions[i].isReturnType || (this.conclusions[i].field && this.conclusions[i].getFilledValuesNum())) {
                json.columns.conclusions.push(this.conclusions[i].select ? this.conclusions[i].field : "");
            }
        }

        for(i = 0; i < this.decisionRows; i+=1) {
            ruleset = {
                id: i + 1
            };
            conditions = [];
            conclusions = [];
            for(j = 0; j < this.conditions.length; j+=1) {
                obj = this.conditions[j].getJSON(i);
                if(obj) {
                    conditions.push(obj);
                }
            }
            for(j = 0; j < this.conclusions.length; j+=1) {
                obj = this.conclusions[j].getJSON(i);
                if(obj.value.length) {
                    conclusions.push(obj);
                }
            }
            ruleset.conditions = conditions;
            ruleset.conclusions = conclusions;
            if(!conditions.length) {
                defaultRuleSets += 1;
            }
            if(conditions.length || defaultRuleSets <= 1) {
                json.ruleset.push(ruleset);
            }
        }

        return json;
    };

//DecisionTableVariable
    var DecisionTableVariable = function(options) {
        Element.call(this);

        this.parent = null;

        this.fieldName = null;
        this.field = null;
        this.fieldType = null;
        this.module = null;
        this.fieldValid = true;

        this.values = [];
        this.fields = null;
        this.combos = {};
        this.inputFields = null;

        this.variableMode = null;
        this.isReturnType = null;
        this.closeButton = null;

        this.select = null;

        this.onBeforeValueOpenPanel = null;
        this.onRemove = null;
        this.onChange = null;
        this.onChangeValue = null;

        DecisionTableVariable.prototype.initObject.call(this, options);
    };

    DecisionTableVariable.prototype = new Element();

    DecisionTableVariable.prototype.initObject = function(options) {
        var defaults = {
            parent: null,

            field: null,

            fields: [],
            combos: {},
            inputFields: [],

            variableMode: "condition",
            isReturnType: false,

            onBeforeValueOpenPanel: null,
            onRemove: null,
            onChange: null,
            onChangeValue: null
        };

        // Do not deep copy here
        $.extend(defaults, options);

        this.parent = defaults.parent;
        this.variableMode = defaults.variableMode;
        this.isReturnType = defaults.isReturnType;

        this.setFields(defaults.fields)
            .setCombos(defaults.combos)
            .setInputFields(defaults.inputFields)
            .setField(defaults.field);

        if (defaults.values) {
            this.setValues(defaults.values);
        }

        this.onBeforeValueOpenPanel = defaults.onBeforeValueOpenPanel;
        this.onRemove = defaults.onRemove;
        this.onChange = defaults.onChange;
        this.onChangeValue = defaults.onChangeValue;
    };

    /**
     * Utility function to retrieve the option tag with a certain value
     * @param {string} the value for the option
     * @return {jQuery elements} matched elements
     */
    DecisionTableVariable.prototype.getOption = function (value) {
        return $(this.select).children("option[value='" + value + "']");
    }

    DecisionTableVariable.prototype.setField = function (newField) {
        var i,
            label,
            option,
            currentField,
            field,
            module,
            moduleFieldConcat;
        if (!this.isReturnType) {
            if (!this.fieldValid) {
                moduleFieldConcat = this.parent.getModuleFieldConcat(this.module, this.field);
                option = this.getOption(moduleFieldConcat);
                if (option.length) {
                    this.select.removeChild(option[0]);
                }
            }
            if (newField) {
                if (typeof newField === 'string') {
                    moduleFieldConcat = newField;
                    field = newField.split(this.parent.moduleFieldSeparator);
                    module = field[0];
                    field = field[1];
                } else {
                    module = newField.module;
                    field = newField.field;
                    moduleFieldConcat = this.parent.getModuleFieldConcat(module, field);
                }
                label = module + ':' + field;
            } else {
                module = '';
                field = '';
                moduleFieldConcat = this.parent.getModuleFieldConcat(module, field);
                label = '';
            }
            this.field = field;
            this.fieldName = null;
            this.fieldType = null;
            this.module = module;
            this.fieldValid = false;
            for (i = 0; i < this.fields.length; i += 1) {
                currentField = this.fields[i];
                if (currentField.value === field && currentField.moduleValue === module) {
                    this.fieldName = currentField.label;
                    this.fieldType = currentField.type;
                    this.fieldValid = true;
                    break;
                }
            }
            if (this.fieldValid) {
                $(this.select).removeClass('field-invalid');
            } else {
                if (this.field == '' && this.module == '') {
                    $(this.select).removeClass('field-invalid');
                } else {
                    $(this.select).addClass('field-invalid');
                }
                option = this.getOption(moduleFieldConcat);
                if (!option.length) {
                    option = this.createHTMLElement('option');
                    option.label = label;
                    option.value = moduleFieldConcat;
                    this.select.insertBefore(option, this.select.firstChild);
                }
            }
            this.select.value = moduleFieldConcat;
            this.parent.validateFields(false);
        }
        return this;
    };

    DecisionTableVariable.prototype.setFields = function(fields) {
        if(fields.push && fields.pop) {
            this.fields = fields;
            if (!this.isReturnType) {
                this.populateSelectElement();
            }
        }
        return this;
    };

    DecisionTableVariable.prototype.setCombos = function (combos) {
        this.combos = combos;
        return this;
    };

    DecisionTableVariable.prototype.setInputFields = function(fields) {
        if(fields.push && fields.pop) {
            this.inputFields = fields;
        }
        return this;
    };

    DecisionTableVariable.prototype.populateSelectElement = function() {
        var i,
            currentGroup,
            optgroup,
            option,
            select,
            label;

        if (this.select) {
            $(this.select).empty();
        }

        select = this.createHTMLElement('select');

        if (this.fields.length) {
            currentGroup = {};
            for(i = 0; i < this.fields.length; i += 1) {
                if (this.variableMode === 'conclusion' && !this.isReturnType && this.fields[i].value === 'email1') {
                    continue;
                }
                if (this.fields[i].moduleText !== currentGroup.label) {
                    if (this.variableMode === 'conclusion' && this.fields[i].moduleValue !== this.parent.base_module) {
                        break;
                    }
                    currentGroup = document.createElement("optgroup");
                    currentGroup.label = this.fields[i].moduleText;
                    select.appendChild(currentGroup);
                }
                option = this.createHTMLElement('option');
                label = SUGAR.App.lang.get(this.fields[i].label, this.base_module);
                if (typeof label === 'object'){
                    label = this.fields[i].label;
                }
                option.label = label;
                option.value = this.fields[i].moduleValue + this.parent.moduleFieldSeparator + this.fields[i].value;
                option.appendChild(document.createTextNode(label));
                if(this.field === option.value) {
                    option.selected = true;
                }
                currentGroup.appendChild(option);
            }
        }
        this.select = select;

        return this;
    };

    DecisionTableVariable.prototype.setValues = function(values) {
        var i;

        if (typeof values !== "object" || !values.push) {
            return this;
        }

        i = 0;
        if(this.variableMode === 'conclusion') {
            for(i = 0; i < values.length; i += 1) {
                if (typeof values[i] === "string" || typeof values[i] === 'number') {
                    this.values.push(new DecisionTableSingleValue({
                        value: values[i],
                        parent: this,
                        fields: this.inputFields
                    }));
                }
            }
        } else {
            for(i = 0; i < values.length; i += 1) {
                this.values.push(new DecisionTableValueEvaluation({
                    value: values[i].value,
                    operator: values[i].operator,
                    parent: this,
                    fields: this.inputFields
                }));
            }
        }
        return this;
    };

    //DecisionTableVariable.prototype.setName = function(name) {
    //    this.name = name;
    //    return this;
    //};



    DecisionTableVariable.prototype.getValueHTML = function(index) {
        if(this.values[index]) {
            return this.values[index].getHTML();
        }

        return null;
    };

    DecisionTableVariable.prototype.createHTML = function() {
        var html = this.createHTMLElement('th'),
            content,
            closeButton;

        if(this.html) {
            return this.html;
        }

        if(this.isReturnType) {
            content = this.createHTMLElement('span');
            content.className = 'decision-table-return';
            content.appendChild(document.createTextNode(
                this.isReturnType ? translate('LBL_PMSE_LABEL_RETURN', DecisionTable.LANG_MODULE) : (this.fieldName || "")
            ));
        } else {
            content = this.select;
        }

        html.appendChild(content);

        if(!this.isReturnType) {
            closeButton = this.createHTMLElement("button");
            closeButton.appendChild(document.createTextNode(" "));
            closeButton.className = 'decision-table-close-button';
            closeButton.title = translate('LBL_PMSE_TOOLTIP_REMOVE_COLUMN','pmse_Business_Rules');
            this.closeButton = closeButton;
            html.appendChild(this.closeButton);
        }

        this.html = html;

        this.attachListeners();

        return this.html;
    };

    DecisionTableVariable.prototype.removeWithoutConfirmation = function () {
        while(this.values.length) {
            this.values[0].remove();
        }
        this.values = null;
        $(this.html).remove();
        if(typeof this.onRemove === 'function') {
            this.onRemove.call(this);
        }
    };


    DecisionTableVariable.prototype.remove = function(force) {
        var self = this;
        if (force) {
            this.removeWithoutConfirmation();
            return this;
        }
        if(!this.parent.canBeRemoved(this)) {
            return this;
        }
        if(this.getFilledValuesNum()) {
            App.alert.show('variable-check', {
                level: 'confirmation',
                messages: translate('LBL_PMSE_MESSAGE_LABEL_REMOVE_VARIABLE','pmse_Business_Rules'),
                onCancel: function() {
                    return;
                },
                onConfirm: function () {
                    self.removeWithoutConfirmation();
                }
            });
        } else {
            this.removeWithoutConfirmation();
        }
        return this;
    };

    DecisionTableVariable.prototype.attachListeners = function() {
        var self = this,
            oldField,
            newField;

        if(!this.html) {
            return this;
        }

        $(this.select).on('change', function(){
            oldField = self.module + self.parent.moduleFieldSeparator  + self.field;
            newField = this.value;

            if (self.hasValues(true)) {
                App.alert.show('select-change-confirm', {
                    level: 'confirmation',
                    messages: translate('LBL_PMSE_MESSAGE_LABEL_CHANGE_COLUMN_TYPE','pmse_Business_Rules'),
                    autoClose: false,
                    onConfirm: function () {
                        self.setField(newField || null);
                        self.clearAllValues();

                        if (typeof self.onChange === 'function') {
                            self.onChange.call(self, self.field, oldField);
                        }
                    },
                    onCancel: function () {
                        self.select.value  = oldField;
                    }
                });
            } else {
                self.setField(this.value || null);
                self.clearAllValues();

                if (typeof self.onChange === 'function') {
                    self.onChange.call(self, self.field, oldField);
                }
            }
        });

        $(this.closeButton).on("click", function() {
            self.remove();
        });

        return this;
    };

    DecisionTableVariable.prototype.clearAllValues = function () {
        var i;
        for (i = 0; i < this.values.length; i += 1) {
            this.values[i].clear();
        }
        return this;
    };

    DecisionTableVariable.prototype.hasValues = function (partiallyFilled) {
        return (this.getFilledValuesNum(partiallyFilled) !== 0);
    };

    DecisionTableVariable.prototype.getFilledValuesNum = function(partiallyFilled) {
        var i,
            n = 0,
            current;
        if (partiallyFilled) {
            for(i = 0; i < this.values.length; i+=1) {
                current = this.values[i];
                if (current.isPartiallyFilled) {
                    if(current.isPartiallyFilled()) {
                        n +=1;
                    }
                } else if (current.filledValue()) {
                    n +=1;
                }
            }
        } else {
            for(i = 0; i < this.values.length; i+=1) {
                if(this.values[i].filledValue()) {
                    n +=1;
                }
            }
        }
        return n;
    };

    DecisionTableVariable.prototype.onBeforeValueOpenPanelHandler = function () {
        var that = this;
        return function (decisionTableValue) {
            if (typeof that.onBeforeValueOpenPanel === 'function') {
                that.onBeforeValueOpenPanel(that, decisionTableValue);
            }
        };
    };

    DecisionTableVariable.prototype.onRemoveValueHandler = function() {
        var that = this;
        return function () {
            var i;
            for(i = 0; i < that.values.length; i+=1) {
                if(that.values[i] === this) {
                    that.values.splice(i, 1);
                    return;
                }
            }
        };
    };

    DecisionTableVariable.prototype.onChangeValueHandler = function() {
        var that = this;
        return function(newVal, oldVal) {
            if(typeof that.onChangeValue === 'function') {
                that.onChangeValue.call(that, this, newVal, oldVal);
            }
        };
    };

    DecisionTableVariable.prototype.addValue = function(value, operator) {
        var value;
        if(this.variableMode === 'conclusion') {
            value = new DecisionTableSingleValue({value: value, parent: this, fields: this.inputFields});
        } else {
            value = new DecisionTableValueEvaluation({value: value, operator: operator, parent: this, fields: this.inputFields});
        }
        value.onBeforeOpenPanel = this.onBeforeValueOpenPanelHandler();
        value.onRemove = this.onRemoveValueHandler();
        value.onChange = this.onChangeValueHandler();
        this.values.push(value);

        return this;
    };

    DecisionTableVariable.prototype.getJSON = function(index) {
        var json = {};
        if(typeof index === 'number') {
            if(this.values[index]) {

                json.value = this.values[index].getValue();

                if(this.variableMode === 'conclusion') {
                    json.conclusion_value = (this.isReturnType ? 'result' : this.field);
                    json.conclusion_type = this.isReturnType ? 'return' : 'variable'; //"expression" type also must be set
                } else {
                    json.variable_name = this.field;
                    json.condition = this.values[index].operator;
                    if(!(!json.value || json.condition) || (!json.value && !json.condition) /*|| (json.value.push && !json.value.length)*/)  {
                        return false;
                    }
                }

                if (!this.isReturnType) {
                    json.variable_module = this.module;
                }

                return json;
            }
        } else {
            return false;
        }
    };

    DecisionTableVariable.prototype.removeValue = function(index) {
        if(this.values[index]) {
            $(this.values[index].getHTML()).remove();
            this.values.splice(index, 1);
        }

        return this;
    };

    DecisionTableVariable.prototype.isValid = function() {
        var valid = {
            valid: true
        }, i, values = 0, validation;
        $(this.select).parent().removeClass("error");
        if(this.variableMode === 'conclusion') {
            for(i = 0; i < this.values.length; i+=1) {
                validation = this.values[i].isValid();
                if(!validation.valid) {
                    return validation;
                }
                if(this.values[i].value.length) {
                    values +=1;
                }
            }
        } else {
            for(i = 0; i < this.values.length; i+=1) {
                validation = this.values[i].isValid();
                if(this.values[i].operator) {
                    values +=1;
                }
                if(!validation.valid) {
                    valid.valid = false;
                    valid.message = validation.message;
                    valid.index = i;
                    return valid;
                }
            }
        }

        if(values && (this.select && !this.select.value)) {
            $(this.select.parentElement).addClass("error");
            valid = {
                valid: false,
                message: translate('LBL_PMSE_MESSAGE_LABEL_DEFINE_COLUMN_TYPE', DecisionTable.LANG_MODULE)
            };
        }

        return valid;
    };

//Value Cells for DecisionTable
//DecisionTableValue
    var DecisionTableValue = function(settings) {
        Element.call(this, settings);
        this.value = null;
        this.expression = null;
        this.onBeforeOpenPanel = null;
        this.onRemove = null;
        this.onChange = null;
        this.parent = null;
        DecisionTableValue.prototype.initObject.call(this, settings);
    };

    DecisionTableValue.prototype = new Element();

    DecisionTableValue.prototype.initObject = function(settings) {
        var defaults = {
            value: [],
            onBeforeOpenPanel: null,
            onRemove: null,
            onChange: null,
            parent: null,
            fields: []
        };

        // Do not deep copy here
        $.extend(defaults, settings || {});

        this.parent = defaults.parent;
        this.expression = new ExpressionContainer({
            variables: defaults.fields,
            onBeforeOpenPanel: this.onBeforeOpenPanelHandler(),
            onChange: this.onChangeExpressionHandler()
        }, this);
        this.setValue(defaults.value);
        this.onBeforeOpenPanel = defaults.onBeforeOpenPanel;
        this.onRemove = defaults.onRemove;
        this.onChange = defaults.onChange;
    };

    DecisionTableValue.prototype.onBeforeOpenPanelHandler = function () {
        var that = this;
        return function (expressionContainer) {
            if (typeof that.onBeforeOpenPanel === 'function') {
                that.onBeforeOpenPanel(that);
            }
        };
    };

    DecisionTableValue.prototype.onChangeExpressionHandler = function() {
        var that = this;
        return function(newVal, oldVal) {
            that.value = this.getObject();
            if(typeof that.onChange === 'function') {
                that.onChange.call(that, newVal, oldVal);
            }
        };
    };

    DecisionTableValue.prototype.updateHTML = function() {};

    DecisionTableValue.prototype.clear = function () {
        this.setValue([]);
        return this;
    };

    DecisionTableValue.prototype.setValue = function(value) {
        var i;

        this.expression.setExpressionValue(value);
        this.value = value;

        return this;
    };

    DecisionTableValue.prototype.createHTML = function() {};

    DecisionTableValue.prototype.onEnterCellHandler = function(controlCreationFunction) {
        var that = this;
        return function() {
            if(typeof controlCreationFunction !== 'function') {
                return;
            }
            var control = controlCreationFunction();
            if (control) {
                $(this.parentElement).empty().append(control);
                $(control).select().focus();
            }
        };
    };

    DecisionTableValue.prototype.onLeaveCellHandler = function(member) {
        var that = this;
        return function() {
            var span = document.createElement('span'),
                cell = this.parentElement, oldValue = that[member], changed = false,
                text;
            span.tabIndex = 0;
            changed = oldValue !== this.value;
            that[member] = this.value;
            if(that[member]) {
                text = $(this).find('option:selected').attr('label');
                span.appendChild(document.createTextNode(text));
            } else {
                span.innerHTML = '&nbsp;';
            }
            try {
                $(cell).empty().append(span);
                if (text && $(span).innerWidth() < span.scrollWidth) {
                    span.setAttribute("title", text);
                } else {
                    span.removeAttribute("title");
                }
            } catch(e){}
            that.isValid();
            if(changed && typeof that.onChange === 'function') {
                that.onChange.call(that, that[member], oldValue);
            }
        };
    };

    DecisionTableValue.prototype.isValid = function() {
        if(this.expression.isValid()) {
            $(this.html).removeClass('error');
            return {
                valid: true
            };
        } else {
            $(this.html).addClass('error');
            return {
                valid: false,
                message: translate('LBL_PMSE_BUSINESSRULES_ERROR_INVALIDEXPRESSION', DecisionTable.LANG_MODULE)
            }
        }
    };

    DecisionTableValue.prototype.attachListeners = function() {};

    DecisionTableValue.prototype.remove = function() {
        $(this.html).remove();
        this.expression.remove();
        if(typeof this.onRemove === 'function') {
            this.onRemove.call(this);
        }
    };

    DecisionTableValue.prototype.getValue = function() {
        return this.expression.getObject();
    };

    DecisionTableValue.prototype.filledValue = function() {
        return !!this.value.length;
    };

//DecisionTableSingleValue
    var DecisionTableSingleValue = function(settings) {
        DecisionTableValue.call(this, settings);
    };

    DecisionTableSingleValue.prototype = new DecisionTableValue();

    DecisionTableSingleValue.prototype.createValueControl = function() {
        var that = this;
        return function() {
            var input = document.createElement('input');
            input.type = 'text';
            input.value = that.value || "";
            return input;
        };
    };

    DecisionTableSingleValue.prototype.updateHTML = function() {
        if(this.html) {
            if(this.value) {
                $(this.html).find('span').text(this.value);
            } else {
                $(this.html).find('span').html('&nbsp;');
            }
            $(this.html).find('input').val(this.value);
        }
        return this;
    };

    DecisionTableSingleValue.prototype.createHTML = function() {
        if(this.html) {
            return this.html;
        }

        var cell;

        cell = this.createHTMLElement('td');

        //span.tabIndex = 0; //<----remove
        cell.appendChild(this.expression.getHTML());

        this.html = cell;

        //this.attachListeners();

        return cell;
    };

//DecisionTableValueEvaluation
    var DecisionTableValueEvaluation = function(settings) {
        DecisionTableValue.call(this, settings);
        this.operator = null;
        DecisionTableValueEvaluation.prototype.initObject.call(this, settings);
    };

    DecisionTableValueEvaluation.prototype = new DecisionTableValue();

    DecisionTableValueEvaluation.prototype.initOperators = function (module) {
        DecisionTableValueEvaluation.prototype.OPERATORS = [
            {
                label: '==',
                value: '==',
            },
            {
                label: '!=',
                value: '!=',
            },
            {
                label: '>=',
                value: '>=',
            },
            {
                label: '<=',
                value: '<=',
            },
            {
                label: '>',
                value: '>',
            },
            {
                label: '<',
                value: '<',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_EQUAL_TEXT', module),
                value: 'equals',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_NOT_EQUAL_TEXT', module),
                value: 'not_equals',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_STARTS_TEXT', module),
                value: 'starts_with',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_ENDS_TEXT', module),
                value: 'ends_with',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_CONTAINS_TEXT', module),
                value: 'contains',
            },
            {
                label: App.lang.get('LBL_PMSE_EXPCONTROL_OPERATOR_NOT_CONTAINS_TEXT', module),
                value: 'does_not_contain',
            }
        ];
    };

    DecisionTableValueEvaluation.prototype.initObject = function(settings) {
        DecisionTableValueEvaluation.prototype.initOperators('pmse_Project');
        this.setOperator(settings.operator || "");
    };

    DecisionTableValueEvaluation.prototype.clear = function () {
        DecisionTableValue.prototype.clear.call(this);
        this.setOperator("");
        return this;
    };

    DecisionTableValueEvaluation.prototype.findOperatorLabel = function(operator) {
        var i;
        for (i = 0; i < this.OPERATORS.length; i++) {
            if (operator == this.OPERATORS[i].value) {
                operator = this.OPERATORS[i].label;
                break;
            }
        }
        return operator;
    };

    DecisionTableValueEvaluation.prototype.setOperator = function(operator) {
        var $span;
        this.operator = operator;
        if (this.html && this.html[0]) {
            $span = jQuery(this.html[0]).find('span').empty();
            if (operator) {
                $span.append(this.findOperatorLabel(operator));
            } else {
                $span.html("&nbsp;");
            }
        }
        return this;
    };

    DecisionTableValueEvaluation.prototype.createHTML = function () {
        if(this.html) {
            return this.html;
        }

        var valueCell, operatorCell, span;
        valueCell = DecisionTableSingleValue.prototype.createHTML.call(this);

        operatorCell = this.createHTMLElement("td");
        operatorCell.className = 'decision-table-operator';
        span = this.createHTMLElement("span");
        span.tabIndex = 0;
        if(this.operator) {
            span.appendChild(document.createTextNode(this.findOperatorLabel(this.operator)));
        } else {
            span.innerHTML = '&nbsp';
        }
        operatorCell.appendChild(span);

        this.html = [operatorCell, valueCell];

        this.attachListeners();

        return this.html;
    };

    DecisionTableValueEvaluation.prototype.fillOperators = function(select, type) {
        var i, option, enabledOperators;

        switch (type.toLowerCase()) {
            case 'date':
            case 'datetime':
            case 'decimal':
            case 'currency':
            case 'float':
            case 'integer':
                enabledOperators = this.OPERATORS.slice(0, 6);
                break;
            case 'textarea':
            case 'textfield':
            case 'email':
            case 'phone':
            case 'url':
            case 'name':
                enabledOperators = this.OPERATORS.slice(6);
                break;
            default:
                enabledOperators = this.OPERATORS.slice(0, 2);
        }

        for(i = 0; i < enabledOperators.length; i+=1) {
            option = this.createHTMLElement("option");

            option.label = enabledOperators[i].label;
            option.value = enabledOperators[i].value;
            option.selected = enabledOperators[i].value === this.operator;
            select.appendChild(option);
        }

        return select;
    };

    DecisionTableValueEvaluation.prototype.createValueControl = function() {
        var that = this;
        return function() {
            var input = document.createElement('input');
            input.type = 'text';
            input.value = that.value || "";
            return input;
        };
    };

    DecisionTableValueEvaluation.prototype.createOperatorControl = function() {
        var that = this;
        return function() {
            var select = document.createElement('select'), parent = that.parent, type = parent.fieldType;
            if (typeof type !== 'string') {
                App.alert.show(null, {
                    level: 'warning',
                    messages: translate('LBL_PMSE_MESSAGE_LABEL_DEFINE_COLUMN_TYPE', DecisionTable.LANG_MODULE),
                    autoClose: true
                });
                return null;
            } else {
                that.fillOperators(select, type);
                select.value = that.operator;
                return select;
            }
        };
    };

    DecisionTableValueEvaluation.prototype.attachListeners = function() {
        if(!this.html || !this.html.push) {
            return this;
        }

        $(this.html[0]).on('focus', 'span', this.onEnterCellHandler(this.createOperatorControl()))
            .on('blur', 'select', this.onLeaveCellHandler('operator'));

        return this;
    };

    DecisionTableValueEvaluation.prototype.filledValue = function() {
        return !!this.operator && DecisionTableValue.prototype.filledValue.call(this);
    };

    DecisionTableValueEvaluation.prototype.isPartiallyFilled = function() {
        return !!this.operator || DecisionTableValue.prototype.filledValue.call(this);
    };

    DecisionTableValueEvaluation.prototype.isValid = function() {
        var res = DecisionTableValue.prototype.isValid.call(this);

        if(!res.valid) {
            $(this.html[0]).removeClass('error');
        } else {
            res = {
                valid: (!!this.value.length === !!this.operator)
            };
            if(!res.valid) {
                $(this.html).addClass('error');
                res.message = translate('LBL_PMSE_MESSAGE_LABEL_MISSING_EXPRESSION_OR_OPERATOR', DecisionTable.LANG_MODULE);
            } else {
                $(this.html).removeClass('error');
            }
        }

        return res;
    };

    DecisionTableValueEvaluation.prototype.getOperator = function() {
        return this.operator;
    };
