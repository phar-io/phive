<?php
namespace PharIo\Phive {

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
         * @return Command
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

                case 'remove' : {
                    return $this->factory->getRemoveCommand($request->getCommandOptions());
                }

                case 'skel' : {
                    return $this->factory->getSkelCommand($request->getCommandOptions());
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
