<?php

namespace App\Http\Controllers;

use App\BookingSlots;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    /**
     * Calendly style booking in three steps, all driven by the query string:
     * ?month=2026-10 shows the calendar, &date=2026-10-07 adds that day's
     * times, and &time=10:00 swaps the calendar for the details form.
     */
    public function create(Request $request, BookingSlots $slots): View
    {
        $firstMonth = $slots->today()->startOfMonth();
        $lastMonth = $slots->lastDay()->startOfMonth();

        $date = $slots->parse($request->query('date'), 'Y-m-d');
        $month = $slots->parse($request->query('month'), 'Y-m') ?? $date ?? $firstMonth;
        $month = $month->startOfMonth()->max($firstMonth)->min($lastMonth);

        $openDays = $slots->between($month, $month->endOfMonth());

        if ($date && ! isset($openDays[$date->toDateString()])) {
            $date = null;
        }

        $times = $date ? $openDays[$date->toDateString()] : [];
        $selected = collect($times)->first(fn (CarbonImmutable $slot): bool => $slot->format('H:i') === $request->query('time'));

        return view('pages.appointment', [
            'month' => $month,
            'previousMonth' => $month->greaterThan($firstMonth) ? $month->subMonth() : null,
            'nextMonth' => $month->lessThan($lastMonth) ? $month->addMonth() : null,
            'openDays' => $openDays,
            'today' => $slots->today(),
            'date' => $date,
            'times' => $times,
            'selected' => $selected,
        ]);
    }

    public function store(StoreAppointmentRequest $request, BookingSlots $slots): RedirectResponse
    {
        $slot = $slots->parse($request->validated('preferred_at'));

        try {
            Appointment::create([
                ...$request->safe()->except(['website', 'preferred_at']),
                'preferred_at' => $slots->toStorage($slot),
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()
                ->route('appointment', ['month' => $slot->format('Y-m'), 'date' => $slot->toDateString()])
                ->withErrors(['preferred_at' => 'That time was just booked by someone else. Please pick another slot.']);
        }

        return redirect()
            ->route('appointment')
            ->with('status', 'Your consultation is booked for '.$slot->format('j F Y, g:i A').'. We will confirm by email.')
            ->with('booked', [
                'at' => $slot->format(BookingSlots::INPUT_FORMAT),
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
            ]);
    }
}
