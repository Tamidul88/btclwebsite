<?php

namespace App;

use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\Exceptions\InvalidFormatException;

/**
 * Works out which consultation slots are open, Calendly style: fixed length
 * slots inside working hours on working days, minus anything already booked,
 * anything too soon, and anything beyond the booking window.
 *
 * Slots are expressed in the booking timezone. The database stores times in
 * the app timezone, so convert with toStorage() / toLocal() at the boundary.
 */
class BookingSlots
{
    public const INPUT_FORMAT = 'Y-m-d\TH:i';

    public function timezone(): string
    {
        return config('site.booking.timezone');
    }

    public function today(): CarbonImmutable
    {
        return CarbonImmutable::now($this->timezone())->startOfDay();
    }

    public function lastDay(): CarbonImmutable
    {
        return $this->today()->addDays(config('site.booking.days_ahead'));
    }

    /**
     * Open slots between two local dates (inclusive), grouped by day.
     * Days without an open slot are left out.
     *
     * @return array<string, list<CarbonImmutable>>
     */
    public function between(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $from = $from->startOfDay()->max($this->today());
        $to = $to->startOfDay()->min($this->lastDay());

        if ($from->greaterThan($to)) {
            return [];
        }

        $earliest = CarbonImmutable::now($this->timezone())->addHours(config('site.booking.min_notice_hours'));
        $booked = $this->bookedBetween($from, $to->endOfDay());
        $duration = config('site.booking.duration');
        $days = [];

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            if (! in_array($day->dayOfWeek, config('site.booking.days'), true)) {
                continue;
            }

            $closes = $day->setTimeFromTimeString(config('site.booking.closes'));

            for ($slot = $day->setTimeFromTimeString(config('site.booking.opens')); $slot->addMinutes($duration)->lessThanOrEqualTo($closes); $slot = $slot->addMinutes($duration)) {
                if ($slot->greaterThanOrEqualTo($earliest) && ! isset($booked[$slot->format('Y-m-d H:i')])) {
                    $days[$day->toDateString()][] = $slot;
                }
            }
        }

        return $days;
    }

    /**
     * @return list<CarbonImmutable>
     */
    public function forDate(CarbonImmutable $date): array
    {
        return $this->between($date, $date)[$date->toDateString()] ?? [];
    }

    public function isAvailable(CarbonImmutable $at): bool
    {
        $at = $at->setTimezone($this->timezone());

        foreach ($this->forDate($at) as $slot) {
            if ($slot->equalTo($at)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Strictly parses a request value (by default "2026-10-07T10:00") as
     * booking local time. Anything that does not round-trip returns null.
     */
    public function parse(mixed $value, string $format = self::INPUT_FORMAT): ?CarbonImmutable
    {
        if (! is_string($value)) {
            return null;
        }

        try {
            $parsed = CarbonImmutable::createFromFormat('!'.$format, $value, $this->timezone());
        } catch (InvalidFormatException) {
            return null;
        }

        return $parsed && $parsed->format($format) === $value ? $parsed : null;
    }

    public function toStorage(CarbonInterface $at): CarbonImmutable
    {
        return CarbonImmutable::instance($at)->setTimezone(config('app.timezone'));
    }

    public function toLocal(CarbonInterface $at): CarbonImmutable
    {
        return CarbonImmutable::instance($at)->setTimezone($this->timezone());
    }

    /**
     * @return array<string, true> keyed by local "Y-m-d H:i"
     */
    private function bookedBetween(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return Appointment::query()
            ->whereBetween('preferred_at', [$this->toStorage($from), $this->toStorage($to)])
            ->pluck('preferred_at')
            ->mapWithKeys(fn (CarbonInterface $at): array => [$this->toLocal($at)->format('Y-m-d H:i') => true])
            ->all();
    }
}
