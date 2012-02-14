<?php

include_once("../include/rest/RestData.php");

abstract class RestObject implements IRestObject {

    private $requestData = array();
    private $httpVerb = null;

    function __construct() {
        $this->requestData["request_method"] = strtolower($_SERVER["REQUEST_METHOD"]);
        $this->requestData["raw_post_data"] = $raw_post = file_get_contents("php://input");

        if (array_key_exists("HTTP_ACCEPT_ENCODING", $_SERVER)) {
            $this->requestData["encoding_type"] = explode(",", $_SERVER["HTTP_ACCEPT_ENCODING"]);
        }

        if (array_key_exists("CONTENT_TYPE", $_SERVER)) {
            $this->requestData["content_type"] = $_SERVER["CONTENT_TYPE"];
        }

        $this->verbToId();
    }

    public function execute() {

    }

    protected function processResuestData() {

    }

    protected function verbToId() {
        $verb = "HTTP_" . $this->requestData["request_method"];
        $verb = strtoupper($verb);
        print "CRAP: {HTTP_GET}\n";
        print "FBAR::=>{$$verb}";
        die;
    }

    public function getRequestData() {
        return $this->requestData;
    }

}