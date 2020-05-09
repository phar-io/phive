<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Phive\Cli\GeneralContext;

class PhiveContext extends GeneralContext {
    public function requiresValue(string $option): bool {
        return $option === 'home';
    }

    public function acceptsArguments(): bool {
        return $this->getOptions()->getArgumentCount() === 0;
    }

    public function canContinue(): bool {
        return $this->acceptsArguments();
    }

    protected function getKnownOptions(): array {
        return [
            'version'     => false,
            'help'        => false,
            'home'        => false,
            'no-progress' => false
        ];
    }
}
