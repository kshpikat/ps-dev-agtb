(function(app) {
    var base_metadata = {
        _hash: '',
        "modules": {
            "Login": {
                "fields": {
                    "username": {
                        "name": "username",
                        "type": "varchar",
                        "required": true
                    },
                    "password": {
                        "name": "password",
                        "type": "password",
                        "required": true
                    }
                },
                "views": {
                    "loginView": {
                        "meta": {
                            "buttons": [
                                {
                                    name: "login_button",
                                    type: "button",
                                    label: "Login",
                                    class: "login-submit",
                                    value: "login",
                                    primary: true,
                                    events: {
                                        click: "function(){ var self = this; " +
                                            "if(this.model.isValid()) {" +
                                            "$('#content').hide(); " +
                                            "app.alert.show('login', {level:'process', title:'Loading', autoclose:false}); " +
                                            "var args={password:this.model.get(\"password\"), username:this.model.get(\"username\")}; " +
                                            "this.app.login(args, null, {error:function(){ app.alert.dismiss('login'); $('#content').show();" +
                                            "console.log(\"login failed!\");},  success:" +
                                            "function(){console.log(\"logged in successfully!\"); $(\".navbar\").show();" +
                                            "$(\"body\").attr(\"id\", \"\"); var app = self.app; " +
                                            "app.events.on('app:sync:complete', function() { " +
                                            "app.alert.dismiss('login'); $('#content').show();" +
                                            "}); " +
                                            "app.sync(" +
                                            "function(){console.log(\"sync success firing\");}); }" +
                                            "});" +
                                            "}" +
                                            "}"
                                    }
                                },
                                {
                                    name: "signup_button",
                                    type: "button",
                                    label: "Signup",
                                    value: "signup",
                                    class: 'pull-left',
                                    events: {
                                        click: "function(){ var self = this; " +
                                            "app.router.signup();" +
                                            "}"
                                    }
                                }
                            ],
                            "panels": [
                                {
                                    "label": "Login",
                                    "fields": [
                                        {name: "username", label: "Username"},
                                        {name: "password", label: "Password"}
                                    ]
                                }
                            ]
                        }
                    }
                },
                "layouts": {
                    "login": {
                        "meta": {
                            //Default layout is a single view
                            "type": "simple",
                            "components": [
                                {view: "loginView"}
                            ]
                        }
                    },
                    "signup": {
                        "meta": {
                            //Default layout is a single view
                            "type": "simple",
                            "components": [
                                {view: "signupView"}
                            ]
                        }
                    }
                }
            },
            "Signup" : {
                "fields" : {
                    "first_name": {
                        "name": "first_name",
                        "type": "varchar",
                        "required": true
                    },
                    "last_name": {
                        "name": "last_name",
                        "type": "varchar",
                        "required": true
                    },
                    "email": {
                        "name": "email",
                        "type": "email",
                        "required": true
                    },
                    "phone_work": {
                        "name": "phone_work",
                        "type": "phone"
                    },
                    "state": {
                        "name": "state",
                        "type": "enum",
                        "options": "state_dom"
                    },
                    "country": {
                        "name": "country",
                        "type": "enum",
                        "options": "countries_dom",
                        "required": true
                    },
                    "company": {
                        "name": "company",
                        "type": "text",
                        "required": true
                    },
                    "jobtitle": {
                        "name": "jobtitle",
                        "type": "text"
                    },
                    "hr1": {
                        "name": "hr1",
                        "type": "hr"
                    }
                },
                "views": {
                    "signupView": {
                        "meta": {
                            "buttons": [
                                {
                                    name: "signup_button",
                                    type: "button",
                                    label: "Sign up",
                                    value: "signup",
                                    primary: true,
                                    events: {
                                        click: "" +
                                            "function(){ var self = this; " +
                                            "   if(this.model.isValid()) {" +
                                            "   $('#content').hide(); " +
                                            "   app.alert.show('signup', {level:'process', title:'Registering', autoclose:false}); " +
                                            "   var contactData={" +
                                            "       first_name:this.model.get(\"first_name\"), " +
                                            "       last_name:this.model.get(\"last_name\")," +
                                            "       email:this.model.get(\"email\")," +
                                            "       phone_work:this.model.get(\"phone_work\")," +
                                            "       state:this.model.get(\"state\")," +
                                            "       country:this.model.get(\"country\")," +
                                            "       company:this.model.get(\"company\")," +
                                            "       jobtitle:this.model.get(\"jobtitle\")" +
                                            "   }; " +
                                            "   this.app.api.signup(contactData, null, " +
                                            "   {" +
                                            "       error:function(){ app.alert.dismiss('signup'); $('#content').show(); },  " +
                                            "       success:function(){" +
                                            "           app.alert.dismiss('signup');" +
                                            "           $(\".modal-footer\").hide();" +
                                            "           $(\".modal-body\").html('<div class=\"alert alert-success tleft\">" +
                                            "               <p><strong>Thank you for signing up!</strong></p><p>" +
                                            "               A customer service representative will contact you shortly to configure your account.</p>" +
                                            "               </div>" +
                                            "           ');" +
                                            "           $('#content').show();" +
                                            "       }" +
                                            "   });" +
                                            "   }" +
                                            "}"
                                    }
                                },
                                {
                                    name: "cancel_button",
                                    type: "button",
                                    label: "Cancel",
                                    value: "signup",
                                    primary: false,
                                    events: {
                                        click: "function(){" +
                                            "app.router.login();" +
                                            "}"
                                    }
                                }
                            ],
                            "panels": [
                                {
                                    "label": "Login",
                                    "fields": [
                                        {name: "first_name", label: "First name"},
                                        {name: "last_name", label: "Last name"},
                                        {name: "hr1", label: ""},
                                        {name: "email", label: "Email"},
                                        {name: "phone_work", label: "(###) ###-#### (optional)"},
                                        {name: "country", label: "Country"},
                                        {name: "state", label: "State"},
                                        {name: "hr1", label: ""},
                                        {name: "company", label: "Company"},
                                        {name: "jobtitle", label: "Job title (optional)"}
                                    ]
                                }
                            ]
                        },
                        controller: "{" +
                            "stateField: function() { return this.$el.find('select[name=state]'); }," +
                            "countryField: function() { return this.$el.find('select[name=country]'); }," +
                            "toggleStateField: function() {" +
                            "if (this.countryField().val()=='USA') {" +
                            "this.stateField().parent().show();" +
                            "} else {" +
                            "this.stateField().parent().hide();" +
                            "this.context.attributes.model.attributes.state = undefined;" +
                            "}" +
                            "}," +
                            "render: function(data) { " +
                            "var that  = this;" +
                            "app.view.View.prototype.render.call(this);" +
                            "that.toggleStateField();" +
                            "this.countryField().on(\"change\", function(ev) { that.render(); });" +
                            "return this;" +
                            "}" +
                            "}"
                    }
                },
                //Layouts map an action to a lyout that defines a set of views and how to display them
                //Different clients will get different layouts for the same actions
                "layouts": {
                    "signup": {
                        "meta": {
                            //Default layout is a single view
                            "type": "simple",
                            "components": [
                                {view: "signupView"}
                            ]
                        }
                    }
                }
            }
        },
        'sugarFields': {
            "text": {
                "views": {
                    "loginView": "<div class=\"control-group\"><label class=\"hide\">{{label}}<\/label> " +
                        "<div class=\"controls\">\n" +
                        "<input type=\"text\" class=\"center\" value=\"{{value}}\" placeholder=\"{{label}}\"></div>  <p class=\"help-block\">" +
                        "<\/p> <\/div>",
                    "signupView": "<div class=\"control-group\"><label class=\"hide\">{{label}}<\/label> " +
                        "<div class=\"controls\">\n" +
                        "<input type=\"text\" class=\"center\" value=\"{{value}}\" placeholder=\"{{label}}\"></div>  <p class=\"help-block\">" +
                        "<\/p> <\/div>"
                },
                controller: "{" +
                    "render : function(){" +
                    "this.app.view.Field.prototype.render.call(this);" +
                    "if (!SUGAR.App.api.isAuthenticated()) { $(\".navbar\").hide(); $(\"body\").attr(\"id\", \"portal\"); }" +
                    "}}"
            },
            "password": {
                "views": {
                    "loginView": "<div class=\"control-group\">" +
                        "<label class=\"hide\">{{label}}</label>" +
                        "<div class=\"controls\">\n" +
                        "<input type=\"password\" class=\"center\" value=\"{{value}}\" placeholder=\"{{label}}\">\n  <\/div>\n" +
                        "<p class=\"help-block\"><a href=\"#\" rel=\"popoverTop\" data-content=\"You need to contact your Sugar Admin to reset your password.\" data-original-title=\"Forgot Your Password?\">Forgot password?</a></p>" +
                        "</div>"
                }
            },
            "button": {
                "views": {
                    "default": "<a href=\"{{#if route}}#{{buildRoute context model route.action route.options}}" +
                        "{{else}}javascript:void(0){{/if}}\" class=\"btn {{class}} {{#if primary}}btn-primary{{/if}}\">" +
                        "{{#if icon}}<i class=\"{{icon}}\"><\/i>{{/if}}{{label}}<\/a>\n"
                }
            },
            "hr": {
                "views": {
                    "default": "<hr>\n"
                }
            },
            "enum": {
                "views": {
                    "signupView": "<div class=\"control-group\"><label class=\"hide\" for=\"input01\">{{label}}<\/label> " +
                        "<select data-placeholder=\"{{label}}\" name=\"{{name}}\">{{#eachOptions fieldDef.options}}<option value=\"{{{this.key}}}\" {{#has this.key ../value}}selected{{/has}}>{{this.value}}</option>{{/eachOptions}}</select>  <p class=\"help-block\">" +
                        "<\/p> <\/div>",
                    "default": ""
                },
                controller: "{" +
                    "fieldTag:\"select\",\n" +
                    "render:function(){" +
                    "   var result = this.app.view.Field.prototype.render.call(this);" +
                //    "   this.$(this.fieldType + \"[name=\" + this.name + \"]\").chosen();" +
                    "   return result;" +
                    "}\n" +
                    "}"
            }
        },
        'viewTemplates': {
            "loginView": "<form name='{{name}}'>" +
                "<div class=\"container welcome\">\n" +
                "<div class=\"row\">\n" +
                "<div class=\"span4 offset4 thumbnail\">\n" +
                "<div class=\"modal-header tcenter\">\n" +
                "<h2 class=\"brand\">SugarCRM</h2>\n" +
                "</div>\n" +
                "{{#each meta.panels}}" +
                "<div class=\"modal-body tcenter\">\n" +
                "{{#each fields}}\n" +
                "<div>{{field ../../context ../../this ../../model}}</div>" +
                "{{/each}}" +
                "</div>          \n" +
                "{{/each}}" +
                "<div class=\"modal-footer\">\n" +
                "{{#each meta.buttons}}" +
                "{{field ../context ../this ../model}}" +
                "{{/each}}" +
                "</div>\n" +
                "</div>                             \n" +
                "</div>\n" +
                "</div>         \n" +
                "</form>",
            "header": "<div class=\"navbar navbar-fixed-top\">\n    <div class=\"navbar-inner\">\n      <div class=\"container-fluid\">\n        <a class=\"cube\" href=\"#\" rel=\"tooltip\" data-original-title=\"Dashboard\"></a>\n        <div class=\"nav-collapse\">\n          <ul class=\"nav\" id=\"moduleList\">\n              {{#each moduleList}}\n              <li {{#eq this ../currentModule}}class=\"active\"{{/eq}}>\n                <a href=\"#{{this}}\">{{this}}</a>\n              </li>\n              {{/each}}\n          </ul>\n          <ul class=\"nav pull-right\" id=\"userList\">\n            <li class=\"divider-vertical\"></li>\n            <li class=\"dropdown\">\n              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">Current User <b class=\"caret\"></b></a>\n              <ul class=\"dropdown-menu\">\n                <li><a href=\"#logout\">Log Out</a></li>\n              </ul>\n            </li>\n            <li class=\"divider-vertical\"></li>\n     <li class=\"dropdown\" id=\"createList\">\n              <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"icon-plus icon-md\"></i> <b class=\"caret\"></b></a>\n              <ul class=\"dropdown-menu\">\n                  {{#each createListLabels}}\n                                <li>\n                                  <a href=\"#{{this.module}}/create\">{{this.label}}</a>\n                                </li>\n                                {{/each}}\n              </ul>\n            </li>\n          </ul>\n          <div id=\"searchForm\">\n            <form class=\"navbar-search pull-right\" action=\"\">\n              <input type=\"text\" class=\"search-query span3\" placeholder=\"Search\" data-provide=\"typeahead\" data-items=\"10\" >\n              <a href=\"\" class=\"btn\"><i class=\"icon-search\"></i></a>\n                <a href=\"#adminSearch\" class=\"pull-right advanced\" data-toggle=\"modal\" rel=\"tooltip\" title=\"Advanced Search Options\" id=\"searchAdvanced\"><i class=\"icon-cog\"></i></a>\n            </form>\n\n          </div>\n        </div><!-- /.nav-collapse -->\n      </div>\n    </div><!-- /navbar-inner -->\n  </div>",
            "signupView": "<form name='{{name}}'>" +
                "<div class=\"container welcome\">\n" +
                "<div class=\"row\">\n" +
                "<div class=\"span4 offset4 thumbnail\">\n" +
                "<div class=\"modal-header tcenter\">\n" +
                "<h2 class=\"brand\">SugarCRM</h2>\n" +
                "</div>\n" +
                "{{#each meta.panels}}" +
                "<div class=\"modal-body tcenter\">\n" +
                "{{#each fields}}\n" +
                "{{field ../../context ../../this ../../model}}" +
                "{{/each}}" +
                "</div>          \n" +
                "{{/each}}" +
                "<div class=\"modal-footer\">\n" +
                "{{#each meta.buttons}}" +
                "{{field ../context ../this ../model}}" +
                "{{/each}}" +
                "</div>\n" +
                "</div>                             \n" +
                "</div>\n" +
                "</div>         \n" +
                "</form>",
            "subnav": "<div class=\"subnav\">" +
                "<div class=\"btn-toolbar pull-left\">" +
                "<h1>{{fieldWithName context this null \"name\"}}</h1>" +
                "</div>" +
                "<div class=\"btn-toolbar pull-right\">" +
                "<div class=\"btn-group\">" +
                "{{#each meta.buttons}}" +
                "{{field ../context ../this ../model}}  " +
                "{{/each}}" +
                "</div>" +
                "</div>" +
                "</div>"
        },
        "appListStrings": {
            "state_dom": {
                "AL": "Alabama",
                "AK": "Alaska",
                "AZ": "Arizona",
                "AR": "Arkansas",
                "CA": "California",
                "CO": "Colorado",
                "CT": "Connecticut",
                "DE": "Delaware",
                "DC": "District Of Columbia",
                "FL": "Florida",
                "GA": "Georgia",
                "HI": "Hawaii",
                "ID": "Idaho",
                "IL": "Illinois",
                "IN": "Indiana",
                "IA": "Iowa",
                "KS": "Kansas",
                "KY": "Kentucky",
                "LA": "Louisiana",
                "ME": "Maine",
                "MD": "Maryland",
                "MA": "Massachusetts",
                "MI": "Michigan",
                "MN": "Minnesota",
                "MS": "Mississippi",
                "MO": "Missouri",
                "MT": "Montana",
                "NE": "Nebraska",
                "NV": "Nevada",
                "NH": "New Hampshire",
                "NJ": "New Jersey",
                "NM": "New Mexico",
                "NY": "New York",
                "NC": "North Carolina",
                "ND": "North Dakota",
                "OH": "Ohio",
                "OK": "Oklahoma",
                "OR": "Oregon",
                "PA": "Pennsylvania",
                "RI": "Rhode Island",
                "SC": "South Carolina",
                "SD": "South Dakota",
                "TN": "Tennessee",
                "TX": "Texas",
                "UT": "Utah",
                "VT": "Vermont",
                "VA": "Virginia ",
                "WA": "Washington",
                "WV": "West Virginia",
                "WI": "Wisconsin",
                "WY": "Wyoming"
            },
            "countries_dom": {
                ABU_DHABI: "ABU DHABI",
                ADEN: "ADEN",
                AFGHANISTAN: "AFGHANISTAN",
                ALBANIA: "ALBANIA",
                ALGERIA: "ALGERIA",
                AMERICAN_SAMOA: "AMERICAN SAMOA",
                ANDORRA: "ANDORRA",
                ANGOLA: "ANGOLA",
                ANTARCTICA: "ANTARCTICA",
                ANTIGUA: "ANTIGUA",
                ARGENTINA: "ARGENTINA",
                ARMENIA: "ARMENIA",
                ARUBA: "ARUBA",
                AUSTRALIA: "AUSTRALIA",
                AUSTRIA: "AUSTRIA",
                AZERBAIJAN: "AZERBAIJAN",
                BAHAMAS: "BAHAMAS",
                BAHRAIN: "BAHRAIN",
                BANGLADESH: "BANGLADESH",
                BARBADOS: "BARBADOS",
                BELARUS: "BELARUS",
                BELGIUM: "BELGIUM",
                BELIZE: "BELIZE",
                BENIN: "BENIN",
                BERMUDA: "BERMUDA",
                BHUTAN: "BHUTAN",
                BOLIVIA: "BOLIVIA",
                BOSNIA: "BOSNIA",
                BOTSWANA: "BOTSWANA",
                BOUVET_ISLAND: "BOUVET ISLAND",
                BRAZIL: "BRAZIL",
                BRITISH_ANTARCTICA_TERRITORY: "BRITISH ANTARCTICA TERRITORY",
                BRITISH_INDIAN_OCEAN_TERRITORY: "BRITISH INDIAN OCEAN TERRITORY",
                BRITISH_VIRGIN_ISLANDS: "BRITISH VIRGIN ISLANDS",
                BRITISH_WEST_INDIES: "BRITISH WEST INDIES",
                BRUNEI: "BRUNEI",
                BULGARIA: "BULGARIA",
                BURKINA_FASO: "BURKINA FASO",
                BURUNDI: "BURUNDI",
                CAMBODIA: "CAMBODIA",
                CAMEROON: "CAMEROON",
                CANADA: "CANADA",
                CANAL_ZONE: "CANAL ZONE",
                CANARY_ISLAND: "CANARY ISLAND",
                CAPE_VERDI_ISLANDS: "CAPE VERDI ISLANDS",
                CAYMAN_ISLANDS: "CAYMAN ISLANDS",
                CEVLON: "CEVLON",
                CHAD: "CHAD",
                CHANNEL_ISLAND_UK: "CHANNEL ISLAND UK",
                CHILE: "CHILE",
                CHINA: "CHINA",
                CHRISTMAS_ISLAND: "CHRISTMAS ISLAND",
                COCOS_KEELING_ISLAND: "COCOS (KEELING) ISLAND",
                COLOMBIA: "COLOMBIA",
                COMORO_ISLANDS: "COMORO ISLANDS",
                CONGO: "CONGO",
                CONGO_KINSHASA: "CONGO KINSHASA",
                COOK_ISLANDS: "COOK ISLANDS",
                COSTA_RICA: "COSTA RICA",
                CROATIA: "CROATIA",
                CUBA: "CUBA",
                CURACAO: "CURACAO",
                CYPRUS: "CYPRUS",
                CZECH_REPUBLIC: "CZECH REPUBLIC",
                DAHOMEY: "DAHOMEY",
                DENMARK: "DENMARK",
                DJIBOUTI: "DJIBOUTI",
                DOMINICA: "DOMINICA",
                DOMINICAN_REPUBLIC: "DOMINICAN REPUBLIC",
                DUBAI: "DUBAI",
                ECUADOR: "ECUADOR",
                EGYPT: "EGYPT",
                EL_SALVADOR: "EL SALVADOR",
                EQUATORIAL_GUINEA: "EQUATORIAL GUINEA",
                ESTONIA: "ESTONIA",
                ETHIOPIA: "ETHIOPIA",
                FAEROE_ISLANDS: "FAEROE ISLANDS",
                FALKLAND_ISLANDS: "FALKLAND ISLANDS",
                FIJI: "FIJI",
                FINLAND: "FINLAND",
                FRANCE: "FRANCE",
                FRENCH_GUIANA: "FRENCH GUIANA",
                FRENCH_POLYNESIA: "FRENCH POLYNESIA",
                GABON: "GABON",
                GAMBIA: "GAMBIA",
                GEORGIA: "GEORGIA",
                GERMANY: "GERMANY",
                GHANA: "GHANA",
                GIBRALTAR: "GIBRALTAR",
                GREECE: "GREECE",
                GREENLAND: "GREENLAND",
                GUADELOUPE: "GUADELOUPE",
                GUAM: "GUAM",
                GUATEMALA: "GUATEMALA",
                GUINEA: "GUINEA",
                GUYANA: "GUYANA",
                HAITI: "HAITI",
                HONDURAS: "HONDURAS",
                HONG_KONG: "HONG KONG",
                HUNGARY: "HUNGARY",
                ICELAND: "ICELAND",
                IFNI: "IFNI",
                INDIA: "INDIA",
                INDONESIA: "INDONESIA",
                IRAN: "IRAN",
                IRAQ: "IRAQ",
                IRELAND: "IRELAND",
                ISRAEL: "ISRAEL",
                ITALY: "ITALY",
                IVORY_COAST: "IVORY COAST",
                JAMAICA: "JAMAICA",
                JAPAN: "JAPAN",
                JORDAN: "JORDAN",
                KAZAKHSTAN: "KAZAKHSTAN",
                KENYA: "KENYA",
                KOREA: "KOREA",
                KOREA_SOUTH: "KOREA, SOUTH",
                KUWAIT: "KUWAIT",
                KYRGYZSTAN: "KYRGYZSTAN",
                LAOS: "LAOS",
                LATVIA: "LATVIA",
                LEBANON: "LEBANON",
                LEEWARD_ISLANDS: "LEEWARD ISLANDS",
                LESOTHO: "LESOTHO",
                LIBYA: "LIBYA",
                LIECHTENSTEIN: "LIECHTENSTEIN",
                LITHUANIA: "LITHUANIA",
                LUXEMBOURG: "LUXEMBOURG",
                MACAO: "MACAO",
                MACEDONIA: "MACEDONIA",
                MADAGASCAR: "MADAGASCAR",
                MALAWI: "MALAWI",
                MALAYSIA: "MALAYSIA",
                MALDIVES: "MALDIVES",
                MALI: "MALI",
                MALTA: "MALTA",
                MARTINIQUE: "MARTINIQUE",
                MAURITANIA: "MAURITANIA",
                MAURITIUS: "MAURITIUS",
                MELANESIA: "MELANESIA",
                MEXICO: "MEXICO",
                MOLDOVIA: "MOLDOVIA",
                MONACO: "MONACO",
                MONGOLIA: "MONGOLIA",
                MOROCCO: "MOROCCO",
                MOZAMBIQUE: "MOZAMBIQUE",
                MYANAMAR: "MYANAMAR",
                NAMIBIA: "NAMIBIA",
                NEPAL: "NEPAL",
                NETHERLANDS: "NETHERLANDS",
                NETHERLANDS_ANTILLES: "NETHERLANDS ANTILLES",
                NETHERLANDS_ANTILLES_NEUTRAL_ZONE: "NETHERLANDS ANTILLES NEUTRAL ZONE",
                NEW_CALADONIA: "NEW CALADONIA",
                NEW_HEBRIDES: "NEW HEBRIDES",
                NEW_ZEALAND: "NEW ZEALAND",
                NICARAGUA: "NICARAGUA",
                NIGER: "NIGER",
                NIGERIA: "NIGERIA",
                NORFOLK_ISLAND: "NORFOLK ISLAND",
                NORWAY: "NORWAY",
                OMAN: "OMAN",
                OTHER: "OTHER",
                PACIFIC_ISLAND: "PACIFIC ISLAND",
                PAKISTAN: "PAKISTAN",
                PANAMA: "PANAMA",
                PAPUA_NEW_GUINEA: "PAPUA NEW GUINEA",
                PARAGUAY: "PARAGUAY",
                PERU: "PERU",
                PHILIPPINES: "PHILIPPINES",
                POLAND: "POLAND",
                PORTUGAL: "PORTUGAL",
                PORTUGUESE_TIMOR: "PORTUGUESE TIMOR",
                PUERTO_RICO: "PUERTO RICO",
                QATAR: "QATAR",
                REPUBLIC_OF_BELARUS: "REPUBLIC OF BELARUS",
                REPUBLIC_OF_SOUTH_AFRICA: "REPUBLIC OF SOUTH AFRICA",
                REUNION: "REUNION",
                ROMANIA: "ROMANIA",
                RUSSIA: "RUSSIA",
                RWANDA: "RWANDA",
                RYUKYU_ISLANDS: "RYUKYU ISLANDS",
                SABAH: "SABAH",
                SAN_MARINO: "SAN MARINO",
                SAUDI_ARABIA: "SAUDI ARABIA",
                SENEGAL: "SENEGAL",
                SERBIA: "SERBIA",
                SEYCHELLES: "SEYCHELLES",
                SIERRA_LEONE: "SIERRA LEONE",
                SINGAPORE: "SINGAPORE",
                SLOVAKIA: "SLOVAKIA",
                SLOVENIA: "SLOVENIA",
                SOMALILIAND: "SOMALILIAND",
                SOUTH_AFRICA: "SOUTH AFRICA",
                SOUTH_YEMEN: "SOUTH YEMEN",
                SPAIN: "SPAIN",
                SPANISH_SAHARA: "SPANISH SAHARA",
                SRI_LANKA: "SRI LANKA",
                ST_KITTS_AND_NEVIS: "ST. KITTS AND NEVIS",
                ST_LUCIA: "ST. LUCIA",
                SUDAN: "SUDAN",
                SURINAM: "SURINAM",
                SW_AFRICA: "SW AFRICA",
                SWAZILAND: "SWAZILAND",
                SWEDEN: "SWEDEN",
                SWITZERLAND: "SWITZERLAND",
                SYRIA: "SYRIA",
                TAIWAN: "TAIWAN",
                TAJIKISTAN: "TAJIKISTAN",
                TANZANIA: "TANZANIA",
                THAILAND: "THAILAND",
                TONGA: "TONGA",
                TRINIDAD: "TRINIDAD",
                TUNISIA: "TUNISIA",
                TURKEY: "TURKEY",
                UGANDA: "UGANDA",
                UKRAINE: "UKRAINE",
                UNITED_ARAB_EMIRATES: "UNITED ARAB EMIRATES",
                UNITED_KINGDOM: "UNITED KINGDOM",
                UPPER_VOLTA: "UPPER VOLTA",
                URUGUAY: "URUGUAY",
                US_PACIFIC_ISLAND: "US PACIFIC ISLAND",
                US_VIRGIN_ISLANDS: "US VIRGIN ISLANDS",
                USA: "USA",
                UZBEKISTAN: "UZBEKISTAN",
                VANUATU: "VANUATU",
                VATICAN_CITY: "VATICAN CITY",
                VENEZUELA: "VENEZUELA",
                VIETNAM: "VIETNAM",
                WAKE_ISLAND: "WAKE ISLAND",
                WEST_INDIES: "WEST INDIES",
                WESTERN_SAHARA: "WESTERN SAHARA",
                YEMEN: "YEMEN",
                ZAIRE: "ZAIRE",
                ZAMBIA: "ZAMBIA",
                ZIMBABWE: "ZIMBABWE"
            }
        },
        "appStrings": {
            ERROR_FIELD_REQUIRED: "Error. This field is required."
        }
    };
    app.events.on("app:init", function() {
        app.metadata.set(base_metadata);
        app.data.declareModels(base_metadata);
    });

    app.view.Field=app.view.Field.extend({
        /**
         * Handles how validation errors are appended to the fields dom element
         *
         * By default errors are appended to the dom into a .help-block class if present
         * and the .error class is added to any .control-group elements in accordance with
         * bootstrap.
         *
         * @param {Object} errors hash of validation errors
         */
        handleValidationError: function(errors) {
            var self = this;
            this.$('.control-group').addClass("error");
            this.$('.help-block').html("");

            // For each error add to error help block
            this.$('.controls').addClass('input-append');
            _.each(errors, function(errorContext, errorName) {
                self.$('.help-block').append(app.error.getErrorString(errorName,errorContext));
            });

            // Remove previous exclamation then add back.
            this.$('.add-on').remove();
            this.$('.controls').find('input').after('<span class="add-on"><i class="icon-exclamation-sign"></i></span>');
        }
    });

})(SUGAR.App);



