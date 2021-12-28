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

use function get_class;
use function sprintf;
use PharIo\FileSystem\File;

class ChecksumService {
    /**
     * @throws InvalidHashException
     */
    public function verify(Hash $expectedHash, File $file): bool {
        $hashClass = get_class($expectedHash);

        switch ($hashClass) {
            case Sha1Hash::class:
                $actual = Sha1Hash::forContent($file->getContent());

                break;
            case Sha256Hash::class:
                $actual = Sha256Hash::forContent($file->getContent());

                break;
            case Sha384Hash::class:
                $actual = Sha384Hash::forContent($file->getContent());

                break;
            case Sha512Hash::class:
                $actual = Sha512Hash::forContent($file->getContent());

                break;

            default:
                throw new InvalidHashException(sprintf('%s is not supported', $hashClass));
        }

        return $actual->equals($expectedHash);
    }
}
