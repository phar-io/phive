<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrationFactory {

    /** @var Factory */
    private $factory;
    /** @var Environment */
    private $environment;

    public function __construct(Factory $factory, Environment $environment) {
        $this->factory     = $factory;
        $this->environment = $environment;
    }

    /**
     * @return Migration[]
     */
    public function getMigrations(): array {
        return [
            new HomePharsXmlMigration($this->factory->getConfig()),
            new HomePhiveXmlMigration($this->factory->getConfig()),
            new ProjectPhiveXmlMigration($this->environment),
            new ProjectAuthXmlMigration($this->environment)
        ];
    }
}
