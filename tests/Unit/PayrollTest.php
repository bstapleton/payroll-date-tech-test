<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    #[Test]
    public function that_leap_years_are_identified_correctly()
    {
        // Probably all replaceable with Carbon, but it's just a stub for now
        $this->assertTrue(2012 % 4 === 0);
        $this->assertFalse(1999 % 4 === 0);

        $this->assertTrue(2024 % 4 === 0);
        $this->assertTrue(2028 % 4 === 0);
        $this->assertFalse(2025 % 4 === 0);
        $this->assertFalse(2026 % 4 === 0);
        $this->assertFalse(2027 % 4 === 0);
    }
}
