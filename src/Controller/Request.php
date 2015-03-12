<?php

namespace MVC\Controller;

use MVC\Utility\Hash;

class Request {

    protected $_GET = array();
    protected $_COOKIE = array();
    protected $_POST = array();

    function __construct()
    {
        $this->_buildRequestData();
    }

    public function isPost()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    public function isGet()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'GET';
    }

    public function isPut()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'PUT';
    }

    public function isPatch()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'PATCH';
    }

    public function isDelete()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'DELETE';
    }

    public function get($key)
    {
        return Hash::extract($this->_GET, $key);
    }

    public function post($key)
    {
        return Hash::extract($this->_POST, $key);
    }

    public function cookie($key)
    {
        return Hash::extract($this->_COOKIE, $key);
    }

    public function hasPost($key)
    {
        return Hash::check($this->_POST, $key);
    }

    public function hasGet($key)
    {
        return Hash::check($this->_GET, $key);
    }

    public function hasCookie($key)
    {
        return Hash::check($this->_COOKIE, $key);
    }

    protected function _buildRequestData()
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
