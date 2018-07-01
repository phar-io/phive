<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\KeyImportResult
 */
class KeyImportResultTest extends TestCase {

    use ScalarTestDataProvider;

    /**
     * @dataProvider intProvider
     *
     * @param int $count
     */
    public function testGetCount($count) {
        $result = new KeyImportResult($count, null);
        $this->assertSame($count, $result->getCount());
    }

    /**
     * @dataProvider stringProvider
     *
     * @param string $fingerprint
     */
    public function testGetFingerprint($fingerprint) {
        $result = new KeyImportResult(1, $fingerprint);
        $this->assertSame($fingerprint, $result->getFingerprint());
    }

}
