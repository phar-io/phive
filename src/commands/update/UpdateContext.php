<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class UpdateContext extends GeneralContext {
    protected function getKnownOptions(): array {
        return [
            'force-accept-unsigned' => false,
            'prefer-offline'        => false,
            'global'                => 'g'
        ];
    }
}
