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
({
    /**
     * Attach a Change event to the field
     */
    events: { 'change' : 'bucketsChanged' },   
    
    /**
     * flag for if the field should render disabled
     */
    disabled: false,
    
    langValue: "",
    
    /**
     * Initialize
     */
    initialize: function(options){
        app.view.Field.prototype.initialize.call(this, options);
        var forecastRanges = this.context.forecasts.config.get("forecast_ranges");
        
        //Check to see if you're a manager on someone else's sheet, disable changes
        if(this.context.forecasts.get("selectedUser")["id"] != app.user.id){
            this.disabled = true;
        }
        
        //show_binary, show_buckets, show_n_buckets logic
        if(forecastRanges == "show_binary"){
            //If we're in binary mode
            this.def.view = "bool";
            this.format = function(value){
                return value == "include";
            };
            this.unformat = function(value){
                return this.$el.find(".checkbox").prop('checked') ? "include" : "exclude";
            };
        }
        else if(forecastRanges == "show_buckets"){
            //Show buckets, but only if we are on our sheet.
            if(!this.disabled){
                this.def.view = "enum";
                this.createBuckets();
            }
            else{
                this.def.view = "default";
                this.getLanguageValue();
            }
        }
    },
    
    /**
     * Render Field
     */
    _render:function () {
        app.view.Field.prototype._render.call(this);
        
        //If we are on our own sheet, and need to show the dropdown, init things
        if(!this.disabled && this.def.view == "enum"){
            this.$el.find("option[value=" + this.value + "]").attr("selected", "selected");
            this.$el.find("select").chosen();
        }
    },
    
    /**
     * Change handler for the buckets field
     */
    bucketsChanged: function(){
        var self = this,
            values = {};
        
        if(self.def.view == "bool"){
            self.value = self.unformat();
            values[self.name] = self.value;
        }

        self.model.set(values);
    },
    
    /**
     * Creates the HTML for the bucket selectors
     * 
     * This function is used to create the select tag for the buckets.  For performance reasons, we only want
     * to iterate over the option list once, so we do that here and store it as a jQuery data element on the Body tag.
     * Also, we check to make sure this hasn't already been done (so we don't do it again, of course).
     */
    createBuckets: function(){
        var self = this;
        self.buckets = $.data(document.body, "buckets");
        
        if(_.isUndefined(self.buckets)){
            var options = app.lang.getAppListStrings(this.def.options) || 'commit_stage_dom';
            self.buckets =  "<select data-placeholder=' ' name='" + self.name + "' style='width: 100px;'>";
            self.buckets +=     "<option value='' selected></option>";
            _.each(options, function(item, key){
                self.buckets += "<option value='" + key + "'>" + item + "</options>"
            });
            self.buckets += "</select>";
            $.data(document.body, "buckets", self.buckets);
        }
    },
    
    /**
     * Gets proper value of the item out of the language file.
     * 
     * If we are in buckets mode and are on a non-editable sheet, we need to display the proper value of this
     * field as determined by the language file.  This function sets the proper key in the field for the hbt to pick it up and
     * display it.
     */
    getLanguageValue: function(){
        var options = app.lang.getAppListStrings(this.def.options) || 'commit_stage_dom';
        this.langValue = options[this.model.get(this.def.name)]; 
    }
})
