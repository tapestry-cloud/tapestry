# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

Tapestry currently operates a one month release cycle between releases, see the projects [milestones](https://github.com/tapestry-cloud/tapestry/milestones) for more information.

## [2.0.0] - Unreleased

### Added
- #284 Refactored `File` entity to `ProjectFile`
- #300 Updated plates version to 4.0

## [1.1.0] - Unreleased

### Added
- #290 Organised Unit tests and updated third party requirements
- #218 Moved compile steps to configuration

## [1.0.12] - 2018-01-03

### Fixed
- #272 Disable self-update command if not executed within a phar
- #148 Front-matter is now parsed in files with an empty body
- #189 getUid can no longer return an empty string

### Added
- #254 Added serve command
- #157 Added lock file support so Tapestry doesn't run concurrently

## [1.0.11] - 2017-11-23

### Fixed
- #255 Permalinks no longer clash on static files

### Added
- #230 Tapestry now warns if the project was previously compiled with a newer version of Tapestry
- #241 Added optional max argument to permalink category template tag 
- #246 Added getEnvironment view helper

## [1.0.10] - 2017-08-01
### Fixed
- #231 Fixed multiple firing of compile begin/end events
- #234 Fixed incorrect import of Exception when loading site kernel
- #235 Fixed Kernel service provider working on Windows but not on Linux when site kernel is named Kernel.php

## [1.0.9] - 2017-07-07
### Fixed
- #209 Fix Url helper parsing of Uri with parameters
- #208 File with multiple ext e.g. main.min.css are now compiled as expected rather than hyphenated e.g. main-min.css
- #219 Fixed permalinks not being valid urls when using the file path on Windows

### Added
- #146 Added --auto-publish flag to build command, [click here](https://www.tapestry.cloud/documentation/commands/#build-command) for more information
- #156 Added permalink registry for the purpose of identifying when two files have the same permalink and warning the user or resolving the conflict

## [1.0.8] - 2017-06-06
### Fixed
- #186 Removed dead code in File class
- #182 Different capitalisation of taxonomy classifications no longer results in duplicate classifications
- #193 Url class now correctly encodes as according to [RFC 3986](http://www.faqs.org/rfcs/rfc3986.html) 

### Added
- #168 Tapestry now warns on a copy source missing rather than failing
- #165 Added {category} permalink template tag
- #175 100% Test Coverage of Url Entity
- #178 Added test coverage for ViewFile Trait
- #185 Added post scheduling
- #180 Increased test coverage of Taxonomy class
- #183 Added breakdown by step to --stopwatch output
- #198 Added Step before and after events
- #181 Added coverage to HTML Renderer

## [1.0.7] - 2017-05-02
### Fixed
- #118 Fixed ability to extend Tapestry Plates extension
- #121 Ignore functionality changed
- #123 PHP files are now treated as PHTML
- #96 phine/phar is replaced by box-project/box2 for generating the .phar
- #119 Added missing tests
- #151 Self update command no longer creates a tmp folder on construct
- #158 `$this->getUrl()` on null due to Blade extension not passing `File` on render method

### Added
- #147 Skip functionality added to paginator 
- #152 Directories prefixed with an underscore are now ignored
- #161 Added `getExcerpt()` helper

## [1.0.6] - 2017-03-20
### Fixed
- #111 Fixed a bug with testing the cli functionality - now tests execute Tapestry in the same way as it is executed in the real world
- #87 Fixed undefined offsets within ContentTypeFactory

### Added
- #92 Added api command for exporting workspace state to a json file. This aids third party tools in integrating Tapestry.
- #99 Added a base filesystem class
- #88 ContentType now mutates its content so you know which content type it belongs to within the template and via third party integrations
- #93 Configuration can now be either YAML or PHP based
- #82 Destination folder is now configurable

## [1.0.5] - 2017-01-17

### Fixed
- #34 Hotfix to PHP Plates incompatibility
- #29 Fixed self-update rollback functionality
- #40 Fixed some blog pages having `$categories` not set in view
- #50, #53 Fixed `PaginationGenerator`
- #56 Fixed markdown not getting rendered within a layout they define within front matter
- #15 Cache is now correctly invalidated upon changes to project config or kernel files
- #9 Cache is now correctly invalidated upon changes to template files (in `_templates`)
- #27 Init project is now split into its own repository (see https://github.com/carbontwelve/tapestry-blog-theme)
- #11 Added Tapestry to packagist (see https://packagist.org/packages/carbontwelve/tapestry)

### Added
- #30 Added View Helpers
- #41 Added `isDraft()` view helper method
- #47 Replace cebe/markdown with michelf/php-markdown

## [1.0.4] - 2016-12-07

### Fixed
- #12 Pretty Permalink configuration is now listed to
- #10 ArrayContainer class is now feature complete and unit tested
- #7 Draft posts are now filtered if configured to be ignored

### Added
- #6 Added `--stopwatch` flag to display how much time and memory was consumed during a build
- #4 Tidied up service providers and execution order
- #5 Added Unit Tests with good coverage
- #8 Per-environment configuration for overloading base configuration dependant upon which `--env` is chosen

## [1.0.3] - 2016-11-18

### Added
- Added self-update functionality
- Refactored execution order so that site kernel gets loaded at bootstrapping

## [1.0.0] - 2016-11-17
First Release