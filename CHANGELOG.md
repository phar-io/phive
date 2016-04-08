# Changelog of phive

## phive 0.3.0 (?? ?? ????)

## phive 0.2.1 (08 Apr 2016)

* Fix: Installing PHARs fails when phar.readonly is set in php.ini (#42)
* Fix: PHIVE binary gets corrupted (#43)
* Fix: Certificate for sks-keyservers.net cannot be loaded when PHIVE is run from a PHAR (#45)
>>>>>>> c9c96d7... update changelog

## phive 0.2.0 (25 Feb 2016)

* Merge PR [#32](https://github.com/phar-io/phive/pull/32) add basic "list" command do show known aliases
* Merge PR [#28](https://github.com/phar-io/phive/pull/28) Add help text for -global option
* Merge PR [#27](https://github.com/phar-io/phive/pull/27) Abbreviations should be uppercased
* Merge PR [#25](https://github.com/phar-io/phive/pull/25) Initial work on Issue [#16](https://github.com/phar-io/phive/issues/16)

* Add: show more info then fingerprint before import (Issue #33)
* Fix: not compatible with php 7 (Issue #31)
* Change: Make -save flag default behaviour (Issue #20)
* Add: '-save' option to write installed phar to phive.xml (Issue #18)
* Add: Allow global installation of PHARs enhancement (Issue #16)
* Add: Implement command to list known aliases enhancement (Issue #4)
* Add: Verify signature of phar (Issue #1) 

## phive 0.1.0 (23 Sep 2015)

* Initial Release
