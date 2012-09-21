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
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 ********************************************************************************/
/*********************************************************************************
 * $Id: upload_file.php 55278 2010-03-15 13:45:13Z jmertic $
 * Description:
 ********************************************************************************/
require_once('include/externalAPI/ExternalAPIFactory.php');

/**
 * @api
 * Manage uploaded files
 */
class UploadFile
{
	public $field_name;
	public $stored_file_name;
	public $original_file_name;
	public $temp_file_location;
	public $use_soap = false;
	public $file;
	public $file_ext;

    /**
     * An error array, meant to be accessed by consumers and callers of this
     * class for reporting status.
     *
     * This array will contain two members:
     *  - code: An error code reported by the uploader
     *  - message: The error string to report
     *
     * @access public
     * @var array
     */
    public $error = array();

	protected static $url = "upload/";

	/**
	 * Upload errors
	 * @var array
	 */
	protected static $filesError = array(
			UPLOAD_ERR_OK => 'UPLOAD_ERR_OK - There is no error, the file uploaded with success.',
			UPLOAD_ERR_INI_SIZE => 'UPLOAD_ERR_INI_SIZE - The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			UPLOAD_ERR_FORM_SIZE => 'UPLOAD_ERR_FORM_SIZE - The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			UPLOAD_ERR_PARTIAL => 'UPLOAD_ERR_PARTIAL - The uploaded file was only partially uploaded.',
			UPLOAD_ERR_NO_FILE => 'UPLOAD_ERR_NO_FILE - No file was uploaded.',
			5 => 'UNKNOWN ERROR',
			UPLOAD_ERR_NO_TMP_DIR => 'UPLOAD_ERR_NO_TMP_DIR - Missing a temporary folder.',
			UPLOAD_ERR_CANT_WRITE => 'UPLOAD_ERR_CANT_WRITE - Failed to write file to disk.',
			UPLOAD_ERR_EXTENSION => 'UPLOAD_ERR_EXTENSION - A PHP extension stopped the file upload.',
			);

	/**
	 * Create upload file handler
	 * @param string $field_name Form field name
	 */
	function UploadFile ($field_name = '')
	{
		// $field_name is the name of your passed file selector field in your form
		// i.e., for Emails, it is "email_attachmentX" where X is 0-9
		$this->field_name = $field_name;
	}

	/**
	 * Setup for SOAP upload
	 * @param string $filename Name for the file
	 * @param string $file
	 */
	function set_for_soap($filename, $file) {
		$this->stored_file_name = $filename;
		$this->use_soap = true;
		$this->file = $file;
	}

	/**
	 * Get URL for a document
	 * @deprecated
	 * @param string stored_file_name File name in filesystem
	 * @param string bean_id note bean ID
	 * @return string path with file name
	 */
	public static function get_url($stored_file_name, $bean_id)
	{
		if ( empty($bean_id) && empty($stored_file_name) ) {
            return self::$url;
		}

		return self::$url . $bean_id;
	}

	/**
	 * Get URL of the uploaded file related to the document
	 * @param SugarBean $document
	 * @param string $type Type of the document, if different from $document
	 * @return string the URL
	 */
	public static function get_upload_url($document, $type = null)
	{
	    if(empty($type)) {
	        $type = $document->module_dir;
	    }
	    return "index.php?entryPoint=download&type=$type&id={$document->id}";
	}

	/**
	 * Try renaming a file to bean_id name
	 * @param string $filename
	 * @param string $bean_id
	 * @return bool Success?
	 */
	protected static function tryRename($filename, $bean_id)
	{
	    $fullname = "upload://$bean_id.$filename";
	    if(file_exists($fullname)) {
            if(!rename($fullname,  "upload://$bean_id")) {
                $this->setError('fatal', "unable to rename file: $fullname => $bean_id");
            }
	        return true;
	    }
	    return false;
	}

