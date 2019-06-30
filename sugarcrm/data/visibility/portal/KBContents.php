<?php
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

namespace Sugarcrm\Sugarcrm\Visibility\Portal;

/**
 * IMPORTANT NOTE: If the below logic is customised/changed, it will need to be updated also for the related object Notes on the Notes.php portal visibility rules
 * IMPORTANT NOTE: Notes have to be filtered based on the visible parent objects
 */
class KBContents extends Portal
{
    public function addVisibilityQuery(\SugarQuery $query, array $options = [])
    {
        $query->where()
            ->equals($options['table_alias'] . '.active_rev', 1)
            ->equals($options['table_alias'] . '.is_external', 1)
            ->equals($options['table_alias'] . '.status', \KBContent::ST_PUBLISHED);
    }
}
