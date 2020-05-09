<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\Version\AnyVersionConstraint;
use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\PharAlias
 */
class PharAliasTest extends TestCase {
    use ScalarTestDataProvider;

    /**
     * @dataProvider stringProvider
     *
     * @param string $value
     */
    public function testValueHandling($value): void {
        $alias = new PharAlias($value, new AnyVersionConstraint(), new AnyVersionConstraint());
        $this->assertSame($value, $alias->asString());
    }
}
