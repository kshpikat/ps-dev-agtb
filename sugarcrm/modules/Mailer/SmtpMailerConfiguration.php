<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

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
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/

require_once "MailerConfiguration.php"; // requires MailerConfiguration in order to extend it

/**
 * Represents the configurations and contains the logic for setting the configurations for an SMTP Mailer.
 *
 * @extends MailerConfiguration
 */
class SmtpMailerConfiguration extends MailerConfiguration
{
    // constants used for documenting which communication protocol configurations are valid
    const CommunicationProtocolNone = "";
    const CommunicationProtocolSsl  = "ssl";
    const CommunicationProtocolTls  = "tls";

    // private members
    private $host;                  // the hostname of the SMTP server to use
    private $port;                  // the SMTP port to use on the server
    private $communicationProtocol; // the SMTP connection prefix ("", "ssl" or "tls")
    private $authenticate;          // true=require authentication on the SMTP server
    private $username;              // the username to use if authenticate=true
    private $password;              // the password to use if authenticate=true

    /**
     * Extends the default configurations for this sending strategy. Adds default SMTP configurations needed to send
     * email over SMTP using PHPMailer.
     *
     * @access public
     */
    public function loadDefaultConfigs() {
        parent::loadDefaultConfigs(); // load the base defaults

        $this->setHost();
        $this->setPort();
        $this->setCommunicationProtocol();
        $this->setAuthenticationRequirement();
        $this->setUsername();
        $this->setPassword();
    }

    /**
     * Sets or overwrites the host configuration. Multiple hosts can be supplied, but all hosts must be separated by a
     * semicolon (e.g. "smtp1.example.com;smtp2.example.com") and hosts will be tried in the order they are provided.
     *
     * The port for the host can be defined using the format:
     *
     *      host:port
     *
     * @access public
     * @param string $host required
     * @throws MailerException
     */
    public function setHost($host = "localhost") {
        if (!is_string($host)) {
            throw new MailerException(
                "Invalid Configuration: host must be a domain name or IP address (string) resolving to the SMTP server",
                MailerException::InvalidConfiguration
            );
        }

        $this->host = $host;
    }

    /**
     * Returns the host configuration.
     *
     * @access public
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Sets or overwrites the port number configuration. Default to 25, which is the default port number for SMTP.
     *
     * @access public
     * @param int $port required
     * @throws MailerException
     */
    public function setPort($port = 25) {
        if (!is_int($port)) {
            throw new MailerException(
                "Invalid Configuration: SMTP port must be an integer",
                MailerException::InvalidConfiguration
            );
        }

        $this->port = $port;
    }

    /**
     * Returns the port number configuration.
     *
     * @access public
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * Sets or overwrites the communication protocol configuration.
     *
     * @access public
     * @param string $communicationProtocol required
     * @throws MailerException
     */
    public function setCommunicationProtocol($communicationProtocol = self::CommunicationProtocolNone) {
        if (!self::isValidCommunicationProtocol($communicationProtocol)) {
            throw new MailerException(
                "Invalid Configuration: communication protocol is invalid",
                MailerException::InvalidConfiguration
            );
        }

        $this->communicationProtocol = $communicationProtocol;
    }

    /**
     * Returns the communication protocol configuration.
     *
     * @access public
     * @return string
     */
    public function getCommunicationProtocol() {
        return $this->communicationProtocol;
    }

    /**
     * Sets the requirement for authenticating with the SMTP server.
     *
     * @access public
     * @param bool $required required
     * @throws MailerException
     */
    public function setAuthenticationRequirement($required = false) {
        if (!is_bool($required)) {
            throw new MailerException(
                "Invalid Configuration: must be a boolean to determine authentication requirements",
                MailerException::InvalidConfiguration
            );
        }

        $this->authenticate = $required;
    }

    /**
     * Returns the configuration indicating whether or not authentication on the SMTP server is required.
     *
     * @access public
     * @return boolean
     */
    public function isAuthenticationRequired() {
        return $this->authenticate;
    }

    /**
     * Sets or overwrites the username configuration.
     *
     * @access public
     * @param string $username required
     * @throws MailerException
     */
    public function setUsername($username = "") {
        if (!is_string($username)) {
            throw new MailerException(
                "Invalid Configuration: username must be a string",
                MailerException::InvalidConfiguration
            );
        }

        $this->username = $username;
    }

    /**
     * Returns the username configuration.
     *
     * @access public
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Sets or overwrites the password configuration.
     *
     * @access public
     * @param string $password required
     * @throws MailerException
     */
    public function setPassword($password = "") {
        if (!is_string($password)) {
            throw new MailerException(
                "Invalid Configuration: password must be a string",
                MailerException::InvalidConfiguration
            );
        }

        //@todo do the from_html() thing?
        $this->password = $password;
    }

    /**
     * Returns the password configuration.
     *
     * @access public
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Returns true/false indicating whether or not $communicationProtocol is a valid, known communication protocol for
     * the context of a Mailer.
     *
     * @static
     * @access public
     * @param string $communicationProtocol required
     * @return bool
     */
    public static function isValidCommunicationProtocol($communicationProtocol) {
        switch ($communicationProtocol) {
            case self::CommunicationProtocolNone:
            case self::CommunicationProtocolSsl:
            case self::CommunicationProtocolTls:
                return true;
                break;
            default:
                return false;
                break;
        }
    }
}
