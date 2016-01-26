/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
/*global FieldOption, Field, Element, OptionTextField, $, document, OptionSelectField,
 getRelativePosition, OptionCheckBoxField, OptionDateField, replaceExpression, editorWindow,
 translate, MultipleItemPanel, PROJECT_MODULE, CriteriaField, PMSE_DECIMAL_SEPARATOR, TextAreaUpdaterItem, OptionNumberField
 */

/**
 * @class UpdaterField
 * Creates an object that can in order to illustrate a group of fields,
 * checkboxes or select items in the HTML it can be inside a form
 *
 *             //i.e.
 *             var updater_field = new UpdaterField({
 *                 //message that the label will display
 *                  label: "This is a label",
 *                  //name that the field has managed
 *                  name: 'the_name',
 *                  //if the field will be submited
 *                  submit: true,
 *                  //proxy to drive the all options sended from to server
 *                  proxy: proxy
 *                  //width of the field object not the text
 *                  fieldWidth: 470,
 *                  //height of the field object not the text
 *                  fieldHeight: 260
 *              });
 *
 * @extends Field
 *
 * @param {Object} options configuration options for the field object
 * @param {Object} parent
 * @constructor
 */
var UpdaterField = function (options, parent) {
    Field.call(this, options, parent);
    this.fields = [];
    this.options = [];
    this.fieldHeight = null;
    this.visualObject = null;
    this.language = {};
    this._variables = [];
    this._datePanel = null;
    this._variablesList = null;
    this._attachedListeners = false;
    this._decimalSeparator = null;
    this._numberGroupingSeparator = null;
    UpdaterField.prototype.initObject.call(this, options);
};

UpdaterField.prototype = new Field();

/**
 * Type of all updater field instances
 * @property {String}
 */
UpdaterField.prototype.type = 'UpdaterField';

/**
 * Initializer of the object will all the given configuration options
 * @param {Object} options
 */
UpdaterField.prototype.initObject = function (options) {
    var defaults = {
        fields: [],
        fieldHeight: null,
        language: {
            LBL_ERROR_ON_FIELDS: 'Please, correct the fields with errors'
        },
        hasCheckbox : false,
        decimalSeparator: ".",
        numberGroupingSeparator: ","
    };
    $.extend(true, defaults, options);
    this.language = defaults.language;
    this.setFields(defaults.fields);
    this.hasCheckbox = defaults.hasCheckbox;
    this._decimalSeparator = defaults.decimalSeparator;
    this._numberGroupingSeparator = defaults.numberGroupingSeparator;
};

/**
 * Sets all option fiels into updater field container
 * @param {Array} items
 * @chainable
 */
UpdaterField.prototype.setFields = function (items) {
    var i, aItems = [], newItem;
    for (i = 0; i < items.length; i += 1) {
        if (items[i].type === 'FieldUpdater') {
            items[i].setParent(this);
            aItems.push(items[i]);
        } else {
            aItems.push(newItem);
        }
    }
    this.fields = aItems;
    return this;
};

/**
 * Gets an object with all option fields values (label, name, type and values), to send the server
 * @return {Object}
 */
UpdaterField.prototype.getObjectValue = function () {
    var f, auxValue = [];

    for (f = 0; f < this.options.length; f += 1) {
        if (!this.options[f].isDisabled()) {
            auxValue.push(this.options[f].getData());
        }
    }
    this.value = JSON.stringify(auxValue);
    return Field.prototype.getObjectValue.call(this);
};

UpdaterField.prototype._parseSettings = function (settings) {
    var map = {
        value: "name",
        text: "label",
        type: "fieldType",
        optionItem: "options",
        required: "required"
    }, parsedSettings = {}, key;
    for (key in settings) {
        if (settings.hasOwnProperty(key) && map[key]) {
            parsedSettings[map[key]] = settings[key];
        }
    }
    return parsedSettings;
};

/**
 * Sets child option fiels into updater container
 * @param {Array} settings
 * @chainable
 */
UpdaterField.prototype.setOptions = function (settings) {
    var i,
        options = [],
        newOption,
        aUsers = [],
        customUsers = {},
        currentSetting,
        aux;

    this.list = settings;
    for (i = 0; i < settings.length; i += 1) {
        /*CREATE INPUT FIELD*/
        currentSetting = this._parseSettings(settings[i]);
        currentSetting.parent = this;
        currentSetting.allowDisabling = this.hasCheckbox;
        currentSetting.disabled = this.hasCheckbox;
        switch (currentSetting.fieldType) {
            case 'TextField':
                newOption =  new TextUpdaterItem(currentSetting);
                break;
            case 'TextArea':
                newOption =  new TextAreaUpdaterItem(currentSetting);
                break;
            case 'Date':
            case 'Datetime':
                newOption =  new DateUpdaterItem(currentSetting);
                break;
            case 'DropDown':
                aUsers = [];
                if (currentSetting.options instanceof Array) {
                    if (currentSetting.value === 'assigned_user_id') {
                        aUsers = [
                            {'text': translate('LBL_PMSE_FORM_OPTION_CURRENT_USER'), 'value': 'currentuser'},
                            {'text': translate('LBL_PMSE_FORM_OPTION_RECORD_OWNER'), 'value': 'owner'},
                            {'text': translate('LBL_PMSE_FORM_OPTION_SUPERVISOR'), 'value': 'supervisor'}
                        ];
                        customUsers = aUsers.concat(currentSetting.options);
                        currentSetting.options = customUsers;
                    }
                } else {
                    if (currentSetting.options) {
                        $.each(currentSetting.options, function (key, value) {
                            aUsers.push({value: key, text: value});
                        });
                    }
                    currentSetting.options = aUsers;

                }
                newOption =  new DropdownUpdaterItem(currentSetting);
                break;
            case 'Checkbox':
                newOption =  new CheckboxUpdaterItem(currentSetting);
                break;
            case 'Currency':
                currentSetting.currency = true;
            case 'Integer':
            case 'Decimal':
            case 'Float':
                newOption =  new NumberUpdaterItem(currentSetting);
                break;
            case 'user':
                currentSetting.searchUrl = PMSE_USER_SEARCH.url;
                currentSetting.searchLabel = PMSE_USER_SEARCH.text;
                currentSetting.defaultSearchOptions = [
                    {text: translate('LBL_PMSE_FORM_OPTION_CURRENT_USER'), value: 'currentuser'},
                    {text: translate('LBL_PMSE_FORM_OPTION_RECORD_OWNER'), value: 'owner'},
                    {text: translate('LBL_PMSE_FORM_OPTION_SUPERVISOR'), value: 'supervisor'}
                ];
                newOption =  new SearchUpdaterItem(currentSetting);
                break;
            case 'team_list':
                currentSetting.disabledAppendOption = !this.hasCheckbox;
                aux = this.parent.getField('act_field_module').getSelectedData();
                var moduleData = App.metadata.getModule(aux.module_name);
                currentSetting.disableTeamSelection = !_.isUndefined(moduleData) && !_.isUndefined(moduleData.isTBAEnabled)
                    ? !moduleData.isTBAEnabled
                    : true;
                newOption = new TeamUpdaterItem(currentSetting);
                break;
            default:
                newOption =  new TextUpdaterItem(currentSetting);
                break;
        }

        options.push(newOption);
    }
    this.options = options;
    this.setOptionsHTML();
    return this;
};

/**
 * Sets html content for each type of option field
 * @chainable
 */
UpdaterField.prototype.setOptionsHTML = function () {
    var i, insert;
    if (this.html) {
        this.visualObject.innerHTML = '';
        for (i = 0; i < this.options.length; i += 1) {
            insert = this.options[i].getHTML();
            if (i % 2 === 0) {
                insert.className += ' updater-inverse';
            }
            this.visualObject.appendChild(insert);
        }
    }
    return this;
};

UpdaterField.prototype.closePanels = function () {
    if (this._datePanel) {
        this._datePanel.close();
    }
    if (this._variablesList) {
        this._variablesList.close();
    }
    return this;
};

UpdaterField.prototype.attachListeners = function () {
    var that = this;
    if (this.html && !this._attachedListeners) {
        jQuery(this.visualObject).on('scroll', function () {
            jQuery(this.parent.body).trigger('scroll');
        });
        jQuery(this.parent.body).on('scroll', function () {
            that.closePanels();
        });
        this._attachedListeners = true;
    }
    return this;
};

/**
 * Creates the basic html node structure for the given object using its
 * previously defined properties
 * @return {HTMLElement}
 */
