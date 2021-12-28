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

use const JSON_BIGINT_AS_STRING;
use const JSON_ERROR_NONE;
use function array_key_exists;
use function explode;
use function is_array;
use function json_decode;
use function json_last_error;
use function json_last_error_msg;
use function sprintf;
use InvalidArgumentException;

class JsonData {
    /** @var string */
    private $raw;

    /** @var array */
    private $parsed;

    /**
     * @param string $raw
     *
     * @throws InvalidArgumentException
     */
    public function __construct($raw) {
        $this->raw = $raw;
        $parsed    = json_decode($raw, true, 512, JSON_BIGINT_AS_STRING);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException(json_last_error_msg(), json_last_error());
        }

        if (!is_array($parsed)) {
            throw new InvalidArgumentException('Given JSON string does not parse into expected/supported structure');
        }
        $this->parsed = $parsed;
    }

    public function getRaw(): string {
        return $this->raw;
    }

    public function getParsed(): array {
        return $this->parsed;
    }

    public function hasFragment(string $fragmentSpecification): bool {
        try {
            $this->getFragment($fragmentSpecification);

            return true;
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    public function getFragment(string $fragmentSpecification) {
        $data = $this->parsed;

        foreach (explode('.', $fragmentSpecification) as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException(
                    sprintf('Fragment %s of %s not found', $key, $fragmentSpecification)
                );
            }
            $data = $data[$key];
        }

        return $data;
    }
}
