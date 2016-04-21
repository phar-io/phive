<?php
namespace PharIo\Phive;

interface HttpProgressHandler {

    /**
     * Method is called with updates from transfers (upload/download)
     * Return false to signal the http client to abort the transfer, true to continue
     *
     * @param HttpProgressUpdate $update
     *
     * @return bool
     */
    public function handleUpdate(HttpProgressUpdate $update);

}
