<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class InstallContext extends GeneralContext {
    public function requiresValue(string $option): bool {
        return \in_array($option, ['target', 'trust-gpg-keys'], true);
    }

    protected function getKnownOptions(): array {
        return [
            'target'                => 't',
            'copy'                  => 'c',
            'global'                => 'g',
            'temporary'             => false,
            'trust-gpg-keys'        => false,
            'force-accept-unsigned' => false
        ];
    }

    protected function getConflictingOptions(): array {
        return [
            ['global' => 'temporary'],
            ['global' => 'target']
        ];
    }
}
