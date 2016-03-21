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
        if ($update->getExpectedDownloadSize() === 0) {
            return true;
        }
        $template = 'Downloading %s [ %s / %s - %3d%% ]';
        $progress = sprintf(
            $template,
            $update->getUrl(),
            $this->formatSize(
                $update->getExpectedDownloadSize(),
                $update->getBytesReceived()
            ),
            $this->formatSize(
                $update->getExpectedDownloadSize(),
                $update->getExpectedDownloadSize()
            ),
            $update->getDownloadPercent()
        );
        $this->output->writeInfo(sprintf("\e[1A%s", $progress));
        return true;
    }

    private function formatSize($expected, $current) {
        if ($expected >= 1048576) { // MB
            return number_format($current / 1048576, 2) . ' MB';
        }
        if ($expected >= 1024) { // KB
            return number_format($current / 1024, 2) . ' KB';
        }
        return $current . ' B';
    }

}
