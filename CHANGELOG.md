# ChangeLog

All notable changes to Phive are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [0.4.0] - 2016-05-16

### Added

* [#52](https://github.com/phar-io/phive/issues/52): Support for Microsoft Windows environments

### Changed

* [#53](https://github.com/phar-io/phive/issues/53): Download `repositories.xml` from `https://phar.io` only when needed

### Fixed

* [#47](https://github.com/phar-io/phive/issues/47): Always write relative paths to `phive.xml`
* [#48](https://github.com/phar-io/phive/issues/48): Fix path to GPG binary on MacOS X
* [#50](https://github.com/phar-io/phive/issues/50): Trying to install an unknown alias does not lead to an exception anymore

## [0.3.0] - 2016-04-21

### Added

* [#9](https://github.com/phar-io/phive/issues/9): `reset` command
* [#10](https://github.com/phar-io/phive/issues/10): Generate Phive configuration file from `composer.json`
* [#11](https://github.com/phar-io/phive/issues/11): Provide proper exit codes
* [#14](https://github.com/phar-io/phive/issues/14): `update` command
* [#22](https://github.com/phar-io/phive/issues/22): Implement support for GitHub repositories
* [#39](https://github.com/phar-io/phive/issues/39): Show download progress
* [#41](https://github.com/phar-io/phive/issues/41): Caret version operator

### Changed

* [#15](https://github.com/phar-io/phive/issues/15): Make cURL SSL checks more secure

## [0.2.1] - 2016-04-08

### Fixed

* [#42](https://github.com/phar-io/phive/issues/42): Installing PHARs fails when `phar.readonly` is set in `php.ini`
* [#43](https://github.com/phar-io/phive/issues/43): PHIVE binary gets corrupted
* [#45](https://github.com/phar-io/phive/issues/45): Certificate for sks-keyservers.net cannot be loaded when PHIVE is run from a PHAR

## [0.2.0] - 2016-02-25

### Added

* [#1](https://github.com/phar-io/phive/issues/1): Verify signature of PHAR
* [#4](https://github.com/phar-io/phive/issues/4): Implement command to list known aliases enhancement
* [#16](https://github.com/phar-io/phive/issues/16): Allow global installation of PHARs
* [#18](https://github.com/phar-io/phive/issues/18): `-save` option to write installed PHAR to `phive.xml`
* [#28](https://github.com/phar-io/phive/issues/28): Help text for `-global` option
* [#32](https://github.com/phar-io/phive/issues/32): Basic `list` command to show known aliases
* [#33](https://github.com/phar-io/phive/issues/33): Show more info than fingerprint before import

### Changed

* [#20](https://github.com/phar-io/phive/issues/20): Make `-save` flag default behaviour
* [#27](https://github.com/phar-io/phive/issues/27): Abbreviations should be uppercased

### Fixed

* [#31](https://github.com/phar-io/phive/issues/31): Not compatible with PHP 7

## 0.1.0 - 2015-09-23

* Initial Release

[0.4.0]: https://github.com/phar-io/phive/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/phar-io/phive/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/phar-io/phive/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/phar-io/phive/compare/0.1.0...0.2.0

