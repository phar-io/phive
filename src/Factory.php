<?php
namespace TheSeer\Phive {

    class Factory {

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
         * @param CLICommandOptions $options
         *
         * @return InstallCommand
         */
        public function getInstallCommand(CLICommandOptions $options) {
            return new InstallCommand(
                $this->getInstallService(),
                new InstallCommandConfig($options)
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
         * @return InstallService
         */
        private function getInstallService() {
            return new InstallService(
                $this->getPharService(),
                $this->getKeyService(),
                $this->getSignatureService(),
                $this->getColoredConsoleLogger()
            );
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
            return new PharDownloader($this->getCurl());
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
            return new GnupgSignatureVerifier($this->getGnupg());
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
            $proxy = '';
            $environment = $this->getEnvironment();
            if ($environment->hasProxy()) {
                $proxy = $environment->getProxy();
            }
            return new Curl($proxy);
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
            $gpg->seterrormode(\gnupg::ERROR_EXCEPTION);
            return $gpg;
        }

    }

}
