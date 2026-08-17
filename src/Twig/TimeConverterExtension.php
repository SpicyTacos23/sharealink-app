<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TimeConverterExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('seconds_to_time', [$this, 'convertSecondsToHourMinute']),
            new TwigFunction('minutes_to_time', [$this, 'convertMinutesToHourMinute']),
        ];
    }

    public function convertSecondsToHourMinute(int $seconds): string
    {
        if ($seconds <= 0) {
            return '0min';
        }

        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($hours > 0) {
            return sprintf('%dh %dmin', $hours, $minutes);
        }

        return sprintf('%dmin', $minutes);
    }

    public function convertMinutesToHourMinute(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0min';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dmin', $hours, $remainingMinutes);
        }

        return sprintf('%dmin', $remainingMinutes);
    }
}
