# In-context editor plugin

This plugin allows in-context editing (click to edit) features to the [OctoberCMS](http://octobercms.com) front-end.

It needs Rainlab Translate plugin to work.

This plugin uses Rainlab Editable plugin code as a base but uses translate strings instead of content blocks.

The editor only allows plain text and line breaks.

Implementation is simply replacing the `{{ message|_ }}` translation tags with the component.

## Using the editme component

First you must ensure that the EditMe component is attached to the page or layout.

It can be displayed on the front end like this:

```
title = "A page"
url = "/a-page"

[editme]
==

<!-- This content will be editable -->
{% component 'editme' message='page.title' %}
```

## Replacing existing translation tags with editable tags.

An example translation tag:

    {{ page.title|_ }}

This tag can be made editable by using the following instead:

    {% component 'editme' message='page.title' %}

## Permissions

Only administrator with the permission *Manage content* are able to edit content. Administrators must also be logged in to the back-end.


## Front-end JavaScript and StyleSheet

The components in this plugin provide custom stylesheet and javascript files to function correctly on the front-end. Ensure that you have `{% scripts %}` and `{% styles %}` in your page or layout.

The styles also depend on the October JavaScript Framework, so ensure the `{% framework %}` tag is also included in your page or layout.