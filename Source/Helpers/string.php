<?php
/**
 * Converts directory separator to correct separator defined by system
 * @param string $string
 * @param string|array $search
 * @param string $sep
 * @return string
 */
function fix_dir_sep(string $string, string|array $search = '\\', string $sep = DIRECTORY_SEPARATOR): string
{
    return str_replace($search, $sep, $string);
}

/**
 * Removes slashes from both ends the string
 * @param string $string
 * @param string $slash
 * @return string
 */
function trim_slashes(string $string, string $slash = '/'): string
{
    return trim($string, $slash);
}