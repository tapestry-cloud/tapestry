# Tapestry
[![Development Build Status](https://travis-ci.org/carbontwelve/tapestry.svg?branch=development)](https://travis-ci.org/carbontwelve/tapestry)
[![StyleCI](https://styleci.io/repos/73839963/shield?branch=master)](https://styleci.io/repos/73839963)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Gitmoji](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat-square)](https://gitmoji.carloscuesta.me)
[![ghit.me](https://ghit.me/badge.svg?repo=carbontwelve/tapestry)](https://ghit.me/repo/carbontwelve/tapestry)

Simple static sites with the league of extraordinary packages [plates](http://platesphp.com/) template system.

## Installing
The easiest method for installing Tapestry is to grab the latest [zipped release here](https://github.com/carbontwelve/tapestry/releases) and unzip the contents into your `$PATH` to make it globally available from your command line.

For Windows environments a `.bat` file is included so that you do not have to type `php tapestry.phar` to run Tapestry; for it to work ensure it is kept in the same folder as the `.phar`.

### Composer
Alternatively Tapestry may be installed via composer globally so long as `~/.composer/vendor/bin` is in your `$PATH`. To do so: `$ composer global require carbontwelve/tapestry`

## Compiling .phar
You can compile your own version of the .phar file by running `bin\compile.php`; `tapestry.phar` and `tapestry.version` files will be generated into the `bin` folder.

## Not invented here
[StaticGen](https://www.staticgen.com/) has a list of other static site generators available, although to my knowledge Tapestry is the only one to use the PHPPlates template engine.

[License](LICENSE)
