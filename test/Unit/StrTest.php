<?php declare(strict_types=1);
namespace Frrame\Test\Unit;
use PHPUnit\Framework\TestCase;
use Frrame\Util\Str;
/**
 * Hand-written coverage for a single class in isolation. See test/Agent and
 * test/User for the other two conventions this suite is split by.
 */
class StrTest extends TestCase{
    public function testLength():void{
        $this->assertSame(5, Str::length('hello'));
    }
    public function testLower():void{
        $this->assertSame('hello', Str::lower('HELLO'));
    }
    public function testUpper():void{
        $this->assertSame('HELLO', Str::upper('hello'));
    }
}