	/**
	 * builds a URL path for an anchor tag
	 * @param string $stored_file_name File name in filesystem
	 * @param string $bean_id note bean ID
	 * @return string $path with file name
	 */
	static public function get_file_path($stored_file_name, $bean_id, $skip_rename = false)
	{
		global $locale;

        // if the parameters are empty strings, just return back the upload_dir
		if ( empty($bean_id) && empty($stored_file_name) ) {
            return "upload://";
		}

		if(!$skip_rename) {
    		self::tryRename(rawurlencode($stored_file_name), $bean_id) ||
    		self::tryRename(urlencode($stored_file_name), $bean_id) ||
    		self::tryRename($stored_file_name, $bean_id) ||
    		self::tryRename($locale->translateCharset( $stored_file_name, 'UTF-8', $locale->getExportCharset()), $bean_id);
		}

		return "upload://$bean_id";
	}

	/**
	 * duplicates an already uploaded file in the filesystem.
	 * @param string $old_id ID of original note
	 * @param string $new_id ID of new (copied) note
	 * @param string $filename Filename of file (deprecated)
	 */
	public static function duplicate_file($old_id, $new_id, $file_name)
	{
		global $sugar_config;

		// current file system (GUID)
		$source = "upload://$old_id";

		if(!file_exists($source)) {
			// old-style file system (GUID.filename.extension)
			$oldStyleSource = $source.$file_name;
			if(file_exists($oldStyleSource)) {
				// change to new style
				if(copy($oldStyleSource, $source)) {
					// delete the old
					if(!unlink($oldStyleSource)) {
                        $GLOBALS['log']->error("upload_file could not unlink [ {$oldStyleSource} ]");
					}
				} else {
                    $GLOBALS['log']->error("upload_file could not copy [ {$oldStyleSource} ] to [ {$source} ]");
                    return false;
				}
			} else {
			    return false;
			}
		}

		$destination = "upload://$new_id";
		if(!copy($source, $destination)) {
            $GLOBALS['log']->error("upload_file could not copy [ {$source} ] to [ {$destination} ]");
            return false;
		}
		return true;
	}

	/**
	 * Get upload error from system
	 * @return string upload error
	 */
	public function get_upload_error()
	{
	    if(isset($this->field_name) && isset($_FILES[$this->field_name]['error'])) {
	        return $_FILES[$this->field_name]['error'];
	    }
	    return false;
	}

	/**
	 * standard PHP file-upload security measures. all variables accessed in a global context
	 * @return bool True on success
	 */
	public function confirm_upload()
	{
		global $sugar_config;

		if(empty($this->field_name) || !isset($_FILES[$this->field_name])) {
		    return false;
		}

        //check to see if there are any errors from upload
		if($_FILES[$this->field_name]['error'] != UPLOAD_ERR_OK) {
		    if($_FILES[$this->field_name]['error'] != UPLOAD_ERR_NO_FILE) {
                if($_FILES[$this->field_name]['error'] == UPLOAD_ERR_INI_SIZE) {
                    //log the error, the string produced will read something like:
                    //ERROR: There was an error during upload. Error code: 1 - UPLOAD_ERR_INI_SIZE - The uploaded file exceeds the upload_max_filesize directive in php.ini. upload_maxsize is 16
                    $errMess = string_format($GLOBALS['app_strings']['UPLOAD_ERROR_TEXT_SIZEINFO'],array($_FILES['filename_file']['error'], self::$filesError[$_FILES['filename_file']['error']],$sugar_config['upload_maxsize']));
                    $this->setError('fatal', $errMess, $_FILES['filename_file']['error']);
                }else{
                    //log the error, the string produced will read something like:
                    //ERROR: There was an error during upload. Error code: 3 - UPLOAD_ERR_PARTIAL - The uploaded file was only partially uploaded.
                    $errMess = string_format($GLOBALS['app_strings']['UPLOAD_ERROR_TEXT'],array($_FILES['filename_file']['error'], self::$filesError[$_FILES['filename_file']['error']]));
                    $this->setError('fatal', $errMess, $_FILES['filename_file']['error']);
                }
		    }
		    return false;
		}

        // Added Sugar API Override flag to FILES to allow PUT API hits to work
		//if(!is_uploaded_file($_FILES[$this->field_name]['tmp_name']) || !isset($_FILES[$this->field_name]['_SUGAR_API_UPLOAD']) || $_FILES[$this->field_name]['_SUGAR_API_UPLOAD'] !== true) {
		//	return false;
		//} elseif($_FILES[$this->field_name]['size'] > $sugar_config['upload_maxsize']) {
		//    $GLOBALS['log']->fatal("ERROR: uploaded file was too big: max filesize: {$sugar_config['upload_maxsize']}");
		//	return false;
		//}
        if (is_uploaded_file($_FILES[$this->field_name]['tmp_name']) || (isset($_FILES[$this->field_name]['_SUGAR_API_UPLOAD']) && $_FILES[$this->field_name]['_SUGAR_API_UPLOAD'] === true)) {
            if($_FILES[$this->field_name]['size'] > $sugar_config['upload_maxsize']) {
                $this->setError('fatal', "ERROR: uploaded file was too big: max filesize: {$sugar_config['upload_maxsize']}");
                return false;
            }
        } else {
            return false;
        }

		if(!UploadStream::writable()) {
		    $this->setError('fatal', "ERROR: cannot write to upload directory");
			return false;
		}

		$this->mime_type = $this->getMime($_FILES[$this->field_name]);
		$this->stored_file_name = $this->create_stored_filename();
		$this->temp_file_location = $_FILES[$this->field_name]['tmp_name'];

		return true;
	}

