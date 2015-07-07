---
layout: docs
title: Bundled Server
permalink: /docs/shell/server/
---

Strata ships with a server on which you can test your application. This server is not meant to be used in production. It is packaged only as a mean to lower the amount of prerequisites needed before one can start developing.

## Starting the server

Using the command line, run the `server` command from your project's base directory.

~~~ sh
$ bin/strata server
~~~

It will kickoff a running instance of your Wordpress installation available at `http://127.0.0.1:5454/`. There is no way of changing this url for the moment.

Additionally the environment will always be `development` when the project is ran from the bundled server.


## .htaccess

Wordpress relies on Apache's .htaccess to generate pretty urls. The bundled server does not read htaccess files and therefore will not replace the full url path.

In practice this means you will always see `index.php` at the base of your permalinks when running from the bundled server. Routing rules do not need to bother handling the extra `index.php`, Strata handles it for you. You routes should be the ones expected in production.
