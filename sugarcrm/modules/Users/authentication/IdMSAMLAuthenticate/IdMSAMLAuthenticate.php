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

use Sugarcrm\IdentityProvider\Authentication\Token\SAML\AcsToken;
use Sugarcrm\IdentityProvider\Authentication\Token\SAML\ConsumeLogoutToken;
use Sugarcrm\IdentityProvider\Authentication\Token\SAML\IdpLogoutToken;
use Sugarcrm\IdentityProvider\Authentication\Token\SAML\InitiateLogoutToken;
use Sugarcrm\IdentityProvider\Authentication\Token\SAML\InitiateToken;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config;
use Sugarcrm\Sugarcrm\IdentityProvider\Authentication\AuthProviderBasicManagerBuilder;
use Sugarcrm\Sugarcrm\Security\InputValidation\InputValidation;
use Sugarcrm\Sugarcrm\Session\SessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class IdMSAMLAuthenticate extends SAMLAuthenticate
{
    /**
     * Get URL to follow to get logged in
     *
     * @param array $returnQueryVars Query variables that should be added to the return URL
     *
     * @return string
     * @throws AuthenticationException
     */
    public function getLoginUrl($returnQueryVars = array())
    {
        $initToken = new InitiateToken();

        $config = $this->getConfig();
        $sameWindow = $config->get('SAML_SAME_WINDOW');

        $relayStateData = [
            'dataOnly' => 1,
        ];
        foreach ($returnQueryVars as $key => $value) {
            if (!is_null($value)) {
                $relayStateData[$key] = $value;
            }
        }
        if (!empty($returnQueryVars['platform']) && $returnQueryVars['platform'] == 'base' && !empty($sameWindow)) {
            unset($relayStateData['dataOnly']);
        }

        if ($relayStateData) {
            $initToken->setAttribute('returnTo', base64_encode(json_encode($relayStateData)));
        }

        $authManager = $this->getAuthProviderBasicBuilder($config)->buildAuthProviders();

        $token = $authManager->authenticate($initToken);

        $url = $token->getAttribute('url');

        return $url;
    }

    public function loginAuthenticate($username, $password, $fallback = false, $params = [])
    {
        if (empty($_POST['SAMLResponse'])) {
            return parent::loginAuthenticate($username, $password, $fallback, $params);
        }

        $acsToken = new AcsToken($_POST['SAMLResponse']);
        $authManager = $this->getAuthProviderBuilder($this->getConfig())->buildAuthProviders();
        $token = $authManager->authenticate($acsToken);

        if (!$token->isAuthenticated()) {
            return false;
        }

        $session = $this->getSession();
        $session['IdPSessionIndex'] = $token->getAttribute('IdPSessionIndex');

        return true;
    }

    /**
     * Get URL to follow to get logged out
     * @return string|array
     */
    public function getLogoutUrl()
    {
        $session = $this->getSession();
        $logoutToken = new InitiateLogoutToken();
        $logoutToken->setAttribute('nameId', $GLOBALS['current_user']->user_name);
        if (array_key_exists('IdPSessionIndex', $session)) {
            $logoutToken->setAttribute('sessionIndex', $session['IdPSessionIndex']);
        }
        $authManager = $this->getAuthProviderBasicBuilder($this->getConfig())->buildAuthProviders();

        $resultToken = $authManager->authenticate($logoutToken);
        switch ($resultToken->getAttribute('method')) {
            case Request::METHOD_POST:
                $params = [
                    'url' => $resultToken->getAttribute('url'),
                    'method' => $resultToken->getAttribute('method'),
                    'params' => $resultToken->getAttribute('parameters'),
                ];
                return $params;
            default:
                return $resultToken->getAttribute('url');
        }
    }

    /**
     * Called when a user requests to logout
     *
     * Override default behavior. Redirect user to special "Logged Out" page in
     * order to prevent automatic logging in.
     */
    public function logout()
    {
        $request = $this->getRequest();
        $requestRelayState = $request->getValidInputRequest('RelayState');
        $samlResponse = $request->getValidInputRequest('SAMLResponse');
        $samlRequest = $request->getValidInputRequest('SAMLRequest');
        if ($samlResponse) {
            $logoutToken = new ConsumeLogoutToken($samlResponse);
        } elseif ($samlRequest) {
            $logoutToken = new IdpLogoutToken($samlRequest);
            if ($requestRelayState) {
                $logoutToken->setAttribute('RelayState', $requestRelayState);
            }
        } else {
            return;
        }
        $logoutToken->setAuthenticated(true);

        $authManager = $this->getAuthProviderBasicBuilder($this->getConfig())->buildAuthProviders();
        $resultToken = $authManager->authenticate($logoutToken);
        if (!$resultToken->isAuthenticated()) {
            $url = $resultToken->hasAttribute('url') ? $resultToken->getAttribute('url') : $requestRelayState;
            if ($url) {
                $this->redirect($url);
            }
            $this->terminate();
        }
    }

    /**
     * Get idm configuration instance.
     *
     * @return \Sugarcrm\Sugarcrm\IdentityProvider\Authentication\Config
     */
    protected function getConfig()
    {
        return new Config(\SugarConfig::getInstance());
    }

    /**
     * @return \Sugarcrm\Sugarcrm\Session\SessionStorageInterface
     */
    protected function getSession()
    {
        return SessionStorage::getInstance();
    }

    /**
     * @return \Sugarcrm\Sugarcrm\Security\InputValidation\Request
     */
    protected function getRequest()
    {
        return InputValidation::getService();
    }

    /**
     * Redirect to the specified url
     *
     * @param string $url
     */
    protected function redirect($url)
    {
        ob_clean();
        RedirectResponse::create($url)->send();
        $this->terminate();
    }

    /**
     * Terminate execution
     */
    protected function terminate()
    {
        exit;
    }

    /**
     * @param Config $config
     *
     * @return AuthProviderBasicManagerBuilder
     */
    protected function getAuthProviderBasicBuilder(Config $config)
    {
        return new AuthProviderBasicManagerBuilder($config);
    }
}
