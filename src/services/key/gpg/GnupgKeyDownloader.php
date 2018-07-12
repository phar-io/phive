<?php
namespace PharIo\Phive;

class GnupgKeyDownloader implements KeyDownloader {

    const PATH = '/pks/lookup';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string[]
     */
    private $keyServers = [];

    /**
     * @var Cli\Output
     */
    private $output;

    /**
     * @param HttpClient $httpClient
     * @param string[]   $keyServers
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
     * @return PublicKey
     * @throws DownloadFailedException
     */
    public function download($keyId) {
        $publicParams = [
            'search'  => '0x' . $keyId,
            'op'      => 'get',
            'options' => 'mr'
        ];
        $infoParams = array_merge($publicParams, [
            'op' => 'index'
        ]);
        foreach ($this->keyServers as $keyServerName) {

            $ipList = $this->resolveHostname($keyServerName);
            foreach($ipList as $ipAddress) {
                $this->output->writeInfo(sprintf('Trying %s (%s)', $keyServerName, $ipAddress));
                try {
                    $keyInfo = $this->httpClient->get((new Url('https://' . $keyServerName . self::PATH))->withParams($infoParams));
                    $publicKey = $this->httpClient->get((new Url('https://' . $keyServerName . self::PATH))->withParams($publicParams));

                    $this->ensureSuccess($keyInfo);
                    $this->ensureSuccess($publicKey);

                } catch (HttpException $e) {
                    $this->output->writeWarning(
                        sprintf('Failed with error code %s: %s', $e->getCode(), $e->getMessage())
                    );
                    continue;
                }

                $this->output->writeInfo('Successfully downloaded key');

                try {
                    return new PublicKey($keyId, $keyInfo->getBody(), $publicKey->getBody());
                } catch (PublicKeyException $e) {
                    throw new DownloadFailedException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }
        throw new DownloadFailedException(sprintf('PublicKey %s not found on key servers', $keyId));
    }

    /**
     * @param $hostname
     *
     * @return array
     */
    private function resolveHostname($hostname) {
        $ipList = array_merge(
            $this->queryDNS($hostname, DNS_A),
            $this->queryDNS($hostname, DNS_AAAA)
        );

        if (!count($ipList)) {
            throw new GnupgKeyDownloaderException(
                sprintf('DNS Problem: Did not find any IP for hostname "%s"', $hostname)
            );
        }

        return $ipList;
    }

    private function queryDNS($hostname, $type) {
        $ipList = [];
        try {
            $results = dns_get_record($hostname, $type);
            foreach($results as $result) {
                $ipList[] = $result[ $type === DNS_A ? 'ip' : 'ipv6' ];
            }
        } catch (\Exception $e) {
        }
        return $ipList;
    }

    /**
     * @throws HttpException
     */
    private function ensureSuccess(HttpResponse $response) {
        if ($response->isNotFound()) {
            throw new HttpException('Key not found on keyserver', $response->getHttpCode());
        }
        if (!$response->isSuccess()) {
            throw new HttpException('Server reported an error', $response->getHttpCode());
        }
    }

}
