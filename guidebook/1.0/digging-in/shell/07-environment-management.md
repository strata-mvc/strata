---
layout: guidebook
title: Environment Management
permalink: /guidebook/1.0/digging-in/shell/environment-management/
menu_group: shell
---

To enforce the sanity of your project's codebase, Strata provides tools that help maintain project files.

## Repairing the installation

If you have reasons to think the directory structure and default project files are improperly set, you can run the repair tool on your project.

Using the command line, run the `repair` command from your project's base directory.

{% highlight bash linenos %}
$ ./strata env repair
{% endhighlight %}

The command will check for existence of known default folders as well as default files.

{% highlight bash linenos %}
Ensuring correct directory structure.
  ├── [SKIP] bin
  ├── [SKIP] config
  ├── [SKIP] db
  ├── [SKIP] doc
  ├── [SKIP] log
  ├── [SKIP] src
  ├── [SKIP] src/Controller
  ├── [SKIP] src/Model
  ├── [SKIP] src/Model/Validator
  ├── [SKIP] src/View
  ├── [SKIP] src/View/helper
  ├── [SKIP] test
  ├── [SKIP] test/Controller
  ├── [SKIP] test/Model
  ├── [SKIP] test/Model/Validator
  ├── [SKIP] test/View
  ├── [SKIP] test/View/Helper
  ├── [SKIP] test/Fixture
  ├── [SKIP] test/Fixture/Wordpress
  └── [SKIP] tmp

Ensuring project files are present.
  ├── [SKIP] src/Controller/AppController.php
  ├── [SKIP] src/Model/AppModel.php
  ├── [SKIP] src/Model/AppCustomPostType.php
  ├── [SKIP] src/View/Helper/AppHelper.php
  ├── [SKIP] config/strata.php
  ├── [SKIP] web/app/mu-plugins/strata-bootstraper.php
  ├── [SKIP] test/strata-test-bootstraper.php
  └── [SKIP] test/Fixture/Wordpress/wordpress-bootstraper.php
{% endhighlight %}

## PSR2 Enforcement

In themes and inside Wordpress, you will see code written using Wordpress' own standard. However, each Strata classes are expected to meet [PSR2 coding guidelines](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

You can run the `psr2format` to go through each of the files under `src/` and `test/` and fix PSR2 formatting errors. This script will modify your files, but should not have a destructive behavior. For additional information on how the formatting is done, you may wish to read on [Phpcbf](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically).

{% highlight bash linenos %}
$  ./strata env psr2format
{% endhighlight %}
