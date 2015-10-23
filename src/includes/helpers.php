<?php

/*

This file is part of Mustard.

Mustard is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Mustard is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Mustard.  If not, see <http://www.gnu.org/licenses/>.

*/

/**
 * Get an instance of the redirector, prefixing it with the base url.
 *
 * @param string|null $to
 * @param int $status
 * @param array $headers
 * @param bool $secure
 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
 */
function mustard_redirect($to = null, $status = 302, $headers = [], $secure = null)
{
    return redirect(env('MUSTARD_BASE', '') . $to, $status, $headers, $secure);
}

/**
 * Convert a Markdown-formatted string to HTML.
 *
 * @param string $text
 * @return string
 */
function mustard_markdown($text)
{
    $output = (new Parsedown)->text($text);

    $output = str_replace('<p>', '<p class="large-12 columns">', $output);

    $output = preg_replace('/<h([1-6])>/', '<h$1 class="large-12 columns">', $output);

    return $output;
}

/**
 * Return a timestamp as a human-readable date string.
 *
 * @param int $timestamp
 * @return string
 */
function mustard_date($timestamp)
{
    return date('Y/m/d', $timestamp);
}

/**
 * Return a timestamp as a human-readable date/time string.
 *
 * @param int $timestamp
 * @return string
 */
function mustard_datetime($timestamp)
{
    return date('Y/m/d H:i:s e', $timestamp);
}

/**
 * Return a number formatted according to a locale's currency.
 *
 * @param mixed $number
 * @param bool $national
 * @param string $locale
 * @return string
 */
function mustard_price($number, $national = false, $locale = null)
{
    if (!is_numeric($number)) return $number;

    if (is_null($locale)) {
        $locale = config('app.locale');
    }

    $old_locale = setlocale(LC_MONETARY, $locale);

    $output = money_format($national ? '%n' : '%i', $number);

    if ($old_locale) {
        setlocale(LC_MONETARY, $old_locale);
    }

    return $output;
}

/**
 * Return a number formatted according to a locale.
 *
 * @param mixed $number
 * @param int $decimalPlaces
 * @param string $locale
 * @return string
 */
function mustard_number($number, $decimalPlaces = 2, $locale = null)
{
    if (!is_numeric($number)) return $number;

    if (is_null($locale)) {
        $locale = config('app.locale');
    }

    $old_locale = setlocale(LC_NUMERIC, $locale);

    $locale_info = localeconv();

    $output = number_format(
        $number,
        $decimalPlaces,
        $locale_info['decimal_point'],
        $locale_info['thousands_sep']
    );

    if ($old_locale) {
        setlocale(LC_NUMERIC, $old_locale);
    }

    return $output;
}

/**
 * Return a date interval as a human-readable string.
 *
 * @param \DateInterval $diff
 * @param int $depth
 * @param bool $short
 * @return string
 */
function mustard_time($diff, $depth = 4, $short = false)
{
    $str = [];

    for ($i = 0; $i < 6; $i++) {
        if (count($str) >= $depth) break;

        switch ($i) {
            case 0:
                if ($diff->y || $str) $str[] = $short
                    ? "{$diff->y}y"
                    : ($diff->y > 1 ? "{$diff->y} years" : "{$diff->y} year");

                break;
            case 1:
                if ($diff->m || $str) $str[] = $short
                    ? "{$diff->m}mo"
                    : ($diff->m > 1 ? "{$diff->m} months" : "{$diff->m} month");

                break;
            case 2:
                if ($diff->d || $str) $str[] = $short
                    ? "{$diff->d}d"
                    : ($diff->d > 1 ? "{$diff->d} days" : "{$diff->d} day");

                break;
            case 3:
                if ($diff->h || $str) $str[] = $short
                    ? "{$diff->h}h"
                    : ($diff->h > 1 ? "{$diff->h} hours" : "{$diff->h} hour");

                break;
            case 4:
                if ($diff->i || $str) $str[] = $short
                    ? "{$diff->i}m"
                    : ($diff->i > 1 ? "{$diff->i} minutes" : "{$diff->i} minute");

                break;
            case 5:
                if ($diff->s || $str) $str[] = $short
                    ? "{$diff->s}s"
                    : ($diff->s > 1 ? "{$diff->s} seconds" : "{$diff->s} second");

                break;
        }
    }

    if ($diff->invert) {
        array_walk($str, function (&$item) {
            $item = '-' . $item;
        });
    }

    return implode(', ', $str);
}
