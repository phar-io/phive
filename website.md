#Welcome to phar.io

##The Phar Installation and Verification Environment (PHIVE) 

Installation and verification of [phar](http://php.net/phar) distributed PHP applications has never been this easy!

Adding all the required tools like PHPUnit, PHPMD and phpDox in their matching versions to a project used to be a lot of
repetitive work: Started by finding the download URL, figuring out what the actually correct and matching version is
followed by verifiying the sha1 and gpg signatures and making the archive executable. And of course you'd have to repeat
this very thing for every tool needed.
 
Alternatively, you could have used composer. And cluttered your project's dependencies with the ones of your
tools. And fight their potential dependency conflicts. 

No more! Now can rely on PHIVE to install and manage your projects tooling needs without all the hassle and without
cluttered dependencies!

##Getting PHIVE

Installation of PHIVE is easy and about the last time you have to do anything phar related manually.
Grab your copy of PHIVE from the [releases](https://github.com/theseer/PHIVE/releases) section at our github page or
follow these 5 simple steps:

    wget -o  https://github.com/theseer/PHIVE/releases/latest/PHIVE.phar
    gpg --keyserver some.where --import 0x1234567890
    gpg --verify https://github.com/theseer/PHIVE/releases/latest/PHIVE.phar.asc PHIVE.phar
    chmod +x PHIVE.phar
    sudo mv PHIVE.phar /usr/bin/PHIVE

##Sample Usages

Once installed, PHIVE is ready for action. Some example invocations are shown below:

    PHIVE install

    PHIVE install phpunit

    PHIVE install phpunit@4.6.1

    PHIVE install --copy phpdox     

    PHIVE install phpdox bin/phpdox
         
    PHIVE install composer
    
    PHIVE install --signature https://proprietary.org/sig.asc https://proprietary.org/some.phar 


##Adding PHIVE support to your own project

###Getting started
To leverage the power of PHIVE, you don't need to do anything but install the tools you need using the PHIVE executable.
PHIVE will automatically record your changes in an xml configuration file so you can replay the process as needed. 

###Advanced
To support for more advanced uses and to be able to replay an installation, a PHIVE configuration file can of course
also be added to your project manually. Running ```PHIVE skel``` will get you an annotated example configuration file to
get you started.


##Phar.io Project Database

You can browse and query the phar.io project database to find out which tools are already known by PHIVE here:
   
[-------------------------------] [search]

If your application is missing and you want to have it added, please open a ticket and/or supply a pull request.

##Popular Projects

* Composer
* PHPUnit
* phpDox
* PHP MessDetector
* PHP CodeSniffer
 
##How it works

PHIVE makes installation easy by providing a means to resolve the given alias to an actual download location, including
the verification of the certificate supplied by the server. Once downloaded, the archive's SHA1/SHA256/SHA512 hash is
verified and so its OpenPGP/GnuPG or OpenSSL signature.

Instead of redownloading the same phar multiple times, the archive is kept at a shared location (by default in ~/.PHIVE)
and only a symbolic link is created for the project. You can of course also explicitly request a copy of the phar to
be made in favor of symbolic links (use --copy).

Phar.io only serves as a central database to resolve alias names but does not provide the actual phar files. If you
want to be independent of phar.io, simply use the fully qualified url to any PHIVE compatbile repository server.

##PHIVE repository

To make your phar based project available to PHIVE users, you need to provide a download location either by following
some simple conventions or by generating a metadata file. The later can be automated using [toolx].

If no metadata file can be found, PHIVE will assume that auto indexing for the repository base url is enabled and
the resulting HTML document contains links to all downloadable phars. Each phar's filename has to follow this pattern:
 
    projectname-<semantic version>
    projectname-nightly
    
For each phar an accompanying .asc file has to be provided, containing the OpenPGP/GnuPG or OpenSSL signature.


##Contributing

[Fork us!](https://github.com/theseer/...)

PHIVE and phar.io are open source projects relesaed under the BSD license. You are welcome to join the development
team! Please refer to the [contributing](https://github.com/theseer/...) document for more details. 

