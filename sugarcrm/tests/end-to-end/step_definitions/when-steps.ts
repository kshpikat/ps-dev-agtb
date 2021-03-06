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

import ModuleMenuCmp from '../components/module-menu-cmp';
import {whenStepsHelper, stepsHelper, Utils, When, seedbed} from '@sugarcrm/seedbed';
import {TableDefinition} from 'cucumber';
import RecordView from '../views/record-view';
import RecordLayout from '../layouts/record-layout';
import ListView from '../views/list-view';
import RliTableRecord from '../views/rli-table';
import SubpanelLayout from '../layouts/subpanel-layout';
import PersonalInfoDrawerLayout from '../layouts/personal-info-drawer-layout';
import {updateForecastConfig} from './steps-helper';
import AlertCmp from '../components/alert-cmp';
import {updateOpportunityConfig} from './steps-helper';
import {chooseModule, chooseRecord, closeWarning, closeAlert, parseInputArray, recordViewHeaderButtonClicks, toggleRecord} from './general_bdd';
import ActivityStream from '../layouts/activity-stream-layout';
import ImportBpmView from '../views/import-bpm-view';
import PreviewHeaderView from '../views/preview-header-view';
import LoginLayout from '../layouts/login-layout';
import QuickCreateMenuCmp from '../components/quick-create-menu-cmp';
import AdminMenuCmp from "../components/admin-menu-cmp";
import PreviewLayout from '../layouts/preview-layout';

/**
 * Select module in modules menu
 *
 * If "moduleName" is visible, it means that it can be located in main menu.
 * If not - trying to open modules dropdown menu and find this module there
 *
 * @example "I choose Accounts in modules menu"
 */
When(/^I choose (\w+) in modules menu(?: and select "([^"]*)" menu item)?$/,
    async function (moduleName: string, itemName?: string): Promise<void> {

        let moduleMenuCmp = seedbed.components['moduleMenu'] as ModuleMenuCmp;
        let isVisible = await moduleMenuCmp.isVisible(moduleName);

        if (isVisible) {
            await moduleMenuCmp.clickItem(moduleName);
        } else {
            await moduleMenuCmp.showAllModules();
            await moduleMenuCmp.clickItem(moduleName, true);
        }
        await this.driver.waitForApp();

        if (itemName) {
            await moduleMenuCmp.clickItemUnderModuleMenu(itemName);
        }

    }, {waitForApp: true});

/**
 * Select item from cached View
 */
