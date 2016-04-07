<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\HttpProgressUpdate
 */
class HttpProgressUpdateTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    public function testGetUrl() {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, 10, 1, 0, 0);
        $this->assertSame($url, $update->getUrl());
    }

    /**
     * @dataProvider intProvider
     *
     * @param int $value
     */
    public function testGetExpectedDownloadSize($value) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, $value, 1, 0, 0);
        $this->assertSame($value, $update->getExpectedDownloadSize());
    }

    /**
     * @dataProvider intProvider
     *
     * @param int $value
     */
    public function testGetBytesReceived($value) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, 10, $value, 0, 0);
        $this->assertSame($value, $update->getBytesReceived());
    }

    /**
     * @dataProvider intProvider
     *
     * @param int $value
     */
    public function testGetExpectedUploadSize($value) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, 0, 0, $value, 0);
        $this->assertSame($value, $update->getExpectedUploadSize());
    }

    /**
     * @dataProvider intProvider
     *
     * @param int $value
     */
    public function testGetBytesSent($value) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, 0, 0, 10, $value);
        $this->assertSame($value, $update->getBytesSent());
    }

    /**
     * @dataProvider percentProvider
     *
     * @param $total
     * @param $received
     * @param $expectedPercent
     */
    public function testGetDownloadPercent($total, $received, $expectedPercent) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, $total, $received, 0, 0);

        $this->assertSame($expectedPercent, $update->getDownloadPercent());
    }

    /**
     * @dataProvider percentProvider
     *
     * @param $total
     * @param $sent
     * @param $expectedPercent
     */
    public function testGetUploadPercent($total, $sent, $expectedPercent) {
        $url = new Url('https://example.com');
        $update = new HttpProgressUpdate($url, 0, 0, $total, $sent);

        $this->assertSame($expectedPercent, $update->getUploadPercent());
    }

    public static function percentProvider() {
        return [
            [0, 0, 0],
            [1000, 100, 10.0]
        ];
    }
}
