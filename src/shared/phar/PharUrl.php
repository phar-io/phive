<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\Version;

class PharUrl extends Url implements PharIdentifier {
    public function getPharName(): string {
        $filename = \pathinfo($this->asString(), \PATHINFO_FILENAME);
        \preg_match('/(.*)-[\d]+.[\d]+.[\d]+.*/', $filename, $matches);

        if (\count($matches) !== 2) {
            $matches[1] = $filename;
        }

        return $matches[1];
    }

    /**
     * @throws UnsupportedVersionConstraintException
     */
    public function getPharVersion(): Version {
        $filename = \pathinfo($this->asString(), \PATHINFO_FILENAME);
        \preg_match('/-([\d]+.[\d]+.[\d]+.*)/', $filename, $matches);

        if (\count($matches) !== 2) {
            \preg_match('/\/([\d]+.[\d]+.[\d]+.*)\//', $this->asString(), $matches);
        }

        if (\count($matches) !== 2) {
            throw new UnsupportedVersionConstraintException(\sprintf('Could not extract PHAR version from %s', $this->asString()));
        }

        return new Version($matches[1]);
    }
}
