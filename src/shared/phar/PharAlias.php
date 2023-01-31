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

class PharAlias implements PharIdentifier {
    /** @var string */
    private $alias;

    /**
     * @param string $alias
     */
    public function __construct($alias) {
        $this->alias = $alias;
    }

    public function asString(): string {
        return $this->alias;
    }
}
