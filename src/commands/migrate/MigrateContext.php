<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class MigrateContext extends GeneralContext {
    protected function getKnownOptions(): array {
        return [
            'status' => 's'
        ];
    }
}
