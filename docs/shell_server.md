---
layout: docs
title: Bundled Server
permalink: /docs/shell/server/
---

Strata ships with a server on which you can test your application. This server is not meant to be used in production. It is packaged only as a mean to lower the amount of prerequisites needed to start developing.

## Starting the server

Using the command line, run the `server` command from your project's base directory.

~~~ sh
$ bin/strata server
~~~

It will kickoff a running instance of your Wordpress installation available at `http://127.0.0.1:5454/`. There is no way of changing this url for the moment.
