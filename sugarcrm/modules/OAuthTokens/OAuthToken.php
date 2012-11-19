<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 *  (i) the "Powered by SugarCRM" logo and
 *  (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/


require_once 'Zend/Oauth/Provider.php';
require_once 'modules/OAuthKeys/OAuthKey.php';

/**
 * OAuth token
 */
class OAuthToken extends SugarBean
{
	public $module_dir = 'OAuthTokens';
	public $object_name = 'OAuthToken';
	public $table_name = 'oauth_tokens';
	public $disable_row_level_security = true;

	public $token;
    public $secret;
    public $tstate;
    public $token_ts;
    public $verify;
    public $consumer;
    public $assigned_user_id;
    public $consumer_obj;
    public $callback_url;
    // authdata is not preserved so far since we don't have any useful data yet
    // so it's an extension point for the future
    public $authdata;

    const REQUEST = 1;
    const ACCESS = 2;
    const INVALID = 3;

    /**
     * This is a depreciated method, please start using __construct() as this method will be removed in a future version
     *
     * @see __construct
     * @depreciated
     */
    public function OAuthToken($token = '', $secret = '')
    {
        $this->__construct($token, $secret);
    }

    public function __construct($token='', $secret='')
	{
	    parent::__construct();
        $this->token = $token;
        $this->secret = $secret;
        $this->setState(self::REQUEST);
	}

	/**
	 * Set token state
	 * @param int $s
	 * @return OAuthToken
	 */
	public function setState($s)
	{
	    $this->tstate = $s;
	    return $this;
	}

	/**
	 * Associate the token with the consumer key
	 * @param OAuthKey $consumer
	 * @return OAuthToken
	 */
	public function setConsumer($consumer)
	{
	    $this->consumer = $consumer->id;
	    $this->consumer_obj = $consumer;
	    return $this;
	}

	/**
	 * Set callback URL for request token
	 * @param string $url
	 * @return OAuthToken
	 */
    public function setCallbackURL($url)
    {
        $this->callback_url = $url;
        return $this;
    }

    /**
	 * Generate random token
	 * @return string
	 */
	protected static function randomValue()
	{
	    return bin2hex(Zend_Oauth_Provider::generateToken(6));
	}

	/**
	 * Generate random token/secret pair and create token
	 * @return OAuthToken
	 */
    static function generate()
    {
        $t = self::randomValue();
        $s = self::randomValue();
        return new self($t, $s);
    }

    public function save()
    {
        $this->token_ts = time();
        if(!isset($this->id)) {
            $this->new_with_id = true;
            $this->id = $this->token;
        }
        parent::save();
    }

    /**
     * Load token by ID
     * @param string $token
     * @param string $oauth_type Either "oauth1" or "oauth2", defaults to "oauth1"
	 * @return OAuthToken
     */
    static function load($token,$oauth_type="oauth1")
	{
	    $ltoken = new self();
	    $ltoken->retrieve($token);
        if(empty($ltoken->id)) return null;
        $ltoken->token = $ltoken->id;
        if(!empty($ltoken->consumer)) {
            $ltoken->consumer_obj = BeanFactory::getBean('OAuthKeys',$ltoken->consumer);
            if(empty($ltoken->consumer_obj->id) || $ltoken->consumer_obj->oauth_type != $oauth_type ) {
                return null;
            }
        }
        return $ltoken;
	}

	/**
	 * Invalidate token
	 */
	public function invalidate()
	{
	    $this->setState(self::INVALID);
	    $this->verify = false;
	    return $this->save();
	}

	/**
	 * Create a new authorized token for specific user
	 * This bypasses normal OAuth process and creates a ready-made access token
	 * @param OAuthKey $consumer
	 * @param User $user
	 * @return OAuthToken
	 */
	public static function createAuthorized($consumer, $user)
	{
	    $token = self::generate();
	    $token->setConsumer($consumer);
	    $token->setState(self::ACCESS);
	    $token->assigned_user_id = $user->id;
        $token->save();
        return $token;
	}

	/**
	 * Authorize request token
	 * @param mixed $authdata
	 * @return string Validation token
	 */
	public function authorize($authdata)
	{
	    if($this->tstate != self::REQUEST) {
	        return false;
	    }
	    $this->verify = self::randomValue();
	    $this->authdata = $authdata;
	    if(isset($authdata['user'])) {
	        $this->assigned_user_id = $authdata['user'];
	    }
	    $this->save();
	    return $this->verify;
	}

	/**
	 * Copy auth data between tokens
	 * @param OAuthToken $token
	 * @return OAuthToken
	 */
	public function copyAuthData(OAuthToken $token)
	{
	    $this->authdata = $token->authdata;
	    $this->assigned_user_id = $token->assigned_user_id;
	    return $this;
	}

	/**
	 * Get query string for the token
	 */
	public function queryString()
	{
	    return "oauth_token={$this->token}&oauth_token_secret={$this->secret}";
	}

	/**
	 * Clean up stale tokens
	 */
    static public function cleanup()
	{
	    global $db;
	    // delete invalidated tokens older than 1 day
	    $db->query("DELETE FROM oauth_token WHERE status = ".self::INVALID." AND token_ts < ".time()-60*60*24);
	    // delete request tokens older than 1 day
	    $db->query("DELETE FROM oauth_token WHERE status = ".self::REQUEST." AND token_ts < ".time()-60*60*24);
	}

	/**
	 * Check if the nonce is valid
	 * @param string $key
	 * @param string $nonce
	 * @param string $ts
	 */
	public static function checkNonce($key, $nonce, $ts)
	{
	    global $db;

	    $res = $db->query(sprintf("SELECT * FROM oauth_nonce WHERE conskey='%s' AND nonce_ts > %d", $db->quote($key), $ts));
	    if($res && $db->fetchByAssoc($res)) {
	        // we have later ts
	        return Zend_Oauth_Provider::BAD_TIMESTAMP;
	    }

	    $res = $db->query(sprintf("SELECT * FROM oauth_nonce WHERE conskey='%s' AND nonce='%s' AND nonce_ts = %d", $db->quote($key), $db->quote($nonce), $ts));
	    if($res && $db->fetchByAssoc($res)) {
	        // Already seen this one
	        return Zend_Oauth_Provider::BAD_NONCE;
        }
        $db->query(sprintf("DELETE FROM oauth_nonce WHERE conskey='%s' AND nonce_ts < %d", $db->quote($key), $ts));
        $db->query(sprintf("INSERT INTO oauth_nonce(conskey, nonce, nonce_ts) VALUES('%s', '%s', %d)", $db->quote($key), $db->quote($nonce), $ts));
	    return Zend_Oauth_Provider::OK;
	}

	/**
	 * Delete token by ID
	 * @param string id
	 * @see SugarBean::mark_deleted($id)
	 */
	public function mark_deleted($id)
	{
	    $this->db->query("DELETE from {$this->table_name} WHERE id='".$this->db->quote($id)."'");
	}

	/**
	 * Delete tokens by consumer ID
	 * @param string $user
	 */
	public static function deleteByConsumer($consumer_id)
	{
	   global $db;
	   $db->query("DELETE FROM oauth_tokens WHERE consumer='".$db->quote($consumer_id) ."'");
	}

	/**
	 * Delete tokens by user ID
	 * @param string $user
	 */
	public static function deleteByUser($user_id)
	{
	   global $db;
	   $db->query("DELETE FROM oauth_tokens WHERE assigned_user_id='".$db->quote($user_id) ."'");
	}


}

function displayDateFromTs($focus, $field, $value, $view='ListView')
{
    $field = strtoupper($field);
    if(!isset($focus[$field])) return '';
    // No date, don't return anything
    if($focus[$field]==-1) return '';
    global $timedate;
    return $timedate->asUser($timedate->fromTimestamp($focus[$field]));
}
