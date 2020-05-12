<?php declare(strict_types = 1);
namespace PharIo\Phive;

class MigrationFactory {

    /** @var Factory */
    private $factory;
    /** @var Cli\Input */
    private $input;

    public function __construct(Factory $factory, Cli\Input $input) {
        $this->factory = $factory;
        $this->input   = $input;
    }

    /**
     * @return Migration[]
     */
    public function getMigrations(): array {
        return [
            new HomePharsXmlMigration($this->factory->getConfig()),
            new HomePhiveXmlMigration($this->factory->getConfig()),
            new ProjectPhiveXmlMigration($this->factory->getConfig(), $this->input),
        ];
    }
}