UpdaterField.prototype.createHTML = function () {
    var fieldLabel, required = '', criteriaContainer, insert, i, style;
    Field.prototype.createHTML.call(this);

    if (this.required) {
        required = '<i>*</i> ';
    }

    fieldLabel = this.createHTMLElement('span');
    fieldLabel.className = 'adam-form-label';
    fieldLabel.innerHTML = this.label + ': ' + required;
    fieldLabel.style.width = this.parent.labelWidth;
    fieldLabel.style.verticalAlign = 'top';
    this.html.appendChild(fieldLabel);

    criteriaContainer = this.createHTMLElement('div');
    criteriaContainer.className = 'adam-item-updater table';
    criteriaContainer.id = this.id;

    if (this.fieldWidth || this.fieldHeight) {
        style = document.createAttribute('style');
        if (this.fieldWidth) {
            style.value += 'width: ' + this.fieldWidth + 'px; ';
        }
        if (this.fieldHeight) {
            style.value += 'height: ' + this.fieldHeight + 'px; ';
        }
        style.value += 'display: inline-block; margin: 0; overflow: auto; padding: 3px;';
        criteriaContainer.setAttributeNode(style);
    }

    for (i = 0; i < this.options.length; i += 1) {
        insert = this.options[i].getHTML();
        if (i % 2 === 0) {
            insert.className = insert.className + ' updater-inverse';
        }
        criteriaContainer.appendChild(insert);
    }

    this.html.appendChild(criteriaContainer);

    if (this.errorTooltip) {
        this.html.appendChild(this.errorTooltip.getHTML());
    }
    if (this.helpTooltip) {
        this.html.appendChild(this.helpTooltip.getHTML());
    }

    this.visualObject = criteriaContainer;

    return this.html;
};

/**
 * Sets values of every option field into an updater Field container,
 * determining the option field type
 * @param {Array} value
 * @chainable
 */
UpdaterField.prototype.setValue = function (value) {
    this.value = value;
    if (this.options && this.options.length > 0) {
        try {
            var fields, i, j;
            fields = JSON.parse(value);
            if (fields && fields.length > 0) {
                for (i = 0; i < fields.length; i += 1) {
                    for (j = 0; j < this.options.length; j += 1) {
                        if (fields[i].field === this.options[j].getName()) {
                            this.options[j].enable();
                            this.options[j].setValue(fields[i].value, fields[i].label);
                            if (fields[i].type === 'team_list') {
                                this.options[j].setPrimaryTeam(fields[i].primary);
                                this.options[j].setAppendTeams(fields[i].append);
                                this.options[j].setSelectedTeams(fields[i].selected_teams);
                            }
                            break;
                        }
                    }
                }
            }
        } catch (e) {}
    }
    return this;
};

/**
 * Determines whether a field is valid checking if required
 * and the value corresponds to the type of data the shows an visual warning
 * @return {Boolean}
 */
UpdaterField.prototype.isValid = function () {
    var i, valid = true, current, field;
    for (i = 0; i < this.options.length; i += 1) {
        field = this.options[i];

        //TODO: create validation for expressions built with expressionControl.
        if (field._parent.hasCheckbox && field.isDisabled()) {
            valid = true;
        } else {
            valid = field.isValid();
        }

        if (!valid) {
            break;
        }
    }

    if (valid) {
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-on');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-off');
        valid = valid && Field.prototype.isValid.call(this);
    } else {
        this.visualObject.scrollTop += getRelativePosition(field.getHTML(), this.visualObject).top;
        this.errorTooltip.setMessage(this.language.LBL_ERROR_ON_FIELDS);
        $(this.errorTooltip.html).removeClass('adam-tooltip-error-off');
        $(this.errorTooltip.html).addClass('adam-tooltip-error-on');
    }
    return valid;
};

UpdaterField.prototype._updateCurrencyFields = function (currency, ignore) {
    var i, j;
    var field;
    var value;
    var targetCurrency = App.metadata.getCurrencies()[currency];
    var originalCurrency;

    for (i = 0; i < this.options.length; i += 1) {
        field = this.options[i];
        if (field !== ignore && field instanceof NumberUpdaterItem && field.isCurrency()) {
            value = field.getValue();
            for (j = 0; j < value.length; j += 1) {
                if (value[j].expType === 'CONSTANT' && value[j].expSubtype === 'currency' && value[j].expField !== currency) {
                    originalCurrency = App.metadata.getCurrencies()[value[j].expField];
                    value[j].expValue = FormPanelCurrency.convertCurrency(value[j].expValue, parseFloat(originalCurrency.conversion_rate), parseFloat(targetCurrency.conversion_rate));
                    value[j].expField = currency;
                    value[j].expLabel = targetCurrency.symbol + "(" + targetCurrency.iso4217 + ") "
                        + FormPanelNumber.format(value[j].expValue, {
                            precision: 2,
                            groupingSeparator: this._numberGroupingSeparator,
                            decimalSeparator: this._decimalSeparator
                        });
                }
            }
            field.setValue(value);
        }
    }
    return this;
};

/**
 * Obtains and creates the variable string according to the format established
 * for handling variables in sugar
 * @param {String} module
 */
UpdaterField.prototype._onValueGenerationHandler = function (module) {
    var  that = this;
    return function () {
        var newExpression;
        var field = that.currentField;
        var control;
        var i, aux2;
        var currentValue = field.getValue()
        var panel;
        var list;
        var usedCurrency = null;
        var aux = true;

        control = field._control;
        if (this instanceof ExpressionControl) {
            panel = arguments[0];
            newExpression = panel.getValueObject();
        } else {
            panel = arguments[0];
            list = arguments[1];
            newExpression = "{::" + module + "::" + arguments[2].value  + "::}";
            i = control.selectionStart;
            i = i || 0;
            aux = currentValue.substr(0, i);
            aux2 = currentValue.substr(i);
            newExpression = aux + newExpression + aux2;
        }

        if (field instanceof NumberUpdaterItem && field.isCurrency()) {
            for (i = 0; i < newExpression.length; i += 1) {
                if (newExpression[i].expType === 'CONSTANT' && newExpression[i].expSubtype === 'currency') {
                    if (usedCurrency !== null && usedCurrency !== newExpression[i].expField) {
                        App.alert.show('br-save-error', {
                            level: 'error',
                            messages: translate('LBL_PMSE_MESSAGE_ERROR_CURRENCIES_MIX'),
                            autoClose: true
                        });
                        aux = false;
                        break;
                    }
                    usedCurrency = newExpression[i].expField;
                }
            }
            if (aux) {
                field.setValue(newExpression);
                if (usedCurrency !== null) {
                    that._updateCurrencyFields(usedCurrency, field);
                }
            } else {
                panel.setValue(field.getValue(), true);
            }
        } else {
            field.setValue(newExpression);
        }

        if (!(panel instanceof ExpressionControl)) {
            panel.close();
        }
    };
};

/**
 * Displays and create the control panel with filled with the possibilities
 * of the sugar variables, change the panel z-index to show correctly,
 * finally add a windows close event for close the control panel
 * @param {Object} field
 */
