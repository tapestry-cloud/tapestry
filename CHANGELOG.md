# 1.0.8
## Bugfixes
- #186 Removed dead code in File class
- #182 Different capitalisation of taxonomy classifications no longer results in duplicate classifications

## Enhancements
- #168 Tapestry now warns on a copy source missing rather than failing
- #165 Added {category} permalink template tag
- #175 100% Test Coverage of Url Entity
- #178 Added test coverage for ViewFile Trait
- #185 Added post scheduling
- #180 Increased test coverage of Taxonomy class
- #183 Added breakdown by step to --stopwatch output

# 1.0.7
## Bugfixes
- #118 Fixed ability to extend Tapestry Plates extension
- #121 Ignore functionality changed
- #123 PHP files are now treated as PHTML
- #96 phine/phar is replaced by box-project/box2 for generating the .phar
- #119 Added missing tests
- #151 Self update command no longer creates a tmp folder on construct
- #158 `$this->getUrl()` on null due to Blade extension not passing `File` on render method

## Enhancements
- #147 Skip functionality added to paginator 
- #152 Directories prefixed with an underscore are now ignored
- #161 Added `getExcerpt()` helper

# 1.0.6
## Bugfixes
- #111 Fixed a bug with testing the cli functionality - now tests execute Tapestry in the same way as it is executed in the real world
- #87 Fixed undefined offsets within ContentTypeFactory

## Enhancements
- #92 Added api command for exporting workspace state to a json file. This aids third party tools in integrating Tapestry.
- #99 Added a base filesystem class
- #88 ContentType now mutates its content so you know which content type it belongs to within the template and via third party integrations
- #93 Configuration can now be either YAML or PHP based
- #82 Destination folder is now configurable

# 1.0.5

## Bugfixes
- #34 Hotfix to PHP Plates incompatibility
- #29 Fixed self-update rollback functionality
- #40 Fixed some blog pages having `$categories` not set in view
- #50, #53 Fixed `PaginationGenerator`
- #56 Fixed markdown not getting rendered within a layout they define within front matter
- #15 Cache is now correctly invalidated upon changes to project config or kernel files
- #9 Cache is now correctly invalidated upon changes to template files (in `_templates`)
- #27 Init project is now split into its own repository (see https://github.com/carbontwelve/tapestry-blog-theme)
- #11 Added Tapestry to packagist (see https://packagist.org/packages/carbontwelve/tapestry)

## Enhancements
- #30 Added View Helpers
- #41 Added `isDraft()` view helper method
- #47 Replace cebe/markdown with michelf/php-markdown
