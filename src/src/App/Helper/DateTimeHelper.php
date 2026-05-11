<?php

declare(strict_types=1);

namespace App\Helper;

use DateTimeImmutable;
use DateTimeZone;

class DateTimeHelper
{
    public const APP_TIMEZONE = 'Europe/Tirane';
    private const STORAGE_TIMEZONE = 'UTC';
    private const STORAGE_FORMAT = 'Y-m-d H:i:s';

    public static function formatDateTimeParts($value): array
    {
        $dateTime = self::toLocalDateTime($value);
        if ($dateTime === null) {
            return ['-', ''];
        }

        return [$dateTime->format('M d, Y'), $dateTime->format('h:i A')];
    }

    public static function format($value, string $format): string
    {
        $dateTime = self::toLocalDateTime($value);
        if ($dateTime === null) {
            return '-';
        }

        return $dateTime->format($format);
    }

    public static function nowForStorage(): string
    {
        return (new DateTimeImmutable('now', new DateTimeZone(self::STORAGE_TIMEZONE)))
            ->format(self::STORAGE_FORMAT);
    }

    public static function localPeriodStorageRange(string $period): array
    {
        $localNow = new DateTimeImmutable('now', new DateTimeZone(self::APP_TIMEZONE));
        $localStart = match ($period) {
            'day' => $localNow->setTime(0, 0),
            'week' => $localNow->modify('monday this week')->setTime(0, 0),
            'month' => $localNow->modify('first day of this month')->setTime(0, 0),
            default => throw new \InvalidArgumentException('Unsupported period: ' . $period),
        };

        $localEnd = match ($period) {
            'day' => $localStart->modify('+1 day'),
            'week' => $localStart->modify('+1 week'),
            'month' => $localStart->modify('+1 month'),
        };

        $storageTimezone = new DateTimeZone(self::STORAGE_TIMEZONE);

        return [
            $localStart->setTimezone($storageTimezone)->format(self::STORAGE_FORMAT),
            $localEnd->setTimezone($storageTimezone)->format(self::STORAGE_FORMAT),
        ];
    }

    public static function toLocalDateTime($value): ?DateTimeImmutable
    {
        if (empty($value)) {
            return null;
        }

        try {
            $dateTime = new DateTimeImmutable((string)$value, new DateTimeZone(self::STORAGE_TIMEZONE));
        } catch (\Throwable $e) {
            return null;
        }

        return $dateTime->setTimezone(new DateTimeZone(self::APP_TIMEZONE));
    }
}
