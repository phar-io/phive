<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli;

class CommandLocator implements Cli\CommandLocator {

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
     * @param Cli\Request $request
     *
     * @throws Cli\CommandLocatorException
     * @return Cli\Command
     */
    public function getCommandForRequest(Cli\Request $request) {
        $command = $request->getCommand();
        switch ($command) {

            case '':
            case 'help': {
                return $this->factory->getHelpCommand();
            }

            case 'version': {
                return $this->factory->getVersionCommand();
            }

            case 'install': {
                return $this->factory->getInstallCommand($request->getCommandOptions());
            }

            case 'list': {
                return $this->factory->getListCommand();
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

            case 'update': {
                return $this->factory->getUpdateCommand($request->getCommandOptions());
            }

            case 'update-repository-list': {
                return $this->factory->getUpdateRepositoryListCommand();
            }

            case 'reset': {
                return $this->factory->getResetCommand($request->getCommandOptions());
            }

            case 'composer': {
                return $this->factory->getComposerCommand($request->getCommandOptions());
            }
            
            default: {
                throw new Cli\CommandLocatorException(
                    sprintf('Command "%s" is not a valid command', $command),
                    Cli\CommandLocatorException::UnknownCommand
                );
            }
        }
    }

}
