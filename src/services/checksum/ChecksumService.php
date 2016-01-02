<?php
namespace PharIo\Phive;

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
                $actual = $file->getSha1Hash();
                break;
            case Sha256Hash::class:
                $actual = $file->getSha256Hash();
                break;
            default:
                throw new InvalidHashException(sprintf('%s is not supported', $hashClass));
        }
        return $actual->equals($expectedHash);
    }

}



