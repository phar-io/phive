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
                return $this->factory->getInstallCommand();
            }

            case 'list': {
                return $this->factory->getListCommand();
            }

            case 'purge': {
                return $this->factory->getPurgeCommand();
            }

            case 'remove': {
                return $this->factory->getRemoveCommand();
            }

            case 'skel': {
                return $this->factory->getSkelCommand();
            }

            case 'update': {
                return $this->factory->getUpdateCommand();
            }

            case 'update-repository-list': {
                return $this->factory->getUpdateRepositoryListCommand();
            }

            case 'reset': {
                return $this->factory->getResetCommand();
            }

            case 'composer': {
                return $this->factory->getComposerCommand();
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
