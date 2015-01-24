<?php

require_once 'modules/ModuleBuilder/parsers/ModuleBuilderParser.php';

/**
 * Parser for role based dropdowns
 * Stores data in (custom/)include/dropdown_filters/roles/<role_id> directory
 *
 */
class ParserRoleDropDownFilter extends ModuleBuilderParser
{
    protected $path = 'include/dropdown_filters/roles';
    protected $varName = 'role_dropdown_filters';

    /**
     * Returns array of options by given role name and dropdown name
     *
     * @param string $role Role ID
     * @param string $dropdown Dropdown name
     * @return array
     */
    public function getOne($role, $dropdown)
    {
        $filePath = SugarAutoLoader::existingCustomOne($this->getFilePath($role, $dropdown));
        if (!file_exists($filePath)) {
            return array();
        }
        require $filePath;
        if (empty(${$this->varName}[$dropdown])) {
            $GLOBALS['log']->error("ParserRoleDropDownFilter :: Cannot find \$$this->varName[$dropdown] in $filePath");
            return array();
        }
        return ${$this->varName}[$dropdown];
    }

    /**
     * Checks if there is role specific metadata for the given dropdown field
     *
     * @param string $name Field name
     * @param string $role Role ID
     *
     * @return boolean
     */
    public function hasMetadata($name, $role)
    {
        $filePath = SugarAutoLoader::existingCustomOne($this->getFilePath($role, $name));
        return file_exists($filePath);
    }

    /**
     * Returns an array of all dropdown options for all roles
     *
     * @return array
     */
    public function getAll()
    {
        return $this->getDropDownFiltersFromFiles($this->getAllFiles());
    }

    /**
     * @return array list of all Role Based language files.
     */
    public function getAllFiles()
    {
        return array_merge(
            glob($this->path . '/*/*.php'),
            glob('custom/' . $this->path . '/*/*.php'),
            glob('custom/application/Ext/Language/*/*/roledropdownfilter.ext.php')
        );
    }

    /**
     * Returns editable dropdown filters defined in the given files
     * 
     * @param array $files
     *
     * @return array dropdown filters found in the given files
     */
    public function getDropDownFiltersFromFiles(array $files)
    {
        ${$this->varName} = array();
        $filePath = "";
        foreach ($files as $file) {
            if (is_array($file) && isset($file['path'])) {
                $file = $file['path'];
            }
            if (is_string($file)) {
                $filePath = $file;
                if (SugarAutoLoader::fileExists($file)) {
                    require $file;
                }
            }
        }

        return $this->validateDropDownFilter(${$this->varName}, basename($filePath, ".php"));
    }

    /**
     * Returns the filter with no longer valid keys removed from the list.
     * @param        $filter
     * @param        $dropdownName
     * @param string $language
     *
     * @return array
     */
    protected function validateDropDownFilter($filter, $dropdownName, $language = 'en_us') {
        $list_strings = return_app_list_strings_language($language);
        $ret[$dropdownName] = array();
        if (isset($list_strings[$dropdownName]) && is_array($list_strings[$dropdownName]) &&
            isset($filter[$dropdownName]) && is_array($filter[$dropdownName])) {
            $dropdownList = $list_strings[$dropdownName];
            foreach($filter[$dropdownName] as $key => $visible) {
                if (isset($dropdownList[$key])) {
                    $ret[$dropdownName][$key] = $visible;
                }
            }
        }

        return $ret;
    }

    /**
     * Returns a file path to the file that stores options for a given role and a dropdown name
     *
     * @param $role
     * @param $name
     * @return string
     */
    protected function getFilePath($role, $name)
    {
        return $this->getFileDir($role) . "/$name.php";
    }

    /**
     * Returns a directory for the given role name
     *
     * @param $role
     * @return string
     */
    protected function getFileDir($role)
    {
        return "$this->path/$role";
    }

    /**
     * Saves $data to the $name dropdown for the $role name
     *
     * @param $role
     * @param $name
     * @param $data
     * @return boolean
     * @throws Exception
     */
    public function handleSave($role, $name, $data)
    {
        $dir = 'custom/' . $this->getFileDir($role);
        if (!SugarAutoLoader::ensureDir($dir)) {
            $GLOBALS['log']->error("ParserRoleDropDownFilter :: Cannot create directory $dir");
            return false;
        }
        $result = write_array_to_file(
            "{$this->varName}['{$name}']",
            $this->convertFormData($data),
            'custom/' . $this->getFilePath($role, $name)
        );
        if ($result) {
            MetaDataManager::refreshSectionCache(MetaDataManager::MM_EDITDDFILTERS, array(), array(
                'role' => $role,
            ));
        }
        return $result;
    }

    /**
     * Converts form data to internal representation
     *
     * @param array $data Form data
     * @return array Internal representation
     */
    protected function convertFormData($data)
    {
        $converted = array();
        $blank = translate('LBL_BLANK', 'ModuleBuilder');
        foreach ($data as $key => $item) {
            if ($key === $blank) {
                $key = '';
            }

            $converted[$key] = (bool) $item;
        }

        return $converted;
    }
}
