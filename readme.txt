# Skip Updates

* Contributors: afragen
* Tags: skip, update
* Requires at least: 5.2
* Requires PHP: 5.6
* Tested up to: 6.7
* Stable tag: 1.2.2
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

#### 1.2.2 / 2024-11-01
* remove `load_plugin_textdomain()`
* composer update

#### 1.2.1 / 2024-07-11
* update tested to
* composer update

#### 1.2.0 / 2023-09-10
* update to WPCS 3.0.0

#### 1.1.3 / 2023-07-21
* add developery stuff
* update tested to

#### 1.1.2 / 2022-05-10
* updated tested to

#### 1.1.1 / 2022-02-08
* use `sanitize_title_with_dashes()` as `sanitize_file_name()` maybe have attached filter that changes output
* use `sanitize_key()` for nonces
* update nonce check in `class SU_List_Table` and `class Settings`
* update `uninstall.php`

#### 1.1.0 / 2021-08-29
* speed up check for testing if plugin or theme is in dot org by using `wp_remote_head`
* longer timeout if plugin or theme is in dot org

#### 1.0.0 / 2021-08-14
* update selector and only allow dot org plugins and themes

#### 0.5.2 / 2021-07-07
* use proper echo and escape not translation escape, thanks @westonruter
* add @10up GitHub Actions integration for WordPress SVN

#### 0.5.1 / 2021-02-18
* better data validation for empty data

#### 0.5.0
* fix redirect on save to only redirect on correct option page

#### 0.4.0 / 2021-02-17
* check data validation for proper plugin/theme slug on save

#### 0.3.0 / 2021-02-16
* change update transient filter to default priority

#### 0.2.0 / 2021-02-12
* initial commit
