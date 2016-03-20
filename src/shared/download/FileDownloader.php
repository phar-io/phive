<?php
namespace PharIo\Phive;

class FileDownloader implements HttpProgressHandler {

    /**
     * @var HttpClient
     */
    private $curl;

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param HttpClient $curl
     * @param Cli\Output $output
     */
    public function __construct(HttpClient $curl, Cli\Output $output) {
        $this->curl = $curl;
        $this->output = $output;
    }

    /**
     * @param Url $url
     *
     * @return File
     * @throws DownloadFailedException
     */
    public function download(Url $url) {

        // force new line for progress update
        $this->output->writeInfo('');

        $response = $this->curl->get($url, [], $this);
        if ($response->getHttpCode() !== 200) {
            throw new DownloadFailedException(
                sprintf(
                    'Download failed (HTTP status code %s) %s',
                    $response->getHttpCode(),
                    $response->getErrorMessage()
                )
            );
        }
        if (empty($response->getBody())) {
            throw new DownloadFailedException('Download failed - response is empty');
        }
        return new File($this->getFilename($url), $response->getBody());
    }

    /**
     * @param Url $url
     *
     * @return Filename
     */
    private function getFilename(Url $url) {
        return new Filename(pathinfo($url, PATHINFO_BASENAME));
    }

    public function handleUpdate(HttpProgressUpdate $update) {
        $total = $update->getExpectedUploadSize();
        if ($total === 0) {
            return;
        }
        $template = sprintf(
            'Downloading %%s [ %%%dd / %%%dd - %%3d%%%% ]',
            strlen($total),
            strlen($total)
        );
        $progress = sprintf(
            $template,
            $update->getUrl(),
            $update->getBytesReceived(),
            $total,
            $update->getDownloadPercent()
        );
        $this->output->writeInfo(sprintf("\e[1A%s", $progress));
    }

}
