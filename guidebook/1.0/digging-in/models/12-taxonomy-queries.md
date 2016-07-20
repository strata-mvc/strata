---
layout: guidebook
title: Taxonomy Queries
permalink: /guidebook/1.0/digging-in/models/taxonomy-queries/
covered_tags: models, taxonomies, queries
menu_group: models
---

While the Taxonomies query API behaves similarly to the Custom Post Types's there are some distinctions as it does not bubble up to a `WP_Query` object but rather to Wordpress' `get_term`, `get_terms` and `get_the_terms` functions.


## $this->where($field, $value);

Querying a Taxonomy using `where()` will eventually bubble up to Wordpress' `[get_terms](https://developer.wordpress.org/reference/functions/get_terms/)` function.

Each `$field` is added to an internal array that is then sent as first parameter to `get_terms` when a call to a fetch method ends the chain.

Each of the allowed values that can be sent as `$field` are the same as those documented in Wordpress codex.

This cannot be chained with a `by()` or a `forEntity()` call.


## $this->forEntity(ModelEntity $entity);

Querying a Taxonomy using `forEntity()` will eventually bubble up to Wordpress' `[get_term](https://developer.wordpress.org/reference/functions/get_term/)` function.

This cannot be chained with a `where()` or a `by()` call.


## $this->by($type, $value);

Querying a Taxonomy using `by()` will eventually bubble up to Wordpress' `[get_the_terms](https://developer.wordpress.org/reference/functions/get_the_terms/)` function.

Each `$field` is added to an internal flag that is then sent as first parameter to `get_the_terms` when a call to a fetch method ends the chain.

Each of the allowed values that can be sent as `$field` are the same as those documented in Wordpress codex.

This cannot be chained with a `where()` or a `forEntity()` call.
