<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Professional End User
 * License Agreement ("License") which can be viewed at
 * http://www.sugarcrm.com/EULA.  By installing or using this file, You have
 * unconditionally agreed to the terms and conditions of the License, and You may
 * not use this file except in compliance with the License. Under the terms of the
 * license, You shall not, among other things: 1) sublicense, resell, rent, lease,
 * redistribute, assign or otherwise transfer Your rights to the Software, and 2)
 * use the Software for timesharing or service bureau purposes such as hosting the
 * Software for commercial gain and/or for the benefit of a third party.  Use of
 * the Software may be subject to applicable fees and any use of the Software
 * without first paying applicable fees is strictly prohibited.  You do not have
 * the right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for
 * requirements.  Your Warranty, Limitations of liability and Indemnity are
 * expressly stated in the License.  Please refer to the License for the specific
 * language governing these rights and limitations under the License.
 * Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.;
 * All Rights Reserved.
 ********************************************************************************/

class RestTestBase extends Sugar_PHPUnit_Framework_TestCase
{
    protected $authToken;

    protected function _restLogin($username = '', $password = '')
    {
        if ( empty($username) && empty($password) ) {
            $username = $GLOBALS['current_user']->user_name;
            // Let's assume test users have a password the same as their username
            $password = $GLOBALS['current_user']->user_name;
        }

        $args = array(
            'username' => $this->_user->user_name,
            'password' => $this->_user->user_name,
            'type' => 'text',
            'client-info' => array(
                'uuid'=>'SugarUnitTest',
            ),
        );
        
        $reply = $this->_restCall('login',json_encode($args));
        if ( empty($reply['reply']['token']) ) {
            throw new Exception("Rest authentication failed.");
        }
        $this->authToken = $reply['reply']['token'];
    }

    protected function _restCall($urlPart,$postBody='',$httpAction='')
    {
        $urlBase = $GLOBALS['sugar_config']['site_url'].'/rest/v9/';
        
        $ch = curl_init($urlBase.$urlPart);
        if (!empty($postBody)) {
            if (empty($httpAction)) {
                $httpAction = 'POST';
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
        } else {
            if (empty($httpAction)) {
                $httpAction = 'GET';
            }
        }
        
        if ( !empty($this->authToken) ) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('oauth_token: '.$this->authToken));
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $httpAction);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        $httpInfo = curl_getinfo($ch); 
        $httpReply = curl_exec($ch);

        return array('info' => $httpInfo, 'reply' => json_decode($httpReply,true), 'replyRaw' => $httpReply);
    }
}