When(/^I select (\*[a-zA-Z](?:\w|\S)*) in (#\S+)$/,
    async function (record: { id: string }, view: any) {
        let listItem = view.getListItem({id: record.id});
        await listItem.clickListItem();
    }, {waitForApp: true});

/**
 * Select item from cached View
 */
When(/^I toggle (checkbox|favorite) for (\*[a-zA-Z](?:\w|\S)*) in (#\S+)$/,
    async function (itemName, record: { id: string }, view: ListView) {
        let listItem = await view.getListItem({id: record.id});
        await listItem.clickItem(itemName);
    }, {waitForApp: true});

/**
 * Select All Records in the list view
 *
 * @example I toggleAll records in #AccountsList.ListView
 */
When(/^I toggleAll records in (#\S+)$/,
    async function (view: ListView) {
        await view.toggleAll();
    }, {waitForApp: true});


/**
 *  Select action from List view Actions dropdown
 *
 *  @example I select "Mass Update" action in #QuotesList.ListView
 */
When(/^I select "(Mass Update|Recalculate Values|Delete Selected|Export)" action in (#\S+)$/,
    async function (action: string, view: ListView) {
        await view.selectAction(action);
    }, {waitForApp: true});

/**
 * Select Generate quote or Delete mass update action in the RLI table of Opportunity record view
 *
 * @example When I select GenerateQuote action in #Opp_ARecord.SubpanelsLayout.subpanels.revenuelineitems
 */
When(/^I select (GenerateQuote|Delete) action in (#\S+)$/,
    async function (itemName, view: SubpanelLayout) {

        await view.clickMenuItem(itemName);
    }, {waitForApp: true});

/**
 * Open the preview for the record
 *
 * @example I click on preview button on *Account_A in #AccountsList.ListView
 */
When(/^I click on preview button on (\*[a-zA-Z](?:\w|\S)*) in (#\S+)$/,
    async function (record: { id: string }, view: ListView) {
        let listItem = view.getListItem({id: record.id});
        await listItem.clickPreviewButton();
    }, {waitForApp: true});


When(/^I wait for (\d+) seconds$/,
    async function (delay: string): Promise<void> {
        await whenStepsHelper.waitStep(parseInt(delay, 10));
    });

When(/^I open ([\w,\/]+) view and login$/,
    async function (module: string): Promise<void> {

        const currentUser = seedbed.userInfo.userId;
        let shouldSetDefaultPrefs = false;
        if( seedbed.userSigninMap[currentUser] === false) {
            shouldSetDefaultPrefs = true;
            seedbed.userSigninMap[currentUser] = true;
        }
        await whenStepsHelper.setUrlHashAndLogin(module, shouldSetDefaultPrefs);
        await this.driver.waitForApp();
    }, {waitForApp: true});

/**
 * Use to logout properly to account for the browser hard refresh
 */
When(/^I logout$/,
    async function () {
        await this.driver.waitForApp();
        let mainMenuCmp: AdminMenuCmp = seedbed.components.AdminMenuCmp;

        await mainMenuCmp.open();
        await this.driver.waitForApp();
        await mainMenuCmp.clickItem('logout');
        await this.driver.pause(3000);
        await this.driver.wd.waitForExist('#sidecar');
        await this.driver.pause(3000);
        await this.driver.waitForApp();
    }, {waitForApp: true});

When(/^I go to "([^"]*)" url$/,
    async function (urlHash): Promise<void> {
        await this.driver.setUrlHash(urlHash);
    }, {waitForApp: true});

When(/^I click on "([^"]*)" menu item$/,
    async function (menuItem: string): Promise<void> {
        let moduleMenuCmp = seedbed.components['moduleMenu'] as ModuleMenuCmp;
        await moduleMenuCmp.clickItemUnderModuleMenu(menuItem);
    }, {waitForApp: true});

// The step requires the view to be opened, it reformats the provided data to format valid for dynamic edit layout
When(/^I provide input for (#\S+) view$/,
    async function (view: RecordView, data: TableDefinition): Promise<void> {

        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }

        let inputData = stepsHelper.getArrayOfHashmaps(data)[0];

        // check for * marked column and cache the record and view if needed
        let uidInfo = Utils.computeRecordUID(inputData);

        seedbed.cucumber.scenario.recordsInfo[uidInfo.uid] = {
            uid: uidInfo.uid,
            originInput: JSON.parse(JSON.stringify(inputData)),
            input: inputData,
            module: view.module,
        };

        await view.setFieldsValue(inputData);

    }, {waitForApp: true});

/**
 *  Click edit button, edit record, save and close alert
 *  (Record must be opened in the record view)
 *
 *  @example:
 *  When I provide input for #DP_1Record
 *      | name | resolution                                       |
 *      | DP_2 | The request is successfully completed by Seedbed |
 */
When(/^I provide input for (#\S+)$/,
    async function (layout: RecordLayout, data: TableDefinition): Promise<void> {

        const editBtn = 'edit';
        const saveBtn = 'save';
        const showMoreBtn = 'show more';

        // Click Edit button to edit the record
        await layout.HeaderView.clickButton(editBtn);
        await this.driver.waitForApp();

        // Click 'Show More'
        await layout.showMore(showMoreBtn);
        await this.driver.waitForApp();

        // Input data validation
        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }

        let inputData = stepsHelper.getArrayOfHashmaps(data)[0];

        // check for * marked column and cache the record and view if needed
        let uidInfo = Utils.computeRecordUID(inputData);

        seedbed.cucumber.scenario.recordsInfo[uidInfo.uid] = {
            uid: uidInfo.uid,
            originInput: JSON.parse(JSON.stringify(inputData)),
            input: inputData,
            module: layout.module,
        };

        // Update Field Values
        await layout.setFieldsValue(inputData);
        await this.driver.waitForApp();

        // Click Save button
        await layout.HeaderView.clickButton(saveBtn);
        await this.driver.waitForApp();

        // Close Alert
        let alert = new AlertCmp({});
        await alert.close();
        await this.driver.waitForApp();

    }, {waitForApp: true});


// The step requires the view to be opened, it reformats the provided data to format valid for dynamic edit layout
When(/^I provide input for (#\S+) view for (\d+) row$/,
    async function (view: any, index: number, data: TableDefinition): Promise<void> {

        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }

        let inputData = stepsHelper.getArrayOfHashmaps(data)[0];

        // check for * marked column and cache the record and view if needed
        let uidInfo = Utils.computeRecordUID(inputData);

        seedbed.cucumber.scenario.recordsInfo[uidInfo.uid] = {
            uid: uidInfo.uid,
            originInput: JSON.parse(JSON.stringify(inputData)),
            input: inputData,
            module: view.module,
        };

        let rowView = view.getRowByIndex(index);

        await rowView.setFieldsValue(inputData);

    }, {waitForApp: true});

/**
 * Click 'Show More', 'Show Less', or 'More Guests' (Meetings and Calls only in case there is more than 5 guests total) button in any layout
 *
 * @example When I click more guests button on #C_1Preview view
 */
When(/^I click (show more|show less|more guests|useful|notuseful) button on (#\S+) view$/, async function (buttonName: string, layout: any) {
    await layout.showMore(buttonName);
}, {waitForApp: true});

/**
 * This step only applicable to Quotes record view which has 4 different sections: Business_Card, Billing_and_Shipping, Quote_Settings, Show_More
 *
 * @example When I toggle Billing_and_Shipping panel on #Quote_3Record.RecordView view
 */
When(/^I toggle (Business_Card|Billing_and_Shipping|Quote_Settings|Show_More) panel on (#\S+) view$/, async function (panelName: string, view: RecordView) {

    await view.togglePanel(panelName);

}, {waitForApp: true});

/**
 * Click on a list view action button
 *
 * @example I click on edit button for *Account_A in #AccountsList.ListView
 */
When(/^I click on (\w+) button for (\*[a-zA-Z](?:\w|\S)*) in (#\S+)$/,
    async function (button, record: { id }, view: ListView) {
        let listItem = view.getListItem({id: record.id});

        let isVisible = await listItem.isVisible(button);

        if (isVisible) {
            await listItem.clickListButton(button);

        } else {

            await listItem.openDropdown();
            await listItem.clickListButton(button);
        }
    }, {waitForApp: true});

/**
 * Set field values from data
 *
 * @example I set values for *Account_A in #AccountsList.ListView
 */
When(/^I set values for (\*[a-zA-Z](?:\w|\S)*) in (#\S+)$/,
    async function (record: { id: string }, view: ListView, data: TableDefinition) {
        let listItem = view.getListItem({id: record.id});

        let row: any;

        for (row of data.hashes()) {
            let field = await listItem.getField(row.fieldName);
            await field.setValue(row.value);
        }

    }, {waitForApp: true});


When(/^I click (\S+) field on (#\S+) view$/,
    async function (fieldName, layout: any) {
        let view = layout.type ? layout.defaultView : layout;
        return view.clickField(fieldName);
    }, {waitForApp: true});

When(/^I click (\S+) field on (\*[a-zA-Z](?:\w|\S)*) record in (#\S+) view$/,
    async function (fieldName: string, record: { id: string }, listView: ListView) {

        let listItem = listView.getListItem({id: record.id});

        await listItem.clickField(fieldName);

    }, {waitForApp: true});

/**
 * This step is needed when new opportunity is created through UI. Opportunity create drawer has RLI
 * section when user can add/remove RLI lines to new opportunity
 *
 * @example "I choose addRLI on #OpportunityDrawer.RLITable view for 1 row"
 */
When(/^I choose (addRLI|removeRLI) on (#[a-zA-Z](?:\w|\S)*) view for (\d+) row$/, async function (buttonName, view: RliTableRecord, index) {

    let rowView = view.getRowByIndex(index);

    await rowView.pressButton(buttonName);

}, {waitForApp: true});


When(/^I dismiss alert$/, async function () {

    await this.driver.alertDismiss();

}, {waitForApp: true});




When(/^I configure Forecasts module$/, async function (table: TableDefinition) {

    let data = table.rowsHash();

    await updateForecastConfig(data);

}, {waitForApp: true});


/**
 * This step required in personal info drawer of GDPR workflow. This steps selects the fields for erasure in Personal Info drawer
 *
 * @example     When I select fields in #PersonalInfoDrawer view
 *               | fieledName            |
 *               | first_name            |
 *               | last_name             |
 *               | title                 |
 *               | primary_address_state |
 *
 */
When(/^I select fields in (#\S+) view$/,
    async function (layout: PersonalInfoDrawerLayout, data: TableDefinition): Promise<void> {

        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }

        const rows = data.rows();
        for (let i = 0; i < rows.length; i++) {
            await layout.clickRowByFieldName(data.rows()[i]);
        }
    }, {waitForApp: true});

let timer = 0;

When(/^I start timer$/, async function () {

    timer = new Date().getTime();

}, {waitForApp: false});

When(/^I stop timer and verify$/, async function (data: TableDefinition) {

    if (data.hashes.length > 1) {
        throw new Error('One line data table entry is expected');
    }

    let specified_threshold = parseInt( data.rows()[0][0] , 10);

    let actual_time = (new Date().getTime() - timer);

    if (actual_time > specified_threshold) {
        throw new Error('It took longer than expected to complete this operation. Max Expected: ' + specified_threshold + '. Actual: ' + actual_time);
    } else {
        seedbed.logger.info('It took ' + actual_time + ' to complete this operation. It is within the specified max time of ' + specified_threshold);
    }

}, {waitForApp: false});

/**
 *  Create custom user
 *
 *  @example
 *
 *    # Create new non-admin user
 *    Given I create custom user "user"
 *    .....
 *    # Logout from Admin and Login as another user
 *    When I logout
 *    When I use account "user"
 *    When I open Home view and login
 */
When<string, string>(
    /^I create custom user\s*(?:"([^"]*)"(?:\/"([^"]*)")?)?$/,
    async function(login: string, password: string): Promise<void> {
        try {
            password = password || login;

           let user = await seedbed.api.create({
                record: {
                    _module: 'Users',
                    first_name: login,
                    user_name: login,
                    user_hash: password,
                    last_name: login + 'LName',
                    status: 'Active',
                    is_admin: false,
                    reports_to_id: '1',
                    reports_to_link: {name: 'Administrator', id: '1'},
                    reports_to_name: 'Administrator',
                    email: [
                        {
                            email_address: login + '@eee.eee',
                            primary_address: true,
                            reply_to_address: false,
                            invalid_email: false,
                            opt_out: false,
                        },
                    ],
                    cookie_consent: 1,
                },
            });

            seedbed.api.created.push(user);

            seedbed.cachedRecords.push(
                login,
                {
                    input: user,
                    id: user.id,
                    module: 'Users'
                }
            );

            // need to log in with the new user
            await seedbed.api.login({
                username: login,
                password: password,
            });

            // set user preferences to avoid user profile wizard for the new user
            await seedbed.api.updatePreferences({
                preferences: seedbed.config.users.defaultPreferences,
            });

            // log in back as user mentioned in scenario.
            await seedbed.api.login({
                username: seedbed.userInfo.username,
                password: seedbed.userInfo.password,
            });
        } catch (err) {
            err.message = `Failed to create custom user '${login}'`;
            throw err;
        }
    },
    { waitForApp: true }
);

/**
 *  Toggle between projecting using Opportunities with RLIs (default) and Opportunities Only mode
 *
 *  @example     Given I configure Opportunities mode
 *                   | name            | value         |
 *                   | opps_view_by    | Opportunities |
 *                   | opps_close_date | earliest      |
 *
 */
When(/^I configure Opportunities mode$/, async function (table: TableDefinition) {
    let data = table.rowsHash();
    await updateOpportunityConfig(data);

}, {waitForApp: true});

When(/^I add new currency$/, async function (data: TableDefinition) {

    const module = 'Currencies';
    // Choose 'Currencies' module
    await chooseModule(module);

    // Click Create button
    const listViewLayout = await seedbed.components[`${module}List`];
    await listViewLayout.HeaderView.clickButton('create');
    await this.driver.waitForApp();

    // Populate data
    const recordView = await seedbed.components[`${module}Drawer`].RecordView;
    const headerView = await seedbed.components[`${module}Drawer`].HeaderView;
    if (data.hashes.length > 1) {
        throw new Error('One line data table entry is expected');
    }

    let inputData = stepsHelper.getArrayOfHashmaps(data)[0];

    // check for * marked column and cache the record and view if needed
    let uidInfo = Utils.computeRecordUID(inputData);

    seedbed.cucumber.scenario.recordsInfo[uidInfo.uid] = {
        uid: uidInfo.uid,
        originInput: JSON.parse(JSON.stringify(inputData)),
        input: inputData,
        module: recordView.module,
    };

    await recordView.setFieldsValue(inputData);

    // Click 'Save' button
    await headerView.clickButton('save');
    await this.driver.waitForApp();

    // Close Alert
    await closeAlert();

}, {waitForApp: false});

/**
 *  Add recipients to new or saved email message
 *
 *  @example
 *      When I add the following recipients to the email in #EmailsRecord.RecordView
 *          | fieldName | value       |
 *          | To        | *A_2        |
 *          | Cc        | *C_2        |
 *          | Bcc       | *L_1, *L_2] |
 */
When(/^I add the following recipients to the email in (#\S+)$/,
    async function (view: RecordView, table: TableDefinition) {
        const listView = await seedbed.components[`EmailsList`].ListView;

        // Activate recipients field. This is only needed in existing Email's record view
        await view.clickButton('activate_recipents_field');
        await this.driver.waitForApp();

        const rows = table.rows();
        for (let row of rows) {
            let recipientsGroup = row[0];
            let recipientsList = row[1];

            // Parse the recipients IDs
            const recordIds = await parseInputArray(recipientsList);

            switch (recipientsGroup.toLowerCase()) {
                case 'to':
                    // Open Related Address Book
                    await view.clickButton('open_address_book_btn');
                    await this.driver.waitForApp();
                    break;

                case 'cc':
                    // Click CC button if CC field is not activated in Email record view
                    if (!(await view.elementExists('buttons.cc_button_active'))) {
                        await view.clickButton('cc_button');
                        await this.driver.waitForApp();
                    }

                    // Open Related Address Book
                    await view.clickButton('open_address_book_btn_cc');
                    await this.driver.waitForApp();
                    break;

                case 'bcc':
                    // Click BCC button if BCC field is not activated in Email record view
                    if (!(await view.elementExists('buttons.bcc_button_active'))) {
                        await view.clickButton('bcc_button');
                        await this.driver.waitForApp();
                    }

                    // Open Related Address Book
                    await view.clickButton('open_address_book_btn_bcc');
                    await this.driver.waitForApp();
                    break;

                default:
                    throw new Error('Such field does not exist!');
            }

            // Toggle specific record(s)
            for (let record of recordIds) {
                await toggleRecord({id: record.id}, listView);
            }

            // Click Done button in the header of Address Book drawer
            const headerView = await seedbed.components[`AuditLogDrawer`].HeaderView;
            await headerView.clickButton('done');
            await this.driver.waitForApp();
        }
    }, {waitForApp: false});

/**
 *  Open CC (BCC) field in the email message.
 *
 *  Note: This step definition applies to record view of Email record
 *
 *  @example: When I click CC button on #EmailsRecord.RecordView
 */
When(/^I click (CC|BCC) button on (#\S+)$/,
    async function (recipientsGroup: string, view: RecordView) {

        switch (recipientsGroup.toLowerCase()) {
            case 'cc':
                if
                (!(await view.elementExists('buttons.cc_button_active'))) {
                    await view.clickButton('cc_button');
                    await this.driver.waitForApp();
                }
                break;
            case 'bcc':
                if (!(await view.elementExists('buttons.bcc_button_active'))) {
                    await view.clickButton('bcc_button');
                    await this.driver.waitForApp();
                }
                break;
            default:
                throw new Error('Such field does not exist!');
        }
    }, {waitForApp: false});


/**
 *  Post messages to Activity Stream
 */
When(/^I post the following activities to (#\S+)$/, async function (layout: ActivityStream, data: TableDefinition) {

    const rows = data.rows();
    for (let row of rows ) {
        let value = row[0];
        await layout.addPost(value);
        await closeAlert();
        await this.driver.waitForApp();
    }
}, {waitForApp: false});

/**
 *  Comment on the top activity
 */
When(/^I comment on the top activity in (#\S+)$/, async function (layout: ActivityStream, data: TableDefinition) {

    const rows = data.rows();
    let i = 1;
    for (let row of rows ) {
        let value = row[0];
        await layout.addComment(1, value);
        await this.driver.waitForApp();
        i++;
    }
}, {waitForApp: false});

/**
 *  Open record view of the specified record
 *
 *  @example
 *  When I open ProspectLists *PRL_1 record view
 */
When(/^I open (\w+) \*(\w+) record view$/,
    async function (module, name: string) {
        // TODO: In the future we should check the current route and if we are already on the correct module/record
        await chooseModule(module);
        let view = await seedbed.components[`${module}List`].ListView;
        let record = await seedbed.cachedRecords.get(name);
        await chooseRecord({id: record.id}, view);
    }, {waitForApp: true}
);

/**
 *  Filter specified record by name and assign record id
 *
 *  Note: Mostly used in the Reports module to assign id to OOTB report
 *
 *  @example
 *  When I filter Reports record *report1 named "All Open Opportunities"
 *
 */
When(/^I filter for the (\w+) record \*(\w+) named "([^"]*)"$/,
    async function (module: string, uid: string, recordName: string) {

        // Get URL fragment
        let router = await seedbed.client.driver.execSync('getRouter', []);

        // Choose module for all modules but 3 special cases:
        // Process Management: 'pmse_Inbox/layout/casesList'
        // Unattended Cases: 'pmse_Inbox/layout/unattendedCases'
        // Dashboards: 'Dashboards?moduleName=Home'
        if (router.value.indexOf('pmse_Inbox/') === -1 && router.value.indexOf('Dashboards?moduleName=Home') === -1 ) {
            await chooseModule(module);
        }

        // Filter list view by the specified record name
        const filterView = await seedbed.components[`${module}List`].FilterView;
        await filterView.setSearchField(recordName);
        await this.driver.waitForApp();

        let argumentsArray = [];

        // for all modules but all 3 'pmse_Inbox' modules
        if (router.value.indexOf('pmse_Inbox') === -1) {
            argumentsArray.push(recordName);
            argumentsArray.push(module);
        } else {
            // Edit module argument for various 'pmse_Inbox' related cases
            if (router.value === 'pmse_Inbox/layout/casesList') {
                argumentsArray.push(`${module}/casesList`);
            } else if (router.value === 'pmse_Inbox/layout/unattendedCases') {
                argumentsArray.push(`${module}/unattendedCases`);
            } else {
                argumentsArray.push(module);
            }
        }

        let command = router.value.indexOf('pmse_Inbox') === -1 ? 'getRecordIDByName' : 'getMostRecentlyCreatedRecord';
        let result = await seedbed.client.driver.execAsync(command, argumentsArray);
        let recordID = result.value;

        // Add record id to the global array of all records with uid using user-provided record identifier
        seedbed.cachedRecords.push(uid,
            {
                id: recordID,
                module: module,
            });

        // Create record layout of the added record
        seedbed.defineComponent(`${uid}Record`, RecordLayout, {
            module: module,
            id: recordID,
        });

        // Create preview layout of the added record
        seedbed.defineComponent(`${uid}Preview`, PreviewLayout, {
            module: module,
            id: recordID,
        });
    }, {waitForApp: true}
);

/**
 *  Upload BPM related file into Sugar (Supported file types: *.bpm, *.pet, *.bpr)
 *
 *  @example
 *  When I create a new record *BP_1 by importing All Modules from "multi-import-all.bpm" file on #pmse_ProjectRecord.ImportBpmView
 */
When(/^I create a new record \*(\w+) by importing(?: (Business Rules|Email Templates|All Modules) from)? "(\S+)" file on (#[a-zA-Z](?:\w|\S)*)$/,
        async function(uid: string, importOptions: string, fileName: string, view: ImportBpmView): Promise<void> {

        const fileType =  fileName.split('.').pop();
        const folderName = fileType;

        // Choose file
        await view.importFile(folderName, fileName);


        let rec_view = await seedbed.components[`${view.module}Record`];

        // Click Import button and Confirm confirmation alert
        switch (fileType.toLowerCase()) {
            case 'bpm':

                // Select import option in case importOptions is specified
                if (importOptions === 'Business Rules') {
                    await view.selectImportOptions(ImportBpmView.ImportOptions['BUSINESS_RULES']);

                } else if (importOptions === 'Email Templates') {
                    await view.selectImportOptions(ImportBpmView.ImportOptions['EMAIL_TEMPLATES']);

                } else if (importOptions === 'All Modules') {
                    await view.selectImportOptions(ImportBpmView.ImportOptions['BUSINESS_RULES']);
                    await view.selectImportOptions(ImportBpmView.ImportOptions['EMAIL_TEMPLATES']);
                }

                await recordViewHeaderButtonClicks('bpm_import_button', rec_view);
                break;

            case 'pet':
                await recordViewHeaderButtonClicks('email_template_import_button', rec_view);
                break ;

            case 'pbr':
                await recordViewHeaderButtonClicks('business_rules_import_button', rec_view);
                break;

            default:
                throw new Error(`The file you try to import is of unknown file type: ${fileType}`);
        }

        await closeWarning('Confirm');
        await this.driver.waitForApp();
        await closeAlert();

        // Get id of jst created record
        let argumentsArray = [];
        argumentsArray.push(view.module);
        let result = await seedbed.client.driver.execAsync('getMostRecentlyCreatedRecord', argumentsArray);
        let recordID = result.value;

        // Add record id to the global array of all records with uid using user-provided record identifier
        seedbed.cachedRecords.push(uid,
            {
                id: recordID,
                module: view.module,
            });

        /* Navigate to the list view after import is complete
        in case of importing Business Process (aka *.bpm)file */
        if (fileType.toLowerCase() === 'bpm') {
            await chooseModule(view.module);
        }
    }, { waitForApp: true }
);

/**
 *  Click button in the record Preview Header
 *
 *  @example
 *  When I click on Edit button in #Account_APreview.PreviewHeaderView
 */
When(/^I click on (Edit|Cancel|Save) button in (#\S+)$/,
    async function (btnName: string, view: PreviewHeaderView) {
        await view.btnClick(btnName.toLowerCase());
    }, {waitForApp: true});

/**
 *  Refresh the browser window
 *
 *  @example
 *  When I refresh the browser
 */
When(/^I refresh the browser$/,
    async function() {
        await seedbed.client.driver.execSync('browserRefresh', []);
    }, {waitForApp: true});

/**
 *  Select specific browser tab
 *
 *  @example
 *  When I switch to tab 0
 */
When(/^I switch to tab (0|1|2)$/,
    async function(tabNum: number) {
        let tabIDs = await this.driver.getTabIds();
        await this.driver.switchTab(tabIDs[tabNum]);
    }, {waitForApp: true});

/**
 *  Open new browser tab and navigate to URL
 *
 *  @example
 *  When I open new browser tab and navigate to "portal/index.html" url
 */
When(/^I open new browser tab(?: and navigate to "(\S+)" url)?$/,
    async function(url: string) {

        let urlParam = url || [];
        await seedbed.client.driver.execSync('openNewTab', urlParam);
    }, {waitForApp: true});


/**
 *  Login to Portal (Warning! This step definition has not been tested.)
 *
 *  @example
 *  When I open new browser tab and navigate to "portal/index.html" url
 *
 */
When(/^I login to Portal with the following credentials in (#\S+):$/,
    async function(layout: LoginLayout, data: TableDefinition) {

        if (data.hashes.length > 1) {
            throw new Error('One line data table entry is expected');
        }

        let inputData = stepsHelper.getArrayOfHashmaps(data)[0];
        await layout.LoginView.login(inputData.hash.portal_name, inputData.hash.portal_pasword);
    }, {waitForApp: true});


/**
 *  Create Record using Quick Create
 *
 *  @example
 *  When I choose Contacts in the Quick Create actions menu
 */
When(/^I choose (\w+) in the Quick Create actions menu$/,
    async function(itemName: string): Promise<void> {
        let quickCreateMenuCmp: QuickCreateMenuCmp = seedbed.components.QuickCreateMenuCmp;

        await quickCreateMenuCmp.open();
        await this.driver.waitForApp();
        await quickCreateMenuCmp.clickItem(itemName);
    },
    { waitForApp: true });


/**
 *  Search record by name and select it in Search and Select drawer
 *
 *  @example
 *  When I select Opp_1 record from Opportunities SearchAndSelect drawer
 */
When(/^I select (\S+) record from (\S+) SearchAndSelect drawer$/,
    async function(recordName: string, module: string): Promise<void> {

        // Search record by name in the list off available records
        await seedbed.components[`${module}SearchAndSelect`].FilterView.setSearchField(recordName);
        await this.driver.waitForApp();

        // Select record in 'Search And Select' drawer
        await seedbed.components[`${module}SearchAndSelect`].selectRecordByName(recordName, module);
        await this.driver.waitForApp();

    }, {waitForApp: true});


