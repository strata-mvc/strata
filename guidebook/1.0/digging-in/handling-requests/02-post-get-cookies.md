---
layout: guidebook
title: POST, GET and Cookies
permalink: /guidebook/1.0/digging-in/handling-requests/post-get-cookies/
covered_tags: request, post, get, cookies
menu_group: handling-requests
---

On each instantiation of the Controller object a Request object is created. It is the wrapper around the current HTTP request. Use this wrapper to safely obtain HTTP request and cookie values.

You may also define another instance of `Request` in one of your object to gain its functionality anywhere else. Understand that data from the original Controller's request object will not transfer to other request instances.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Model\Service;

use Strata\Controller\Request;

class MyOtherApiService {

    public $request;

    public function __construct()
    {
        $this->request = new Request();
    }
}
{% endhighlight %}
{% include terminal_end.html %}

## Determining HTTP Request Method

There are 5 methods supplied by `Request` that check for the current HTTP request method:

* `isGet()`
* `isPost()`
* `isPut()`
* `isPatch()`
* `isDelete()`

## Checking for an HTTP or a cookie value

You can *check* for the presence of data on each of the request types using the following methods. All of these can take the path to a variable in dot notation. For example, `$key` could be `'user.firstname'`.

* `hasPost($key)`
* `hasGet($key)`
* `hasCookie($key)`
* `hasFile($key)`

## Obtaining the HTTP or cookie value

You can *obtain* the current value stored in each of the request types datasources using the following methods. All of these take the path to the variable in dot notation. For example, `$key` could be `'user.firstname'`.

* `post($key)`
* `get($key)`
* `cookie($key)`
* `file($key)`


## Using it in a Controller

This example illustrates the many ways of using the `Request` object in a Controller.

{% include terminal_start.html %}
{% highlight php linenos %}
<?php
namespace App\Controller;

use App\Model\Song;

class SongsController extends AppController {

    public function before()
    {
        // Obtains the equivalent of $_COOKIE['currentUserName'].
        debug($this->request->cookie("currentUserName"));
    }

    public function index()
    {
        // Obtains the equivalent of $_GET['foo'].
        debug($this->request->get("foo"));
    }

    public function save()
    {
        if ($this->request->isPost()) {
            // This expects a form input named <input name="song[id]" ... >
            // Obtains the equivalent of $_POST['song']['id'].
            debug($this->request->post("song.id"));

            // This expects a form input named <input name="song[name]" ... >
            // Obtains the equivalent of $_POST['song']['name'].
            debug($this->request->post("song.name"));
        }
    }
}
{% endhighlight %}
{% include terminal_end.html %}
