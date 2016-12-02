<?php
namespace PharIo\Phive;

use PharIo\Version\Version;

class PharUrl extends Url implements PharIdentifier {

    /**
     * @return string
     */
    public function getPharName() {
        $filename = pathinfo((string)$this, PATHINFO_FILENAME);
        preg_match('/(.*)-[\d]+.[\d]+.[\d]+.*/', $filename, $matches);
        if (count($matches) !== 2) {
            $matches[1] = $filename;
        }

        return $matches[1];
    }

    /**
     * @return Version
     * @throws UnsupportedVersionConstraintException
     */
    public function getPharVersion() {
        $filename = pathinfo((string)$this, PATHINFO_FILENAME);
        preg_match('/-([\d]+.[\d]+.[\d]+.*)/', $filename, $matches);
        if (count($matches) !== 2) {
            preg_match('/\/([\d]+.[\d]+.[\d]+.*)\//', (string)$this, $matches);
        }
        if (count($matches) !== 2) {
            throw new UnsupportedVersionConstraintException(sprintf('Could not extract PHAR version from %s', $this));
        }

        return new Version($matches[1]);
    }

    /**
     * @return string
     */
    public function asString() {
        return (string)$this;
    }

}
