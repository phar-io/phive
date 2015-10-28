<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'phario\\phive\\aliasresolver' => '/services/phar/AliasResolver.php',
                'phario\\phive\\anyversionconstraint' => '/shared/versionconstraints/AnyVersionConstraint.php',
                'phario\\phive\\coloredconsoleoutput' => '/shared/cli/ColoredConsoleOutput.php',
                'phario\\phive\\commandlocator' => '/commands/CommandLocator.php',
                'phario\\phive\\config' => '/shared/config/Config.php',
                'phario\\phive\\consoleinput' => '/shared/cli/ConsoleInput.php',
                'phario\\phive\\consoleoutput' => '/shared/cli/ConsoleOutput.php',
                'phario\\phive\\curl' => '/shared/curl/Curl.php',
                'phario\\phive\\curlconfig' => '/shared/curl/CurlConfig.php',
                'phario\\phive\\curlexception' => '/shared/exceptions/CurlException.php',
                'phario\\phive\\curlresponse' => '/shared/curl/CurlResponse.php',
                'phario\\phive\\directory' => '/shared/Directory.php',
                'phario\\phive\\directoryexception' => '/shared/exceptions/DirectoryException.php',
                'phario\\phive\\downloadfailedexception' => '/shared/exceptions/DownloadFailedException.php',
                'phario\\phive\\environment' => '/shared/Environment.php',
                'phario\\phive\\exactversionconstraint' => '/shared/versionconstraints/ExactVersionConstraint.php',
                'phario\\phive\\factory' => '/Factory.php',
                'phario\\phive\\file' => '/shared/File.php',
                'phario\\phive\\filedownloader' => '/shared/download/FileDownloader.php',
                'phario\\phive\\gnupg' => '/shared/GnuPG.php',
                'phario\\phive\\gnupgkeydownloader' => '/services/key/gpg/GnupgKeyDownloader.php',
                'phario\\phive\\gnupgkeyimporter' => '/services/key/gpg/GnupgKeyImporter.php',
                'phario\\phive\\gnupgsignatureverifier' => '/services/signature/gpg/GnupgSignatureVerifier.php',
                'phario\\phive\\gnupgverificationresult' => '/services/signature/gpg/GnupgVerificationResult.php',
                'phario\\phive\\greaterthanorequaltoversionconstraint' => '/shared/versionconstraints/GreaterThanOrEqualToVersionConstraint.php',
                'phario\\phive\\helpcommand' => '/commands/help/HelpCommand.php',
                'phario\\phive\\input' => '/shared/cli/Input.php',
                'phario\\phive\\installationfailedexception' => '/shared/exceptions/InstallationFailedException.php',
                'phario\\phive\\installcommand' => '/commands/install/InstallCommand.php',
                'phario\\phive\\installcommandconfig' => '/commands/install/InstallCommandConfig.php',
                'phario\\phive\\ioexception' => '/shared/exceptions/IOException.php',
                'phario\\phive\\keydownloader' => '/services/key/KeyDownloader.php',
                'phario\\phive\\keyimporter' => '/services/key/KeyImporter.php',
                'phario\\phive\\keyimportresult' => '/services/key/KeyImportResult.php',
                'phario\\phive\\keyservice' => '/services/key/KeyService.php',
                'phario\\phive\\output' => '/shared/cli/Output.php',
                'phario\\phive\\phar' => '/shared/phar/Phar.php',
                'phario\\phive\\pharalias' => '/shared/phar/PharAlias.php',
                'phario\\phive\\phardownloader' => '/services/phar/PharDownloader.php',
                'phario\\phive\\pharinstaller' => '/services/phar/PharInstaller.php',
                'phario\\phive\\phariorepository' => '/shared/repository/PharIoRepository.php',
                'phario\\phive\\phariorepositorylist' => '/shared/repository/PharIoRepositoryList.php',
                'phario\\phive\\phariorepositorylistfileloader' => '/shared/repository/PharIoRepositoryListFileLoader.php',
                'phario\\phive\\pharrepository' => '/shared/repository/PharRepository.php',
                'phario\\phive\\pharrepositoryexception' => '/shared/exceptions/PharRepositoryException.php',
                'phario\\phive\\pharservice' => '/services/phar/PharService.php',
                'phario\\phive\\phiveversion' => '/commands/version/PhiveVersion.php',
                'phario\\phive\\phivexmlconfig' => '/shared/config/PhiveXmlConfig.php',
                'phario\\phive\\purgecommand' => '/commands/purge/PurgeCommand.php',
                'phario\\phive\\purgecommandconfig' => '/commands/purge/PurgeCommandConfig.php',
                'phario\\phive\\release' => '/shared/phar/Release.php',
                'phario\\phive\\releasecollection' => '/shared/phar/ReleaseCollection.php',
                'phario\\phive\\removecommand' => '/commands/remove/RemoveCommand.php',
                'phario\\phive\\removecommandconfig' => '/commands/remove/RemoveCommandConfig.php',
                'phario\\phive\\resolveexception' => '/shared/exceptions/ResolveException.php',
                'phario\\phive\\signatureservice' => '/services/signature/SignatureService.php',
                'phario\\phive\\signatureverifier' => '/services/signature/SignatureVerifier.php',
                'phario\\phive\\skelcommand' => '/commands/skel/SkelCommand.php',
                'phario\\phive\\skelcommandconfig' => '/commands/skel/SkelCommandConfig.php',
                'phario\\phive\\specificmajorandminorversionconstraint' => '/shared/versionconstraints/SpecificMajorAndMinorVersionConstraint.php',
                'phario\\phive\\specificmajorversionconstraint' => '/shared/versionconstraints/SpecificMajorVersionConstraint.php',
                'phario\\phive\\updaterepositorylistcommand' => '/commands/update-repository-list/UpdateRepositoryListCommand.php',
                'phario\\phive\\url' => '/shared/Url.php',
                'phario\\phive\\verificationfailedexception' => '/shared/exceptions/VerificationFailedException.php',
                'phario\\phive\\version' => '/shared/Version.php',
                'phario\\phive\\versioncommand' => '/commands/version/VersionCommand.php',
                'phario\\phive\\versionconstraintgroup' => '/shared/versionconstraints/VersionConstraintGroup.php',
                'phario\\phive\\versionconstraintinterface' => '/shared/versionconstraints/VersionConstraintInterface.php',
                'phario\\phive\\versionconstraintparser' => '/shared/versionconstraints/VersionConstraintParser.php',
                'phario\\phive\\writablexmlrepository' => '/shared/repository/WritableXmlRepository.php',
                'phario\\phive\\xmlrepository' => '/shared/repository/XmlRepository.php',
                'phario\\phive \\releaseexception' => '/shared/exceptions/ReleaseException.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    },
    true,
    true
);
// @codeCoverageIgnoreEnd
