<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class SkelContext extends GeneralContext {
    protected function getKnownOptions(): array {
        return [
            'force' => 'f',
            'auth'  => 'a'
        ];
    }
}
