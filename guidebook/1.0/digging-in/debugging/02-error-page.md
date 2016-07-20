---
layout: guidebook
title: The error page
permalink: /guidebook/1.0/digging-in/debugging/error-page/
covered_tags: error, development
menu_group: debugging
---

Much like error pages in development frameworks, Strata's error page will take over during development to help you pinpoint coding mistakes. It will trigger on PHP fatal errors and uncaught Exceptions when `WP_ENV` is set to `development` in the project's configuration.

A contextual preview of the PHP file will show you what triggered the error alongside a stack trace of what has been executed prior to the encounter of this error.

Finally, routing information will also be printed in order for Strata to give you information on the route it has tried to invoke.

![A sample error page in Strata](/images/error-page.png)
