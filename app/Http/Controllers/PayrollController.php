<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollCheckRequest;
use Illuminate\Http\JsonResponse;
use Citco\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class PayrollController extends Controller
{
    protected Carbon $payday;

    public function check(PayrollCheckRequest $request): JsonResponse
    {
        $year = $request->input('year');
        $month = $request->input('month');

        $date = Carbon::create()->setYear($year)->setMonth($month)->setDay(1);

        $this->calculateTransferDate($date);

        return response()->json([
            'data' => [
                'payday' => $this->payday->format('Y-m-d'),
                'transfer_date' => $this->payday->subDays(4)->format('Y-m-d'),
            ]
        ]);
    }

    public function show(PayrollCheckRequest $request): Response
    {
        return Inertia::render('Welcome', [
            'response' => [
                'success' => true,
                'data' => $this->check($request),
            ],
        ]);
    }

    protected function calculateTransferDate(Carbon $date): Carbon
    {
        $this->payday = $date->endOfMonth();

        // Loop, subtracting a day until $this->payday is neither a weekend nor a bank holiday
        do {
            $this->payday = $this->payday->subDay();
        } while ($this->checkIfExcluded($this->payday));

        // TODO: presumably similar handling if the transfer date (-4 days) is a weekend or bank holiday too?

        return $this->payday;
    }

    /**
     * Here we check to see if a date is a weekend or bank holiday
     *
     * @param Carbon $date
     * @return bool
     */
    protected function checkIfExcluded(Carbon $date): bool
    {
        return $date->isWeekend() || $date->isBankHoliday();
    }
}
