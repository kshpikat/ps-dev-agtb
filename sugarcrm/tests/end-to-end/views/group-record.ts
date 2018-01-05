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

import BaseView from './base-view';

/**
 * Represents Record view.
 *
 * @class GroupRecord
 * @extends BaseView
 */
export default class GroupRecord extends BaseView {

    public id: string;

    constructor(options) {
        super(options);

        this.id = options.id;
        this.module = 'ProductBundles';

        this.selectors = this.mergeSelectors({
            $: this.id ? `[data-record-id="${this.id}"]` : '',
            buttons: {
                save: '.btn.inline-save',
                cancel: '.btn.inline-cancel',
                GroupMenu: '.btn.btn-invisible.dropdown-toggle.edit-dropdown-toggle',
                'in-line-save': '.btn.inline-save.btn-link.btn-invisible.ellipsis_inline',
                'in-line-cancel': '.btn.inline-cancel.btn-link.btn-invisible.ellipsis_inline'
            },
            menu: {
                editGroup: '[name=edit_bundle_button]',
                deleteGroup: '[name=delete_bundle_button]',
            }
        });
    }

    public async pressButton(buttonName) {
        await this.driver.click(this.$(`buttons.${buttonName.toLowerCase()}`));
    }

    public async openLineItemMenu() {
        await this.driver.click(this.$('buttons.GroupMenu'));
    }

    public async clickMenuItem(itemName) {
        await this.driver.click(this.$(`menu.${itemName}`));
    }

}
