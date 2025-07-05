<?php

if (!function_exists('color_value_format')) {
    /**
     * Format a value with an optional color.
     *
     * @param  string  $value
     * @param  string|null  $color
     * @return string
     */
    function color_value_format(string $value, ?string $color = null): string
    {
        if (empty($color)) {
            return $value;
        }

        return '$${\color{'.$color.'}'.$value.'}$$';
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string.
     *
     * @param  string|null  $date
     * @param  string  $format
     *
     * @return string|null
     */
    function format_date(?string $date, string $format = 'Y-m-d'): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return $date;
        }
    }
}
