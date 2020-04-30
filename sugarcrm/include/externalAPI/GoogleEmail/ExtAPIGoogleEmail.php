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

/**
 * ExtAPIGoogleEmail
 */
class ExtAPIGoogleEmail extends ExternalAPIBase
{
    public $supportedModules = array('OutboundEmail', 'InboundEmail');
    public $authMethod = 'oauth2';
    public $connector = 'ext_eapm_google';

    public $useAuth = true;
    public $requireAuth = true;

    protected $scopes = array(
        Google_Service_Gmail::MAIL_GOOGLE_COM,
    );

    public $needsUrl = false;
    public $sharingOptions = null;

    const APP_STRING_ERROR_PREFIX = 'ERR_GOOGLE_API_';

    /**
     * Returns the Google Client object used to access Google servers, with
     * configurations set as we need
     *
     * @return Google_Client
     */
    public function getClient()
    {
        return $this->getGoogleClient();
    }

    /**
     * Creates a new instance of the Google client used to talk to Google
     * servers and configures it with the proper settings
     *
     * @return Google_Client
     */
    protected function getGoogleClient()
    {
        $config = $this->getGoogleOauth2Config();

        $client = new Google_Client();
        $client->setClientId($config['properties']['oauth2_client_id']);
        $client->setClientSecret($config['properties']['oauth2_client_secret']);
        $client->setRedirectUri($config['redirect_uri']);
        $client->setState('email');
        $client->setScopes($this->scopes);
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');

        return $client;
    }

    /**
     * Gets the configuration of the Google connector, and sets the correct
     * callback URI for this particular context (email)
     *
     * @return array
     */
    protected function getGoogleOauth2Config()
    {
        $config = array();
        require SugarAutoLoader::existingCustomOne('modules/Connectors/connectors/sources/ext/eapm/google/config.php');
        $config['redirect_uri'] = rtrim(SugarConfig::getInstance()->get('site_url'), '/')
            . '/index.php?module=EAPM&action=GoogleOauth2Redirect';

        return $config;
    }

    /**
     * Authenticates a user's authorization code with Google servers. On success,
     * Returns the token information as well as the ID of the EAPM bean created
     * to store the token information.
     *
     * @param string $code the authorization code provided by Google
     * @return array|bool
     */
    public function authenticate($code)
    {
        try {
            $client = $this->getClient();
            $client->authenticate($code);
        } catch (Google_Auth_Exception $e) {
            $GLOBALS['log']->error($e->getMessage());
            return false;
        }

        $eapmId = null;
        $token = $client->getAccessToken();
        if ($token) {
            $eapmId = $this->saveToken($token);
        }

        return array(
            'token' => $token,
            'eapmId' => $eapmId,
        );
    }

    /**
     * Saves a token in the EAPM table. If an EAPM bean ID is provided (and it
     * exists), that row will be updated. Otherwise, will create a new row
     *
     * @param string $accessToken the token information to store
     * @param string|null $eapmId optional: ID of the EAPM record to resave
     * @return string the ID of the EAPM bean saved
     */
    protected function saveToken($accessToken, $eapmId = null)
    {
        $bean = $this->getEAPMBean($eapmId);
        if (empty($bean->id)) {
            $bean->assigned_user_id = null;
            $bean->application = 'Google';
            $bean->validated = true;
        }
        $bean->api_data = $accessToken;
        $bean->skipReassignment = true;
        return $bean->save();
    }

    /**
     * Contacts the Google servers to revoke the token access for the given EAPM
     * bean ID. On success, also soft-deletes that row of the EAPM table.
     *
     * @param string $eapmId the ID of the EAPM bean to revoke token access for
     * @return bool true iff successful
     */
    public function revokeToken($eapmId)
    {
        $eapmBean = $this->getEAPMBean($eapmId);
        if (!empty($eapmBean->id)) {
            try {
                $client = $this->getClient();
                $client->setAccessToken($eapmBean->api_data);
                $client->revokeToken();
            } catch (Google_Auth_Exception $e) {
                return false;
            }

            $eapmBean->mark_deleted($eapmBean->id);
        }

        return true;
    }

    /**
     * Helper function for retrieving an EAPM bean by ID. Encoding is set to
     * false, so JSON formatted token strings will not be encoded. If no bean
     * is found, will return a new EAPM bean
     *
     * @param null|string $eapmId the ID of the EAPM bean to retrieve
     * @return null|SugarBean the retrieved EAPM bean, or a new one if not found
     */
    protected function getEAPMBean($eapmId = null)
    {
        return BeanFactory::getBean('EAPM', $eapmId, false);
    }

    /**
     * Uses an authenticated token to query the Google server to retrieve the
     * Google account's email address
     *
     * @param string $eapmId the ID of the EAPM bean storing the account's Oauth2 token
     * @return string
     */
    public function getEmailAddress($eapmId)
    {
        $eapmBean = $this->getEAPMBean($eapmId);
        if (!empty($eapmBean->id)) {
            try {
                $client = $this->getClient();
                $client->setAccessToken($eapmBean->api_data);
                $gmailClient = new Google_Service_Gmail($client);
                return $gmailClient->users->getProfile('me')->emailAddress;
            } catch (Google_Service_Exception $e) {
                $GLOBALS['log']->error($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Builds an array containing the credentials used by PHPMailer to authenticate
     * the given account with the Google SMTP server using Oauth2
     *
     * @param string $eapmId the ID of the EAPM bean storing the Google Oauth2 token
     * @return array|bool the Oauth credentials if successful; false otherwise
     */
    public function getPHPMailerOauthCredentials($eapmId)
    {
        $eapmBean = $this->getEAPMBean($eapmId);
        if (empty($eapmBean->id) || empty($eapmBean->api_data)) {
            return false;
        }

        try {
            // Get the Google connector configuration
            $config = $this->getGoogleOauth2Config();

            $apiData = json_decode($eapmBean->api_data, true);
            return [
                'clientId' => $config['properties']['oauth2_client_id'] ?? '',
                'clientSecret' => $config['properties']['oauth2_client_secret'] ?? '',
                'refreshToken' => $apiData['refresh_token'] ?? '',
            ];
        } catch (Exception $e) {
            $GLOBALS['log']->error($e->getMessage());
        }

        return false;
    }
}
