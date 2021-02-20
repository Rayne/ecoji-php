# CHANGELOG

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org).

## [Unreleased]

* Added `Dockerfile` of the official [Docker container](https://hub.docker.com/r/rayne/ecoji)
* Dropped official support for PHP 7.0 and 7.1
* Added support for PHP 7.3, PHP 7.4 and 8.0
* Added Dockerfiles for PHP 7.2, 7.3, 7.4 and 8.0
* The application's Docker image is now utilizing PHP 8.0
* Added convenience scripts to build all container variants
* Added convenience scripts to run unit tests in development containers

## [1.1.0]

### Added

* Added CLI program `/bin/ecoji`
* Added `Rayne\Ecoji\EcojiStream` for stream encoding and decoding

## [1.0.1] - 2018-03-14

### Fixed

* Fixed decoding of messages with newlines

## 1.0.0 - 2018-03-13

Initial release.

[Unreleased]: https://github.com/Rayne/ecoji-php/compare/1.1.0...HEAD
[1.1.0]: https://github.com/Rayne/ecoji-php/compare/1.0.1...1.1.0
[1.0.1]: https://github.com/Rayne/ecoji-php/compare/1.0.0...1.0.1
