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
({
    initialize: function(options) {
        var request = {
                page_data: {},
                keys: [],
                chapter_details: {},
                section_details: {},
                page_details: {},
                parent_link: '',
                view: 'index'
            },
            chapter_name,
            content_name,
            chapter,
            section,
            page;

        this._super('initialize', [options]);

        chapter_name = this.context.get('chapter_name');
        content_name = this.context.get('content_name');

        // load up the styleguide css if not already loaded
        //TODO: cleanup styleguide.css and add to main file
        if ($('head #styleguide_css').length === 0) {
            $('<link>')
                .attr({
                    rel: 'stylesheet',
                    href: 'styleguide/assets/css/styleguide.css',
                    id: 'styleguide_css'
                })
                .appendTo('head');
        }

        document.title = $('<span/>').html('Styleguide &#187; SugarCRM').text();

        // request.page_data = this.meta.metadata.page_data;
        request.page_data = app.metadata.getLayout(this.module, 'styleguide').metadata.chapters;

        request.keys = [chapter_name];
        if (!_.isUndefined(content_name) && !_.isEmpty(content_name)) {
            Array.prototype.push.apply(request.keys, content_name.split('-'));
        }

        chapter = request.page_data[request.keys[0]];
        request.chapter_details = {
            title: chapter.title,
            description: chapter.description
        };
        if (chapter.index && request.keys.length > 1 && request.keys[1] !== 'index') {
            section = chapter.sections[request.keys[1]];
            request.section_details = {
                title: section.title,
                description: section.description
            };
            if (section.index && request.keys.length > 2 && request.keys[2] !== 'index') {
                page = section.pages[request.keys[2]];
                request.page_details = {
                    title: page.title,
                    description: page.description,
                    url: page.url
                };
                request.view = content_name;
                request.parent_link = '-' + request.keys[0][request.keys[1]];
                window.prettyPrint && prettyPrint();
            } else {
                request.page_details = request.section_details;
            }
        } else {
            request.page_details = request.chapter_details;
        }

        this.context.set('request', request);
    }
})