UpdaterField.prototype.openPanelOnItem = function (field) {
    var that = this, settings, inputPos, textSize, subjectInput, i,
        variablesDataSource = project.getMetadata("targetModuleFieldsDataSource"), currentFilters, list, targetPanel,
        currentOwner, fieldType = field.getFieldType(), constantPanelCfg;
    if (!(field instanceof DateUpdaterItem || field instanceof NumberUpdaterItem)) {
        if (!this._variablesList) {
            this._variablesList = new FieldPanel({
                className: "updateritem-panel",
                appendTo: (this.parent && this.parent.parent && this.parent.parent.html) || null,
                items: [
                    {
                        type: "list",
                        bodyHeight: 100,
                        collapsed: false,
                        itemsContent: "{{text}}",
                        fieldToFilter: "type",
                        title: translate('LBL_PMSE_UPDATERFIELD_VARIABLES_LIST_TITLE').replace(/%MODULE%/g, App.lang.getModuleName(PROJECT_MODULE))
                    }
                ],
                onItemValueAction: this._onValueGenerationHandler(PROJECT_MODULE),
                onOpen: function () {
                    jQuery(that.currentField.html).addClass("opened");
                },
                onClose: function () {
                    jQuery(that.currentField.html).removeClass("opened");
                }
            });
        }
        if (this._datePanel && this._datePanel.isOpen()) {
            this._datePanel.close();
        }
        targetPanel = this._variablesList;
        list = this._variablesList.getItems()[0];
        currentFilters = list.getFilter();
        //We check if the variables list has the same filter than the one we need right now,
        //if it do then we don't need to apply the data filtering for a new criteria
        if (fieldType === 'TextField' || fieldType === 'TextArea' || fieldType === 'Name') {
            if (list.getFilterMode() === 'inclusive') {
                list.setFilterMode('exclusive')
                    .setDataItems(this._variables, "type", ["Checkbox", "DropDown"]);
            }
        } else if (!(currentFilters.length === 1 && currentFilters.indexOf(field._fieldType) > 0)) {
            list.setFilterMode('inclusive')
                .setDataItems(this._variables, "type", field._fieldType);
        }
        this.currentField = field;
    } else {
        if (!this._datePanel) {
            this._datePanel = new ExpressionControl({
                className: "updateritem-panel",
                onChange: this._onValueGenerationHandler(PROJECT_MODULE),
                appendTo: (this.parent && this.parent.parent && this.parent.parent.html) || null,
                decimalSeparator: this._decimalSeparator,
                numberGroupingSeparator: this._numberGroupingSeparator,
                dateFormat: App.date.getUserDateFormat(),
                timeFormat: SUGAR.App.user.getPreference("timepref"),
                currencies: project.getMetadata("currencies"),
                onOpen: function () {
                    jQuery(that.currentField.html).addClass("opened");
                },
                onClose: function () {
                    jQuery(that.currentField.html).removeClass("opened");
                }
            });
        }
        //Check if the panel is already configured for the current field's type
        //in order to do it, we verify if the current field class is the same that the previous field's.
        if (!this.currentField || this.currentField !== field) {
            if (field instanceof DateUpdaterItem) {
                if (fieldType === 'Date') {
                    constantPanelCfg = {
                        date: true,
                        timespan: true
                    };
                } else {
                    constantPanelCfg = {
                        datetime: true,
                        timespan: true
                    };
                }
                this._datePanel.setOperators({
                    arithmetic: ["+", "-"]
                }).setConstantPanel(constantPanelCfg);
            } else {
                this._datePanel.setOperators({
                    arithmetic: true,
                    group: true
                });
                if (field.isCurrency()) {
                    this._datePanel.setConstantPanel({
                        currency: true,
                        basic: {
                            number: true
                        }
                    });
                } else {
                    this._datePanel.setConstantPanel({
                        basic: {
                            number: true
                        }
                    });
                }
            }
            this._datePanel.setVariablePanel({
                data: [{
                    name: App.lang.getModuleName(PROJECT_MODULE),
                    value: PROJECT_MODULE,
                    items: this._variables
                }],
                dataFormat: "hierarchical",
                typeField: "type",
                typeFilter: field instanceof DateUpdaterItem ? ["Date", "Datetime"] : field._fieldType,
                textField: "text",
                valueField: "value",
                dataChildRoot: "items",
                moduleTextField: "name",
                moduleValueField: "value"
            });
        }
        this.currentField = field;
        //We can't send an empty string since JSON can't parse it
        this._datePanel.setValue(field.getValue() || [], true);
        if (this._variablesList && this._variablesList.isOpen()) {
            this._variablesList.close();
        }
        targetPanel = this._datePanel;
    }

    subjectInput = field._control;
    currentOwner = targetPanel.getOwner();
    if (currentOwner !== subjectInput) {
        targetPanel.close();
        targetPanel.setOwner(subjectInput);
        targetPanel.open();
    } else {
        if (targetPanel.isOpen()) {
            targetPanel.close();
        } else {
            targetPanel.open();
        }
    }
    return this;
};
UpdaterField.prototype.setVariables = function (variables) {
    this._variables = variables;
    return this;
};

//UpdaterItem
var UpdaterItem = function (settings) {
    Element.call(this, settings);
    this._parent = null;
    this._name = null;
    this._label = null;
    this._required = null;
    this._dom = {};
    this._activationControl = null;
    this._control = null;
    this._disabled = null;
    this._value = null;
    this._fieldType = null;
    this._showConfigButton = null;
    this._configButton = null;
    this._attachedListeners = false;
    this._dirty = false;
    this._allowDisabling = true;
    this._controlContainer = null;
    UpdaterItem.prototype.init.call(this, settings);
};

UpdaterItem.prototype = new Element();
UpdaterItem.prototype.constructor = UpdaterItem;
UpdaterItem.prototype.type = "UpdaterItem";

UpdaterItem.prototype.init = function(settings) {
    var defaults = {
        parent: null,
        name: this.id,
        label: "[updater item]",
        required: false,
        disabled: true,
        allowDisabling: true,
        value: "",
        fieldType: null
    };

    jQuery.extend(true, defaults, settings);

    this.setParent(defaults.parent)
        .setName(defaults.name)
        .setLabel(defaults.label)
        .setRequired(defaults.required)
        .setValue(defaults.value)
        .setFieldType(defaults.fieldType);

    this._showConfigButton = false;

    if (defaults.disabled) {
        this.disable();
    } else {
        this.enable();
    }
    if (defaults.allowDisabling) {
        this.allowDisabling();
    } else {
        this.disallowDisabling();
    }
};

UpdaterItem.prototype.allowDisabling = function () {
    this._allowDisabling = true;
    if (this._activationControl) {
        this._activationControl.style.display = "";
    }
    return this;
};

UpdaterItem.prototype.disallowDisabling = function () {
    this._allowDisabling = false;
    if (this._activationControl) {
        this._activationControl.style.display = "none";
    }

    return this;
};

UpdaterItem.prototype.setParent = function (parent) {
    if (!(parent === null || parent instanceof UpdaterField)) {
        throw new Error("setParent(): The parameter must be an instance of UpdaterField or null.");
    }
    this._parent = parent;
    return this;
};

UpdaterItem.prototype.setName = function (name) {
    if (!(typeof name === 'string' && name)) {
        throw new Error("setName(): The parameter must be a non empty string.");
    }
    this._name = name;
    return this;
};

UpdaterItem.prototype.getName = function () {
    return this._name;
};

UpdaterItem.prototype.setLabel = function (label) {
    if (typeof label !== 'string') {
        throw new Error("setLabel(): The parameter must be a string.");
    }
    this._label = label;
    if (this._dom.labelText) {
        this._dom.labelText.textContent = label;
    }
    return this;
};

UpdaterItem.prototype.setRequired = function (required) {
    var requireContent = "*";
    this._required = !!required;
    if (this._dom.requiredContainer) {
        if (!this._required) {
            requireContent = "";
        }
        this._dom.requiredContainer.textContent = requireContent;
    }
    return this;
};

UpdaterItem.prototype.isRequired = function () {
    return this._required;
};


UpdaterItem.prototype.isValid = function () {
    return this._required ? this._value !== '' : true;
};

UpdaterItem.prototype.clear = function () {
    if (this._control) {
        this._control.value = "";
    }
    this._value = "";
    return this;
};

UpdaterItem.prototype.disable = function () {
    if (this._activationControl) {
        this._activationControl.checked = false;
        this._disableControl();
    }
    this._disabled = true;
    this.clear();
    return this;
};

UpdaterItem.prototype.enable = function () {
    if (this._activationControl) {
        this._activationControl.checked = true;
        this._enableControl();
    }
    this._disabled = false;
    return this;
};

UpdaterItem.prototype.isDisabled = function () {
    return this._disabled;
};

UpdaterItem.prototype._setValueToControl = function (value) {
    this._control.value = value;
    return this;
};

UpdaterItem.prototype._getValueFromControl = function () {
    return this._control.value;
};

UpdaterItem.prototype.setValue = function (value) {
    if (typeof value !== 'string') {
        throw new Error("setValue(): The parameter must be a string.");
    }
    if (this._control) {
        this._setValueToControl(value);
        this._value = this._getValueFromControl();
    } else {
        this._value = value;
    }
    return this;
};

UpdaterItem.prototype.getValue = function () {
    return this._value;
};

UpdaterItem.prototype.setFieldType = function (fieldType) {
    if (!(fieldType === null || typeof fieldType === "string")) {
        throw new Error("setFieldType(): The parameter must be a string or null.");
    }
    this._fieldType = fieldType;
    return this;
};

UpdaterItem.prototype.getFieldType = function () {
    return this._fieldType;
};

UpdaterItem.prototype._createControl = function () {
    if (!this._control) {
        throw new Error("_createControl(): This method must be called from an UpdaterItem's subclass.");
    }
    jQuery(this._control).addClass("updateritem-control");
    return this._control;
};

UpdaterItem.prototype._createConfigButton = function () {
    if (!this._showConfigButton) {
        return null;
    }
    var button = this.createHTMLElement("a");
    button.href = "#";
    button.className = "adam-itemupdater-cfg fa fa-cog";
    this._configButton = button;
    return this._configButton;
};

UpdaterItem.prototype._disableControl = function () {
    this._control.disabled = true;
    return this;
};

UpdaterItem.prototype._enableControl = function () {
    this._control.disabled = false;
    return this;
};

