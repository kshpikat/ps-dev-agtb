<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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

require_once 'PMSEIntermediateEvent.php';
require_once 'modules/pmse_Inbox/engine/PMSEHandlers/PMSEBeanHandler.php';

class PMSESendMessageEvent extends PMSEIntermediateEvent
{
    /**
     *
     * @var type
     */
    private $eventDefinitionBean;

    /**
     *
     * @var type
     */
    private $locale;


    /**
     *
     * @global type $locale
     * @codeCoverageIgnore
     */
    public function __construct()
    {

        global $locale;
        $this->locale = $locale;
        $this->eventDefinitionBean = BeanFactory::getBean('pmse_BpmEventDefinition');
        parent::__construct();

    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getEventDefinitionBean()
    {
        return $this->eventDefinitionBean;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     *
     * @return type
     * @codeCoverageIgnore
     */
    public function setEventDefinitionBean($eventDefinitionBean)
    {
        $this->eventDefinitionBean = $eventDefinitionBean;
    }

    /**
     *
     * @param type $locale
     * @codeCoverageIgnore
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * This method prepares the response of the current element based on the
     * $bean object and the $flowData, an external action such as
     * ROUTE or ADHOC_REASSIGN could be also processed.
     *
     * This method probably should be override for each new element, but it's
     * not mandatory. However the response structure always must pass using
     * the 'prepareResponse' Method.
     *
     * As defined in the example:
     *
     * $response['route_action'] = 'ROUTE'; //The action that should process the Router
     * $response['flow_action'] = 'CREATE'; //The record action that should process the router
     * $response['flow_data'] = $flowData; //The current flowData
     * $response['flow_filters'] = array('first_id', 'second_id'); //This attribute is used to filter the execution of the following elements
     * $response['flow_id'] = $flowData['id']; // The flowData id if present
     *
     *
     * @param type $flowData
     * @param type $bean
     * @param type $externalAction
     * @return type
     */
    public function run($flowData, $bean = null, $externalAction = '', $arguments = array())
    {
        if ($externalAction == 'RESUME_EXECUTION') {
            $this->sendEmail($flowData);
            return $this->prepareResponse($flowData, 'NONE', 'NONE');
        } else {
            $flowData['cas_flow_status'] = 'QUEUE';
            return $this->prepareResponse($flowData, 'QUEUE', 'CREATE');
        }
    }

    /**
     *
     * @param type $flowData
     * @return type
     */
    public function sendEmail($flowData)
    {
        $this->eventDefinitionBean->retrieve($flowData['bpmn_id']);
        $templateId = $this->eventDefinitionBean->evn_criteria;
        $json = htmlspecialchars_decode($this->eventDefinitionBean->evn_params);
        $bean = $this->caseFlowHandler->retrieveBean($flowData['cas_sugar_module'], $flowData['cas_sugar_object_id']);
        $addresses = $this->emailHandler->processEmailsFromJson($bean, $json, $flowData);
        $result = $this->emailHandler->sendTemplateEmail($flowData['cas_sugar_module'],
            $flowData['cas_sugar_object_id'], $addresses, $templateId);

        if (!$result['result']) {
            throw new PMSEElementException($result['ErrorInfo'], $flowData, $this);
        } elseif (!empty($result['ErrorMessage'])) {
            throw new PMSEElementException($result['ErrorMessage'], $flowData, $this);
        }
    }


}
