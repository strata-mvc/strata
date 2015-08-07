<?php
namespace Strata\Controller;

use Strata\Utility\Hash;

/**
 * Handles access to request data, whether from post, get or cookies. It encodes data accordingly and
 * does simple data integrity validation.
 */
class Request {

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
     * Goes through the posted data and strips additional characters added
     * along the way.
     * @return null
     */
    private function _buildRequestData()
    {
        $strip_slashes_deep = function ($value) use (&$strip_slashes_deep) {
            return is_array($value) ? array_map($strip_slashes_deep, $value) : htmlspecialchars(stripslashes($value));
        };
        $this->_GET = array_map($strip_slashes_deep, $_GET);
        $this->_POST = array_map($strip_slashes_deep, $_POST);
        $this->_COOKIE = array_map($strip_slashes_deep, $_COOKIE);
    }

}
?>
