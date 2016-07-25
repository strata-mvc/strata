---
layout: guidebook
title: The error page
permalink: /guidebook/1.0/digging-in/debugging/error-page/
covered_tags: error, development
menu_group: debugging
---

Much like error pages in other development frameworks, Strata's error page will take over during development to help you pinpoint and diagnose coding issues. The page will trigger on fatal PHP errors and the various uncaught Exceptions when `WP_ENV` is set to `development` in the project's configuration.

A contextual preview of the PHP file will show you which exact line of code triggered the error alongside a stack trace of what has been executed prior to encountering the error.

Routing information will also be printed in order for Strata to give you information on the route it has tried to invoke.

![A sample error page in Strata](/assets/images/error-page.png)
