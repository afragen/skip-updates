# Skip Updates

* Contributors: [Andy Fragen](https://github.com/afragen), [contributors](https://github.com/afragen/skip-updates/graphs/contributors)
* Tags: plugin, theme, update
* Requires at least: 5.2
* Requires PHP: 5.6
* Tested up to: 5.7
* Stable tag: main
* Donate link: http://thefragens.com/github-updater-donate
* License: MIT

A plugin that allows for adding installed dot org hosted plugins or themes to skip updating.

## Description

This is a plugin that will add a Settings menu, **Skip Updates**, where plugins or themes may be added by selecting the type of either plugin or theme and entering the slug.

Please note that a plugin slug has the format of `my-plugin/my-plugin.php` and a theme has a slug in the format of `twentynineteen`.

## Usage

The `"type"` element is from the following list.

* plugin
* theme

The `"slug"` element is either the plugin slug or the theme stylesheet slug.

### Examples

```
type: plugin
slug: akismet/akismet.php
```

or

```
type: theme
slug: twentynineteen
```

Above are examples for a plugin or a theme. Please notice the diffence in the `slug`.

## Development
PRs are welcome against the `develop` branch.
