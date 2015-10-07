<?php
namespace Strata\Controller;

use Strata\Utility\Hash;

/**
 * Handles access to request data, whether from post, get or cookies. It encodes data accordingly and
 * does simple data integrity validation.
 */
class Request
{

    /**
     * A cache of the parsed Get values in the current request.
     *
     * @var array
     */
    private $_GET = array();

    /**
     * A cache of the parsed Cookie values in the current request.
     *
     * @var array
     */
    private $_COOKIE = array();

    /**
     * A cache of the parsed Post values in the current request.
     *
     * @var array
     */
    private $_POST = array();

    /**
     * A cache of the parsed Files values in the current request.
     *
     * @var array
     */
    private $_FILES = array();

    function __construct()
    {
        // Parse the request data upon each instantiation.
        $this->_buildRequestData();
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
     * Returns the get parameter matching $key.
     * @param string $key The name of the posted value
     * @return mixed
     */
    public function get($key)
    {
        return Hash::get($this->_GET, $key);
    }

    /**
     * Returns the post parameter matching $key.
     * @param string $key The name of the posted value
     * @return mixed
     */
    public function post($key)
    {
        return Hash::get($this->_POST, $key);
    }

    /**
     * Returns the file parameter matching $key.
     * @param string $key The name of the posted value
     * @return mixed
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
     * Returns the posted form parameters
     * @return mixed
     */
    public function data()
    {
        return Hash::get($this->_POST, "data");
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
     * Explains whether the post parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasPost($key)
    {
        return Hash::check($this->_POST, $key);
    }

    /**
     * Explains whether the get parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasGet($key)
    {
        return Hash::check($this->_GET, $key);
    }

    /**
     * Explains whether the cookie parameter matching $key has a value.
     * @param string $key The name of the cookie value
     * @return boolean
     */
    public function hasCookie($key)
    {
        return Hash::check($this->_COOKIE, $key);
    }

    /**
     * Explains whether the file parameter matching $key has a value.
     * @param string $key The name of the file value
     * @return boolean
     */
    public function hasFile($key)
    {
        return Hash::check($this->_FILES, "data.name." . $key);
    }

    public function requestValidates($nonce = "", $honeypot = "")
    {
        return $this->nonceValidates($nonce) && $this->honeypotValidates($honeypot);
    }


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

    public function generateNonceKey($mixedNonceSalt = null)
    {
        if (is_string($mixedNonceSalt)) {
            return $mixedNonceSalt;
        }

        if (is_a($mixedNonceSalt, "Strata\\Model\\CustomPostType\\ModelEntity")) {
            $distinction =  $mixedNonceSalt->isBound() ? $mixedNonceSalt->ID : "strata_request";
            return get_class($mixedNonceSalt) . $distinction;
        }

        return "strata_nonce";
    }


    /**
     * Goes through the posted data and strips additional characters added
     * along the way.
     * @return null
     */
    private function _buildRequestData()
    {
        $strip_slashes_deep = function ($value) use (&$strip_slashes_deep) {
            return is_array($value) ? array_map($strip_slashes_deep, $value) : sanitize_text_field(stripslashes($value));
        };
        $this->_GET = array_map($strip_slashes_deep, $_GET);
        $this->_POST = array_map($strip_slashes_deep, $_POST);
        $this->_COOKIE = array_map($strip_slashes_deep, $_COOKIE);
        $this->_FILES = array_map($strip_slashes_deep, $_FILES);
    }
}
