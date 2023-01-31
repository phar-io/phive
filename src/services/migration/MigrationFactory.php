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
