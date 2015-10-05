<?php
namespace PharIo\Phive {

    use TheSeer\CLI;

    class InstallCommandConfig {

        /**
         * @var CLI\CommandOptions
         */
        private $cliOptions;

        /**
         * @var Config
         */
        private $config;

        /**
         * InstallCommandConfig constructor.
         *
         * @param CLI\CommandOptions $options
         * @param Config            $config
         */
        public function __construct(CLI\CommandOptions $options, Config $config) {
            $this->cliOptions = $options;
            $this->config = $config;
        }

        /**
         * @return Directory
         */
        public function getWorkingDirectory() {
            return $this->config->getWorkingDirectory();
        }

        /**
         * @return Url
         * @throws CLI\CommandOptionsException
         */
        public function getPharUrl() {
            return new Url($this->cliOptions->getArgument(0));
        }

        /**
         * @return bool
         * @throws CLI\CommandOptionsException
         */
        public function hasPharUrl() {
            return strpos($this->cliOptions->getArgument(0), 'https://') !== false;
        }

        /**
         * @return PharAlias
         * @throws CLI\CommandOptionsException
         */
        public function getPharAlias() {
            $alias = $this->cliOptions->getArgument(0);
            $aliasSegments = explode('@', $alias, 2);
            if (count($aliasSegments) === 2) {
                $versionConstraint = new ExactVersionConstraint($aliasSegments[1]);
            } else {
                $versionConstraint = new AnyVersionConstraint();
            }
            return new PharAlias($aliasSegments[0], $versionConstraint);
        }

        /**
         * @return bool
         */
        public function makeCopy() {
            return $this->cliOptions->isSwitch('copy');
        }

    }

}
