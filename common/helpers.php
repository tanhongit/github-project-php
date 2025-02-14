<?php

if (!function_exists('color_value_format')) {
    /**
     * @param  string  $value
     * @param  string  $color
     *
     * @return string
     */
    function color_value_format(string $value, string $color): string
    {
        $value = str_replace(' ', ' \space ', $value);

        return '$${\color{'.$color.'}'.$value.'}$$';
    }
}
