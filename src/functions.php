<?php

namespace EvozonPhp\SimpleBruteForceBundle;

/**
 * Canonicalize string.
 *
 * @param string $string
 *
 * @return string
 */
function canonicalize(string $string): string
{
    $encoding = mb_detect_encoding($string);

    return $encoding
        ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
        : mb_convert_case($string, MB_CASE_LOWER);
}
