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
     * Factory constructor.
     *
     * @param PhiveVersion $version
     */
    public function __construct(PhiveVersion $version = null) {
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
            $this->getEnvironment()
        );
    }

    /**
     * @return CommandLocator
     */
    private function getCommandLocator() {
        return new CommandLocator($this);
    }

    /**
     * @return VersionCommand
     */
    public function getVersionCommand() {
        return new VersionCommand(
            $this->getPhiveVersion(),
            $this->getConsoleOutput()
        );
    }

    /**
     * @return PhiveVersion
     */
    private function getPhiveVersion() {
        if (!$this->version) {
            $this->version = new PhiveVersion();
        }
        return $this->version;
    }

    /**
     * @return Cli\Output
     */
    private function getConsoleOutput() {
        return new Cli\ColoredConsoleOutput(Cli\ConsoleOutput::VERBOSE_INFO);
    }

    /**
     * @return HelpCommand
     */
    public function getHelpCommand() {
        return new HelpCommand(
            $this->getPhiveVersion(),
            $this->getEnvironment(),
            $this->getConsoleOutput()
        );
    }

    /**
     * @return Environment
     */
    private function getEnvironment() {
        return Environment::fromSuperGlobals();
    }

    /**
     * @param Cli\Options $options
     *
     * @return SkelCommand
     */
    public function getSkelCommand(Cli\Options $options) {
        return new SkelCommand(
            new SkelCommandConfig($options, getcwd()),
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
            $this->getEnvironment()
        );
    }

    /**
     * @return PhiveXmlConfig
     */
    private function getPhiveXmlConfig() {
        return new PhiveXmlConfig(
            new XmlFile(
                new Filename(__DIR__ . '/../phive.xml'),
                'https://phar.io/phive',
                'phive'
            )
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
                'hkps.pool.sks-keyservers.net',
                __DIR__ . '/../conf/ssl/ca_certs/sks-keyservers.netCA.pem'
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
     * @param Cli\Options $options
     *
     * @return RemoveCommand
     */
    public function getRemoveCommand(Cli\Options $options) {
        return new RemoveCommand(
            new RemoveCommandConfig($options, $this->getConfig()),
            $this->getPhveInstallDB(),
            $this->getPharService(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return PhiveInstallDB
     */
    private function getPhveInstallDB() {
        return new PhiveInstallDB(
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
    public function getPharService() {
        return new PharService(
            $this->getPharDownloader(),
            $this->getPharInstaller(),
            $this->getPhveInstallDB(),
            $this->getAliasResolver(),
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
            $this->getSignatureService(),
            $this->getChecksumService()
        );
    }

    /**
     * @return SignatureService
     */
    public function getSignatureService() {
        return new SignatureService($this->getGnupgSignatureVerifier());
    }

    /**
     * @return SignatureVerifier
     */
    public function getGnupgSignatureVerifier() {
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
    public function getKeyService() {
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
     * @return AliasResolver
     */
    private function getAliasResolver() {

        return new AliasResolver(
            $this->getSourcesList()
        );
    }

    /**
     * @return SourceRepositoryLoader
     */
    private function getPharIoRepositoryFactory() {
        return new SourceRepositoryLoader($this->getFileDownloader());
    }

    /**
     * @param Cli\Options $options
     *
     * @return InstallCommand
     */
    public function getInstallCommand(Cli\Options $options) {
        return new InstallCommand(
            new InstallCommandConfig(
                $options,
                $this->getConfig(),
                $this->getPhiveXmlConfig()
            ),
            $this->getPharService(),
            $this->getPhiveXmlConfig(),
            $this->getEnvironment()
        );
    }

    /**
     * @param Cli\Options $options
     *
     * @return UpdateCommand
     */
    public function getUpdateCommand(Cli\Options $options) {
        return new UpdateCommand(
            new UpdateCommandConfig(
                $options,
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
     * @param Cli\Options $options
     *
     * @return PurgeCommand
     */
    public function getPurgeCommand(Cli\Options $options) {
        return new PurgeCommand(
            new PurgeCommandConfig(
                $options,
                $this->getConfig()
            ),
            $this->getPhveInstallDB(),
            $this->getColoredConsoleOutput()
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

}
