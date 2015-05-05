---
layout: docs
title: Shell
permalink: /docs/shell/
---

Strata ships with multiple CLI tools that allow you to automate actions. The basic call to trigger the command line interface is:

~~~ sh
$ bin/strata _command_ _arguments_
~~~

## Bundled tools

* [Documentation tool](/docs/shell/documenting)
* [Class Generator](/docs/shell/generator)
* [Migration](/docs/shell/migrations)
* [Bundled server](/docs/shell/server)
* [Testing suite](/docs/shell/testing)

## Custom commands

{% include workinprogress.html %}

For the moment, you cannot add custom project-level commands to the generator. This functionality is a top priority for us as apps require a mean of running cron tasks, maintenance scripts and so on.