UpdaterField.prototype.isDirty = function () {
    return this._dirty;
};

UpdaterItem.prototype._onChange = function () {
    var that = this;
    return function (e) {
        var currValue = that._value;
        that._value = that._getValueFromControl();
        if (that._value !== currValue) {
            that._dirty = true;
        }
    };
};

UpdaterItem.prototype.getData = function () {
    return {
        name: this._label,
        field: this._name,
        value: this._value,
        type: this._fieldType
    };
};

UpdaterItem.prototype.attachListeners = function () {
    var that = this;
    if (this.html && !this._attachedListeners) {
        jQuery(this._activationControl).on("change", function (e) {
            if (e.target.checked) {
                that.enable();
            } else {
                that.disable();
            }
        });
        jQuery(this._configButton).on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (that._parent && !that._disabled) {
                that._parent.openPanelOnItem(that);
            }
        });
        jQuery(this._control).on("change", this._onChange());
    }
    return this;
};

UpdaterItem.prototype.createHTML = function () {
    var label,
        controlContainer,
        activationControl,
        labelContent,
        labelText,
        requiredContainer,
        messageContainer,
        configButton,
        messageContainer;

    if (!this.html) {
        Element.prototype.createHTML.call(this);
        jQuery(this.html).addClass("updaterfield-item");
        this.style.removeProperties(['width', 'height', 'position', 'top', 'left', 'z-index']);

        label = this.createHTMLElement('label');
        label.className = 'adam-itemupdater-label';

        controlContainer = this.createHTMLElement("div");
        controlContainer.className = "adam-itemupdater-controlcontainer";

        activationControl = this.createHTMLElement("input");
        activationControl.type = "checkbox";
        activationControl.className = "adam-itemupdater-activation";

        labelContent = this.createHTMLElement("span");
        labelContent.className = "adam-itemupdater-labelcontent";

        labelText = this.createHTMLElement("span");
        labelText.className = "adam-itemupdater-labeltext";

        requiredContainer = this.createHTMLElement("span");
        requiredContainer.className = "adam-itemupdater-required required noshadow";

        messageContainer = this.createHTMLElement("div");
        messageContainer.className = "adam-itemupdater-message";

        labelContent.appendChild(labelText);
        labelContent.appendChild(requiredContainer);

        label.appendChild(activationControl);
        label.appendChild(labelContent);

        controlContainer.appendChild(this._createControl());
        this._createConfigButton();
        if (this._configButton) {
            controlContainer.appendChild(this._configButton);
        }

        this._dom.labelText = labelText;
        this._dom.requiredContainer = requiredContainer;

        this._activationControl = activationControl;
        this._controlContainer = controlContainer;
        this.html.appendChild(label);
        this.html.appendChild(controlContainer);
        this.html.appendChild(messageContainer);

        this.setLabel(this._label)
            .setRequired(this._required);
        if (this._disabled) {
            this.disable();
        } else {
            this.enable();
        }
        if (this._allowDisabling) {
            this.allowDisabling();
        } else {
            this.disallowDisabling();
        }
        this.attachListeners();
        this.setValue(this._value);
    }
    return this.html;
};

// TeamUpdaterItem
var TeamUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    this._primaryTeam = null;
    this._disabledAppendOption = null;
    this._appendTeamsCheckbox = null;
    this._appendTeamsLabel = null;
    this._selectedTeams = null;
    this._appendTeams = null;
    this._addButton = null;
    this._disabledTeamSelection = null;
    TeamUpdaterItem.prototype.init.call(this, settings);
};

TeamUpdaterItem.prototype = new UpdaterItem();
TeamUpdaterItem.prototype.constructor = TeamUpdaterItem;
TeamUpdaterItem.prototype.type = 'TeamUpdaterItem';

TeamUpdaterItem.TEAM_ACTION = {
    PRIMARY: 0,
    ADD: 1,
    REMOVE: 2,
    LOCK: 3
};

TeamUpdaterItem.prototype.init = function (settings) {
    var defaults = {
        primaryTeam: null,
        appendTeams: false,
        disabledAppendOption: false,
        selectedTeams: [],
        disableTeamSelection: true
    };

    jQuery.extend(true, defaults, settings);

    this.setPrimaryTeam(defaults.primaryTeam)
        .setAppendTeams(defaults.appendTeams)
        .setSelectedTeams(defaults.selectedTeams);

    if (defaults.disableTeamSelection) {
        this.disableTeamSelection();
    } else {
        this.enableTeamSelection();
    }

    if (defaults.disabledAppendOption) {
        this.disableAppendOption();
    } else {
        this.enableAppendOption();
    }
};

TeamUpdaterItem.prototype.enableTeamSelection = function () {
    this._disabledTeamSelection = false;
    if (this._control) {
        $(this._control)
            .find('.btn.adam-team-action[name=lock]').show().end()
            .find('.select2-container').css('width', '184px');
    }
    return this;
};

TeamUpdaterItem.prototype.disableTeamSelection = function () {
    this._disabledTeamSelection = true;
    if (this._control) {
        $(this._control)
            .find('.btn.adam-team-action[name=lock]').hide().end()
            .find('.select2-container').css('width', '220px');
    }
    return this;
};

TeamUpdaterItem.prototype._updateTeamSelection = function () {
    if (this._disabledTeamSelection) {
        this.disableTeamSelection();
    } else {
        this.enableTeamSelection();
    }
    return this;
};

TeamUpdaterItem.prototype.disableAppendOption = function () {
    this._disabledAppendOption = true;
    if (this._appendTeamsLabel) {
        jQuery(this._appendTeamsLabel).hide();
    }
    return  this;
};

TeamUpdaterItem.prototype.enableAppendOption = function () {
    this._disabledAppendOption = false;
    if (this._appendTeamsLabel) {
        jQuery(this._appendTeamsLabel).show();
    }
    return  this;
};

TeamUpdaterItem.prototype.isValid = function () {
    return this._required ? !!this._value.length : true;
};

TeamUpdaterItem.prototype.setSelectedTeams = function (teams) {
    var existingValues = [], that = this;
    if (!jQuery.isArray(teams)) {
        throw new Error('setSelectedTeams(): The parameter must be an array.');
    }

    this._selectedTeams = teams.slice(0);

    if (this._control) {
        jQuery(this._control).find('.adam-team-updater-line').each(function (index, item) {
            var data = that._getLineData(item);

            if (data !== null) {
                if (that._selectedTeams.indexOf(data.id) >= 0) {
                    jQuery(item).find('.adam-team-action[name=lock]').addClass('active')
                        .find('i').removeClass('fa-lock').addClass('active fa-unlock-alt');
                    existingValues.push(data.id);
                }
            }
        });
        this._selectedTeams = existingValues;
    }

    return this;
};

TeamUpdaterItem.prototype.setAppendTeams = function (bln) {
    var lines, $line, that = this;
    bln = !!bln;
    this._appendTeams = bln;
    if (this._appendTeamsCheckbox) {
        this._appendTeamsCheckbox.checked = bln;
    }
    if (!bln && this._primaryTeam === null) {
        lines = jQuery(this._control).find('.adam-team-updater-line').toArray();
        _.find(lines, function (el) {
            var data = that._getLineData(el);
            if (data !== null) {
                jQuery(el).find('.adam-team-action[name=primary]').addClass('active')
                    .trigger('change', [TeamUpdaterItem.TEAM_ACTION.PRIMARY]);
                return true;
            }
        });
    }
    return this;
};

TeamUpdaterItem.prototype.setPrimaryTeam = function (team) {
    var lines, i, data;
    this._primaryTeam = team;
    if (this._control) {
        lines = jQuery(this._control).find('.adam-team-updater-line').find('.adam-team-action')
            .removeClass('active').end().toArray();
        for (i = 0; i < lines.length; i += 1) {
            data = this._getLineData(lines[i]);
            if (data.id === this._primaryTeam) {
                jQuery(lines[i]).find('.adam-team-action[name=primary]').addClass('active');
                return this;
            }
        }
    }
    return this;
};

TeamUpdaterItem.prototype.clear = function () {
    this._primaryTeam = null;
    this.setValue([]);
    return this;
};

TeamUpdaterItem.prototype._setValueToControl = function (value) {
    var that = this, count = 0;
    if (!this._control) {
        return this;
    }
    jQuery(this._control).empty();
    value.forEach(function (item, index, arr) {
        that._addNewInputLine(item);
        count ++;
    });
    this.setPrimaryTeam(this._primaryTeam)
        .setSelectedTeams(this._selectedTeams);
    if (!count) {
        this._addNewInputLine();
    }
    return this;
};

