# Changelog

All notable changes to Phive are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [0.15.3] - 2024-08-22

### Fixed
* [#431](https://github.com/phar-io/phive/issues/431): Error while executing: Preparing php v8.4.0

### Added
* [#430](https://github.com/phar-io/phive/issues/430): Warn about missing configuration when running "phive install"


## [0.15.2] - 2022-08-02

__Please note__: Version 0.15.x requires PHP 7.3 or later.

### Fixed
* [#373](https://github.com/phar-io/phive/issues/373): Fix array access in gitlab repo
* [#375](https://github.com/phar-io/phive/issues/375): Fix gitlab repo tests
* Fix Namespace for auth file

## Changed
* Make auth type string case insensitive

## [0.15.1] - 2022-03-28

__Please note__: Version 0.15.x requires PHP 7.3 or later.

### Fixed
* [#327](https://github.com/phar-io/phive/issues/327): "Broken cURL install" message is not being displayed
* [#361](https://github.com/phar-io/phive/issues/361): Problem with 'remove' command on Windows
* [#363](https://github.com/phar-io/phive/issues/363): phive install failure: Undefined variable: output
* [#366](https://github.com/phar-io/phive/issues/366): purge command deleted the wrong version

### Changed
* [#325](https://github.com/phar-io/phive/pull/325): Improve the documentation concerning GPG key handling
* [#341](https://github.com/phar-io/phive/pull/341): Display identifier when no release matches (thanks @villfa)
* [#342](https://github.com/phar-io/phive/pull/342): Update error message with the right version
* [#346](https://github.com/phar-io/phive/pull/346): Fetch 100 github releases at a time (also on phar.io resolved aliases)



## [0.15.0] - 2021-07-24

__Please note__: Version 0.15.x requires PHP 7.3 or later.

__Please also note__:
As the SKS keyserver pool finally ceased operation, keys only hosted on their infrastructure
will no longer be available to phive and installations might fail if the respective maintainer
did not (re)publish their keys on still active servers. If not already done so, please tell them
to use `keys.openpgp.org` as their preferred key server.  

As of this version, `--trust-gpg-keys` accepts *fingerprint* as well as *key id* strings 

### Fixed
* [#312](https://github.com/phar-io/phive/issues/312): phive install does not silently import keys
* [#319](https://github.com/phar-io/phive/issues/319): Fatal error on PHP 8.1 (thanks @jrfnl)

### Removed
* All Code dealing with the SKS keyserver pools has been removed

### Changed

* [#310](https://github.com/phar-io/phive/issues/310): Version constraints using the caret operator (^) now honor pre-1.0 releases, e.g. ^0.3 translates to 0.3.*)


## [0.14.5] - 2020-11-30

Please note: Version 0.14.x requires PHP 7.2 or later.

### Fixed
* [#273](https://github.com/phar-io/phive/issues/273): Exception when `tput` not found
* [#284](https://github.com/phar-io/phive/issues/284): Make phive compatible with Xdebug 3.0
* [#287](https://github.com/phar-io/phive/issues/287): "installed" attribute in .phive/phars.xml is not updated after tool downgrade
* [#291](https://github.com/phar-io/phive/issues/291): Ensure Migrations are aware of configuration options like `--home`

### Added
* [#286](https://github.com/phar-io/phive/issues/286): Allow to set phive home directory via environment variable `PHIVE_HOME`


## [0.14.4] - 2020-06-12

Please note: Version 0.14.x requires PHP 7.2 or later.

### Fixed

* [#266](https://github.com/phar-io/phive/issues/266): "Ubuntu 20.04 & PHP7.4: phive install phploc" errors


## [0.14.3] - 2020-06-03

Please note: Version 0.14.x requires PHP 7.2 or later.

### Fixed

* [#262](https://github.com/phar-io/phive/issues/262): "Cannot write phar to ..." error
* [#261](https://github.com/phar-io/phive/issues/261): "phive status" prints wrong path to XML file

### Changed

* Phive should now properly clean up after itself, no more dangeling repo files in `/tmp`
* Broken symlinks no longer break installation of phars 
* Phive's phar build as well as cli wrapper now actually enforce use of PHP 7.2+


## [0.14.2] - 2020-05-19

Please note: Version 0.14.x requires PHP 7.2 or later.

### Fixed

* [#259](https://github.com/phar-io/phive/issues/259): "../.." is prepended to the path in the xml file after installation


## [0.14.1] - 2020-05-18

Please note: Version 0.14.x requires PHP 7.2 or later.

### Fixed

* [#257](https://github.com/phar-io/phive/issues/257): Error running phive install on 0.14.0


## [0.14.0] - 2020-05-17

Please note: Version 0.14.x requires PHP 7.2 or later.
Thanks to [MacFJA](https://github.com/MacFJA) for major contributions to this release!

This release uses `phar-io/version` 3.0. As a result, semantic version strings are now normalized.
That means strings like `v1.2.0` or `1.2` will turn into `1.2.0`.  

### Added

* [#40](https://github.com/phar-io/phive/issues/40): Support installing PHARs from locations requiring authentication via [PR #232](https://github.com/phar-io/phive/pull/232) by [MacFJA](https://github.com/MacFJA)
* [#72](https://github.com/phar-io/phive/issues/72): List of all installed Phars via [PR #229](https://github.com/phar-io/phive/pull/229) by [MacFJA](https://github.com/MacFJA) 
* via [PR #230](https://github.com/phar-io/phive/pull/230) by [MacFJA](https://github.com/MacFJA): GitLab as an alias resolver
* [#223](https://github.com/phar-io/phive/issues/223): phive outdated: How to know when packages needs to be updated?

### Fixed

* [#179](https://github.com/phar-io/phive/issues/179): tools dir is created even when not needed
* [#218](https://github.com/phar-io/phive/issues/218): Error when we are unable to read CLI input via [PR #235](https://github.com/phar-io/phive/pull/235) by [MacFJA](https://github.com/MacFJA) 
* [#226](https://github.com/phar-io/phive/issues/226): Crappy connection results in type error
* [#228](https://github.com/phar-io/phive/issues/228): [ERROR] No RateLimit present in response
* [#236](https://github.com/phar-io/phive/issues/236): Temporary option not used via [PR #238](https://github.com/phar-io/phive/pull/238) by [MacFJA](https://github.com/MacFJA)

### Changed
  
* [#217](https://github.com/phar-io/phive/issues/217): Hard dependency on `which` command via [PR #231](https://github.com/phar-io/phive/pull/231) by [MacFJA](https://github.com/MacFJA)
* Updated dependencies
* `ext/gnupg` should work again

### Removed

* Keyserver `keys.fedoraproject.org` removed from keyserver list as it ceased operation


## [0.13.5] - 2020-09-21

Please note: Version 0.13.x is the last to support PHP 7.1. Users are encouraged to upgrade to PHP 7.2 or later.

### Fixed
* GitHub blocks HEAD requests to their API Rate Limit endpoint, use GET


## [0.13.4] - 2020-09-18

Please note: Version 0.13.x is the last to support PHP 7.1. Users are encouraged to upgrade to PHP 7.2 or later.

### Fixed
* Add missing case fix to previous backported RateLimit parsing fix


## [0.13.3] - 2020-05-27

Please note: Version 0.13.x is the last to support PHP 7.1. Users are encouraged to upgrade to PHP 7.2 or later.

### Fixed
* Backport RateLimit parsing

## [0.13.2] - 2019-10-30

Please note: Version 0.13.x is the last to support PHP 7.1. Users are encouraged to upgrade to PHP 7.2 or later.

### Fixed

* Removed dangling debug line


## [0.13.1] - 2019-10-29

Please note: Version 0.13.x is the last to support PHP 7.1. Users are encouraged to upgrade to PHP 7.2 or later.

### Fixed

* [#208](https://github.com/phar-io/phive/issues/208): Parsing key data failed with error code 2: unlink
* via [PR #207](https://github.com/phar-io/phive/pull/207) by [jan-di](https://github.com/jan-di): Support paths with spaces in the username (on windows)
* [#153](https://github.com/phar-io/phive/issues/153): via [PR #214](https://github.com/phar-io/phive/pull/214) by [jaapio](https://github.com/jaapio): install --copy flag ignored

## [0.13.0] - 2019-09-19

Please note: Version 0.13.x is the last to support PHP 7.1. Uses are encouraged to upgrade to PHP 7.2 or later.

### Fixed

* [#206](https://github.com/phar-io/phive/issues/206): Phpstan key produces errors

### Changed

* Since the new openpgp keyserver does not offer the same information on keys
  as the sks keyservers, the local gpg binary / ext/gnupg is now used to gather
  information on keys prior to importing them to the phive key ring.
  Please report any issues this change may cause!

* [#185](https://github.com/phar-io/phive/issues/185): Support 0x prefix in --trust-gpg-keys


## [0.12.4] - 2019-08-11

### Fixed

* [#203](https://github.com/phar-io/phive/issues/203): Error installing behat

## [0.12.3] - 2019-07-24

### Fixed

* [#202](https://github.com/phar-io/phive/issues/202): Installing different version of phpstan or php-cs-fixer does not work.

### Changed

* Added `keys.openpgp.org` to keyserver list
* Changed order of keyservers: new verifying keyserver first, use fedora & ubuntu second, sks as last resort
  (This will be less of an issue once [#158](https://github.com/phar-io/phive/issues/158) is implemented.)

## [0.12.2] - 2019-06-02

### Fixed

* [#181](https://github.com/phar-io/phive/issues/181): Fixed umlauts in GPG key info
* Retries of failed key downloads now always use a different key server

### Changed

* PHPStan added to the repository (thanks to @szepeviktor!)
* Improved error message on failed signature verification
* Added `keys.fedoraproject.org` and `keyserver.ubuntu.com` to keyservers
* Dropped support for PHP < 7.0
* Dropped support for HHVM

## [0.12.1] - 2018-12-05

### Fixed

* [#143](https://github.com/phar-io/phive/issues/143): Improved error message when local Curl installation cannot verify SSL certificates 
* [#173](https://github.com/phar-io/phive/issues/173): SemVer error when installing Phing

### Changed

* Added minimalistic Markdown support for improved console output
* Updated help text

## [0.12.0] - 2018-09-11

### Added

* [#134](https://github.com/phar-io/phive/issues/134): Allow insecure installation of PHARs

### Changed

* [#117](https://github.com/phar-io/phive/issues/117): Improved error message when encountering an unsigned 
GitHub release 
* [#130](https://github.com/phar-io/phive/issues/130): Globally installed PHARs will now be placed in `/usr/local/bin/` 
under Linux

### Fixed

* [#140](https://github.com/phar-io/phive/issues/140): `--copy` does not work on Windows

## [0.11.0] - 2018-06-26

### Changed
* Upgrade phar-io/version to 2.0.0
* Changed DNS resolving to make downloading of keys more reliable
* Do not run DNS query for AAAA records on IPv4 only hosts (Windows)
* [#138](https://github.com/phar-io/phive/pull/138): Show key Ids for installed phars
* [#141](https://github.com/phar-io/phive/pull/141): Improved BatPharActivator

### Fixed
* [#130](https://github.com/phar-io/phive/issues/130): Call to a member function format() on null
* [#137](https://github.com/phar-io/phive/issues/137): Removing globally installed PHARs fails
* [#142](https://github.com/phar-io/phive/issues/136): Error when installing phing
* [#144](https://github.com/phar-io/phive/issues/144): Broken progress output on Windows
* [#145](https://github.com/phar-io/phive/issues/145): Version constraint ^3.0.0-alpha1 is not supported
* [#147](https://github.com/phar-io/phive/issues/147): Exception while installing PHPUnit

## [0.10.0] - 2018-03-25

### Changed

* [#125](https://github.com/phar-io/phive/issues/125): The reliability of GPG key lookup requests has been improved by respecting the list of IPs returned by a DNS lookup for `hkps.pool.sks-keyservers.net`   

### Fixed

* [#132](https://github.com/phar-io/phive/issues/132): Executing `phive --version` and `phive --help` now works as expected
* [#76](https://github.com/phar-io/phive/issues/76): The source URL of a PHAR is now stored in `phive.xml` 

## [0.9.0] - 2017-12-31

### Added

* [#127](https://github.com/phar-io/phive/issues/127): Notify user when GitHub's API rate limit is exceeded and use 
a GitHub auth token provided via the `GITHUB_AUTH_TOKEN` environment variable to extend the limit to 5000 requests/hour

## [0.8.2] - 2017-10-10

### Fixed

* [#115](https://github.com/phar-io/phive/issues/115): Fixed an uncaught exception when 
trying to install from a non-existing GitHub repository alias 

## [0.8.1] - 2017-09-22

### Added

* [#114](https://github.com/phar-io/phive/issues/114): Retry HTTP requests up to five times

### Fixed

* [#113](https://github.com/phar-io/phive/issues/113): Fixed a case sensitivity issue with PHAR aliases   

## [0.8.0] - 2017-08-26

### Added

* [#94](https://github.com/phar-io/phive/issues/94): Check if a PHAR's requirements are met during installation
* [#86](https://github.com/phar-io/phive/issues/86): Add global option `--no-progress`

## [0.7.2] - 2017-06-06

### Fixed

* [#108](https://github.com/phar-io/phive/issues/108): PHAR files are always symlinked on install, even with "-c" option

## [0.7.1] - 2017-06-02

### Changed

* [#105](https://github.com/phar-io/phive/issues/105): Always link PHARs to `/usr/bin` on Linux and to `/usr/local/bin` on macOS. 

### Fixed

* [#107](https://github.com/phar-io/phive/issues/107): Installing multiple PHARs when `~/.phive/phars.xml` does not exist works as expected now.

## [0.7.0] - 2017-05-19

### Added

`phive install` will now try to install PHARs from the local cache first before connecting to remote repositories. 
`phive update` will always look for newer versions in remote repositories first unless the `--prefer-offline` flag
 is provided.

* [#103](https://github.com/phar-io/phive/issues/103): Added automatic periodic updates of `repositories.xml`
* [#87](https://github.com/phar-io/phive/issues/87): Implement `--prefer-offline` commandline option for update command  

### Changed

* [#99](https://github.com/phar-io/phive/issues/99): Tests are now compatible with PHPUnit 6
* [#104](https://github.com/phar-io/phive/issues/104): Allow `v` and `V` as a version prefix on GitHub repositories

### Fixed

* [#102](https://github.com/phar-io/phive/issues/102): Global installations are now tracked in `~/.phive/phive.xml` 
and do not affect project-specific `phive.xml` files anymore
* [#96](https://github.com/phar-io/phive/issues/96): `phive.xml` is not altered when `phive install` did not install 
any changed PHARs

## [0.6.3] - 2017-02-05

### Changed
* Added Signature url into release infos
* internal refactoring
* [#89](https://github.com/phar-io/phive/issues/89): Use phar-io/version library
* [#84](https://github.com/phar-io/phive/issues/84): Use caret operator by default

### Fixed
* [#80](https://github.com/phar-io/phive/issues/80): Check if running environment supports colored output
* [#82](https://github.com/phar-io/phive/issues/82): Timeout for slow downloads
* [#95](https://github.com/phar-io/phive/issues/95): Composer command fails with error

## [0.6.2] - 2016-09-17

### Changed
* refined download progress visualisation
* implemented HTTP-cache layer
* internal refactoring

### Fixed
* [#78](https://github.com/phar-io/phive/issues/78): Custom location stored in `phive.xml` is now respected when installing and updating PHARs

## [0.6.1] - 2016-08-12

### Changed

* [#71](https://github.com/phar-io/phive/issues/71): Do no update PHARs if there is no newer version
* [#74](https://github.com/phar-io/phive/issues/74): Allow downgrading PHARs
* [#75](https://github.com/phar-io/phive/issues/75): Running ```phive install``` with arguments will now overwrite existing entries in ```phive.xml```

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

[Unreleased]: https://github.com/phar-io/phive/compare/0.15.0...HEAD
[0.15.0]: https://github.com/phar-io/phive/compare/0.14.5...0.15.0
[0.14.5]: https://github.com/phar-io/phive/compare/0.14.4...0.14.5
[0.14.4]: https://github.com/phar-io/phive/compare/0.14.3...0.14.4
[0.14.3]: https://github.com/phar-io/phive/compare/0.14.2...0.14.3
[0.14.2]: https://github.com/phar-io/phive/compare/0.14.1...0.14.2
[0.14.1]: https://github.com/phar-io/phive/compare/0.14.0...0.14.1
[0.14.0]: https://github.com/phar-io/phive/compare/0.13.2...0.14.0
[0.13.5]: https://github.com/phar-io/phive/compare/0.13.4...0.13.5
[0.13.4]: https://github.com/phar-io/phive/compare/0.13.3...0.13.4
[0.13.3]: https://github.com/phar-io/phive/compare/0.13.2...0.13.3
[0.13.2]: https://github.com/phar-io/phive/compare/0.13.0...0.13.2
[0.13.1]: https://github.com/phar-io/phive/compare/0.13.0...0.13.1
[0.13.0]: https://github.com/phar-io/phive/compare/0.12.4...0.13.0
[0.12.4]: https://github.com/phar-io/phive/compare/0.12.3...0.12.4
[0.12.3]: https://github.com/phar-io/phive/compare/0.12.2...0.12.3
[0.12.2]: https://github.com/phar-io/phive/compare/0.12.1...0.12.2
[0.12.1]: https://github.com/phar-io/phive/compare/0.12.0...0.12.1
[0.12.0]: https://github.com/phar-io/phive/compare/0.11.0...0.12.0
[0.11.0]: https://github.com/phar-io/phive/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/phar-io/phive/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/phar-io/phive/compare/0.8.2...0.9.0
[0.8.2]: https://github.com/phar-io/phive/compare/0.8.1...0.8.2
[0.8.1]: https://github.com/phar-io/phive/compare/0.8.0...0.8.1
[0.8.0]: https://github.com/phar-io/phive/compare/0.7.2...0.8.0
[0.7.2]: https://github.com/phar-io/phive/compare/0.7.1...0.7.2
[0.7.1]: https://github.com/phar-io/phive/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/phar-io/phive/compare/0.6.3...0.7.0
[0.6.3]: https://github.com/phar-io/phive/compare/0.6.2...0.6.3
[0.6.2]: https://github.com/phar-io/phive/compare/0.6.1...0.6.2
[0.6.1]: https://github.com/phar-io/phive/compare/0.6.0...0.6.1
[0.6.0]: https://github.com/phar-io/phive/compare/0.5.0...0.6.0
[0.5.0]: https://github.com/phar-io/phive/compare/0.4.1...0.5.0
[0.4.1]: https://github.com/phar-io/phive/compare/0.4.0...0.4.1
[0.4.0]: https://github.com/phar-io/phive/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/phar-io/phive/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/phar-io/phive/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/phar-io/phive/compare/0.1.0...0.2.0
