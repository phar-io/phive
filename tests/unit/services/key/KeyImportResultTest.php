<?php declare(strict_types = 1);
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
    public function testGetCount($count): void {
        $result = new KeyImportResult($count, '');
        $this->assertSame($count, $result->getCount());
    }

    /**
     * @dataProvider stringProvider
     *
     * @param string $fingerprint
     */
    public function testGetFingerprint($fingerprint): void {
        $result = new KeyImportResult(1, $fingerprint);
        $this->assertSame($fingerprint, $result->getFingerprint());
    }
}
