<?php
namespace PharIo\Phive;

use TheSeer\CLI;

class CommandLocator implements CLI\CommandLocator {

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
     * @param CLI\Request $request
     *
     * @throws CLI\CommandLocatorException
     * @return CLI\Command
     */
    public function getCommandForRequest(CLI\Request $request) {
        $command = $request->getCommand();
        switch ($command) {
            case 'help': {
                return $this->factory->getHelpCommand();
            }

            case 'version': {
                return $this->factory->getVersionCommand();
            }

            case 'install': {
                return $this->factory->getInstallCommand($request->getCommandOptions());
            }

            case 'purge': {
                return $this->factory->getPurgeCommand($request->getCommandOptions());
            }

            case 'remove': {
                return $this->factory->getRemoveCommand($request->getCommandOptions());
            }

            case 'skel': {
                return $this->factory->getSkelCommand($request->getCommandOptions());
            }

            case 'update-repository-list': {
                return $this->factory->getUpdateRepositoryListCommand();
            }

            default: {
                throw new CLI\CommandLocatorException(
                    sprintf('Command "%s" is not a valid command', $command),
                    CLI\CommandLocatorException::UnknownCommand
                );
            }
        }
    }

}


