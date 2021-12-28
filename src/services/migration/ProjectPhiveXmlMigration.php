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

use PharIo\FileSystem\Filename;

class ProjectPhiveXmlMigration extends UserFileMigration {
    public function __construct(Config $config, Cli\Input $input) {
        parent::__construct(
            $input,
            $config->getWorkingDirectory()->file('phive.xml'),
            $config->getProjectInstallation()
        );
    }

    public function mustMigrate(): bool {
        return false;
    }

    public function getDescription(): string {
        return 'Move the \'phive.xml\' inside the new \'.phive/\' configuration directory.';
    }

    protected function doMigrate(Filename $legacy, Filename $new): void {
        $new->getDirectory()->ensureExists();
        $new->putContent($legacy->read()->getContent());
    }

    protected function getFileDescription(): string {
        return 'project Phive configuration';
    }
}
