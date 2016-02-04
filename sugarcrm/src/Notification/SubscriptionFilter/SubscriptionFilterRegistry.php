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

namespace Sugarcrm\Sugarcrm\Notification\SubscriptionFilter;

/**
 * Registry of SubscriptionFilters. Ge list of files form "include/sfr.php" and form "custom/include/sfr.php".
 *
 * For customization SubscriptionFilter need overwrite custom class
 * or reassign in custom registry("custom/include/sfr.php")
 *
 * Class SubscriptionFilterRegistry
 * @package Notification
 */
class SubscriptionFilterRegistry
{
    /**
     * Path to file in which store cached dictionary array.
     */
    const CACHE_FILE = 'Notification/sfr.php';

    /**
     * Path to file in which stored dictionary array, support customisation.
     */
    const REGISTRY_FILE = 'include/sfr.php';

    /**
     * Full path to SubscriptionFilterInterface with nameSpace.
     */
    const SF_INTERFACE = 'Sugarcrm\\Sugarcrm\\Notification\\SubscriptionFilter\\SubscriptionFilterInterface';

    /**
     * Variable name in which store dictionary array
     */
    const VARIABLE = 'sfr';

    /**
     * Returns object of SubscriptionFilterRegistry, customized if it's present.
     *
     * @return SubscriptionFilterRegistry
     */
    public static function getInstance()
    {
        $path = 'Sugarcrm\Sugarcrm\Notification\SubscriptionFilter\SubscriptionFilterRegistry';
        $class = \SugarAutoLoader::customClass($path);

        return new $class();
    }

    /**
     * Get subscription filter by name
     *
     * @param string $name class name
     * @return SubscriptionFilterInterface|null subscription filter instance
     */
    public function getFilter($name)
    {
        $sfList = $this->getDictionary();
        if (isset($sfList[$name])) {
            return new $sfList[$name]();
        } else {
            return null;
        }
    }

    /**
     * Retrieving array(dictionary array with subscription filter class names and path to it)
     *
     * Retrieving array(dictionary array with subscription filter class names and path to it)
     * from cache file if it not exists rebuild cache
     *
     * @return array class names and path to it
     */
    protected function getDictionary()
    {
        $data = self::getCache();
        if (is_null($data)) {
            $data = self::scan();
            self::setCache($data);
        }

        return $data;
    }

    /**
     * Retrieving dictionary array from cache file if it exists
     *
     * Retrieving array(dictionary array with subscription filter  names and full class name to it)
     * from cache file if it exists
     *
     * @return array|null dictionary array from cache
     */
    protected function getCache()
    {
        $path = sugar_cached(static::CACHE_FILE);
        if (\SugarAutoLoader::fileExists($path)) {
            return $this->getDataFromFile($path);
        } else {
            return null;
        }
    }

    /**
     * Retrieving dictionary array from file if it exists
     *
     * Retrieving array(dictionary array with subscription filter class names and full class name to it)
     * from file if it exists
     *
     * @param $path to file
     * @return array dictionary array from file
     */
    protected function getDataFromFile($path)
    {
        include($path);

        if (isset(${static::VARIABLE})) {
            return ${static::VARIABLE};
        } else {
            return array();
        }

    }

    /**
     * Build dictionary array with carrier class names and paths
     *
     *  array(
     *      'name' => 'FullClassPath'
     *  );
     *
     * @return array
     */
    protected function scan()
    {
        $baseRegistry = $this->getDataFromFile(self::REGISTRY_FILE);
        $registry = $baseRegistry;
        foreach ($registry as $name => $class) {
            $customClass = \SugarAutoLoader::customClass($class);
            if (in_array($class, class_parents($customClass))) {
                $registry[$name] = $customClass;
            }
        }

        $customRegistryFile = 'custom/' . self::REGISTRY_FILE;
        if (\SugarAutoLoader::fileExists($customRegistryFile)) {
            foreach ($this->getDataFromFile($customRegistryFile) as $name => $class) {
                if (array_key_exists($name, $registry)) {
                    if (in_array($baseRegistry[$name], class_parents($class))) {
                        $registry[$name] = $class;
                    }
                } else {
                    if (in_array(self::SF_INTERFACE, class_implements($class))) {
                        $registry[$name] = $class;
                    }
                }
            }
        }

        return $registry;
    }

    /**
     * Saving array(dictionary array with carrier class names and path to it) to cache file
     *
     * @param array $data class names and path to it
     */
    protected function setCache($data)
    {
        create_cache_directory(static::CACHE_FILE);
        write_array_to_file(static::VARIABLE, $data, sugar_cached(static::CACHE_FILE));
    }

    /**
     * Function return SubscriptionFilter names(retrieve from cache)
     *
     * @return string[] SubscriptionFilter names
     */
    public function getFilters()
    {
        return array_keys($this->getDictionary());
    }
}
