<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class StatusContext extends GeneralContext {
    protected function getKnownOptions(): array {
        return [
            'all' => 'a',
            'global' => 'g'
        ];
    }

    protected function getConflictingOptions(): array {
        return [
            ['all' => 'global']
        ];
    }

}
