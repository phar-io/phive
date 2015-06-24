<?php
namespace TheSeer\Phive {

    class Factory {

        /**
         * @var Curl
         */
        private $curl;

        /**
         * @return CLI
         */
        public function getCLI() {
            return new CLI($this->getCommandLocator());
        }

        /**
         * @return VersionCommand
         */
        public function getVersionCommand() {
            return new VersionCommand($this->getPhiveVersion());
        }

        /**
         * @return HelpCommand
         */
        public function getHelpCommand() {
            return new HelpCommand();
        }

        /**
         * @return PharDatabase
         */
        private function getPharDatabase() {
            return new PharDatabase(
                $this->getConfig()->getHomeDirectory() . '/phars.xml',
                $this->getConfig()->getHomeDirectory()->child('phars')
            );
        }

        /**
         * @param CLICommandOptions $options
         *
         * @return InstallCommand
         */
        public function getInstallCommand(CLICommandOptions $options) {
            return new InstallCommand(
                new InstallCommandConfig($options, $this->getConfig()),
                $this->getPharRepository(),
                $this->getPharService(),
                $this->getColoredConsoleLogger()
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
         * @return PharIoClient
         */
        private function getPharIoClient() {
            return new PharIoClient();
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
            return new PharInstaller($this->getConfig()->getHomeDirectory()->child('phars'));
        }

        /**
         * @return PharService
         */
        public function getPharService() {
            return new PharService($this->getPharDownloader(), $this->getPharInstaller());
        }

        /**
         * @return SignatureService
         */
        public function getSignatureService() {
            return new SignatureService($this->getGnupgSignatureVerifier());
        }

        /**
         * @return GnupgSignatureVerifier
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
                $this->getColoredConsoleLogger()
            );
        }

        /**
         * @return GnupgKeyImporter
         */
        private function getGnupgKeyImporter() {
            return new GnupgKeyImporter($this->getGnupg());
        }

        /**
         * @return GnupgKeyDownloader
         */
        private function getPgpKeyDownloader() {
            return new GnupgKeyDownloader(
                $this->getCurl(), include __DIR__ . '/../conf/pgp-keyservers.php', $this->getColoredConsoleLogger()
            );
        }

        /**
         * @return ColoredConsoleLogger
         */
        private function getColoredConsoleLogger() {
            return  new ColoredConsoleLogger(ConsoleLogger::VERBOSE_INFO);
        }

        /**
         * @return ConsoleLogger
         */
        private function getConsoleLogger() {
            return new ConsoleLogger(ConsoleLogger::VERBOSE_INFO);
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
            putenv('GNUPGHOME=' . $this->getConfig()->getHomeDirectory()->child('gpg'));
            $gpg = new \Gnupg();
            $gpg->seterrormode(\Gnupg::ERROR_EXCEPTION);
            return $gpg;
        }

        /**
         * @return PharRepository
         */
        private function getPharRepository() {
            return new PharRepository(
                $this->getPharDatabase(),
                $this->getPharService(),
                $this->getSignatureService(),
                $this->getKeyService(),
                $this->getColoredConsoleLogger()
            );
        }

    }

}