TeamUpdaterItem.prototype._getValueFromControl = function () {
    var value = [], that = this;
    this._primaryTeam = null;
    this._selectedTeams = [];
    jQuery(this._control).find('.adam-team-updater-line').each(function () {
        var input = jQuery(this).find('input'),
            data = input.select2('data');

        if (data !== null) {
            if (value.indexOf(data.id) < 0) {
                value.push(data.id);    
            }
            if (jQuery(this).find('.adam-team-action.active[name=primary]').length) {
                that._primaryTeam = data.id;
            }
            if (that._selectedTeams.indexOf(data.id) < 0 && jQuery(this).find('.adam-team-action.active[name=lock]').length) {
                that._selectedTeams.push(data.id);
            }
        }
    });

    return value;
};

TeamUpdaterItem.prototype._getTeamName = function (value) {
    var teams = (project && project.getMetadata("teams_details")) || [],
        i;

    for(i = 0; i < teams.length; i += 1) {
        if (teams[i].id === value) {
            return jQuery.trim(teams[i].name + " " + teams[i].name_2);
        }
    }
    return value;
};

TeamUpdaterItem.prototype._initSelection = function () {
    var that = this;
    return function (element, callback) {
        var value = element.val();
        callback({
            id: value,
            text: that._getTeamName(value)
        });
    };
};

TeamUpdaterItem.prototype._getSearchFunction = function () {
    return _.debounce(function (queryObject) {
        var proxy = new SugarProxy(), term = queryObject.term,
            resultsPerPage = 5;

        proxy.url = "Teams?filter[0][name][$starts]=%TERM%&fields=id,name&max_num=%NUM%&offset=%OFFSET%"
            .replace(/%TERM%/g, term).replace(/%NUM%/, resultsPerPage).replace(/%OFFSET%/, (queryObject.page - 1) * resultsPerPage);
        proxy.getData(null, {
            success: function (data) {
                var finalData = [];

                data.records.forEach(function (item, index, arr) {
                    finalData.push({
                        id: item.id,
                        text: item.name
                    });
                });

                queryObject.callback({
                    more: data.nextOffset >= 0,
                    results: finalData
                });
            },
            error: function () {
                // TODO: show error message
            }
        });

    }, 1500);
};

TeamUpdaterItem.prototype._queryFunction = function () {
    var that = this;

    return function (queryObject) {
        var term = jQuery.trim(queryObject.term);

        if (term) {
            (that._getSearchFunction())(queryObject);
        } else {
            queryObject.callback({
                results: []
            });
        }
    };
};

TeamUpdaterItem.prototype._openSearchMore = function (select) {
    var that = this, zIndex, $select = jQuery(select);
    return function () {
        zIndex = $(that.html).closest(".adam-modal").zIndex();
        $select.select2("close");
        $(that.html).closest(".adam-modal").zIndex(-1);
        App.drawer.open({
                layout: "selection-list",
                context: {module: "Teams"}
            },
            _.bind(function (drawerValues) {
                $(that.html).closest(".adam-modal").zIndex(zIndex);
                if (!_.isUndefined(drawerValues)) {
                    $select.select2("val", drawerValues.id, true);
                }
            }, this)
        );
    };
};

TeamUpdaterItem.prototype._addNewInputLine = function (value) {
    var select, div, dropdownHTML, additionalList, listItem, tpl, $select;

    if (!this._control) {
        return this;
    }
    div = this.createHTMLElement("div");
    div.className = 'adam-team-updater-line';
    select = this.createHTMLElement("input");
    div.appendChild(select);
    this._control.appendChild(div);
    $select = jQuery(select).select2({
        minimumInputLength: 1,
        formatInputTooShort: '',
        allowClear: false,
        query: this._queryFunction(),
        width: this.fieldWidth || '184px',
        placeholder: translate('LBL_PMSE_UPDATERFIELD_ADD_TEAM'),
        initSelection: this._initSelection()
    });

    if (value) {
        $select.select2("val", value, false);
    }

    this._addButtonsToLine(div);

    if (this._disabled) {
        $select.select2("disable");
    }

    dropdownHTML = $select.data("select2").dropdown;
    additionalList = this.createHTMLElement('ul');
    additionalList.className = 'select2-results adam-searchmore-list';
    listItem = this.createHTMLElement('li');
    tpl = this.createHTMLElement('div');
    tpl.className = 'select2-result-label';
    tpl.appendChild(document.createTextNode(translate('LBL_SEARCH_AND_SELECT_ELLIPSIS')));
    listItem.appendChild(tpl);
    additionalList.appendChild(listItem);
    dropdownHTML.append(additionalList);
    $(additionalList).find('li').on('mousedown', this._openSearchMore(select));

    return this;
};

TeamUpdaterItem.prototype._createControl = function () {
    this._control = this.createHTMLElement("div");
    this._setValueToControl(this._value);

    return UpdaterItem.prototype._createControl.call(this);
};

TeamUpdaterItem.prototype._getAddButton = function () {
    var addButton;

    if (this._addButton) {
        return this._addButton;
    }

    addButton = this.createHTMLElement('button')
    addButton.className = 'btn adam-team-action';
    i = this.createHTMLElement('i');
    i.className = 'fa fa-plus';
    addButton.appendChild(i);
    addButton.name = 'add';
    this._addButton = addButton;

    return addButton;
};

TeamUpdaterItem.prototype._addButtonsToLine = function (line) {
    var primaryButton = this.createHTMLElement('button'),
        lockButton, addButton, removeButton,
        i = this.createHTMLElement('i');

    if ($(line).hasClass('adam-line-filled')) {
        return this;
    }

    i.className = 'fa fa-star';
    primaryButton.appendChild(i);
    primaryButton.className = 'btn adam-team-action ';
    primaryButton.name = 'primary';

    removeButton = primaryButton.cloneNode(false);
    i = i.cloneNode(false);
    i.className = 'fa fa-minus';
    removeButton.appendChild(i);
    removeButton.name = 'remove';

    lockButton = primaryButton.cloneNode(false);
    i = i.cloneNode(false);
    i.className = 'fa fa-lock';
    lockButton.appendChild(i);
    lockButton.name = 'lock';

    if ($(this._control).find('*').index(line) === 0) {
        removeButton.style.visibility = 'hidden';
    }

    addButton = this._getAddButton();

    primaryButton.disabled = lockButton.disabled = removeButton.disabled = addButton.disabled = this._disabled;

    line.appendChild(primaryButton);
    line.appendChild(lockButton);
    line.appendChild(removeButton);
    line.appendChild(addButton);
    $(line).addClass('adam-line-filled');

    return this._updateTeamSelection();
};

TeamUpdaterItem.prototype.setValue = function (value) {
    if (value === "") {
        value = [];
    }
    if (!jQuery.isArray(value)) {
        throw new Error("setValue(): The parameter must be an array.");
    }
    if (this._control) {
        this._setValueToControl(value);
        this._value = this._getValueFromControl();
        this._updateTeamActionsVisibility();
    } else {
        this._value = value;
    }
    return this;
};

TeamUpdaterItem.prototype._updateTeamActionsVisibility = function () {
    if (this._value.length > 1) {
        $(this._control).find('.adam-team-action[name=remove]').css('visibility', '');
    } else {
        $(this._control).find('.adam-team-action[name=remove]').first().css('visibility', 'hidden');
    }
    $(this._control).find('.adam-team-updater-line').last().append(this._addButton);
    return this;
};

TeamUpdaterItem.prototype._onChange = function () {
    var that = this;
    return function (e, type) {
        var parentListener = UpdaterItem.prototype._onChange.call(that), $target = jQuery(e.target),
            $line = $target.closest('.adam-team-updater-line'), wasEmpty = $line.find('.adam-team-action').length === 0;

        switch (type) {
            case undefined:
                if (wasEmpty) {
                    that._addButtonsToLine($line.get(0));
                }

                if (that._getLineData($line) === null || !that._getValueFromControl().length) {
                    $line.find('.adam-team-action[name=primary]').removeClass('active');
                } else if (!that.isAppendMode() && that._primaryTeam === null) {
                    $line.find('.adam-team-action[name=primary]').addClass('active');
                }
                break;
            case TeamUpdaterItem.TEAM_ACTION.REMOVE:
                if (that._primaryTeam === null && !that.isAppendMode()) {
                    $line.find('.adam-team-action[name=primary]').addClass('active');
                }
                break;
        }

        parentListener(e);
        that._updateTeamActionsVisibility();

        if (wasEmpty && type !== TeamUpdaterItem.TEAM_ACTION.REMOVE) {
            that._addNewInputLine();
        }
    };
};

