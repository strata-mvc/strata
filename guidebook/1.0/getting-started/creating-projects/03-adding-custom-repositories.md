---
layout: guidebook
title: Adding Custom Repositories
permalink: /guidebook/1.0/getting-started/creating-projects/adding-custom-repositories/
covered_tags: composer, repository, installation
menu_group: creating-projects
---

You may add custom or private repositories to your projects. Following [Composer's documentation](https://getcomposer.org/doc/05-repositories.md) you can easily set up an ecosystem of basic company code or reusable custom libraries.

Every one of the custom packages will require their own `composer.json` file which must instruct how they are to be installed by the package manager.

These may be installed as Wordpress plugins or as regular PHP projects. The way packages are installed is defined by the `type` key and predefined automatic installers. By default a Composer dependency is installed in the `vendor` directory.

### Configuring a package

Regular PHP packages can be created by running Composer's `init` command within the package's root directory. A wizard will guide you through the process of creating a valid `composer.json` file.

{% highlight bash linenos %}
$ composer init
{% endhighlight %}

## Private repositories

### Exposing your repository

Inform Composer of the location of your private repository by adding it to the `repositories` block. In this example we give the location of private a Bitbucket Git repository.

{% highlight json linenos %}
{
    "repositories": [{
        "type": "composer",
        "url": "https://wpackagist.org"
    }, {
        "type": "git",
        "url": "https://bitbucket.org/my_company/autoloader-strata-middleware.git"
    }]
}
{% endhighlight %}


### Adding the requirement

With this information Composer will be able to find your private `required` packages and will ask you for credentials as needed when installing or updating.

{% highlight json linenos %}
{
    "require": {
        "php": ">=5.4",
        "johnpbloch/wordpress": "4.4.2",
        "francoisfaubert/strata": "dev-master",
        "my_company/autoloader-strata-middleware": ">=1.5.5"
    }
}
{% endhighlight %}
