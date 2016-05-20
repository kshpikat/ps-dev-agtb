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


use Sugarcrm\Sugarcrm\Dav\Cal\Adapter\Factory as CalDavAdapterFactory;
use Sugarcrm\Sugarcrm\JobQueue\Manager\Manager as JQManager;

/**
 * Add calls and meetings to events
 */
class SugarUpgradeAddMeetingsAndCallsToEvents extends UpgradeScript
{
    /**
     * {@inheritdoc}
     */
    public $order = 9998;

    /**
     * {@inheritdoc}
     * @var int
     */
    public $type = self::UPGRADE_DB;

    /**
     * Should prepare export for all records of all supported modules (Meetings, Calls, etc)
     *
     * {@inheritdoc}
     */
    public function run()
    {
        // This upgrade is for version lower than 7.8.0.0
        if (version_compare($this->from_version, '7.8.0.0RC4', '>=')) {
            return;
        }

        // Get all supported modules
        $factory = $this->getCalDavAdapterFactory();
        $modules = $factory->getSupportedModules();

        $this->db->query($this->db->truncateTableSQL("caldav_synchronization"));
        $this->db->query($this->db->truncateTableSQL("caldav_queue"));

        foreach ($modules as $module) {
            $verifyResult = true;
            $bean = BeanFactory::getBean($module);

            $properties = array(
                'repeat_root_id',
                'repeat_parent_id',
                'repeat_dow',
                'repeat_type',
            );

            foreach ($properties as $property) {
                if (isset($bean->field_defs[$property]['source'])
                    && ($bean->field_defs[$property]['source'] == 'non-db')
                ) {
                    $verifyResult = false;
                    break;
                }
            }

            $query = $this->getQuery();
            $query->from($bean);
            $query->select(array('id'));
            $query->orderBy('repeat_parent_id', 'ASC');
            $query->orderBy('date_start', 'ASC');
            $rows = $query->execute();

            foreach ($rows as $row) {
                /** @var \Call | \Meeting $beanForUpdate */
                $beanForUpdate = BeanFactory::getBean($module, $row['id']);

                if ($verifyResult) {
                    if (!empty($beanForUpdate->repeat_parent_id)) {
                        $beanForUpdate->repeat_root_id = $beanForUpdate->repeat_parent_id;
                    } else {
                        $beanForUpdate->repeat_root_id = $beanForUpdate->id;
                    }

                    if ($beanForUpdate->repeat_type != 'Weekly') {
                        $beanForUpdate->repeat_dow = '';
                    }
                }

                $beanForUpdate->save(false, array('disableCalDavHook' => true));
            }
        }

        if (!empty($this->getConfigurator()->config['caldav_enable_sync'])) {
            $this->getJQManager()->CalDavRebuild();
        }
    }

    /**
     * Returns Query class to work with
     *
     * @return SugarQuery
     */
    protected function getQuery()
    {
        return new SugarQuery();
    }

    /**
     * * Returns factory class to get list of supported modules
     *
     * @return CalDavAdapterFactory
     */
    protected function getCalDavAdapterFactory()
    {
        return new CalDavAdapterFactory();
    }

    /**
     * Return Configurator.
     *
     * @return Configurator
     */
    public function getConfigurator()
    {
        return new Configurator();
    }

    /**
     * Get manager object for handler processing.
     *
     * @return JQManager
     */
    protected function getJQManager()
    {
        return new JQManager();
    }
}
