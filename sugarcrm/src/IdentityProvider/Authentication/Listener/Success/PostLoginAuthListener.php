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

namespace Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Listener\Success;

require_once 'modules/Versions/CheckVersions.php';

use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;

class PostLoginAuthListener
{
    /**
     * set user in globals and session
     * @param AuthenticationEvent $event
     */
    public function execute(AuthenticationEvent $event)
    {
        global $log;
        /** @var \User $currentUser */
        $currentUser = $event->getAuthenticationToken()->getUser()->getSugarUser();
        $sugarConfig = \SugarConfig::getInstance();

        //THIS SECTION IS TO ENSURE VERSIONS ARE UP TO DATE

        $invalid_versions = get_invalid_versions();
        if (!empty($invalid_versions)) {
            if (isset($invalid_versions['Rebuild Relationships'])) {
                unset($invalid_versions['Rebuild Relationships']);

                // flag for pickup in DisplayWarnings.php
                $_SESSION['rebuild_relationships'] = true;
            }

            if (isset($invalid_versions['Rebuild Extensions'])) {
                unset($invalid_versions['Rebuild Extensions']);

                // flag for pickup in DisplayWarnings.php
                $_SESSION['rebuild_extensions'] = true;
            }

            $_SESSION['invalid_versions'] = $invalid_versions;
        }

        //just do a little house cleaning here
        unset($_SESSION['login_password']);
        unset($_SESSION['login_error']);
        unset($_SESSION['login_user_name']);
        unset($_SESSION['ACL']);

        $uniqueKey = $sugarConfig->get('unique_key');

        //set the server unique key
        if (!empty($uniqueKey)) {
            $_SESSION['unique_key'] = $uniqueKey;
        }

        //set user language
        $_SESSION['authenticated_user_language'] = InputValidation::getService()->getValidInputRequest(
            'login_language',
            'Assert\Language',
            $sugarConfig->get('default_language')
        );

        $log->debug("authenticated_user_language is " . $_SESSION['authenticated_user_language']);

        // Clear all uploaded import files for this user if it exists
        $tmp_file_name = \ImportCacheFiles::getImportDir() . "/IMPORT_" . $currentUser->id;

        if (file_exists($tmp_file_name)) {
            unlink($tmp_file_name);
        }
    }
}
