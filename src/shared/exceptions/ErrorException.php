<?php declare(strict_types = 1);
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

use Throwable;

class ErrorException extends \ErrorException implements Exception {
    /** @var array */
    private $context;

    public function __construct(string $message, int $code, int $severity, string $filename, int $lineno, array $context, Throwable $previous = null) {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
        $this->context = $context;
    }

    public function getContext(): array {
        return $this->context;
    }
}
