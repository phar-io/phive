
##The Phar Installation and Verification Environment (PHIVE) 

Installation and verification of [phar](http://php.net/phar) distributed PHP applications has never been this easy!

Adding all the required tools like PHPUnit, PHPMD and phpDox in their matching versions to a project used to be a lot of
repetitive work: Started by finding the download URL, figuring out what the actually correct and matching version is
followed by verifiying the sha1 and gpg signatures and making the archive executable. And of course you'd have to repeat
this very thing for every tool needed.
 
Alternatively, you could have used composer. And cluttered your project's dependencies with the ones of your
tools. And fight their potential dependency conflicts. 

No more! Now you can rely on PHIVE to install and manage your projects tooling needs without all the hassle and without
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
    
    phive install https://proprietary.org/some.phar 

    phive install -copy https://proprietary.org/some.phar
     
    phive remove  some.phar
    
    phive version

 
##How it works

PHIVE makes installation easy by downloading the phar archive from the given location, including the verification of
the certificate supplied by the server. Once downloaded, the archive's SHA1/SHA256/SHA512 hash is verified and so its
OpenPGP/GnuPG or OpenSSL signature.

Instead of redownloading the same phar multiple times, the archive is kept at a shared location (by default in ~/.phive)
and only a symbolic link is created for the project. You can of course also explicitly request a copy of the phar to
be made in favor of symbolic links (use -copy).

##Contributing

[Fork us!](https://github.com/phar-io/phive)

PHIVE and phar.io are open source projects relesaed under the BSD license. You are welcome to join the development
team!  

