<?php
namespace PharIo\Phive;

interface KeyDownloader {

    /**
     * @param $keyId
     *
     * @return PublicKey
     */
    public function download($keyId);

}
