<?php
declare(strict_types=1);

// ======================================================================
// WINTECH SOLUTIONS — SECURE CONTACT FORM HANDLER  v2.0
// Fixes: Email Header Injection, CSRF, Rate Limiting, Info Disclosure
// ======================================================================

session_start();

// === SECURITY HEADERS (also set these in .htaccess for static pages) ==
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Cache-Control: no-store, no-cache, must-revalidate");

// === CONSTANTS =========================================================
define('RECIPIENT_EMAIL',   'info@wintechsite.com');
define('RATE_LIMIT_MAX',    3);
define('RATE_LIMIT_WINDOW', 900);   // 15 minutes
define('MAX_NAME_LEN',      100);
define('MAX_EMAIL_LEN',     254);   // RFC 5321 max
define('MAX_PHONE_LEN',     20);
define('MAX_MESSAGE_LEN',   3000);

// === UTILITY FUNCTIONS =================================================

/**
 * Strip CR, LF, NULL, and TAB from any value destined for an email header.
 * These characters are the root cause of Email Header Injection attacks.
 */
function sanitizeForHeader(string $value): string {
    return preg_replace('/[\r\n\t\0]/', '', $value);
}

/**
 * Validate CSRF token using constant-time comparison to prevent
 * timing-based attacks on string comparison.
 */
function validateCsrfToken(string $submitted): bool {
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}

/**
 * Session-based rate limiter — max N submissions per window.
 * For high-traffic production sites, replace with Redis/DB per-IP limiting.
 */
function isRateLimited(): bool {
    if (!isset($_SESSION['rate'])) {
        $_SESSION['rate'] = ['count' => 0, 'first' => time()];
    }

    $rate = &$_SESSION['rate'];

    if ((time() - $rate['first']) > RATE_LIMIT_WINDOW) {
        $rate = ['count' => 0, 'first' => time()];
    }

    if ($rate['count'] >= RATE_LIMIT_MAX) {
        return true;
    }

    $rate['count']++;
    return false;
}

/**
 * Safe redirect — never puts user input directly in a Location header.
 */
function redirect(string $url, int $code = 303): never {
    header("Location: " . $url, true, $code);
    exit;
}

// === MAIN HANDLER ======================================================

// 1. Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("index.php");
}

// 2. Honeypot check — bots fill hidden fields, real users don't
//    Respond with fake-success so bots learn nothing
if (!empty($_POST['website'])) {
    redirect("index.php?status=success#contact");
}

// 3. CSRF token validation
$submittedCsrf = trim($_POST['csrf_token'] ?? '');
if (!validateCsrfToken($submittedCsrf)) {
    http_response_code(403);
    redirect("index.php?status=csrf_error#contact");
}
// One-time-use: invalidate the token immediately after validation
unset($_SESSION['csrf_token']);

// 4. Rate limiting — prevent form spam flooding
if (isRateLimited()) {
    http_response_code(429);
    redirect("index.php?status=rate_limited#contact");
}

// 5. Collect and sanitize inputs
//    sanitizeForHeader() strips \r\n to block header injection
//    FILTER_SANITIZE_EMAIL removes disallowed characters from email
$name    = sanitizeForHeader(strip_tags(trim($_POST['name']    ?? '')));
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = preg_replace('/[^\d\s\+\-\(\)]/', '', trim($_POST['phone'] ?? ''));
$message = strip_tags(trim($_POST['message'] ?? ''));

// 6. Input validation with length caps
$valid = true;
if (empty($name)    || strlen($name)    > MAX_NAME_LEN)    { $valid = false; }
if (empty($message) || strlen($message) > MAX_MESSAGE_LEN) { $valid = false; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))             { $valid = false; }
if (strlen($email)  > MAX_EMAIL_LEN)                        { $valid = false; }
if (!empty($phone)  && strlen($phone)   > MAX_PHONE_LEN)    { $valid = false; }

if (!$valid) {
    http_response_code(400);
    redirect("index.php?status=validation_error#contact");
}

// 7. Build the email
//    Subject: NO user-supplied data — eliminates Subject-line header injection
$subject  = "New Website Enquiry \xe2\x80\x94 Wintech Solutions";

$body     = "New contact form submission from Wintech Solutions.\n";
$body    .= str_repeat("-", 52) . "\n";
$body    .= "Name:      " . $name    . "\n";
$body    .= "Email:     " . $email   . "\n";
$body    .= "Phone:     " . (!empty($phone) ? $phone : "Not provided") . "\n";
$body    .= str_repeat("-", 52) . "\n";
$body    .= "Message:\n"  . $message . "\n";
$body    .= str_repeat("-", 52) . "\n";
$body    .= "Submitted: " . date('Y-m-d H:i:s T') . "\n";

// Headers: no user-supplied data in From/Subject
// Reply-To uses the validated email address ONLY (not the $name)
// X-Mailer intentionally omitted — prevents PHP version disclosure
$headers  = "From: Wintech Web Form <no-reply@wintech.space>\r\n";
$headers .= "Reply-To: " . $email . "\r\n"; // FILTER_VALIDATE_EMAIL already confirmed safe
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "Content-Transfer-Encoding: 8bit\r\n";

// 8. Attempt to send
//    NOTE: For production, replace mail() with PHPMailer + SMTP to avoid
//    synchronous blocking and improve deliverability. See audit report.
$sent = mail(RECIPIENT_EMAIL, $subject, $body, $headers);

redirect($sent
    ? "index.php?status=success#contact"
    : "index.php?status=error#contact"
);
