<?php

/**
 * @param string $text The content to estimate.
 * @param int    $wpp  Words per minute.
 * @return object
 */
function calc_reading_time($text, $wpp = 200)
{
    $word_count = str_word_count(strip_tags($text));

    $minutes = floor($word_count / $wpp);

    $minutes = $minutes < 1
        ? 1
        : $minutes;

    $suffix = $minutes == 1
        ? '1 minuto'
        : "{$minutes} minutos";

    return (object) [
        'full'        => "Lectura de {$suffix}",
        'abbreviated' => $suffix,
    ];
}
