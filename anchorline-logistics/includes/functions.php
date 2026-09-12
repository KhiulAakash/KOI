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

/**
 * Renders the eye-icon button that reveals/hides a password field.
 * $targetId must match the <input>'s id - js/main.js finds the button
 * by its data-target and toggles that input's type. Progressive
 * enhancement: with JavaScript off the button just doesn't render as
 * clickable-looking and the password field works exactly as before.
 */
function password_toggle_button($targetId)
{
    return '<button type="button" class="password-toggle" data-target="' . e($targetId) . '" aria-pressed="false">'
        . '<svg class="icon-show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
        . '<svg class="icon-hide" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-6.06M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a21.7 21.7 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>'
        . '<span class="sr-only">Show password</span>'
        . '</button>';
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
