<?php
/**
 * @param string $str
 * @param int $max
 * @return bool
 */
function has_length_greater_than(string $str, int $max): bool
{
    $trs = trim($str, ' ');
    return strlen($str) > $max;
}

function has_length_less_than(string $str, int $min): bool
{
    $str = trim($str, ' ');
    return strlen($str) < $min;
}

function is_valid_email(string $email): bool
{
    $email = trim($email, ' ');
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_slug(string $slug, string $custom_regex = null): bool
{
    $slug = trim($slug, ' ');
    $regex = $custom_regex ?? '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
    return preg_match($regex, $slug);
}

function is_exactly(mixed $obj1, mixed $obj2): bool
{
    return $obj1 === $obj2;
}