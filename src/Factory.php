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
            return new VersionCommand($this->getVersion());
        }

        /**
         * @return HelpCommand
         */
        public function getHelpCommand() {
            return new HelpCommand();
        }

        /**
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

        private function getVersion() {
            return new Version();
        }

        private function getInstallService() {
            return new InstallService(
                $this->getPharIoClient(),
                $this->getPharDownloader()
            );
        }

        private function getPharIoClient() {
            return new PharIoClient();
        }

        private function getPharDownloader() {
            return new PharDownloader();
        }

    }

}
