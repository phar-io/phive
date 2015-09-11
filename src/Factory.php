<?php
namespace PharIo\Phive {

    use TheSeer\CLI;

    class Factory {

        /**
         * @var Curl
         */
        private $curl;

        /**
         * @return CLI\Runner
         */
        public function getRunner() {
            return new CLI\Runner($this->getCommandLocator());
        }

        /**
         * @return VersionCommand
         */
        public function getVersionCommand() {
            return new VersionCommand($this->getPhiveVersion(), $this->getConsoleOutput());
        }

        /**
         * @return HelpCommand
         */
        public function getHelpCommand() {
            return new HelpCommand();
        }

        /**
         * @param CLI\CommandOptions $options
         *
         * @return SkelCommand
         */
        public function getSkelCommand(CLI\CommandOptions $options) {
            return new SkelCommand(new SkelCommandConfig($options, getcwd()), $this->getPhiveVersion());
        }

        /**
         * @param CLI\CommandOptions $options
         *
         * @return RemoveCommand
         */
        public function getRemoveCommand(CLI\CommandOptions $options) {
            return new RemoveCommand(
                new RemoveCommandConfig($options, $this->getConfig()),
                $this->getPharRepository(),
                $this->getPharService(),
                $this->getColoredConsoleOutput()
            );
        }

        /**
         * @param CLI\CommandOptions $options
         *
         * @return InstallCommand
         */
        public function getInstallCommand(CLI\CommandOptions $options) {
            return new InstallCommand(
                new InstallCommandConfig($options, $this->getConfig()),
                $this->getPharService()
            );
        }

        /**
         * @return CommandLocator
         */
        private function getCommandLocator() {
            return new CommandLocator($this);
        }

        /**
         * @return PhiveVersion
         */
        private function getPhiveVersion() {
            return new PhiveVersion();
        }

        /**
         * @return PharDownloader
         */
        private function getPharDownloader() {
            return new PharDownloader($this->getCurl(), $this->getSignatureService());
        }

        /**
         * @return PharInstaller
         */
        private function getPharInstaller() {
            return new PharInstaller(
                $this->getConfig()->getHomeDirectory()->child('phars'), $this->getColoredConsoleOutput()
            );
        }

        /**
         * @return PharService
         */
        public function getPharService() {
            return new PharService($this->getPharDownloader(), $this->getPharInstaller(), $this->getPharRepository());
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
         * @return KeyService
         */
        public function getKeyService() {
            return new KeyService(
                $this->getPgpKeyDownloader(),
                $this->getGnupgKeyImporter(),
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
         * @return GnupgKeyDownloader
         */
        private function getPgpKeyDownloader() {
            return new GnupgKeyDownloader(
                $this->getCurl(), include __DIR__ . '/../conf/pgp-keyservers.php', $this->getColoredConsoleOutput()
            );
        }

        /**
         * @return Output
         */
        private function getColoredConsoleOutput() {
            return new ColoredConsoleOutput(ConsoleOutput::VERBOSE_INFO);
        }

        /**
         * @return Output
         */
        private function getConsoleOutput() {
            return new ConsoleOutput(ConsoleOutput::VERBOSE_INFO);
        }

        /**
         * @return Curl
         */
        private function getCurl() {
            if (null === $this->curl) {
                $config = new CurlConfig('Phive ' . $this->getPhiveVersion()->getVersion());
                $config->addLocalSslCertificate(
                    'hkps.pool.sks-keyservers.net', __DIR__ . '/../conf/ssl/ca_certs/sks-keyservers.netCA.pem'
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
         * @return Environment
         */
        private function getEnvironment() {
            return new Environment($_SERVER);
        }

        /**
         * @return Config
         */
        private function getConfig() {
            return new Config($this->getEnvironment());
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
         * @return PharRepository
         */
        private function getPharRepository() {
            return new PharRepository(
                $this->getConfig()->getHomeDirectory() . '/phars.xml',
                $this->getConfig()->getHomeDirectory()->child('phars')
            );
        }

    }

}
