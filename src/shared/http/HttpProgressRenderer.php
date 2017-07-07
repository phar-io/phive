<?php
namespace PharIo\Phive;

class HttpProgressRenderer implements HttpProgressHandler {

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param Cli\Output $output
     */
    public function __construct(Cli\Output $output) {
        $this->output = $output;
    }

    /**
     * @param Url $url
     */
    public function start(Url $url) {
        $this->output->writeInfo(sprintf('Downloading %s', $url));
    }

    public function finished() {
        $this->output->writeProgress('');
    }

    /**
     * @param HttpProgressUpdate $update
     *
     * @return bool
     */
    public function handleUpdate(HttpProgressUpdate $update) {
        if ($update->getExpectedDownloadSize() === 0) {
            return true;
        }

        $template = ' ↳ |%s| %s / %s - %3d%%';

        $this->output->writeProgress(
            sprintf(
                $template,
                $this->getProgressBar($update->getDownloadPercent()),
                $this->formatSize(
                    $update->getExpectedDownloadSize(),
                    $update->getBytesReceived()
                ),
                $this->formatSize(
                    $update->getExpectedDownloadSize(),
                    $update->getExpectedDownloadSize()
                ),
                $update->getDownloadPercent()
            )
        );

        return true;
    }

    /**
     * @param int $expected
     * @param int $current
     *
     * @return string
     */
    private function formatSize($expected, $current) {
        if ($expected >= 1048576) { // MB
            return number_format($current / 1048576, 2) . ' MB';
        }
        if ($expected >= 1024) { // KB
            return number_format($current / 1024, 2) . ' KB';
        }

        return $current . ' B';
    }

    /**
     * @param float $downloadPercent
     *
     * @return string
     */
    private function getProgressBar($downloadPercent) {
        $barCount = floor($downloadPercent / 2.5);
        $barString = str_pad('', $barCount, '=') . '>';

        return str_pad($barString, 40, ' ', STR_PAD_RIGHT);
    }

}
