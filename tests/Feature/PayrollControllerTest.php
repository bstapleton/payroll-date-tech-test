<?php

namespace Feature;

use Citco\Carbon;
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

    /**
     * Given the last day f the month is a Sunday
     * And the preceding Friday is not a bank holiday
     * When requesting the payday
     * Then the payday should be set to the previous Friday in the requested month/year combination
     *
     * @return void
     */
    #[Test]
    public function payday_is_sunday()
    {
        $response = $this->postJson('/api/', [
            'year' => 2025,
            'month' => 3,
        ]);

        $response->assertJsonFragment([
            'payday' => '2025-03-28',
            'transfer_date' => '2025-03-24',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);

        // It's a Friday
        $this->assertTrue($payday->isFriday());

        // It's not a bank holiday
        $this->assertFalse($payday->isBankHoliday());

        // The transfer date is 4 days prior
        $this->assertTrue(Carbon::parse($response->json()['data']['transfer_date'])->format('Y-m-d') === $payday->subDays(4)->format('Y-m-d'));
    }

    /**
     * Given the day-before-last of the month is a normal non-bank-holiday workday
     * When requesting the payday
     * Then the payday should be set to the day-before-last of the provided month/year combination
     *
     * @return void
     */
    #[Test]
    public function payday_is_normal_weekday()
    {
        $response = $this->postJson('/api/', [
            'year' => 2025,
            'month' => 4,
        ]);

        $response->assertJsonFragment([
            'payday' => '2025-04-29',
            'transfer_date' => '2025-04-25',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);

        $this->assertTrue($payday->isTuesday());
        $this->assertFalse($payday->isBankHoliday());
        $this->assertTrue(Carbon::parse($response->json()['data']['transfer_date'])->format('Y-m-d') === $payday->subDays(4)->format('Y-m-d'));
    }

    /**
     * Given the last day of the month is Easter Sunday
     * When requesting the payday
     * Then the payday should be set to the Thursday before the last day of the provided month/year combination
     *
     * @return void
     */
    #[Test]
    public function payday_is_during_easter()
    {
        $response = $this->postJson('/api/', [
            'year' => 2024,
            'month' => 3,
        ]);

        $response->assertJsonFragment([
            'payday' => '2024-03-28',
            'transfer_date' => '2024-03-24',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);

        $this->assertTrue($payday->isThursday());
        $this->assertFalse($payday->isBankHoliday());
        $this->assertTrue(Carbon::parse($response->json()['data']['transfer_date'])->format('Y-m-d') === $payday->subDays(4)->format('Y-m-d'));
    }

    /**
     * Given the last day of the month falls on the August Bank Holiday
     * When requesting the payday
     * Then the payday should be set to the Friday before the last day of the provided month/year combination
     *
     * @return void
     */
    #[Test]
    public function august_bank_holiday()
    {
        $response = $this->postJson('/api/', [
            'year' => 2020,
            'month' => 8,
        ]);

        $response->assertJsonFragment([
            'payday' => '2020-08-28',
            'transfer_date' => '2020-08-24',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);

        $this->assertTrue($payday->isFriday());
        $this->assertFalse($payday->isBankHoliday());
        $this->assertTrue(Carbon::parse($response->json()['data']['transfer_date'])->format('Y-m-d') === $payday->subDays(4)->format('Y-m-d'));
    }

    /**
     * Given a consumer has set the flag to handle backdating of the transfer date
     * When requesting a payday where the original transfer dat would land on either a weekend or Bank Holiday
     * Then the transfer date should also be backdated to the nearest non-weekend, non-bank-holiday day
     *
     * @return void
     */
    #[Test]
    public function transfer_back_dating_is_handled_correctly()
    {
        $response = $this->postJson('/api/', [
            'year' => 2022,
            'month' => 12,
            'backdate_transfer_day' => true,
        ]);

        $response->assertJsonFragment([
            'payday' => '2022-12-30',
            'transfer_date' => '2022-12-23',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);
        $this->assertFalse($payday->isBankHoliday());
        $transferDate = Carbon::parse($response->json()['data']['transfer_date']);
        $this->assertFalse($transferDate->isBankHoliday());
        $this->assertFalse($transferDate->isWeekend());

        // Then re-run the request again without the flag being se tot confirm it still works as intended
        $response = $this->postJson('/api/', [
            'year' => 2022,
            'month' => 12,
        ]);

        $response->assertJsonFragment([
            'payday' => '2022-12-30',
            'transfer_date' => '2022-12-26',
        ]);

        $payday = Carbon::parse($response->json()['data']['payday']);
        $this->assertFalse($payday->isBankHoliday());
        $transferDate = Carbon::parse($response->json()['data']['transfer_date']);
        $this->assertEquals('Boxing Day', $transferDate->isBankHoliday());
    }
}
