<?php
namespace PharIo\Phive;

use PharIo\FileSystem\File;

class ChecksumService {

    /**
     * @param Hash $expectedHash
     * @param File $file
     *
     * @return bool
     * @throws InvalidHashException
     */
    public function verify(Hash $expectedHash, File $file) {
        $hashClass = get_class($expectedHash);
        switch ($hashClass) {
            case Sha1Hash::class:
                $actual = Sha1Hash::forContent($file->getContent());
                break;
            case Sha256Hash::class:
                $actual = Sha256Hash::forContent($file->getContent());
                break;
            default:
                throw new InvalidHashException(sprintf('%s is not supported', $hashClass));
        }
        return $actual->equals($expectedHash);
    }

}
