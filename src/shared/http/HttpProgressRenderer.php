<?php declare(strict_types = 1);
namespace PharIo\Phive;

class HttpProgressRenderer implements HttpProgressHandler {

    /** @var Cli\Output */
    private $output;

    /** @var Url */
    private $url;

    /** @var bool */
    private $first;

    /** @var string */
    private $prevProgress = '';

    public function __construct(Cli\Output $output) {
        $this->output = $output;
        $this->first  = true;
    }

    public function start(Url $url): void {
        $this->url   = $url;
        $this->first = true;
    }

    public function finished(): void {
        $this->output->writeProgress('');
    }

    public function handleUpdate(HttpProgressUpdate $update): bool {
        if ($update->getExpectedDownloadSize() === 0) {
            return true;
        }

        if ($this->first) {
            $this->output->writeInfo(\sprintf('Downloading %s', (string)$this->url));
            $this->first = false;
        }

        $progressString = $update->getDownloadPercent();

        if ((string)$progressString === $this->prevProgress) {
            return true;
        }
        $this->prevProgress = (string)$progressString;

        $template = ' ╰|%s| %s / %s - %3d%%';

        $this->output->writeProgress(
            \sprintf(
                $template,
                $this->getProgressBar($progressString),
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

    private function formatSize(int $expected, int $current): string {
        if ($expected >= 1048576) { // MB
            return \number_format($current / 1048576, 2) . ' MB';
        }

        if ($expected >= 1024) { // KB
            return \number_format($current / 1024, 2) . ' KB';
        }

        return $current . ' B';
    }

    private function getProgressBar(float $downloadPercent): string {
        $barCount  = \floor($downloadPercent / 2.5);
        $barString = \str_pad('', (int)$barCount, '=') . '>';

        return \str_pad($barString, 40, ' ', \STR_PAD_RIGHT);
    }
}
