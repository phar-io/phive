<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive;

use function sprintf;

class CommandLocator implements Cli\CommandLocator {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    /**
     * @throws Cli\CommandLocatorException
     */
    public function getCommand(string $command): Cli\Command {
        switch ($command) {
            case '': {
                return $this->factory->getDefaultCommand();
            }

            case 'help': {
                return $this->factory->getHelpCommand();
            }

            case 'version': {
                return $this->factory->getVersionCommand();
            }

            case 'install': {
                return $this->factory->getInstallCommand();
            }

            case 'outdated': {
                return $this->factory->getOutdatedCommand();
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

            case 'status': {
                return $this->factory->getStatusCommand();
            }

            case 'selfupdate':
            case 'self-update': {
                return $this->factory->getSelfupdateCommand();
            }

            case 'migrate':
                return $this->factory->getMigrateCommand();

            default: {
                throw new Cli\CommandLocatorException(
                    sprintf('Command "%s" is not a valid command', $command),
                    Cli\CommandLocatorException::UnknownCommand
                );
            }
        }
    }
}
