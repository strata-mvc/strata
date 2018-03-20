<?php

namespace Strata\Controller;

use Strata\Utility\Hash;
use Strata\Strata;

/**
 * Handles safe access to HTTP request data, whether from POST, GET, files or cookies.
 * It encodes data accordingly and does basic data integrity validation.
 */
class Request
{
    /**
     * A cache of the parsed GET values in the current request.
     * @var array
     */
    private $_GET = array();

    /**
     * A cache of the parsed Cookie values in the current request.
     * @var array
     */
    private $_COOKIE = array();

    /**
     * A cache of the parsed POST values in the current request.
     * @var array
     */
    private $_POST = array();

    /**
     * A cache of the parsed Files values in the current request.
     * @var array
     */
    private $_FILES = array();

    /**
     * PHP request data is parsed upon each Request instantiation.
     */
    public function __construct()
    {
        $this->buildRequestData();
    }

    /**
     * Checks if the current value is of type POST.
     * @return boolean
     */
    public function isPost()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    /**
     * Checks if the current value is of type GET.
     * @return boolean
     */
    public function isGet()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
    }

    /**
     * Checks if the current value is of type PUT.
     * @return boolean
     */
    public function isPut()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'PUT';
    }

    /**
     * Checks if the current value is of type PATCH.
     * @return boolean
     */
    public function isPatch()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'PATCH';
    }

    /**
     * Checks if the current value is of type DELETE.
     * @return boolean
     */
    public function isDelete()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'DELETE';
    }

    /**
     * Sets a value in Strata's version of the POST array
     * @param string $key Variable name
     * @param mixed $value Variable value
     */
    public function setPost($key, $value)
    {
        $this->_POST = Hash::insert($this->_POST, $key, $value);
    }

    /**
     * Sets a value in Strata's version of the GET array
     * @param string $key Variable name
     * @param mixed $value Variable value
     */
    public function setGet($key, $value)
    {
        $this->_GET = Hash::insert($this->_GET, $key, $value);
    }

    /**
     * Returns the GET parameter matching $key.
     * @param string $key The name of the GET value
     * @return mixed
     */
    public function get($key)
    {
        return Hash::get($this->_GET, $key);
    }

    /**
     * Returns the POST parameter matching $key.
     * @param string $key The name of the POST value
     * @return mixed
     */
    public function post($key)
    {
        return Hash::get($this->_POST, $key);
    }

    /**
     * Returns the file parameter matching $key.
     * @param string $key The name of the POST value
     * @return array
     */
    public function file($key)
    {
        return array(
            "name" => Hash::get($this->_FILES, "data.name." .$key),
            "type" => Hash::get($this->_FILES, "data.type." . $key),
            "tmp_name" => Hash::get($this->_FILES, "data.tmp_name." . $key),
            "error" => Hash::get($this->_FILES, "data.error." . $key),
            "size" => Hash::get($this->_FILES, "data.size." . $key),
        );
    }

    /**
     * Returns the POST form parameters created using the FormHelper
     * @return array
     */
    public function data()
    {
        $workingWith = $this->isGet() ? $this->_GET : $this->_POST;
        $data = (array)Hash::get($workingWith, "data");

        // The previous line will have ignored uploaded files.
        // Added them to the array if we find something.
        if ($this->hasFiles()) {
            return $this->addFilesList($data);
        }

        return $data;
    }

    /**
     * Returns the cookie parameter matching $key.
     * @param string $key The name of the cookie value
     * @return mixed
     */
    public function cookie($key)
    {
        return Hash::get($this->_COOKIE, $key);
    }

    /**
     * Informs whether the POST parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasPost($key)
    {
        return Hash::check($this->_POST, $key);
    }

    /**
     * Informs whether the GET parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasGet($key)
    {
        return Hash::check($this->_GET, $key);
    }

    /**
     * Informs whether the cookie parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasCookie($key)
    {
        return Hash::check($this->_COOKIE, $key);
    }

    /**
     * Informs whether the file parameter matching $key has a value.
     * @param string $key The name of the file value
     * @return boolean
     */
    public function hasFile($key)
    {
        return Hash::check($this->_FILES, "data.name." . $key);
    }

    /**
     * Returns whether the request contains uploaded files
     * @return boolean
     */
    public function hasFiles()
    {
        return Hash::check($this->_FILES, "data.name") && count($this->_FILES["data"]["name"]);
    }

    /**
     * Returns whether the request can validate it's nonce and it's honeypot.
     * This function is expected to be used when the request isPost() mainly.
     * @param  string $nonce    A Wordpress nonce
     * @param  string $honeypot A honeypot input name
     * @return boolean
     */
    public function requestValidates($nonce = "", $honeypot = "")
    {
        return $this->nonceValidates($nonce) && $this->honeypotValidates($honeypot);
    }

    /**
     * Returns whether the honeypots validates. It is expected that
     * the $name input did not send any value when posting.
     * @param  string $name A honeypot input name
     * @return boolean
     */
    public function honeypotValidates($name)
    {
        $value = "";

        if ($this->hasPost($name)) {
            $value = $this->post($name);
        } elseif ($this->hasGet($name)) {
            $value = $this->get($name);
        }

        return empty($value);
    }

    /**
     * Attempts to confirm the validation of the Wordpress nonce.
     * @param  string $mixedNonceSalt
     * @return boolean
     * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
     */
    public function nonceValidates($mixedNonceSalt)
    {
        $token = null;

        if ($this->hasPost("authenticity_token")) {
            $token = $this->post("authenticity_token");
        } elseif ($this->hasGet("authenticity_token")) {
            $token = $this->get("authenticity_token");
        }

        if (is_null($token)) {
            return false;
        }

        $key = $this->generateNonceKey($mixedNonceSalt);

        return wp_verify_nonce($token, $key);
    }

    /**
     * Generated a Wordpress nonce value.
     * @param  mixed $mixedNonceSalt Something to salt the nonce
     * @return string
     */
    public function generateNonceKey($mixedNonceSalt = null)
    {
        if (!is_string($mixedNonceSalt)) {
            if (is_object($mixedNonceSalt)) {
                $methods = get_class_methods($mixedNonceSalt);
                $mixedNonceSalt = get_class($mixedNonceSalt) . implode("", $methods);
            } elseif (is_array($mixedNonceSalt)) {
                $mixedNonceSalt = json_encode($mixedNonceSalt);
            }
        }

        $strataSalt = Strata::app()->hasConfig('security.salt') ?
            Strata::app()->getConfig('security.salt') :
            crc32(getenv('SERVER_ADDR'));

        return $strataSalt . md5($mixedNonceSalt);
    }

    /**
     * Goes through the PHP request data, sanitizes and strips additional characters added
     * along the way.
     * @return null
     */
    private function buildRequestData()
    {
        $strip_slashes_deep = function ($value) use (&$strip_slashes_deep) {
            if (is_object($value)) {
                $value = json_decode(json_encode($value), true);
            }

            if (is_array($value)) {
                return array_map($strip_slashes_deep, $value);
            }

            return sanitize_text_field(stripslashes($value));
        };

        $this->_GET = array_map($strip_slashes_deep, $_GET);
        $this->_POST = array_map($strip_slashes_deep, $_POST);
        $this->_COOKIE = array_map($strip_slashes_deep, $_COOKIE);
        $this->_FILES = array_map($strip_slashes_deep, $_FILES);
    }

    /**
     * Adds uploaded files to the $data array.
     * @param array $data
     */
    private function addFilesList($data)
    {
        foreach ($this->_FILES['data']['name'] as $key => $filenames) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = array();
            }

            foreach ($filenames as $file => $filedata) {
                $data[$key][$file] = $this->file("$key.$file");
            }
        }

        return $data;
    }
}
