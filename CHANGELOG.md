# Changelog

All notable changes to Phive are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [0.6.0] - 2016-07-29

### Added
* [#67](https://github.com/phar-io/phive/issues/67): Implement alternative separators for version specification
* [#69](https://github.com/phar-io/phive/issues/69): Add cli option to provide trusted gpg key IDs

### Fixed
* [#70](https://github.com/phar-io/phive/issues/70): Updating and removing PHARs installed via GitHub now works properly
* [#73](https://github.com/phar-io/phive/issues/73): Selfupdate now properly installs the new PHAR

## [0.5.0] - 2016-07-15

### Added
* Added exit code 5 to signal parameter validation errors  
* [#7](https://github.com/phar-io/phive/issues/7): Implement selfupdate command
* [#17](https://github.com/phar-io/phive/issues/17): Add support for custom repository list
* [#13](https://github.com/phar-io/phive/issues/13): Implement status command
* [#58](https://github.com/phar-io/phive/issues/58): Implement .bat-Wrapper for Windows

### Changed
* via [PR #56](https://github.com/phar-io/phive/pull/57) by [sebastianbergmann](https://github.com/sebastianbergmann): Changelog rewritten to respect the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
* [#29](https://github.com/phar-io/phive/issues/29) and [#55](https://github.com/phar-io/phive/issues/55): Rewritten CLI parameter processing code 
* Phive now exits with code 10 in case of an internal error (previously: 5)
* [#38](https://github.com/phar-io/phive/issues/38): installed version of a PHAR is now added to phive.xml and respected by the install command
* [#63](https://github.com/phar-io/phive/issues/63): Disallow the combined used of `--target` and `--global` in `phive install`
* [#59](https://github.com/phar-io/phive/issues/59): Always create a copy when installing a PHAR globally
* [#60](https://github.com/phar-io/phive/issues/60): Properly write the location of an installed PHAR to phive.xml

### Fixed

* [#64](https://github.com/phar-io/phive/issues/64): Fixed the global `--home` option to change Phive's home directory

## [0.4.1] - 2016-06-02
### Added
* Additional testsuite running regression tests on the Phive PHAR

### Fixed
* via [PR #57](https://github.com/phar-io/phive/pull/57) by [haeber](https://github.com/haeber): code cleanup / typo
* Installing PHARs from GitHub releases now correctly resolves the PHAR's name
* Remove command removes phar node from the project's phive.xml
* Write absolute paths to usages in phars.xml
* Update command now creates symlinks in the correct location


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
* [#4](https://github.com/phar-io/phive/issues/4) via [PR #32](https://github.com/phar-io/phive/pull/32) by [Flyingmana](https://github.com/Flyingmana): Implement command to list known aliases enhancement
* [#16](https://github.com/phar-io/phive/issues/16) via [PR #25](https://github.com/phar-io/phive/pull/25) by [sebastianbergmann](https://github.com/sebastianbergmann): Allow global installation of PHARs
* [#18](https://github.com/phar-io/phive/issues/18): `-save` option to write installed PHAR to `phive.xml`
* [#28](https://github.com/phar-io/phive/issues/28) via [PR #28](https://github.com/phar-io/phive/pull/28) by [sebastianbergmann](https://github.com/sebastianbergmann): Help text for `-global` option
* [#32](https://github.com/phar-io/phive/issues/32): Basic `list` command to show known aliases
* [#33](https://github.com/phar-io/phive/issues/33): Show more info than fingerprint before import

### Changed
* [#20](https://github.com/phar-io/phive/issues/20): Make `-save` flag default behaviour
* [#27](https://github.com/phar-io/phive/issues/27) via [PR #27](https://github.com/phar-io/phive/pull/27) by [sebastianbergmann](https://github.com/sebastianbergmann): Abbreviations should be uppercased

### Fixed
* [#31](https://github.com/phar-io/phive/issues/31): Not compatible with PHP 7


## 0.1.0 - 2015-09-23
* Initial Release

## [Unreleased]


[Unreleased]: https://github.com/phar-io/phive/compare/0.5.0...HEAD
[0.5.0]: https://github.com/phar-io/phive/compare/0.4.1...0.5.0
[0.4.1]: https://github.com/phar-io/phive/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/phar-io/phive/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/phar-io/phive/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/phar-io/phive/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/phar-io/phive/compare/0.1.0...0.2.0
