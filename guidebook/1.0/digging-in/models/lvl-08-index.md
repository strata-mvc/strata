---
layout: guidebook
title: Models
permalink: /guidebook/1.0/digging-in/models/
entry_point: true
menu_group: models
group_label: Models
group_theme: Digging In
---

Models are where the logic being the application's custom processes, validations and everything that is generally defined as "business logic" is located.

Strata has multiple types of Models :

* **Model**: a class that does not represent a WP_Post object
* **CustomPostType**: a class that does represent a WP_Post object
* **Taxonomy**: a class that represent a WP_Term object

These model classes all spawn object of the `ModelEntity` type.

Models can sometimes be visualized as a database table while ModelEntities can be visualized as the table's rows. Model methods are also expected to return corresponding ModelEntity objects that can be manipulated. In Strata, there is no direct link between a Model and a table because it does not maintain a separate ORM than Wordpress' default `$wpdb`.

That means you are expected to place querying methods in the Model and application logic in the ModelEntity.