	/**
	 * Guess MIME type for file
	 * @param string $filename
	 * @return string MIME type
	 */
	function getMimeSoap($filename){

		if( function_exists( 'ext2mime' ) )
		{
			$mime = ext2mime($filename);
		}
		else
		{
			$mime = ' application/octet-stream';
		}
		return $mime;

	}

	/**
	 * Get MIME type for uploaded file
	 * @param array $_FILES_element $_FILES element required
	 * @return string MIME type
	 */
	function getMime($_FILES_element)
	{
		$filename = $_FILES_element['name'];
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);

        //If no file extension is available and the mime is octet-stream try to determine the mime type.
        $recheckMime = empty($file_ext) && ($_FILES_element['type']  == 'application/octet-stream');

		if( $_FILES_element['type'] && !$recheckMime) {
			$mime = $_FILES_element['type'];
		} elseif( function_exists( 'mime_content_type' ) ) {
			$mime = mime_content_type( $_FILES_element['tmp_name'] );
		} elseif( function_exists( 'ext2mime' ) ) {
			$mime = ext2mime( $_FILES_element['name'] );
		} else {
			$mime = ' application/octet-stream';
		}
		return $mime;
	}

	/**
	 * gets note's filename
	 * @return string
	 */
	function get_stored_file_name()
	{
		return $this->stored_file_name;
	}

	/**
	 * creates a file's name for preparation for saving
	 * @return string
	 */
	function create_stored_filename()
	{
		global $sugar_config;

		if(!$this->use_soap) {
			$stored_file_name = $_FILES[$this->field_name]['name'];
			$this->original_file_name = $stored_file_name;

			/**
			 * cn: bug 8056 - windows filesystems and IIS do not like utf8.  we are forced to urlencode() to ensure that
			 * the file is linkable from the browser.  this will stay broken until we move to a db-storage system
			 */
			if(is_windows()) {
				// create a non UTF-8 name encoding
				// 176 + 36 char guid = windows' maximum filename length
				$end = (strlen($stored_file_name) > 176) ? 176 : strlen($stored_file_name);
				$stored_file_name = substr($stored_file_name, 0, $end);
				$this->original_file_name = $_FILES[$this->field_name]['name'];
			}
		    $stored_file_name = str_replace("\\", "", $stored_file_name);
		} else {
			$stored_file_name = $this->stored_file_name;
			$this->original_file_name = $stored_file_name;
		}

		$this->file_ext = pathinfo($stored_file_name, PATHINFO_EXTENSION);
        // cn: bug 6347 - fix file extension detection
        foreach($sugar_config['upload_badext'] as $badExt) {
            if(strtolower($this->file_ext) == strtolower($badExt)) {
                $stored_file_name .= ".txt";
                $this->file_ext="txt";
                break; // no need to look for more
            }
        }
		return $stored_file_name;
	}

	/**
	 * moves uploaded temp file to permanent save location
	 * @param string $bean_id ID of parent bean
	 * @return bool True on success
	 */
	function final_move($bean_id)
	{
	    $destination = $bean_id;
	    if(substr($destination, 0, 9) != "upload://") {
            $destination = "upload://$bean_id";
	    }
        if($this->use_soap) {
        	if(!file_put_contents($destination, $this->file)){
        	    $this->setError('fatal', "ERROR: can't save file to $destination");
                return false;
        	}
		} else {
			if(!UploadStream::move_uploaded_file($_FILES[$this->field_name]['tmp_name'], $destination)) {
                if (isset($_FILES[$this->field_name]['_SUGAR_API_UPLOAD']) && $_FILES[$this->field_name]['_SUGAR_API_UPLOAD'] === true) {
                    // Try to move it manually
                    if (copy($_FILES[$this->field_name]['tmp_name'], $destination)) {
                        unlink($_FILES[$this->field_name]['tmp_name']);
                    } else {
                        $this->setError('fatal', "ERROR: can't move_uploaded_file to $destination. You should try making the directory writable by the webserver");
                        return false;
                    }
                } else {
                    $this->setError('fatal', "ERROR: can't move_uploaded_file to $destination. You should try making the directory writable by the webserver");
                    return false;
                }
			}
		}
		return true;
	}

	/**
	 * Upload document to external service
	 * @param SugarBean $bean Related bean
	 * @param string $bean_id
	 * @param string $doc_type
	 * @param string $file_name
	 * @param string $mime_type
	 */
	function upload_doc($bean, $bean_id, $doc_type, $file_name, $mime_type)
	{
		if(!empty($doc_type)&&$doc_type!='Sugar') {
			global $sugar_config;
	        $destination = $this->get_upload_path($bean_id);
	        sugar_rename($destination, str_replace($bean_id, $bean_id.'_'.$file_name, $destination));
	        $new_destination = $this->get_upload_path($bean_id.'_'.$file_name);

		    try{
                $this->api = ExternalAPIFactory::loadAPI($doc_type);

                if ( isset($this->api) && $this->api !== false ) {
                    $result = $this->api->uploadDoc(
                        $bean,
                        $new_destination,
                        $file_name,
                        $mime_type
                        );
                } else {
                    $result['success'] = FALSE;
                    // FIXME: Translate
                    $GLOBALS['log']->error("Could not load the requested API (".$doc_type.")");
                    $result['errorMessage'] = 'Could not find a proper API';
                }
            }catch(Exception $e){
                $result['success'] = FALSE;
                $result['errorMessage'] = $e->getMessage();
                $GLOBALS['log']->error("Caught exception: (".$e->getMessage().") ");
            }
            if ( !$result['success'] ) {
                sugar_rename($new_destination, str_replace($bean_id.'_'.$file_name, $bean_id, $new_destination));
                $bean->doc_type = 'Sugar';
                // FIXME: Translate
                if ( ! is_array($_SESSION['user_error_message']) )
                    $_SESSION['user_error_message'] = array();

                $error_message = isset($result['errorMessage']) ? $result['errorMessage'] : $GLOBALS['app_strings']['ERR_EXTERNAL_API_SAVE_FAIL'];
                $_SESSION['user_error_message'][] = $error_message;

            }
            else {
                unlink($new_destination);
            }
        }

	}

	/**
	 * returns the path with file name to save an uploaded file
	 * @param string $bean_id ID of the parent bean
	 * @return string path
	 */
	function get_upload_path($bean_id)
	{
		$file_name = $bean_id;

		// cn: bug 8056 - mbcs filename in urlencoding > 212 chars in Windows fails
		$end = (strlen($file_name) > 212) ? 212 : strlen($file_name);
		$ret_file_name = substr($file_name, 0, $end);

		return "upload://$ret_file_name";
	}

	/**
	 * deletes a file
	 * @param string $bean_id ID of the parent bean
	 * @param string $file_name File's name
	 * @return bool Success?
	 */
	static public function unlink_file($bean_id,$file_name = '')
	{
	    if(file_exists("upload://$bean_id$file_name")) {
            return unlink("upload://$bean_id$file_name");
	    }
    }

    /**
     * Get upload file location prefix
     * @return string prefix
     */
    public function get_upload_dir()
    {
        return "upload://";
    }

    /**
     * Return real FS path of the file
     * @param string $path
     * @return string path
     */
    public static function realpath($path)
    {
       if(substr($path, 0, 9) == "upload://") {
           $path = UploadStream::path($path);
       }
       $ret = realpath($path);
       return $ret?$ret:$path;
    }

    /**
     * Return path of uploaded file relative to uploads dir
     * @param string $path
     * @return string path
     */
    public static function relativeName($path)
    {
        if(substr($path, 0, 9) == "upload://") {
            $path = substr($path, 9);
        }
        return $path;
    }

    /**
     * Gets the last reported error. Optionally will return just the error message.
     *
     * @param bool $messageOnly
     * @return array|null
     */
    public function getError($messageOnly = false) {
        return $messageOnly ? $this->getErrorMessage() : $this->error;
    }

    /**
     * Gets just the error message from the last reported error.
     *
     * @return string|null
     */
    public function getErrorMessage() {
        return empty($this->error['message']) ? null : $this->error['message'];
    }

    /**
     * Sets an error message and optional error code
     *
     * @param string $type
     * @param string $message
     * @param int $code
     */
    protected function setError($type, $message, $code = 0) {
        // Read it into the error array
        $this->error['message'] = $message;
        $this->error['code'] = $code;

        // Send it to the log
        $GLOBALS['log']->$type($message);
    }
}

