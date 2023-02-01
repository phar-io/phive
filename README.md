## The Phar Installation and Verification Environment (PHIVE)

Installation and verification of [phar](http://php.net/phar) distributed PHP applications has never been this easy!

[![Integrate](https://github.com/phar-io/phive/actions/workflows/integrate.yaml/badge.svg)](https://github.com/phar-io/phive/actions/workflows/integrate.yaml)


Adding all the required tools like PHPUnit, PHPMD and phpDox in their matching versions to a project used to be a lot of
repetitive work: Started by finding the download URL, figuring out what the actually correct and matching version is
followed by verifying the sha1 and gpg signatures and making the archive executable. And of course you'd have to repeat
this very thing for every tool needed.

Alternatively, you could have used composer. And cluttered your project's dependencies with the ones of your
tools. And fight their potential dependency conflicts.

No more! Now you can rely on PHIVE to install and manage your project's tooling needs without all the hassle and without
cluttered dependencies!

## Getting PHIVE

Installation of PHIVE is easy and about the last time you have to do anything phar related manually.
Grab your copy of PHIVE from the [releases](https://github.com/phar-io/phive/releases) section at our GitHub page or
follow these simple steps:

```bash
wget -O phive.phar "https://phar.io/releases/phive.phar"
wget -O phive.phar.asc "https://phar.io/releases/phive.phar.asc"
gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x6AF725270AB81E04D79442549D8A98B29B2D5D79
gpg --verify phive.phar.asc phive.phar
rm phive.phar.asc
chmod +x phive.phar
mv phive.phar /usr/local/bin/phive
```

## Homebrew

If you have Homebrew you can easily install it with:
```bash
brew install phive
```

## Getting started

`phive` expects a command to be specified to run. To get a list of available commands and their
respective options, you can can run `phive` either with `--help`, `help` or without any additional
parameters. To give you a general idea on how to use it, have a look at the following sample usages.


## Sample Usages

Once installed, PHIVE is ready for action. Some example invocations are shown below:

    phive install phpunit
    phive install --copy phpdox
    phive install phpdox bin/phpdox
    phive install https://phar.phpunit.de/phpunit-4.8.6.phar
    phive install --temporary phpunit@~5.0

    phive update
    phive update --prefer-offline
    phive update phpunit

## How it works

PHIVE makes installation easy by downloading the phar archive from the given location, including the verification of
the certificate supplied by the server. Once downloaded, the archive's SHA1/SHA256/SHA512 hash is verified and so its
OpenPGP/GnuPG or OpenSSL signature.

Instead of redownloading the same phar multiple times, the archive is kept at a shared location (by default in `~/.phive`)
and only a symbolic link is created for the project. You can of course also explicitly request a copy of the phar to
be made in favor of symbolic links (use `--copy`).

## Contributing

PHIVE and phar.io are open source projects released under the BSD license. You are welcome to join the development
team!

