---
layout: docs
title: Controller Request
permalink: /docs/controllers/request/
---

On each instantiation of the Controller object, a linked Request object is created. It is the wrapper around the current HTTP request. Use this wrapper to safely obtain HTTP request and cookie values.

~~~ php
<?php
namespace Mywebsite\Controller;

use Mywebsite\Model\Song;

class SongsController extends \Mywebsite\Controller\AppController {

    public function save()
    {
        if ($this->request->isPost()) {

            // This expects a form input named <input name="song[id]" ... >
            debug($this->request->post("song.id"));

            // This expects a form input named <input name="song[name]" ... >
            debug($this->request->post("song.name"));
        }
    }
}
?>
~~~

You may also define a new instance of `Request` in one of your object to gain its functionality.

~~~ php
<?php
namespace Mywebsite\Model;

use Strata\Controller\Request;

class SubmissionModel {

    public $request;

    public function __construct()
    {
        $this->request = new Request();
    }
}
?>
~~~

## Determining HTTP Request Method

There are 5 methods supplied by `Request` that check for a HTTP request method:

* `isGet()`
* `isPost()`
* `isPut()`
* `isPatch()`
* `isDelete()`

## Checking for an HTTP or a cookie value

You can check for the presence of data on HTTP methods and in cookies using the following methods. All of these take the path to the variable in dot notation. For example, `$key` could be `'user.firstname'`.

* `hasPost($key)`
* `hasGet($key)`
* `hasCookie($key)`

## Obtaining the HTTP or cookie value

You can obtain the value of form variables and of cookies using the following methods. All of these take the path to the variable in dot notation. For example, `$key` could be `'user.firstname'`.

* `post($key)`
* `get($key)`
* `cookie($key)`
