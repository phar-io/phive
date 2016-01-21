<?php
namespace PharIo\Phive;

class GnupgKeyDownloader implements KeyDownloader {

    const PATH = '/pks/lookup';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Url[]
     */
    private $keyServers = [];

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param HttpClient $httpClient
     * @param Url[]      $keyServers
     * @param Cli\Output $output
     */
    public function __construct(HttpClient $httpClient, array $keyServers, Cli\Output $output) {
        $this->httpClient = $httpClient;
        $this->keyServers = $keyServers;
        $this->output = $output;
    }

    /**
     * @param string $keyId
     *
     * @return string
     * @throws DownloadFailedException
     */
    public function download($keyId) {
        $params = [
            'search'  => '0x' . $keyId,
            'op'      => 'get',
            'options' => 'mr'
        ];
        foreach ($this->keyServers as $keyServer) {
            $this->output->writeInfo(sprintf('Trying %s', $keyServer));
            $result = $this->httpClient->get(new Url($keyServer . self::PATH), $params);
            if ($result->getHttpCode() == 200) {
                $this->output->writeInfo('Sucessfully downloaded key');
                return $result->getBody();
            }
            $this->output->writeWarning(
                sprintf('Failed with status code %s: %s', $result->getHttpCode(), $result->getErrorMessage())
            );
        }
        throw new DownloadFailedException(sprintf('Key %s not found on key servers', $keyId));
    }

}
