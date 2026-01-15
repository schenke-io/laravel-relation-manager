<?php

namespace SchenkeIo\LaravelRelationManager\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelRelationManager\Enums\EnumHelper;

enum TestEnum
{
    use EnumHelper;
    case Alpha;
    case Beta;
}

class EnumHelperTest extends TestCase
{
    public function test_from_returns_case()
    {
        $this->assertEquals(TestEnum::Alpha, TestEnum::from('Alpha'));
        $this->assertEquals(TestEnum::Beta, TestEnum::from('Beta'));
    }

    public function test_from_throws_exception_on_invalid_name()
    {
        $this->expectException(\ValueError::class);
        TestEnum::from('Gamma');
    }

    public function test_try_from_returns_case_or_null()
    {
        $this->assertEquals(TestEnum::Alpha, TestEnum::tryFrom('Alpha'));
        $this->assertNull(TestEnum::tryFrom('Gamma'));
    }
}
