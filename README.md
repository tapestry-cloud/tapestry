# Tapestry
[![Development Build Status](https://travis-ci.org/tapestry-cloud/tapestry.svg?branch=development)](https://travis-ci.org/tapestry-cloud/tapestry)
[![StyleCI](https://styleci.io/repos/73839963/shield?branch=master)](https://styleci.io/repos/73839963)
[![Code Climate](https://codeclimate.com/github/tapestry-cloud/tapestry/badges/gpa.svg)](https://codeclimate.com/github/tapestry-cloud/tapestry)
[![Test Coverage](https://codeclimate.com/github/tapestry-cloud/tapestry/badges/coverage.svg)](https://codeclimate.com/github/tapestry-cloud/tapestry/coverage)
[![Packagist](https://img.shields.io/packagist/v/carbontwelve/tapestry.svg?style=flat-square)](https://packagist.org/packages/carbontwelve/tapestry)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg?style=flat-square)](https://php.net/)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Gitmoji](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat-square)](https://gitmoji.carloscuesta.me)
[![ghit.me](https://ghit.me/badge.svg?repo=carbontwelve/tapestry)](https://ghit.me/repo/carbontwelve/tapestry)

## About Tapestry
Tapestry is a static site generator that uses the [plates](http://platesphp.com/) template system by the league of extraordinary packages. Tapestry aims to be fast, easy to use and easy to extend. It has been inspired by similar generators [Sculpin](https://sculpin.io/) and [Jigsaw](http://jigsaw.tighten.co/). Tapestry is designed for developers who prefer to use native PHP templates over compiled template languages such as Twig or Blade. 

### Highlights
* Native PHP templates with the use of the [plates](http://platesphp.com/) template system
* [Blog aware](https://www.tapestry.cloud/documentation/your-content/) out of the box
* Built to be extendable with [plugins](https://www.tapestry.cloud/documentation/working-examples/#plugins)

## Learning Tapestry
The [Tapestry documentation](https://www.tapestry.cloud/documentation/) provides a thorough insight into the inner workings of Tapestry. Making it as easy as possible to get started generating your sites.

## Installing Tapestry
The recommended method for installing Tapestry is to grab the latest [zipped release here](https://github.com/carbontwelve/tapestry/releases) and unzip the contents into your `$PATH` to make it globally available from your command line.

For Windows environments a `.bat` file is included so that you do not have to type `php tapestry.phar` to run Tapestry; for it to work ensure it is kept in the same folder as the `.phar`.

For alternative methods of installing Tapestry see the [install documentation here](https://www.tapestry.cloud/documentation/installation).

## License
Tapestry is open sourced software licensed under the [MIT License](LICENSE).

## Not invented here
[StaticGen](https://www.staticgen.com/) has a list of other static site generators available, although to my knowledge Tapestry is the only one to use the PHPPlates template engine.
