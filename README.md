# Skip Updates

* Contributors: [Andy Fragen](https://github.com/afragen), [contributors](https://github.com/afragen/skip-updates/graphs/contributors)
* Tags: skip, update
* Requires at least: 5.2
* Requires PHP: 5.6
* Stable tag: main
* Donate link: http://thefragens.com/github-updater-donate
* License: MIT

A plugin that allows for adding installed dot org hosted plugins or themes to skip updating.

## Description

Have you ever had a custom plugin or theme accidentally overwritten by an update from a wp.org plugin or theme with an identical slug? There are several very old Trac tickets describing this behavior and are still waiting for a solution in core. That solution is now available.

This is a plugin that will add a Settings menu, **Skip Updates**, where plugins or themes may be added by selecting the plugin or theme from the dropdown. Once added, the plugin or theme will **no longer receive updates or update notices from wp.org**.

Only plugins or themes on wp.org are displayed for selection.

This does require that this plugin is installed and activated for this protection. Fortunately there is a [plugin dependency installer](https://github.com/afragen/wp-dependency-installer) library that may be added to your plugin or theme that is able to install and activate any plugin, including this one, in either an optional or mandatory fashion.

## Development
PRs are welcome against the `develop` branch.

## Changelog

Please see the Github repository: [CHANGELOG.md](https://github.com/afragen/skip-updates/blob/main/CHANGES.md).
