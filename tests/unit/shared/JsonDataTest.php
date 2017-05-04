<?php
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\JsonData
 */
class JsonDataTest extends TestCase {

    public function testThrowsExceptionIfRawValueIsNotJson() {
        $this->expectException(\InvalidArgumentException::class);
        new JsonData('foo');
    }

    public function testThrowsExceptionIfRawValueIsNotAJsonObjectOrArray() {
        $this->expectException(\InvalidArgumentException::class);
        new JsonData(json_encode('foo'));
    }

    public function testGetRawReturnsExpectedValue() {
        $raw = json_encode(['foo' => 'bar']);

        $data = new JsonData($raw);
        $this->assertSame($raw, $data->getRaw());
    }

    public function testGetParsedReturnsExpectedValue() {
        $raw = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $expected = new \stdClass();
        $expected->foo = 'bar';

        $this->assertEquals($expected, $data->getParsed());
    }

    public function testHasFragmentReturnsFalse() {
        $raw = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertFalse($data->hasFragment('foobar'));
    }

    public function testHasFragmentReturnsTrue() {
        $raw = json_encode(['foo' => 'bar']);
        $data = new JsonData($raw);

        $this->assertTrue($data->hasFragment('foo'));
    }

    public function testGetFragmentThrowsExceptionIfFragmentDoesNotExist() {
        $raw = json_encode(
            [
                'parent' => [
                    'child' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        );

        $data = new JsonData($raw);
        $this->expectException(\InvalidArgumentException::class);

        $data->getFragment('parent.child.foobar');
    }

    public function testGetFragmentReturnsExpectedValue() {
        $raw = json_encode(
            [
                'parent' => [
                    'child' => [
                        'foo' => 'bar'
                    ]
                ]
            ]
        );

        $data = new JsonData($raw);

        $this->assertSame('bar', $data->getFragment('parent.child.foo'));

        $expectedObject = new \stdClass();
        $expectedObject->foo = 'bar';
        $this->assertEquals($expectedObject, $data->getFragment('parent.child'));
    }

}
