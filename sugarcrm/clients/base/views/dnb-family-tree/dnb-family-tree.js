/*
 * By installing or using this file, you are confirming on behalf of the entity
 * subscribed to the SugarCRM Inc. product ('Company') that Company is bound by
 * the SugarCRM Inc. Master Subscription Agreement (“MSA”), which is viewable at:
 * http://www.sugarcrm.com/master-subscription-agreement
 *
 * If Company is not bound by the MSA, then by installing or using this file
 * you are agreeing unconditionally that Company will be bound by the MSA and
 * certifying that you have authority to bind Company accordingly.
 *
 * Copyright  2004-2013 SugarCRM Inc.  All rights reserved.
 */
({
    extendsFrom: 'DnbView',

    // idCounter used for jsTree metadata
    idCounter: 1,

    duns_num: null,

    //current family tree
    currentFT: null,

    events: {
        'click .backToList' : 'backToFamilyTree'
    },

    initialize: function(options) {
        this._super('initialize', [options]);
        if (this.layout.collapse) {
            this.layout.collapse(true);
        }
        this.layout.on('dashlet:collapse', this.loadFamilyTree, this);
        app.events.on('dnbcompinfo:duns_selected', this.collapseDashlet, this);
    },

    loadData: function(options) {
        if (this.model.get('duns_num'))
            this.duns_num = this.model.get('duns_num');
    },

    /**
     * Refresh dashlet once Refresh link clicked from gear button
     * To show updated contact information from DNB service
     */
    refreshClicked: function() {
        this.loadFamilyTree(false);
    },

    /**
     * Handles the dashlet expand | collapse events
     * @param  {Boolean} isCollapsed
     */
    loadFamilyTree: function(isCollapsed) {
        if (!isCollapsed) {
            //check if account is linked with a D-U-N-S
            if (this.duns_num) {
                this.getDNBFamilyTree(this.duns_num, 'LNK_FF');
            } else if (!_.isUndefined(app.controller.context.get('dnb_temp_duns_num'))) {
                //check if D-U-N-S is set in context by refresh dashlet
                this.getDNBFamilyTree(app.controller.context.get('dnb_temp_duns_num'), 'LNK_FF');
            } else {
                this.template = app.template.get(this.name + '.dnb-no-duns');
                if (!this.disposed) {
                    this.render();
                }
            }
        }
    },

    /** Obtain family tree for a given duns_num and product code
     * @param {String} duns_num for the given company
     * @param {String} prod_code possible values are LNK_FF | LNK_UPF
     */
    getDNBFamilyTree: function(duns_num, prod_code) {
        var self = this;
        self.duns_num = duns_num;
        self.idCounter = 1;
        self.template = app.template.get(self.name);
        if (!self.disposed) {
            self.render();
            self.$('#dnb-family-tree-loading').show();
            self.$('#dnb-family-tree-details').hide();
        }
        var ftParams = {
            'duns_num' : self.duns_num,
            'prod_code' : prod_code
        };
        //check if cache has this data already
        var cacheKey = 'dnb:familytree:' + ftParams.duns_num + ':' + ftParams.prod_code;
        var cacheContent = app.cache.get(cacheKey);
        if (cacheContent) {
            var dupeCheckParams = {
                'type': 'duns',
                'apiResponse': cacheContent,
                'module': 'familytree'
            };
            this.baseDuplicateCheck(dupeCheckParams, this.renderFamilyTree);
        } else {
            var dnbFamilyTreeURL = app.api.buildURL('connector/dnb/familytree', '', {},{});
            var resultData = {'product': null, 'errmsg': null};
            app.api.call('create', dnbFamilyTreeURL, {'qdata': ftParams},{
                success: function(data) {
                    var responseCode = self.getJsonNode(data, self.appendSVCPaths.responseCode),
                        responseMsg = self.getJsonNode(data, self.appendSVCPaths.responseMsg);
                    if (responseCode && responseCode === self.responseCodes.success) {
                        resultData.product = data;
                        self.currentFT = resultData;
                        app.cache.set(cacheKey, data);
                    } else {
                        resultData.errmsg = responseMsg || app.lang.get('LBL_DNB_SVC_ERR');
                    }
                    self.renderFamilyTree(resultData);
                },
                error: _.bind(self.checkAndProcessError, self)
            });
        }
    },

    /**
     * Back to Family Tree
     */
    backToFamilyTree: function() {
        if (this.currentFT) {
            this.renderFamilyTree(this.currentFT);
        }
    },

    /**
     * Convert Family Response to JSTree Plugin Format
     * @param {Object} data
     * @return {Object}
     */
    dnbToJSTree: function(data) {
        var jsTreeData = {};
        jsTreeData.data = [];
        var jsonPath = 'OrderProductResponse.OrderProductResponseDetail.Product.Organization';
        if (this.checkJsonNode(data, jsonPath)) {
            jsTreeData.data.push(this.getDataRecursive(data.OrderProductResponse.OrderProductResponseDetail.Product.Organization));
        }
        return jsTreeData;
    },

    /**
     * Recursively nested family trees and convert them to js tree plugin JSON data format
     * @param {Object} data
     * @return {Object}
     */
    getDataRecursive: function(data) {
        var intermediateData = {};
        var orgNamePath = 'OrganizationName.OrganizationPrimaryName.OrganizationName.$';
        var cityNamePath = 'Location.PrimaryAddress.PrimaryTownName';
        var countryNamePath = 'Location.PrimaryAddress.CountryISOAlpha2Code';
        var stateNamePath = 'Location.PrimaryAddress.TerritoryOfficialName';
        var dunsPath = 'SubjectHeader.DUNSNumber';
        var childrenPath = 'Linkage.FamilyTreeMemberOrganization';
        var orgName = this.checkJsonNode(data, orgNamePath) ? data.OrganizationName.OrganizationPrimaryName.OrganizationName['$'] : '';
        var dunsNum = this.checkJsonNode(data, dunsPath) ? data.SubjectHeader.DUNSNumber : '';
        var countryName = this.checkJsonNode(data, countryNamePath) ? data.Location.PrimaryAddress.CountryISOAlpha2Code : '';
        var stateName = this.checkJsonNode(data, stateNamePath) ? data.Location.PrimaryAddress.TerritoryOfficialName : '';
        var cityName = this.checkJsonNode(data, cityNamePath) ? data.Location.PrimaryAddress.PrimaryTownName : '';
        var dunsHTML = '&nbsp;&nbsp;<span class="label label-success pull-right">' + app.lang.get('LBL_DNB_DUNS') + '</span>',
            duplicateHTML = '&nbsp;&nbsp;<span class="label label-important pull-right">' + app.lang.get('LBL_DNB_DUPLICATE') + '</span>';
        intermediateData.metadata = {'id' : this.idCounter};
        intermediateData.attr = {'id' : this.idCounter, 'duns': dunsNum};
        this.idCounter++;
        intermediateData.data = orgName + ((stateName != '' && stateName != null) ? (', ' + stateName) : '')
            + (countryName != '' ? (', ' + countryName) : '');
        if (parseInt(dunsNum, 10) == parseInt(this.duns_num, 10)) {
            intermediateData.data = intermediateData.data + dunsHTML;
            intermediateData.state = 'open';
            this.initialSelect = [1, intermediateData.metadata.id];
            this.initialOpen = [1, intermediateData.metadata.id];
        } else if (data.isDupe) {
            intermediateData.data = intermediateData.data + duplicateHTML;
        }
        if (intermediateData.metadata.id === 1) {
            intermediateData.state = 'open';
        }
        if (this.checkJsonNode(data, childrenPath) && data.Linkage.FamilyTreeMemberOrganization.length > 0) {
            var childRootData = data.Linkage.FamilyTreeMemberOrganization;
            //for each child do a getDataRecursive
            intermediateData.children = _.map(childRootData, this.getDataRecursive, this);
        }
        return intermediateData;
    },

    /**
     *  renders the family tree using the jsTree plugin
     *  @param {Object} familyTreeData -- dnb api response for family tree call
     */
    renderFamilyTree: function(familyTreeData) {
        if (this.disposed) {
            return;
        }
        var self = this;
        self.template = app.template.get(self.name);
        self.render();
        if (!familyTreeData.errmsg && familyTreeData.product) {
            self.$('#dnb-family-tree').jstree({
                // generating tree from json data
                'json_data' : self.dnbToJSTree(familyTreeData.product),
                // plugins used for this tree
                'plugins' : ['json_data', 'ui', 'types'],
                'core' : {
                    'html_titles' : true
                }
            }).bind('loaded.jstree', function() {
                // do stuff when tree is loaded
                self.$('#dnb-family-tree').addClass('jstree-sugar');
                self.$('#dnb-family-tree > ul').addClass('list');
                self.$('#dnb-family-tree > ul > li > a').addClass('jstree-clicked');
            }).bind('select_node.jstree', function (e, data) {
                // do stuff when a node is selected
                if (data.rslt.e.target.getAttribute('href')) {
                    var duns_num = data.rslt.obj.attr('duns');
                    if (duns_num) {
                        self.getCompanyDetails(duns_num);
                    }
                } else {
                    data.inst.toggle_node(data.rslt.obj);
                }
            });
        } else if (familyTreeData.errmsg) {
            self.dnbFamilyTree = {};
            self.dnbFamilyTree.errmsg = familyTreeData.errmsg;
            self.render();
        }
        self.$('#dnb-family-tree-loading').hide();
        self.$('#dnb-family-tree-details').show();
        //hide import button when rendering the list
        if (self.layout.getComponent('dashlet-toolbar').getField('import_dnb_data')) {
            self.layout.getComponent('dashlet-toolbar').getField('import_dnb_data').getFieldElement().hide();
        }
    },

    /**
     * Gets D&B Company Details For A DUNS number
     * @param {String } duns_num
     */
    getCompanyDetails: function(duns_num) {
        if (this.disposed) {
            return;
        }
        this.template = app.template.get('dnb.dnb-company-details');
        this.render();
        this.$('div#dnb-company-details').hide();
        this.baseCompanyInformation(duns_num, this.compInfoProdCD.std, app.lang.get('LBL_DNB_FAMILY_TREE_BACK'), this.renderCompanyDetails);
    }
})
