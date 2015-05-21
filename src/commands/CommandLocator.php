<?php
namespace TheSeer\Phive {

    class CommandLocator {

        /**
         * @var Factory
         */
        private $factory;

        /**
         * @param Factory $factory
         */
        public function __construct(Factory $factory) {
            $this->factory = $factory;
        }

        /**
         * @param CLIRequest $request
         *
         * @throws CommandLocatorException
         * @return CommandInterface
         */
        public function getCommandForRequest(CLIRequest $request) {
            $command = $request->getCommand();
            switch ($command) {
                case 'help' : {
                    return $this->factory->getHelpCommand();
                }
                case 'version' : {
                    return $this->factory->getVersionCommand();
                }

                case 'install' : {
                    return $this->factory->getInstallCommand($request->getCommandOptions());
                }

                default: {
                    throw new CommandLocatorException(
                        sprintf('Command "%s" is not a valid command', $command),
                        CommandLocatorException::UnknownCommand
                    );
                }
            }
        }

    }

}
