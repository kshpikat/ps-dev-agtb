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
 * @class TileViewItem
 * @extends BaseView
 */
export default class TileViewItem extends BaseView {

    public id: string;
    public index: number;
    public current: boolean;

    constructor(options) {

        super(options);

        this.selectors = this.mergeSelectors({
            $: 'li[data-modelid*="{{id}}"]',
            listItem: {
                listItemName: '.pipeline-tile',
                buttons: {
                    delete: '.rowaction.btn.delete',
                },

                tileName: '.name',
                tileBody: '.tile-body .ui-corner-all div',

                tileContent: {
                  $: '.tile-body',
                  rowOfData: {
                      $: 'span:nth-child({{tileContentRow}})',
                      div: 'div',
                      // Comment Log field in the tile view
                      commentLog: {
                          $: '.commentLog',
                          // Comment log message div
                          message: {
                              $: 'div.msg-div:nth-child({{msgIndex}})',
                              // Message author
                              messageAuthor: '[data-fieldname=author_name] a',
                              // Message content
                              messageContent: '.msg-content',
                            }
                          }
                      }
                  }
                }
        });

        this.id = options.id;
        this.index = options.index;
        this.current = !this.id && !this.index;
    }

    /**
     * Drag and Drop tile to specified column in the Tile View
     *
     * @param colName {string} name of the column to drag tile to
     */
    public async dragAndDropTile (colName: string) {
        let src_by = this.$('listItem.tileBody', {id: this.id} );

        let columnNumber: number = 0;
        let found: boolean = false;

        //assuming there won't be more than 40 columns at most:
        while((columnNumber < 40) && !found) {
            columnNumber++;
            let targetColTitle_by = `//div[@class='main-content']//table//thead//th[${columnNumber}]/div[normalize-space()='${colName}']`;

            let isExists = await this.driver.isElementExist(targetColTitle_by);
            if(isExists) {
                found = true;
            }
        }

        if(!found) {
            throw new  Error(`Could not find the column titled "${colName}" in Tile View!`);
        }
        let des_by = `//div[@id='my-pipeline-content']//tbody//td[${columnNumber}]/ul`;


        let driver = this.driver;
        await driver.moveToObject(src_by);
        await driver.moveTo(null, 0, 0);
        await driver.pause(1000);
        await driver.buttonDown(0);
        await driver.moveToObject(des_by);
        await driver.pause(1000);
        await driver.moveTo(null, 5, 3);
        await driver.pause(1000);
        await driver.buttonUp(0);
        await driver.pause(1000);
    }


    /**
     * Click on list view item list element (name in most cases)
     *
     * @returns {*}
     */
    public async clickListItem() {

        let selector = this.$('listItem.tileName', {id: this.id});
        let rowSelector = this.$();

        return this.driver
            .execSync('scrollToSelector', [rowSelector])
            .click(selector);
    }

    /**
     * Check state of tile delete button
     *
     * @returns {Promise<any>}
     */
    public async isDeleteButtonDisabled() {

        let selector = this.$('listItem.buttons.delete', {id: this.id}) + '.disabled';
        let rowSelector = this.$('listItem.tileName');

        await this.driver.moveToObject(rowSelector);
        await this.driver.waitForApp();
        return await this.driver.isElementExist(selector);
    }

    /**
     * Click on delete record button (top-right corner of each tile) in tile view
     *
     * @param itemName
     * @returns {Promise<void>}
     */
    public async clickDeleteButton(itemName) {

        let selector = this.$('listItem.buttons.' + itemName.toLowerCase(), {id: this.id});
        let rowSelector = this.$('listItem.tileName');

        await this.driver.moveToObject(rowSelector);
        await this.driver.waitForApp();
        await this.driver.click(selector);
        await this.driver.waitForApp();
    }

    /**
     * Checks if button is visible
     *
     * @param itemName
     * @returns {Promise<Client<boolean>>}
     */
    public async isVisible(itemName) {
        return this.driver.isVisible(this.$('listItem.buttons.' + itemName.toLowerCase()));
    }

    /**
     * Get field value in the tile content
     *
     * @param tileContentRow
     * @returns {Promise<any>}
     */
    public async getTileFieldValue(tileContentRow) {

        // construct selector to comment log
        let selector = this.$('listItem.tileContent.rowOfData.commentLog', {id: this.id, tileContentRow});

        // get tile body row value if it is not comment log
        let isCommentLog = await this.driver.isElementExist(selector);
        if ( !isCommentLog ) {
            selector = this.$('listItem.tileContent.rowOfData.div', {id: this.id, tileContentRow});
            return this.driver.getText(selector);
        } else {
            // process comment log field differently
            return this.getDataFromCommentLog(tileContentRow);
        }
    }

    /**
     * Get record name from tile title using record id.
     *
     * @returns {Promise<any>}
     */
    public async getTileName() {

        let selector = this.$('listItem.tileName', {id: this.id} );
        await this.driver.scroll(selector);
        return this.driver.getText(selector);
    }

    /**
     * Check column of specified opportunity record
     *
     * @param columnName
     * @returns {Promise<boolean>}
     */
    public async checkTileViewColumn(columnName): Promise<boolean> {

        // Prepend record css with part containing column name
        let selector = `.column[data-column-name="${columnName}"] ${this.$()}`;

        // Check if css containing column name exists
        return this.driver.isElementExist(selector);
    }

    /**
     * Return string of Authors & Messages from Comment Log field
     *
     * @param {string} tileContentRow - index of the comment log row in the tile body
     * @return {string} returnedValues comma separated string of all messages in Comment Log
     */
    private async getDataFromCommentLog (tileContentRow: string): Promise<string> {

        let returnedValues: string = '';
        let value: string = '';
        let cssPath: string =  'listItem.tileContent.rowOfData.commentLog.message';
        let selector: string = '';

        const MAX_NUMBER_OF_MESSAGES_IN_COMMENTS_LOG = 10;

        for (var msgIndex=1; msgIndex <= MAX_NUMBER_OF_MESSAGES_IN_COMMENTS_LOG; msgIndex++ ) {
            let msgSelector = this.$(cssPath, {id: this.id, tileContentRow, msgIndex});

            // If message exists in comment log
            if ( await this.driver.isElementExist(msgSelector) ) {
                // add comma separator
                if (msgIndex !== 1) {
                    returnedValues = returnedValues.concat(', ');
                }
                // Construct selector to get the author of the message
                selector  = this.$(cssPath + '.messageAuthor', {id: this.id, tileContentRow, msgIndex});
                value = await this.driver.getText(selector);
                returnedValues = returnedValues.concat(value + ': ');

                // Construct selector to get the text of the message
                selector  = this.$(cssPath + '.messageContent', {id: this.id, tileContentRow, msgIndex});
                value = await this.driver.getText(selector);
                returnedValues = returnedValues.concat(value);
            // exit the loop if no more messages found in the comment log
            } else {
                break;
            }
        }

        return returnedValues;
    }
}