TeamUpdaterItem.prototype._disableControl = function () {
    $(this._control).find('.adam-line-filled').remove().end()
        .find('input').select2('disable');
    if (this._appendTeamsCheckbox) {
        this._appendTeamsCheckbox.disabled = true;
    }
    this._value = [];
    this._primaryTeam = null;
    return UpdaterItem.prototype._disableControl.call(this);
};

TeamUpdaterItem.prototype._enableControl = function () {
    $(this._control).find('input').select2('enable').end()
        .find('button').attr("disabled", false);
    if (this._appendTeamsCheckbox) {
        this._appendTeamsCheckbox.disabled = false;
    }
    return UpdaterItem.prototype._enableControl.call(this);
};

TeamUpdaterItem.prototype.isAppendMode = function () {
    return this._appendTeams && !this._disabledAppendOption;
};

TeamUpdaterItem.prototype.getData = function () {
    return {
        name: this._label,
        field: this._name,
        value: this._value,
        primary: this._primaryTeam,
        selected_teams: this._disabledTeamSelection ? [] : this._selectedTeams,
        append: this.isAppendMode(),
        type: this._fieldType
    };
};

TeamUpdaterItem.prototype._getLineData = function (line) {
    line = jQuery(line);
    return line.find("input").select2('data');
};

TeamUpdaterItem.prototype._performTeamAction = function () {
    var that = this;

    return function () {
        var $button = jQuery(this), changed = false,
            $line = jQuery(this).closest('.adam-team-updater-line'),
            lineData = that._getLineData($line),
            actionID;
        switch (this.name) {
            case 'primary':
                if (lineData !== null) {
                    if (that.isAppendMode() || !$button.hasClass('active')) {
                        $(that._control).find('.adam-team-action.active[name=' + this.name + ']').not($button).removeClass('active');
                        $button.toggleClass('active');
                        changed = $button;
                        actionID = TeamUpdaterItem.TEAM_ACTION.PRIMARY;
                    }
                }
                break;
            case 'add':
                if (lineData !== null) {
                    that._addNewInputLine();
                }
                break;
            case 'lock':
                if (lineData !== null) {
                    $button.toggleClass('active').find('i').toggleClass('fa-lock fa-unlock-alt');
                    changed = $button;
                    actionID = TeamUpdaterItem.TEAM_ACTION.LOCK;
                }
                break;
            case 'remove':
                actionID = TeamUpdaterItem.TEAM_ACTION.REMOVE;
                if ($line.find('.adam-team-action.active[name=primary]').length > 0) {
                    that._primaryTeam = null;
                }
                $line.remove();
                changed = jQuery(that._control);
                break;
        }
        if (changed) {
            changed.trigger('change', [actionID]);
        }
    };
};

TeamUpdaterItem.prototype.attachListeners = function () {
    var that = this;
    if (this.html && !this._attachedListeners && this._appendTeamsCheckbox) {
        UpdaterItem.prototype.attachListeners.call(this);
        jQuery(this._control).on('click', '.adam-team-action', this._performTeamAction());
        jQuery(this._appendTeamsCheckbox).on('change', function() {
            that.setAppendTeams(this.checked);
        });
    }
    return this;
};

TeamUpdaterItem.prototype.createHTML = function () {
    var label, checkbox;
    if (!this.html) {
        UpdaterItem.prototype.createHTML.call(this);
        label = this.createHTMLElement('label');
        checkbox = this.createHTMLElement('input');
        checkbox.className = 'adam-team-append';
        checkbox.type = 'checkbox';
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(translate('LBL_SELECT_APPEND_TEAMS')));
        this._appendTeamsLabel = label;
        this._controlContainer.appendChild(label);
        this._appendTeamsCheckbox = checkbox;

        this.setAppendTeams(this._appendTeams);

        if (this._disabledAppendOption) {
            this.disableAppendOption();
        } else {
            this.enableAppendOption();
        }

        if (this._disabled) {
            this.disable();
        } else {
            this.enable();
        }

        this.attachListeners();
    }
    return this;
};

//TextUpdaterItem
var TextUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    this._maxLength = null;
    TextUpdaterItem.prototype.init.call(this, settings);
};

TextUpdaterItem.prototype = new UpdaterItem();
TextUpdaterItem.prototype.constructor = TextUpdaterItem;
TextUpdaterItem.prototype.type = "TextUpdaterItem";

TextUpdaterItem.prototype.init = function (settings) {
    var defaults = {
        maxLength: 0
    };

    jQuery.extend(true, defaults, settings);
    this._showConfigButton = true;
    this.setMaxLength(defaults.maxLength);
};

TextUpdaterItem.prototype.setMaxLength = function (maxLength) {
    if (typeof maxLength === 'string' && /\d+/.test(maxLength)) {
        maxLength = parseInt(maxLength, 10);
    }
    if (typeof maxLength !== 'number') {
        throw new Error("setMaxLength(): The parameter must be a number.");
    }
    this._maxLength = maxLength;
    if (this._control) {
        if (maxLength) {
            this._control.maxLength = maxLength;
        } else {
            this._control.removeAttribute("maxlength");
        }

    }
    return this;
};

TextUpdaterItem.prototype._createControl = function () {
    var control = this.createHTMLElement("input");
    control.type = "text";
    this._control = control;
    this.setMaxLength(this._maxLength);
    return UpdaterItem.prototype._createControl.call(this);
};
//DateUpdaterItem
var DateUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    DateUpdaterItem.prototype.init.call(this, settings);
};

DateUpdaterItem.prototype = new UpdaterItem();
DateUpdaterItem.prototype.constructor = DateUpdaterItem;
DateUpdaterItem.prototype.type = "DateUpdaterItem";

DateUpdaterItem.prototype.init = function (settings) {
    var defaults = {
        value: "[]"
    };

    jQuery.extend(true, defaults, settings);

    this.setValue(defaults.value);
};

DateUpdaterItem.prototype.isValid = function () {
    return this._required ? !!this._value.length : true;
};

DateUpdaterItem.prototype._setValueToControl = function (value) {
    var friendlyValue = "", i, dateFormat, timeFormat;
    value.forEach(function(value, index, arr) {
        if (value && value.expType === 'CONSTANT') {
            if (!dateFormat) {
                dateFormat = SUGAR.App.date.convertFormat(SUGAR.App.user.getPreference("datepref"));
            }
            if (value.expSubtype === "datetime") {
                if (!timeFormat) {
                    timeFormat = SUGAR.App.date.convertFormat(SUGAR.App.user.getPreference("timepref"));
                }
                aux = App.date(value.expValue);
                value.expLabel = aux.format(dateFormat + " " + timeFormat);
            } else if (value.expSubtype === "date") {
                aux = App.date(value.expValue);
                value.expLabel = aux.format(dateFormat);
            }
        }
        friendlyValue += " " + value.expLabel;
    });
    this._control.value = friendlyValue;
    return this;
};

DateUpdaterItem.prototype.setValue = function (value) {
    if (typeof value === 'string') {
        value = value || "[]";
        value = JSON.parse(value);
    }
    if (this._control) {
        this._setValueToControl(value);
    }
    this._value = value;
    return this;
};

DateUpdaterItem.prototype.clear = function () {
    UpdaterItem.prototype.clear.call(this);
    this._value = "[]";
    return this;
};

DateUpdaterItem.prototype._createControl = function () {
    var control = this.createHTMLElement("input");
    control.type = "text";
    control.readOnly = true;
    this._control = control;
    return UpdaterItem.prototype._createControl.call(this);
};

DateUpdaterItem.prototype._createConfigButton = function () {
    return null;
};

DateUpdaterItem.prototype.attachListeners = function () {
    var that = this;
    if (this.html && !this._attachedListeners) {
        UpdaterItem.prototype.attachListeners.call(this);
        jQuery(this._control).on("focus", function () {
            if (that._parent && !this._disabled) {
                that._parent.openPanelOnItem(that);
            }
        });
        this._attachedListeners = true;
    }
};
//CheckboxUpdaterItem
var CheckboxUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
};

CheckboxUpdaterItem.prototype = new UpdaterItem();
CheckboxUpdaterItem.prototype.constructor = CheckboxUpdaterItem;
CheckboxUpdaterItem.prototype.type = "CheckboxUpdaterItem";

CheckboxUpdaterItem.prototype.isValid = function () {
    return this._required ? !!field.getValue() : true;
};

CheckboxUpdaterItem.prototype.setValue = function (value) {
    if (this._control) {
        this._setValueToControl(value);
        this._value = this._getValueFromControl();
    } else {
        this._value = !!value;
    }
    return this;
};

CheckboxUpdaterItem.prototype._createControl = function () {
    var control = this.createHTMLElement('input');
    control.type = "checkbox";
    this._control = control;
    return UpdaterItem.prototype._createControl.call(this);
};

