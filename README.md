wordpress-mvc
=============

### Why

Wordpress is a fantastic blog engine and is more and more used as a full fledged CMS. When building more complex websites or applications, one may find that you can’t quite cleanly separate the business logic from the templating engine.

### What it does

Wordpress MVC allows you to link permalinks with Controllers to offer you an elegant place to place code and keep the templates as clean as possible. It also maps Models to custom post types so model relationships can be programmed elegantly.

### What it doesn't do

We do not want to change how Wordpress works. Integrators and front-end programmers should not notice what is being processed in the controllers except for explicitly exposed shortcodes and view variables. We also do not want to interfere with core Wordpress variables, libraries, plugins and themes or custom posts types created outside the MVC process.

More details can be found on the [official website](http://francoisfaubert.com/wordpress-mvc/)
