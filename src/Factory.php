<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class Factory {

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @return CLI\Runner
     */
    public function getRunner() {
        return new CLI\Runner($this->getCommandLocator(), $this->getConsoleOutput(), $this->getPhiveVersion());
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
        return new PhiveVersion();
    }

    /**
     * @return CLI\Output
     */
    private function getConsoleOutput() {
        return new CLI\ConsoleOutput(CLI\ConsoleOutput::VERBOSE_INFO);
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
        return new Environment($_SERVER);
    }

    /**
     * @param CLI\Options $options
     *
     * @return SkelCommand
     */
    public function getSkelCommand(CLI\Options $options) {
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
     * @return PharIoRepositoryListFileLoader
     */
    private function getPharIoRepositoryListFileLoader() {
        return new PharIoRepositoryListFileLoader(
            $this->getConfig()->getRepositoryListUrl(),
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
            $this->getPhiveXmlConfig()
        );
    }

    /**
     * @return PhiveXmlConfig
     */
    private function getPhiveXmlConfig() {
        return new PhiveXmlConfig(new Filename(__DIR__ . '/../phive.xml'));
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
     * @return Curl
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
     * @return CLI\Output
     */
    private function getColoredConsoleOutput() {
        return new CLI\ColoredConsoleOutput(CLI\ConsoleOutput::VERBOSE_INFO);
    }

    /**
     * @param CLI\Options $options
     *
     * @return RemoveCommand
     */
    public function getRemoveCommand(CLI\Options $options) {
        return new RemoveCommand(
            new RemoveCommandConfig($options, $this->getConfig()),
            $this->getPharRepository(),
            $this->getPharService(),
            $this->getColoredConsoleOutput()
        );
    }

    /**
     * @return PharRepository
     */
    private function getPharRepository() {
        return new PharRepository(
            $this->getConfig()->getHomeDirectory()->file('/phars.xml'),
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
            $this->getPharRepository(),
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
     * @return CLI\ConsoleInput
     */
    private function getConsoleInput() {
        return new CLI\ConsoleInput($this->getConsoleOutput());
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
            new PharIoRepositoryList(
                $this->getPharIoRepositoryListFileLoader()->load()
            )
        );
    }

    /**
     * @return PharIoRepositoryFactory
     */
    private function getPharIoRepositoryFactory() {
        return new PharIoRepositoryFactory($this->getFileDownloader());
    }

    /**
     * @param CLI\Options $options
     *
     * @return InstallCommand
     */
    public function getInstallCommand(CLI\Options $options) {
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
     * @param CLI\Options $options
     *
     * @return PurgeCommand
     */
    public function getPurgeCommand(CLI\Options $options) {
        return new PurgeCommand(
            new PurgeCommandConfig(
                $options,
                $this->getConfig()
            ),
            $this->getPharRepository(),
            $this->getColoredConsoleOutput()
        );
    }

}