CheckboxUpdaterItem.prototype._createConfigButton = function () {
    return null;
};

CheckboxUpdaterItem.prototype.clear = function () {
    if (this._control) {
        this._control.checked = false;
    }
    this._value = false;
    return this;
};

CheckboxUpdaterItem.prototype._setValueToControl = function (value) {
    this._control.checked = !!value;
    return this;
};

CheckboxUpdaterItem.prototype._getValueFromControl = function () {
    return this._control.checked;
};

CheckboxUpdaterItem.prototype._onChange = function () {
    var that = this;
    return function (e) {
        var currValue = that._value;
        that._value = that._getValueFromControl();
        if (that._value !== currValue) {
            that._dirty = true;
        }
    };
};
//TextAreaUpdaterItem
var TextAreaUpdaterItem = function (settings) {
    TextUpdaterItem.call(this, settings);
};

TextAreaUpdaterItem.prototype = new TextUpdaterItem();
TextAreaUpdaterItem.prototype.constructor = TextAreaUpdaterItem;
TextAreaUpdaterItem.prototype.type = "TextAreaUpdaterItem";

TextAreaUpdaterItem.prototype._createControl = function () {
    var control = this.createHTMLElement('textarea');
    this._control = control;
    return UpdaterItem.prototype._createControl.call(this);
};
//NumberUpdaterItem
var NumberUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    this._currency = null;
    NumberUpdaterItem.prototype.init.call(this, settings);
};

NumberUpdaterItem.prototype = new UpdaterItem();
NumberUpdaterItem.prototype.constructor = NumberUpdaterItem;
NumberUpdaterItem.prototype.type = "NumberUpdaterItem";

NumberUpdaterItem.prototype.init = function (settings) {
    var defaults = {
        value: "[]",
        currency: false
    };
    jQuery.extend(true, defaults, settings);
    this._showConfigButton = true;
    this._currency = !!defaults.currency;
    this.setValue(defaults.value);
};

NumberUpdaterItem.prototype.isValid = function () {
    return this._required ? !!this._value.length : true;
};

NumberUpdaterItem.prototype.isCurrency = function() {
    return this._currency;
};

NumberUpdaterItem.prototype._setValueToControl = function (value) {
    var friendlyValue = "", i;
    value.forEach(function(value, index, arr) {
        friendlyValue += " " + value.expLabel;
    });
    this._control.value = friendlyValue;
    return this;
};


NumberUpdaterItem.prototype.setValue = function (value) {
    if (typeof value === 'string') {
        value = value || "[]";
        value = JSON.parse(value);
    }
    if (this._control) {
        this._setValueToControl(value);
    }
    this._value = value;
    return this;
};

NumberUpdaterItem.prototype.isValid = function () {
    var isValid = true;
    var value = this.getValue().slice(0);
    var i;
    var prev = null;
    var currType;
    var currValue;

    // TODO: the next validation must be implemented in ExpressionControl based on the kind of result it expects to return.

    // We need to validate that a valid expression is inserted in the control
    // First, check the syntax
    for (i = 0; i < value.length; i += 1) {
        currType = value[i].expType;
        currValue = value[i].expValue;
        if (prev === null) {
            if (currType === 'ARITMETIC' && currValue !== '(') {
                return false;
            }
        } else {
            if ((currType === 'CONSTANT' || currType === 'VARIABLE' || (currType === 'ARITMETIC' && currValue === '(')) && (prev.expType === 'CONSTANT' || prev.expType === 'VARIABLE' || (prev.expType === 'ARITMETIC' && prev.expValue === ')'))) {
                return false;
            } else if ((currType === 'ARITMETIC' && currValue !== '(') && (prev.expType === 'ARITMETIC' && prev.expValue !== '(')) {
                return false;
            }
        }
        prev = value[i];
    }

    if (currType === 'ARITMETIC' && currValue !== ')') {
        return false;
    }

    if (!isValid) {
        return isValid;
    }
    return UpdaterItem.prototype.isValid.call(this);
};

NumberUpdaterItem.prototype._createControl = function () {
    var control = this.createHTMLElement("input");
    control.type = "text";
    control.readOnly = true;
    this._control = control;
    return UpdaterItem.prototype._createControl.call(this);
};

//DropdownUpdaterItem
var DropdownUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    this._options = [];
    this._massiveAction = false;
    this._initialized = false;
    DropdownUpdaterItem.prototype.init.call(this, settings);
};

DropdownUpdaterItem.prototype = new UpdaterItem();
DropdownUpdaterItem.prototype.constructor = DropdownUpdaterItem;
DropdownUpdaterItem.prototype.type = "DropdownUpdaterItem";

DropdownUpdaterItem.prototype.init = function (settings) {
    var defaults = {
        options: [],
        value: ""
    };

    jQuery.extend(true, defaults, settings);

    this.setOptions(defaults.options)
        .setValue(defaults.value);

    this._initialized = true;
};

DropdownUpdaterItem.prototype._existsValueInOptions = function (value) {
    var i;
    for (i = 0; i < this._options.length; i += 1) {
        if (this._options[i].value === value) {
            return true;
        }
    }
    return false;
};

DropdownUpdaterItem.prototype._getFirstAvailabelValue = function () {
    return (this._options[0] && this._options[0].value) || "";
};

DropdownUpdaterItem.prototype.setValue = function (value) {
    if (this._options) {
        if (!(typeof value === 'string' || typeof value === 'number')) {
            throw new Error("setValue(): The parameter must be a string.");
        }
        if (isInDOM(this._control)) {
            this._setValueToControl(value);
            this._value = this._getValueFromControl();
        } else {
            if (this._existsValueInOptions(value)) {
                this._value = value;
            } else {
                this._value = this._getFirstAvailabelValue();
            }
        }
    }
    return this;
};

DropdownUpdaterItem.prototype._paintItem = function (option) {
    var optionHTML;
    optionHTML = this.createHTMLElement('option');
    optionHTML.textContent = optionHTML.label = option.text;
    optionHTML.value = option.value;
    this._control.appendChild(optionHTML);
    return this;
};

DropdownUpdaterItem.prototype._paintItems = function () {
    var i;
    if (this._control) {
        jQuery(this._control).empty();
        for (i = 0; i < this._options.length; i += 1) {
            this._paintItem(this._options[i]);
        }
    }
    return this;
};

DropdownUpdaterItem.prototype.addOption = function (option) {
    var newOption;
    if (typeof option === 'string' || typeof option === 'number') {
        newOption = {
            text: option,
            value: option
        };
    } else {
        newOption = {
            text: option.text || option.value,
            value: option.value || option.text
        };
    }
    this._options.push(newOption);
    if (!this._massiveAction && this.html) {
        this._paintItem(newOption);
    }
    return this;
};

DropdownUpdaterItem.prototype.clearOptions = function () {
    this._options = [];
    if (this._control) {
        jQuery(this._control).empty();
    }
    return this;
};

DropdownUpdaterItem.prototype.setOptions = function (options) {
    var i;
    if (!jQuery.isArray(options)) {
        throw new Error("setOptions(): The parameter must be an array.");
    }
    this._massiveAction = true;
    this.clearOptions();
    for (i = 0; i < options.length; i += 1) {
        this.addOption(options[i]);
    }
    this._massiveAction = false;
    this._paintItems();
    if (this._initialized) {
        this.setValue(this._value);
    }
    return this;
};

DropdownUpdaterItem.prototype._createConfigButton = function () {
    return null;
};

DropdownUpdaterItem.prototype._createControl = function () {
    if (!this._control) {
        this._control = this.createHTMLElement('select');
    }
    return UpdaterItem.prototype._createControl.call(this);
};

DropdownUpdaterItem.prototype.createHTML = function () {
    if (!this.html) {
        UpdaterItem.prototype.createHTML.call(this);
        this._paintItems();
        this.setValue(this._value);
    }
    return this.html;
};
//SearchUpdaterItem
var SearchUpdaterItem = function (settings) {
    UpdaterItem.call(this, settings);
    this._pageSize = null;
    this._searchLabel = null;
    this._searchValue = null;
    SearchUpdaterItem.prototype.init.call(this, settings);
};

SearchUpdaterItem.prototype = new UpdaterItem();
SearchUpdaterItem.prototype.constructor = SearchUpdaterItem;
SearchUpdaterItem.prototype.type = 'SearchUpdaterItem';

/**
 * @inheritdoc
 */