/**
 * @internal
 * Upload file stream handler
 */
class UploadStream
{
    const STREAM_NAME = "upload";
    protected static $upload_dir;

    /**
     * Method checks Suhosin restrictions to use streams in php
     *
     * @static
     * @return bool is allowed stream or not
     */
    public static function getSuhosinStatus()
    {
        // looks like suhosin patch doesn't block protocols, only suhosin extension (tested on FreeBSD)
        // if suhosin is not installed it is okay for us
        if (extension_loaded('suhosin') == false)
        {
            return true;
        }
        $configuration = ini_get_all('suhosin', false);

        // suhosin simulation is okay for us
        if ($configuration['suhosin.simulation'] == true)
        {
            return true;
        }

        // checking that UploadStream::STREAM_NAME is allowed by white list
        $streams = $configuration['suhosin.executor.include.whitelist'];
        if ($streams != '')
        {
            $streams = explode(',', $streams);
            foreach($streams as $stream)
            {
                $stream = explode('://', $stream, 2);
                if (count($stream) == 1)
                {
                    if ($stream[0] == UploadStream::STREAM_NAME)
                    {
                        return true;
                    }
                }
                elseif ($stream[1] == '' && $stream[0] == UploadStream::STREAM_NAME)
                {
                    return true;
                }
            }

            $GLOBALS['log']->fatal('Stream ' . UploadStream::STREAM_NAME . ' is not listed in suhosin.executor.include.whitelist and blocked because of it');
            return false;
        }

        // checking that UploadStream::STREAM_NAME is not blocked by black list
        $streams = $configuration['suhosin.executor.include.blacklist'];
        if ($streams != '')
        {
            $streams = explode(',', $streams);
            foreach($streams as $stream)
            {
                $stream = explode('://', $stream, 2);
                if ($stream[0] == UploadStream::STREAM_NAME)
                {
                    $GLOBALS['log']->fatal('Stream ' . UploadStream::STREAM_NAME . 'is listed in suhosin.executor.include.blacklist and blocked because of it');
                    return false;
                }
            }
            return true;
        }

        $GLOBALS['log']->fatal('Suhosin blocks all streams, please define ' . UploadStream::STREAM_NAME . ' stream in suhosin.executor.include.whitelist');
        return false;
    }

