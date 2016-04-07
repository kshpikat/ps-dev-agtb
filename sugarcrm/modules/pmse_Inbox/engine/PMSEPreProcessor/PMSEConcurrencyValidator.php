<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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

require_once 'modules/pmse_Inbox/engine/PMSEPreProcessor/PMSEBaseValidator.php';
require_once 'modules/pmse_Inbox/engine/PMSEPreProcessor/PMSEValidate.php';

use Sugarcrm\Sugarcrm\Util\Arrays\ArrayFunctions\ArrayFunctions;
use Sugarcrm\Sugarcrm\ProcessManager\Registry;

/**
 * Description of PMSEConcurrencyValidator
 * The concurrency validator class purpose is to filter duplicate requests
 * from the same event and process since it's possible to send twice the data
 * from a direct request.
 *
 */
class PMSEConcurrencyValidator extends PMSEBaseValidator implements PMSEValidate
{
    /**
     * Validates that if a second request from the same event and bean record
     * is received, the second request should be invalidated and thus ignored.
     * @param PMSERequest $request
     * @return \PMSERequest
     */
    public function validateRequest(PMSERequest $request)
    {
        $this->logger->info("Validate Request " . get_class($this));
        $this->logger->debug(array("Request data:", $request));

        $args = $request->getArguments();
        $flowId = isset($args['idFlow']) ? $args['idFlow'] : (isset($args['flow_id']) ? $args['flow_id'] : '0');
        $flows = Registry\Registry::getInstance()->get('locked_flows', array());
        if (!isset($flows[$flowId])) {
            $request->validate();
        } else {
            $request->invalidate();
        }
        return $request;
    }
}