SearchUpdaterItem.prototype.init = function(settings) {
    var defaults = {
        value: '',
        pageSize: 5,
        searchValue: 'id',
        searchLabel: 'text',
        searchMore: false,
        searchUrl: null,
        defaultSearchOptions: []
    };

    jQuery.extend(true, defaults, settings);

    this._pageSize = typeof defaults.pageSize === 'number' && defaults.pageSize >= 1 ? Math.floor(defaults.pageSize) : 0;

    this._searchUrl = defaults.searchUrl;
    this._defaultSearchOptions = defaults.defaultSearchOptions;
    this._searchLabel = defaults.searchLabel;
    this._searchValue = defaults.searchValue;
    this.setValue(defaults.value);

    if (defaults.searchMore) {
        this.enableSearchMore(defaults.searchMore);
    } else {
        this.disableSearchMore();
    }
};

SearchUpdaterItem.prototype._createSearchMoreOption = function () {
    var dropdownHTML, additionalList, listItem, tpl;
    if (this._control && !this._searchMoreList) {
        dropdownHTML = $(this._control).data("select2").dropdown;
        additionalList = this.createHTMLElement('ul');
        additionalList.className = 'select2-results adam-searchmore-list';
        listItem = this.createHTMLElement('li');
        tpl = this.createHTMLElement('div');
        tpl.className = 'select2-result-label';
        tpl.appendChild(document.createTextNode(translate('LBL_SEARCH_AND_SELECT_ELLIPSIS')));
        listItem.appendChild(tpl);
        additionalList.appendChild(listItem);
        dropdownHTML.append(additionalList);
        this._searchMoreList = additionalList;
    }
    return this;
};

/**
 * Enables the Search More functionality
 * @param {Object} settings
 */
SearchUpdaterItem.prototype.enableSearchMore = function (settings) {

    if (typeof settings !== 'object') {
        throw new Error("enableSearchMore(): The parameter must be an object.");
    }
    this._searchMore = settings;
    if (this._control) {
        this._createSearchMoreOption();
        this._searchMoreList.style.display = '';
    }
    return this;
};

/**
 * Disables the field
 * @inheritdoc
 **/
SearchUpdaterItem.prototype._disableControl = function () {
    if (this.select2Control) {
        this.select2Control.select2("disable");
    }
    return this;
};

SearchUpdaterItem.prototype._enableControl = function () {
    if (this.select2Control) {
        this.select2Control.select2("enable");
    }
    return this;
};

/**
 * Disables the Search More functionality
 */
SearchUpdaterItem.prototype.disableSearchMore = function () {
    this._searchMore = false;
    if (this._control) {
        this._createSearchMoreOption();
        this._searchMoreList.style.display = 'none';
    }
    return this;
};

/**
 * @inheritdoc
 * @param {String} value
 */
SearchUpdaterItem.prototype._setValueToControl = function (value) {
    if (this.html && this.select2Control) {
        this.select2Control.select2("val", value);
    }
    return this;
};

SearchUpdaterItem.prototype._getValueFromControl = function () {
    if (this.select2Control) {
        return this.select2Control.select2("val");
    }
};

/**
 * @inheritdoc
 * @param {String} value
 * @param {String} label
 */
SearchUpdaterItem.prototype.setValue = function(value, label) {
    if (value && this.select2Control) {
        if (label) {
            this.select2Control.select2('data', {value: value, text: label});
        } else {
            return UpdaterItem.prototype.setValue.call(this, value);
        }
    }
    this._value = value || '';
    return this;
};

/**
 * @inheritdoc
 * @returns {object}
 */
SearchUpdaterItem.prototype.getData = function () {
    var data = UpdaterItem.prototype.getData.call(this);
    data.label = this.getSelectedText();
    return data;
};

/**
 * @inheritdoc
 */
SearchUpdaterItem.prototype.clear = function() {
    if (this._control) {
        this.select2Control.select2("val", "");
    }
    this._value = "";
    return this;
};

/**
 * Return the label associated with the selected select2 choice
 * @returns {string}
 */
SearchUpdaterItem.prototype.getSelectedText = function() {
    return (this.select2Control.select2('data') && this.select2Control.select2('data').text) || '';
}

/**
 * Opens the related drawer
 * @private
 */
SearchUpdaterItem.prototype._openSearchMore = function () {
    var that = this, zIndex;
    return function () {
        zIndex = $(that.html).closest(".adam-modal").zIndex();
        that.select2Control.select2("close");
        $(that.html).closest(".adam-modal").zIndex(-1);
        App.drawer.open({
                layout: "selection-list",
                context: that._searchMore
            },
            _.bind(function (drawerValues) {
                $(that.html).closest(".adam-modal").zIndex(zIndex);
                if (!_.isUndefined(drawerValues)) {
                    that.setValue(drawerValues.id, drawerValues.name);
                    that._onChange();
                }
            }, this)
        );
    };
};

/**
 * Attach event listeners to the component's HTML.
 * @inheritdoc
 */
SearchUpdaterItem.prototype.attachListeners = function () {
    if (this.html && !this._attachedListeners) {
        UpdaterItem.prototype.attachListeners.call(this);
        this.select2Control.on("change", this._onChange());
        $(this._searchMoreList).find('li').on('mousedown', this._openSearchMore());
    }
    return this;
};

/**
 * Create a select2, and bind a change event to it
 * @inheritdoc
 */
SearchUpdaterItem.prototype.createHTML = function() {
    var self = this;
    if (!this.html) {
        UpdaterItem.prototype.createHTML.call(this);
    }
    return this.html;
}

/**
 * @inheritdoc
 */
SearchUpdaterItem.prototype._createControl = function() {
    var control = this.createHTMLElement('input');
    control.type = 'text';

    this.select2Control = $(control);
    this.select2Control.select2({
        query: _.bind(this._queryFunction, this),
        initSelection: _.bind(this._initSelection, this),
        width: this.fieldWidth || '220px'
    });
    var self = this;
    this.select2Control.on('change', function () {
        var s2obj = self.select2Control.select2('data');
        self.select2Control.data('text', s2obj.text);
        self.setValue(s2obj.id);
    });

    this._control = this.select2Control.data("select2").container.get(0);

    if (this._searchMore) {
        this.enableSearchMore(this._searchMore);
    } else {
        this.disableSearchMore();
    }

    return UpdaterItem.prototype._createControl.call(this);
};

/**
 * No config button necessary
 * @return {null}
 */
SearchUpdaterItem.prototype._createConfigButton = function() {
    return null;
};

SearchUpdaterItem.prototype._resizeListSize = function () {
    var list = this.select2Control.data("select2").dropdown,
        listItemHeight;
    list = $(list).find('ul[role=listbox]');
    listItemHeight = list.find('li').eq(0).outerHeight();
    list.get(0).style.maxHeight = (listItemHeight * this._pageSize) + 'px';
    return this;
};

/**
 * select2 internal query function. See select2 documentation for further info
 * @param options
 */
SearchUpdaterItem.prototype._queryFunction = function(options) {
    var self = this;
    var finalData = options.page > 1 ? [] : this._filterSelections(options, this._defaultSearchOptions);
    var callbackOptions = {more: false};
    var term = $.trim(options.term);
    var that = this;

    if (term) {
        var proxy = new SugarProxy();
        proxy.url = this._searchUrl.replace(/\{%TERM%\}/g, term)
            .replace(/\{%OFFSET%\}/g, (options.page - 1) * this._pageSize);

        if (this._pageSize > 0) {
            proxy.url = proxy.url.replace(/\{%PAGESIZE%\}/g, this._pageSize);
        }

        proxy.getData(null, {
            success: function(data) {
                callbackOptions.more = data.next_offset >= 0 ? true : false;
                _.each(data.records, function(result) {
                    finalData.push({
                        id: result[self._searchValue],
                        text: result[self._searchLabel]
                    });
                });
                callbackOptions.results = finalData;
                options.callback(callbackOptions);
                that._resizeListSize();
            },
            error: function() {
                console.log('failure', arguments);
            }
        });
    } else {
        callbackOptions.results = finalData;
        options.callback(callbackOptions);
    }
};

/**
 * Initialization function to be used internally by select2. Check select2's documentation for further info
 * @param {jQuery} element
 * @param {function} callback
 */
SearchUpdaterItem.prototype._initSelection = function(element, callback) {
    var id = this.select2Control.select2('val');
    var text = this.select2Control.data('text') || id;
    if (id && text) {
        callback({
            id: id,
            text: text
        });
    }
};

/**
 * Formats search results into a format that select2 can understand
 * @param {object} searchOptions
 * @param {array} results
 * @return {Array}
 */
SearchUpdaterItem.prototype._filterSelections = function(searchOptions, results) {
    var finalData = [];
    var term = $.trim(searchOptions.term);
    _.each(results, function(result) {
        if (!term || searchOptions.matcher(term, result.text)) {
            finalData.push({
                id: result.value,
                text: result.text
            });
        }
    }, this);

    return finalData;
};