    /**
     * Get upload directory
     * @return string
     */
    public static function getDir()
    {
        if(empty(self::$upload_dir)) {
            self::$upload_dir = rtrim($GLOBALS['sugar_config']['upload_dir'], '/\\');
            if(empty(self::$upload_dir)) {
                self::$upload_dir = "upload";
            }
            if(!file_exists(self::$upload_dir)) {
                sugar_mkdir(self::$upload_dir, 0755, true);
            }
        }
        return self::$upload_dir;
    }

    /**
     * Check if upload dir is writable
     * @return bool
     */
    public static function writable()
    {
        return is_writable(self::getDir());
    }

    /**
     * Register the stream
     */
    public function register()
    {
        stream_register_wrapper(self::STREAM_NAME, __CLASS__);
    }

    /**
     * Get real FS path of the upload stream file
     * @param string $path Upload stream path (with upload://)
     * @return string FS path
     */
    public static function path($path)
    {
    	$path = substr($path, strlen(self::STREAM_NAME)+3); // cut off upload://
    	$path = str_replace("\\", "/", $path); // canonicalize path
    	if($path == ".." || substr($path, 0, 3) == "../" || substr($path, -3, 3) == "/.." || strstr($path, "/../")) {
    		$GLOBALS['log']->fatal("Invalid uploaded file name supplied: $path");
    		return null;
    	}
        return self::getDir()."/".$path;
    }

