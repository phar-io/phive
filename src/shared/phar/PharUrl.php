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

use const PATHINFO_FILENAME;
use function count;
use function pathinfo;
use function preg_match;
use function sprintf;
use PharIo\Version\Version;

class PharUrl extends Url implements PharIdentifier {
    public function getPharName(): string {
        $filename = pathinfo($this->asString(), PATHINFO_FILENAME);
        preg_match('/(.*)-[\d]+.[\d]+.[\d]+.*/', $filename, $matches);

        if (count($matches) !== 2) {
            $matches[1] = $filename;
        }

        return $matches[1];
    }

    /**
     * @throws UnsupportedVersionConstraintException
     */
    public function getPharVersion(): Version {
        $filename = pathinfo($this->asString(), PATHINFO_FILENAME);
        preg_match('/-[vVrR]?([\d]+.[\d]+.[\d]+.*)/', $filename, $matches);

        if (count($matches) !== 2) {
            preg_match('/\/[vVrR]?([\d]+.[\d]+.[\d]+.*)\//', $this->asString(), $matches);
        }

        if (count($matches) !== 2) {
            throw new UnsupportedVersionConstraintException(sprintf('Could not extract PHAR version from %s', $this->asString()));
        }

        return new Version($matches[1]);
    }
}
