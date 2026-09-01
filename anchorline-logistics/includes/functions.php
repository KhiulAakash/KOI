<?php
/** Small shared helpers used across every page. */

/** Escape a value for safe HTML output. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Queue a one-time message to show after a redirect (post/redirect/get). */
function flash_set($type, $message)
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** Read and clear any queued flash messages. */
function flash_get()
{
    $flashes = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flashes;
}

function is_valid_waybill($value)
{
    return (bool) preg_match('/^ANC-\d{4}-(NSW|VIC|QLD|WA|SA|TAS|NT|ACT)$/i', trim((string) $value));
}

function is_valid_au_phone($value)
{
    // Accept any spacing/hyphen grouping (0412 345 678, 04 1234 5678, 0412345678, ...)
    // and check the underlying digit count, rather than one fixed grouping pattern.
    $digits = preg_replace('/\D/', '', (string) $value);
    return (bool) preg_match('/^0\d{9}$/', $digits);
}

function redirect($path)
{
    header('Location: ' . $path);
    exit;
}

/** Human label for the enum-style codes stored in the database. */
function label_for($map, $key)
{
    return $map[$key] ?? $key;
}

/* Small helpers for re-rendering a form with server-side validation errors. */
function field_invalid($errors, $key)
{
    return isset($errors[$key]) ? ' is-invalid' : '';
}

function field_error($errors, $key)
{
    return isset($errors[$key]) ? e($errors[$key]) : '';
}

function field_aria_invalid($errors, $key)
{
    return isset($errors[$key]) ? 'true' : 'false';
}

const SERVICE_LABELS = [
    'road'          => 'Road freight and line-haul',
    'warehouse'     => 'Warehousing and 3PL',
    'cold'          => 'Cold chain',
    'international' => 'Sea, air and customs',
    'other'         => 'Something else',
];

const CONSIGNMENT_STATUS_LABELS = [
    'booked'     => 'Booking accepted',
    'collected'  => 'Collected',
    'in_transit' => 'In transit',
    'held'       => 'Held at depot',
    'delivered'  => 'Delivered',
];

const ENQUIRY_STATUS_LABELS = [
    'new'       => 'New',
    'contacted' => 'Contacted',
    'closed'    => 'Closed',
];
