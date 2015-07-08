---
layout: page
title: Screencasts
permalink: /screencasts/
---


{% for category in site.categories %}
    {% if  category | first  == "screencasts" %}
<ul>
{% for posts in category %}
    {% for post in posts %}
        {% if post.url %}
            <li>
                <h3><a href="{{ post.url }}">{{ post.title }}</a></h3>
                <time>{{ post.date | date: "%-d %B %Y" }}</time>
                <p>{{ post.excerpt }}</p>
            </li>
        {% endif %}
    {% endfor %}
{% endfor %}
</ul>
    {% endif %}
{% endfor %}

