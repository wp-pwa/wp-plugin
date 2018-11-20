# [1.8.0](https://github.com/frontity/wp-plugin/compare/v1.7.6...v1.8.0) (2018-11-16)


### Bug Fixes

* **transients:** never expire them and set hit or miss when id is zero ([b4095f2](https://github.com/frontity/wp-plugin/commit/b4095f2))


### Features

* **wp-embeds:** send height from wp post embeds ([9e61012](https://github.com/frontity/wp-plugin/commit/9e61012))

## [1.7.6](https://github.com/frontity/wp-plugin/compare/v1.7.5...v1.7.6) (2018-11-08)


### Bug Fixes

* **amp:** fix types for custom post types in AMP ([dc3c8cf](https://github.com/frontity/wp-plugin/commit/dc3c8cf))

## [1.7.5](https://github.com/frontity/wp-plugin/compare/v1.7.4...v1.7.5) (2018-11-02)


### Bug Fixes

* **attachment-ids:** return ids with integer values and purge transients ([6a25ff1](https://github.com/frontity/wp-plugin/commit/6a25ff1))

## [1.7.4](https://github.com/frontity/wp-plugin/compare/v1.7.3...v1.7.4) (2018-10-31)


### Bug Fixes

* **forbidden-images:** create fix_forbidden_image WIP ([b390d3e](https://github.com/frontity/wp-plugin/commit/b390d3e))
* **forbidden-images:** use fix_forbidden_media when the query is true ([2ca8ef9](https://github.com/frontity/wp-plugin/commit/2ca8ef9))
* **purifier:** do not override 'RemoveEmtpy.Predicate' default values ([f80b966](https://github.com/frontity/wp-plugin/commit/f80b966))

## [1.7.3](https://github.com/frontity/wp-plugin/compare/v1.7.2...v1.7.3) (2018-10-24)


### Bug Fixes

* **purifier:** add exceptions to remove empty elements ([07438e6](https://github.com/frontity/wp-plugin/commit/07438e6))

## [1.7.2](https://github.com/frontity/wp-plugin/compare/v1.7.1...v1.7.2) (2018-10-24)


### Bug Fixes

* **injector:** remove the check for the REST API base slug of cpt's ([55b21d0](https://github.com/frontity/wp-plugin/commit/55b21d0))
* **latest-link:** use home link only if the custom post type is 'post' ([5182bd5](https://github.com/frontity/wp-plugin/commit/5182bd5))

## [1.7.1](https://github.com/frontity/wp-plugin/compare/v1.7.0...v1.7.1) (2018-10-19)


### Bug Fixes

* **image-ids:** do not treat ids equal zero as a transient cache miss ([2e15a5c](https://github.com/frontity/wp-plugin/commit/2e15a5c))

# [1.7.0](https://github.com/frontity/wp-plugin/compare/v1.6.4...v1.7.0) (2018-10-19)


### Features

* **wp-pwa:** add query to disable htmlPurifier ([b515d51](https://github.com/frontity/wp-plugin/commit/b515d51))
* **wp-pwa:** use transients when getting image ids from wp-query ([a5bea25](https://github.com/frontity/wp-plugin/commit/a5bea25))

## [1.6.4](https://github.com/frontity/wp-plugin/compare/v1.6.3...v1.6.4) (2018-09-26)


### Bug Fixes

* **semantic-release:** add github back and finish automation ([825afa0](https://github.com/frontity/wp-plugin/commit/825afa0))

## [1.6.3](https://github.com/frontity/wp-plugin/compare/v1.6.2...v1.6.3) (2018-09-26)


### Bug Fixes

* **semantic-release:** add push for master as well ([d223345](https://github.com/frontity/wp-plugin/commit/d223345))
* **semantic-release:** add rebase ([4577fc8](https://github.com/frontity/wp-plugin/commit/4577fc8))
* **semantic-release:** add success cmd ([2cde44e](https://github.com/frontity/wp-plugin/commit/2cde44e))

## [1.6.2](https://github.com/frontity/wp-plugin/compare/v1.6.1...v1.6.2) (2018-09-26)


### Bug Fixes

* **semantic-release:** add wp-pwa.php to git assets ([a86fb4d](https://github.com/frontity/wp-plugin/commit/a86fb4d))

## [1.6.1](https://github.com/frontity/wp-plugin/compare/v1.6.0...v1.6.1) (2018-09-26)


### Bug Fixes

* **semantic-release:** fix json ([af0490b](https://github.com/frontity/wp-plugin/commit/af0490b))
* **semantic-release:** reorder and add empty steps ([f008b2f](https://github.com/frontity/wp-plugin/commit/f008b2f))

## [1.6.1](https://github.com/frontity/wp-plugin/compare/v1.6.0...v1.6.1) (2018-09-26)


### Bug Fixes

* **semantic-release:** fix json ([af0490b](https://github.com/frontity/wp-plugin/commit/af0490b))

# [1.6.0](https://github.com/frontity/wp-plugin/compare/v1.5.1...v1.6.0) (2018-09-25)


### Features

* **api-fields:** add option to whitelist api fields ([867d759](https://github.com/frontity/wp-plugin/commit/867d759))
* **api-fields:** change whitelist for blacklist ([a1d0811](https://github.com/frontity/wp-plugin/commit/a1d0811))

## [1.5.1](https://github.com/frontity/wp-plugin/compare/v1.5.0...v1.5.1) (2018-09-11)


### Bug Fixes

* **version:** update version in php files and use trunk ([150ddf6](https://github.com/frontity/wp-plugin/commit/150ddf6))

# [1.5.0](https://github.com/frontity/wp-plugin/compare/v1.4.15...v1.5.0) (2018-09-11)


### Bug Fixes

* **excludes:** add excludes to AMP ([b718b0e](https://github.com/frontity/wp-plugin/commit/b718b0e))
* **general:** undo manifest and service workers ([448c4dd](https://github.com/frontity/wp-plugin/commit/448c4dd))
* **html-purifier:** adds support for <audio> to htmlpurifier ([0261b70](https://github.com/frontity/wp-plugin/commit/0261b70))
* **html-purifier:** install htmlpurifier-html5 library for html5 support ([bd7013f](https://github.com/frontity/wp-plugin/commit/bd7013f))
* **image-ids:** support image-attributes hook ([d7a7f22](https://github.com/frontity/wp-plugin/commit/d7a7f22))
* **image-ids:** support image-attributes hook ([635a31b](https://github.com/frontity/wp-plugin/commit/635a31b))
* **image-ids:** support local urls of images ([ecc47fe](https://github.com/frontity/wp-plugin/commit/ecc47fe))
* **image-ids:** use proper attribute name ([7d07089](https://github.com/frontity/wp-plugin/commit/7d07089))
* **injector:** use array if excludes is null ([0357caa](https://github.com/frontity/wp-plugin/commit/0357caa))
* **latest:** use 'home' url if 'forceFrontPage' is enabled ([a254a1a](https://github.com/frontity/wp-plugin/commit/a254a1a)), closes [#5](https://github.com/frontity/wp-plugin/issues/5)
* **latest-from-cpt:** use quotes in array ([820f3b0](https://github.com/frontity/wp-plugin/commit/820f3b0))
* **purifier:** add text field for title and excerpt ([a01fde3](https://github.com/frontity/wp-plugin/commit/a01fde3)), closes [#10](https://github.com/frontity/wp-plugin/issues/10)
* **semantic-release:** add env-cmd for environment variables ([4bddc08](https://github.com/frontity/wp-plugin/commit/4bddc08))
* **semantic-release:** add github and trigger a new release ([8de6f2f](https://github.com/frontity/wp-plugin/commit/8de6f2f)), closes [#3](https://github.com/frontity/wp-plugin/issues/3)
* **semantic-release:** change commit message to pass conventional ([8042dd7](https://github.com/frontity/wp-plugin/commit/8042dd7))
* **semantic-release:** test without github ([6216b4b](https://github.com/frontity/wp-plugin/commit/6216b4b))


### Features

* **htmlpurifier:** add option to purge cache ([c3d6051](https://github.com/frontity/wp-plugin/commit/c3d6051))
* **htmlpurifier:** add UI button for purge ([518772e](https://github.com/frontity/wp-plugin/commit/518772e))
* **image-ids:** add ids to gallery images using wp_get_attachment_link ([145c235](https://github.com/frontity/wp-plugin/commit/145c235))
* **image-ids:** use classes to get ids when possible ([5088f86](https://github.com/frontity/wp-plugin/commit/5088f86))

## [1.4.12](https://github.com/frontity/wp-plugin/compare/v1.4.11...v1.4.12) (2018-07-11)


### Bug Fixes

* **semantic-release:** add github and trigger a new release ([8de6f2f](https://github.com/frontity/wp-plugin/commit/8de6f2f)), closes [#3](https://github.com/frontity/wp-plugin/issues/3)
* **semantic-release:** change commit message to pass conventional ([8042dd7](https://github.com/frontity/wp-plugin/commit/8042dd7))

## [1.4.11](https://github.com/frontity/wp-plugin/compare/v1.4.10...v1.4.11) (2018-07-11)


### Bug Fixes

* **semantic-release:** test without github ([6216b4b](https://github.com/frontity/wp-plugin/commit/6216b4b))

## [1.4.11](https://github.com/frontity/wp-plugin/compare/v1.4.10...v1.4.11) (2018-07-11)


### Bug Fixes

* **semantic-release:** test without github ([6216b4b](https://github.com/frontity/wp-plugin/commit/6216b4b))

## [1.4.11](https://github.com/frontity/wp-plugin/compare/v1.4.10...v1.4.11) (2018-07-11)


### Bug Fixes

* **semantic-release:** test without github ([6216b4b](https://github.com/frontity/wp-plugin/commit/6216b4b))
