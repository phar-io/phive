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

use function in_array;
use PharIo\Phive\Cli\GeneralContext;

class PhiveContext extends GeneralContext {
    public function requiresValue(string $option): bool {
        return in_array($option, ['home', 'config'], true);
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
            'config'      => false,
            'no-progress' => false
        ];
    }
}
