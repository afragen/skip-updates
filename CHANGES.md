#### [unreleased]
* speed up check for testing if plugin or theme is in dot org by using `wp_remote_head`
* longer timeout if plugin or theme is in dot org

#### 1.0.0 / 2021-08-14
* update selector and only allow dot org plugins and themes

#### 0.5.2 / 2021-07-07
* use proper echo and escape not translation escape, thanks @westonruter
* add @10up GitHub Actions integration for WordPress SVN

#### 0.5.1 / 2021-02-18
* better data validation for empty data

#### 0.5.0 / 2021-02-18
* fix redirect on save to only redirect on correct option page

#### 0.4.0 / 2021-02-17
* check data validation for proper plugin/theme slug on save

#### 0.3.0 / 2021-02-16
* change update transient filter to default priority

#### 0.2.0 / 2021-02-12
* initial commit