    /**
     * Ensure upload subdir exists
     * @param string $path Upload stream path (with upload://)
     * @param bool $writable
     * @return boolean
     */
    public static function ensureDir($path, $writable = true)
    {
        $path = self::path($path);
        if(!is_dir($path)) {
           return sugar_mkdir($path, 0755, true);
        }
        return true;
    }

    public function dir_closedir()
    {
        closedir($this->dirp);
    }

    public function dir_opendir ($path, $options )
    {
        $this->dirp = opendir(self::path($path));
        return !empty($this->dirp);
    }

    public function dir_readdir()
    {
        return readdir($this->dirp);
    }

    public function dir_rewinddir()
    {
        return rewinddir($this->dirp);
    }

    public function mkdir($path, $mode, $options)
    {
        return mkdir(self::path($path), $mode, ($options&STREAM_MKDIR_RECURSIVE) != 0);
    }

    public function rename($path_from, $path_to)
    {
        return rename(self::path($path_from), self::path($path_to));
    }

    public function rmdir($path, $options)
    {
        return rmdir(self::path($path));
    }

    public function stream_cast ($cast_as)
    {
        return $this->fp;
    }

    public function stream_close ()
    {
        fclose($this->fp);
        return true;
    }

    public function stream_eof ()
    {
        return feof($this->fp);
    }
   public function stream_flush ()
    {
        return fflush($this->fp);
    }

    public function stream_lock($operation)
    {
        return flock($this->fp, $operation);
    }

    public function stream_open($path, $mode)
    {
        $fullpath = self::path($path);
        if(empty($fullpath)) return false;
        if($mode == 'r') {
            $this->fp = fopen($fullpath, $mode);
        } else {
            // if we will be writing, try to transparently create the directory
            $this->fp = @fopen($fullpath, $mode);
            if(!$this->fp && !file_exists(dirname($fullpath))) {
                mkdir(dirname($fullpath), 0755, true);
                $this->fp = fopen($fullpath, $mode);
            }
        }
        return !empty($this->fp);
    }

    public function stream_read($count)
    {
        return fread($this->fp, $count);
    }

    public function stream_seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->fp, $offset, $whence) == 0;
    }

    public function stream_set_option($option, $arg1, $arg2)
    {
        return true;
    }

    public function stream_stat()
    {
        return fstat($this->fp);
    }

    public function stream_tell()
    {
        return ftell($this->fp);
    }
    public function stream_write($data)
    {
        return fwrite($this->fp, $data);
    }

    public function unlink($path)
    {
        unlink(self::path($path));
        return true;
    }

    public function url_stat($path, $flags)
    {
        return @stat(self::path($path));
    }

    public static function move_uploaded_file($upload, $path)
    {
        return move_uploaded_file($upload, self::path($path));
    }
}

