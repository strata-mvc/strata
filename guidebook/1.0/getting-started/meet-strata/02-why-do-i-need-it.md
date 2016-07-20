---
layout: guidebook
title: Why do I need it?
permalink: /guidebook/1.0/getting-started/meet-strata/why-do-i-need-it/

menu_group: meet-strata
next_page: /guidebook/1.0/getting-started/meet-strata/when-should-i-use-it/
next_page_label: When is Strata especially useful?
---

## Wordpress as a CMS

Wordpress is a fantastic blogging engine that it frequently used as a full fledged CMS for complex applications that no longer strictly behave as blogs.

In these situations, one may find that you can't quite cleanly separate you different programming concepts cleaning. Lines of code that really should be part of the same class tend to be spread out over multiple `add_filter` or `add_action` calls.

This creates potential for hardships because the Wordpress API is not clearly intended for the purpose of isolating the business logic. Neither is it fully intended _not_ to work like a blog  and may lack flexibility to perform other behaviors in an expressive way. For example, queries outside of the loop are quite possible but not very elegant or descriptive.

## Everything in its right place

Debugging existing Wordpress applications can quickly become tedious. It is hard to enforce a recurring method of placing your code files across different projects or different teams.

Because filters and actions may be modified by just about everything in the project and because these hooks express changes to the default blog mechanics and not exactly to the current application's features, you cannot refactor your code easily in your sandbox with the complete knowledge of how the rest of the code will be impacted.

In Wordpress a lot of things are happening without you knowing or controlling how. Plugins are often very aggressive in how they influence your application. It's a very useful concepts for small simple projects, but not for more complex applications.

Using common MVC concepts, Strata allows you to take the application's logic outside of the theme and more it to specialized class files with a clear intent. This makes to code reusable, testable and much more expressive. In turns this creates simper view files that also become reusable.
