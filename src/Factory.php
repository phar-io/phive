<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class Factory {

    /**
     * @var HttpClient
     */
    private $curl;

    /**
     * @var PhiveVersion
     */
    private $version;

    /**
     * @var Cli\Request
     */
    private $request;

    /**
     * @param Cli\Request  $request
     * @param PhiveVersion $version
     */
    public function __construct(Cli\Request $request, PhiveVersion $version = null) {
        $this->request = $request;
        $this->version = $version;
    }

    /**
     * @return Cli\Runner
     */
    public function getRunner() {
        return new Cli\Runner(
            $this->getCommandLocator(),
            $this->getConsoleOutput(),
            $this->getPhiveVersion(),
            $this->getEnvironment(),
            $this->request
        );
    }

    /**
     * @return VersionCommand
     */
    public function getVersionCommand() {
        return new VersionCommand;
    }

    /**
     * @return HelpCommand
     */
    public function getHelpCommand() {
        return new HelpCommand(
            $this->getEnvironment(),
            $this->getConsoleOutput()
        );
    }

    /**
     * @return SkelCommand
     */
    public function getSkelCommand() {
        return new SkelCommand(
            new SkelCommandConfig($this->request->getCommandOptions(), getcwd()),
            $this->getPhiveVersion()
        );
    }

    /**
     * @return UpdateRepositoryListCommand
     */
    public function getUpdateRepositoryListCommand() {
        return new UpdateRepositoryListCommand($this->getPharIoRepositoryListFileLoader());
    }

    /**
     * @return RemoveCommand
     */
    public function getRemoveCommand() {
        return new RemoveCommand(
            new RemoveCommandConfig($this->request->getCommandOptions(), $this->getTargetDirectoryLocator()),
            $this->getPharRegistry(),
            $this->getPharService(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return ResetCommand
     */
    public function getResetCommand() {
        return new ResetCommand(
            new ResetCommandConfig($this->request->getCommandOptions()),
            $this->getPharRegistry(),
            $this->getEnvironment(),
            $this->getPharInstaller()
        );
    }

    /**
     * @return InstallCommand
     */
    public function getInstallCommand() {
        return new InstallCommand(
            new InstallCommandConfig(
                $this->request->getCommandOptions(), $this->getPhiveXmlConfig(), $this->getTargetDirectoryLocator()
            ),
            $this->getPharService(),
            $this->getPhiveXmlConfig(),
            $this->getEnvironment()
        );
    }

    /**
     * @return UpdateCommand
     */
    public function getUpdateCommand() {
        return new UpdateCommand(
            new UpdateCommandConfig(
                $this->request->getCommandOptions(),
                $this->getConfig(),
                $this->getPhiveXmlConfig()
            ),
            $this->getPharService(),
            $this->getPhiveXmlConfig()
        );
    }

    /**
     * @return ListCommand
     */
    public function getListCommand() {
        return new ListCommand(
            $this->getSourcesList(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return PurgeCommand
     */
    public function getPurgeCommand() {
        return new PurgeCommand(
            new PurgeCommandConfig(
                $this->request->getCommandOptions(),
                $this->getConfig()
            ),
            $this->getPharRegistry(),
            $this->getColoredConsoleOutput()
        );
    }

    public function getComposerCommand() {
        return new ComposerCommand(
            new ComposerCommandConfig(
                $this->request->getCommandOptions(),
                $this->getConfig(),
                $this->getPhiveXmlConfig()
            ),
            $this->getComposerService(),
            $this->getPharService(),
            $this->getPhiveXmlConfig(),
            $this->getEnvironment(),
            $this->getConsoleInput()
        );
    }

    /**
     * @return TargetDirectoryLocator
     */
    private function getTargetDirectoryLocator()
    {
        return new TargetDirectoryLocator($this->getConfig(), $this->getPhiveXmlConfig(), $this->request->getCommandOptions());
    }

    /**
     * @return CommandLocator
     */
    private function getCommandLocator() {
        return new CommandLocator($this);
    }

    /**
     * @return Cli\Output
     */
    private function getConsoleOutput() {
        return new Cli\ColoredConsoleOutput(Cli\ConsoleOutput::VERBOSE_INFO);
    }

    /**
     * @return PhiveVersion
     */
    private function getPhiveVersion() {
        if (!$this->version) {
            $this->version = new GitAwarePhiveVersion($this->getGit());
        }
        return $this->version;
    }

    /**
     * @return Git
     */
    private function getGit() {
        return new Git($this->getEnvironment()->getWorkingDirectory());
    }

    /**
     * @return Environment
     */
    private function getEnvironment() {
        return Environment::fromSuperGlobals();
    }

    /**
     * @return SourcesListFileLoader
     */
    private function getPharIoRepositoryListFileLoader() {
        return new SourcesListFileLoader(
            $this->getConfig()->getSourcesListUrl(),
            $this->getConfig()->getHomeDirectory()->file('repositories.xml'),
            $this->getFileDownloader(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return Config
     */
    private function getConfig() {
        return new Config(
            $this->getEnvironment(),
            $this->request->getCommandOptions()
        );
    }

    /**
     * @return FileDownloader
     */
    private function getFileDownloader() {
        return new FileDownloader(
            $this->getCurl(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return HttpClient
     */
    private function getCurl() {
        if (null === $this->curl) {
            $config = new CurlConfig('Phive ' . $this->getPhiveVersion()->getVersion());
            $config->addLocalSslCertificate(
                new LocalSslCertificate(
                    'hkps.pool.sks-keyservers.net',
                    __DIR__ . '/../conf/ssl/ca_certs/sks-keyservers.netCA.pem'
                )
            );
            $environment = $this->getEnvironment();
            if ($environment->hasProxy()) {
                $config->setProxy($environment->getProxy());
            }
            $this->curl = new Curl($config);
        }
        return $this->curl;
    }

    /**
     * @return Cli\Output
     */
    private function getColoredConsoleOutput() {
        return new Cli\ColoredConsoleOutput(Cli\ConsoleOutput::VERBOSE_INFO);
    }

    /**
     * @return PharRegistry
     */
    private function getPharRegistry() {
        return new PharRegistry(
            new XmlFile(
                $this->getConfig()->getHomeDirectory()->file('/phars.xml'),
                'https://phar.io/phive/installdb',
                'phars'
            ),
            $this->getConfig()->getHomeDirectory()->child('phars')
        );
    }

    /**
     * @return PharService
     */
    private function getPharService() {
        return new PharService(
            $this->getPharDownloader(),
            $this->getPharInstaller(),
            $this->getPharRegistry(),
            $this->getAliasResolverService(),
            $this->getColoredConsoleOutput(),
            $this->getPharIoRepositoryFactory()
        );
    }

    /**
     * @return PharDownloader
     */
    private function getPharDownloader() {
        return new PharDownloader(
            $this->getFileDownloader(),
            $this->getGnupgSignatureVerifier(),
            $this->getChecksumService(),
            $this->getPharRegistry()
        );
    }

    /**
     * @return SignatureVerifier
     */
    private function getGnupgSignatureVerifier() {
        return new GnupgSignatureVerifier($this->getGnupg(), $this->getKeyService());
    }

    /**
     * @return \Gnupg
     */
    private function getGnupg() {
        $home = $this->getConfig()->getHomeDirectory()->child('gpg');
        if (extension_loaded('gnupg')) {
            putenv('GNUPGHOME=' . $home);
            $gpg = new \Gnupg();
            $gpg->seterrormode(\Gnupg::ERROR_EXCEPTION);
        } else {
            $gpg = new GnuPG(
                $this->getConfig()->getGPGBinaryPath(),
                $home
            );
            if (!class_exists('\Gnupg')) {
                class_alias(GnuPG::class, '\Gnupg');
            }
        }
        return $gpg;
    }

    /**
     * @return KeyService
     */
    private function getKeyService() {
        return new KeyService(
            $this->getPgpKeyDownloader(),
            $this->getGnupgKeyImporter(),
            $this->getColoredConsoleOutput(),
            $this->getConsoleInput()
        );
    }

    /**
     * @return GnupgKeyDownloader
     */
    private function getPgpKeyDownloader() {
        return new GnupgKeyDownloader(
            $this->getCurl(),
            include __DIR__ . '/../conf/pgp-keyservers.php',
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return KeyImporter
     */
    private function getGnupgKeyImporter() {
        return new GnupgKeyImporter($this->getGnupg());
    }

    /**
     * @return Cli\ConsoleInput
     */
    private function getConsoleInput() {
        return new Cli\ConsoleInput($this->getConsoleOutput());
    }

    /**
     * @return ChecksumService
     */
    private function getChecksumService() {
        return new ChecksumService();
    }

    /**
     * @return PharInstaller
     */
    private function getPharInstaller() {
        return new PharInstaller(
            $this->getConfig()->getHomeDirectory()->child('phars'),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return PharIoAliasResolver
     */
    private function getPharIoAliasResolver() {
        return new PharIoAliasResolver(
            $this->getSourcesList()
        );
    }

    /**
     * @return SourcesList
     */
    private function getSourcesList() {
        return new SourcesList(
            new XmlFile(
                $this->getPharIoRepositoryListFileLoader()->load(),
                'https://phar.io/repository-list',
                'repositories'
            )
        );
    }

    /**
     * @return SourceRepositoryLoader
     */
    private function getPharIoRepositoryFactory() {
        return new SourceRepositoryLoader($this->getFileDownloader());
    }

    /**
     * @return PhiveXmlConfig
     */
    private function getPhiveXmlConfig() {
        return new PhiveXmlConfig(
            new XmlFile(
                $this->getEnvironment()->getWorkingDirectory()->file('phive.xml'),
                'https://phar.io/phive',
                'phive'
            )
        );
    }

    private function getComposerService() {
        return new ComposerService($this->getSourcesList());
    }

    private function getAliasResolverService() {
        $service = new AliasResolverService();

        $service->addResolver(
            $this->getGithubAliasResolver()
        );

        $service->addResolver(
            $this->getPharIoAliasResolver()
        );

        return $service;
    }

    private function getGithubAliasResolver() {
        return new GithubAliasResolver($this->getCurl());
    }

}
