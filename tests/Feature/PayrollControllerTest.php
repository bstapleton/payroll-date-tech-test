<?php

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PayrollControllerTest extends TestCase
{
    const YEAR_LOWER_LIMIT = 2000;

    #[Test]
    public function invalid_year_format()
    {
        $response = $this->postJson('/api/', [
            'year' => 'foo',
            'month' => 1,
        ]);

        $response->assertStatus(422);

        $data = json_decode($response->getContent(), true);
        $this->assertSame('The year field must be an integer.', $data['errors']['year'][0]);
    }

    #[Test]
    public function invalid_year_range()
    {
        $response = $this->postJson('/api/', [
            'year' => self::YEAR_LOWER_LIMIT - 1,
            'month' => 1,
        ])->assertStatus(422);

        $data = json_decode($response->getContent(), true);
        $yearErrorArray = $data['errors']['year'];
        $this->assertTrue(in_array('The year field must be at least ' . self::YEAR_LOWER_LIMIT . '.', $yearErrorArray));
    }

    #[Test]
    public function invalid_month_format()
    {
        $response = $this->postJson('/api/', [
            'year' => 2012,
            'month' => 'bar',
        ])->assertStatus(422);

        $data = json_decode($response->getContent(), true);
        $this->assertSame('The month field must be an integer.', $data['errors']['month'][0]);
    }

    #[Test]
    public function invalid_month_maximum()
    {
        $response = $this->postJson('/api/', [
            'year' => 2012,
            'month' => 13,
        ])->assertStatus(422);

        $data = json_decode($response->getContent(), true);
        $yearErrorArray = $data['errors']['month'];
        $this->assertTrue(in_array('The month field must not be greater than 12.', $yearErrorArray));
    }

    #[Test]
    public function invalid_month_minimum()
    {
        $response = $this->postJson('/api/', [
            'year' => 2012,
            'month' => -1,
        ])->assertStatus(422);

        $data = json_decode($response->getContent(), true);
        $yearErrorArray = $data['errors']['month'];
        $this->assertTrue(in_array('The month field must be at least 1.', $yearErrorArray));
    }

    #[Test]
    public function validated_request()
    {
        $this->postJson('/api/', [
            'year' => 2025,
            'month' => 10,
        ])->assertStatus(200);
    }
}
