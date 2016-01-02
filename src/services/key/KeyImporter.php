<?php
namespace PharIo\Phive;

interface KeyImporter {

    /**
     * @param string $key
     *
     * @return KeyImportResult
     */
    public function importKey($key);

}
