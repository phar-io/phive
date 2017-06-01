<?php
namespace PharIo\Phive;

use PharIo\FileSystem\Filename;

class SymlinkPharActivator implements PharActivator {

    /**
     * @param Filename $pharLocation
     * @param Filename $linkDestination
     *
     * @return Filename
     */
    public function activate(Filename $pharLocation, Filename $linkDestination) {
        $this->ensureDestinationIsWritable($linkDestination);

        symlink($pharLocation->asString(), $linkDestination->asString());
        return $linkDestination;
    }

    /**
     * @param Filename $destination
     *
     * @throws FileNotWritableException
     */
    private function ensureDestinationIsWritable(Filename $destination) {
        if (!$destination->getDirectory()->isWritable()) {
            throw new FileNotWritableException(sprintf('File %s is not writable.', $destination->asString()));
        }
    }

}
