<?php
// ============================================================
// Database Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'ac_service_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'Hot and Cold Engineering');
define('APP_VERSION', '1.0');

define('MAPS_API_KEY', '');

define('SESSION_TIMEOUT',    1800);  // 30 min idle
define('LOGIN_MAX_ATTEMPTS',    5);
define('LOGIN_LOCKOUT_TIME',  900);  // 15 min lockout

define('WHATSAPP_ENABLED', true);

// ---- Session (secure flags before start) ----
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    // Uncomment next line when serving over HTTPS:
    // ini_set('session.cookie_secure', '1');
    session_start();
}

// ---- Idle session timeout ----
function checkSessionTimeout() {
    if (!isset($_SESSION['user_id'])) return;
    if (isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        header('Location: ' . getAppRoot() . 'login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// ---- Resolve app root URL path dynamically ----
function getAppRoot() {
    $dir = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
    $maxDepth = 5;
    for ($i = 0; $i < $maxDepth; $i++) {
        if (file_exists($dir . '/index.php') && file_exists($dir . '/includes/config.php')) {
            $docRoot = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), '/\\');
            $rel = str_replace('\\', '/', substr($dir, strlen($docRoot)));
            return rtrim($rel, '/') . '/';
        }
        $parent = dirname($dir);
        if ($parent === $dir) break;
        $dir = $parent;
    }
    return '/';
}

// ---- Cache headers ----
function sendNoCacheHeaders() {
    if (headers_sent()) return;
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
}

// ---- Database (errors logged, never shown to browser) ----
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('[AC Service] DB error: ' . $e->getMessage());
            die('<div style="font-family:Arial;padding:40px;color:#c0392b;">'
              . '<h2>Database connection error</h2>'
              . '<p>Could not connect to the database. Check settings in '
              . '<strong>includes/config.php</strong> and ensure MySQL is running.</p></div>');
        }
    }
    return $pdo;
}

// ---- Auth ----
function requireLogin() {
    checkSessionTimeout();
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . getAppRoot() . 'login.php');
        exit;
    }
}

function isOwner() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'owner';
}

// ---- CSRF ----
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField() {
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars(csrfToken()) . '">';
}

function verifyCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals(csrfToken(), $token)) {
        http_response_code(403);
        die('<div style="font-family:Arial;padding:40px;color:#c0392b;">'
          . '<h2>403 – Invalid request</h2>'
          . '<p>Security token mismatch. Please go back and try again.</p></div>');
    }
}

// ---- Login rate limiting (per IP, stored in session) ----
function checkLoginRateLimit() {
    $key = 'rl_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    $until = $_SESSION[$key]['until'] ?? 0;
    if (time() < $until) {
        $wait = ceil(($until - time()) / 60);
        return "Too many failed attempts. Please wait {$wait} minute(s).";
    }
    return null;
}

function recordLoginFailure() {
    $key      = 'rl_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    $attempts = ($_SESSION[$key]['count'] ?? 0) + 1;
    $until    = $_SESSION[$key]['until'] ?? 0;
    if ($attempts >= LOGIN_MAX_ATTEMPTS) {
        $until    = time() + LOGIN_LOCKOUT_TIME;
        $attempts = 0;
    }
    $_SESSION[$key] = ['count' => $attempts, 'until' => $until];
}

function clearLoginFailures() {
    $key = 'rl_' . md5($_SERVER['REMOTE_ADDR'] ?? '');
    unset($_SESSION[$key]);
}

// ---- Formatting helpers ----
function formatAmount($amount) {
    return '₹' . number_format($amount, 2);
}

function formatDate($date) {
    if (!$date) return '-';
    return date('d M Y', strtotime($date));
}

function buildGoogleMapsEmbedUrl($url) {
    $url = trim((string)$url);
    if ($url === '') return '';
    if (!filter_var($url, FILTER_VALIDATE_URL)) return '';
    $parts = parse_url($url);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return '';
    $scheme = strtolower($parts['scheme']);
    if ($scheme !== 'http' && $scheme !== 'https') return '';

    $host = strtolower($parts['host']);
    $allowedHosts = [
        'google.com',
        'www.google.com',
        'maps.google.com',
        'google.co.in',
        'www.google.co.in',
        'maps.app.goo.gl',
        'goo.gl',
    ];
    $isAllowed = false;
    foreach ($allowedHosts as $h) {
        if ($host === $h || (strlen($host) > strlen($h) && substr($host, -strlen($h)) === $h)) {
            $isAllowed = true;
            break;
        }
    }
    if (!$isAllowed) return '';

    $path = $parts['path'] ?? '';
    if (strpos($path, '/maps/embed') !== false) {
        return $url;
    }
    $sep = (!empty($parts['query'])) ? '&' : '?';
    return $url . $sep . 'output=embed';
}

function extractMapsIframeSrc($input) {
    $input = trim((string)$input);
    if ($input === '') return '';

    $src = '';
    if (stripos($input, '<iframe') !== false) {
        if (preg_match('/src\\s*=\\s*[\'"]([^\'"]+)[\'"]/i', $input, $m)) {
            $src = trim($m[1]);
        }
    } else {
        $src = $input;
    }

    if ($src === '' || !filter_var($src, FILTER_VALIDATE_URL)) return '';
    $parts = parse_url($src);
    if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return '';
    $scheme = strtolower($parts['scheme']);
    if ($scheme !== 'http' && $scheme !== 'https') return '';

    $host = strtolower($parts['host']);
    $allowedHosts = [
        'google.com',
        'www.google.com',
        'maps.google.com',
        'google.co.in',
        'www.google.co.in',
        'maps.app.goo.gl',
        'goo.gl',
    ];
    $isAllowed = false;
    foreach ($allowedHosts as $h) {
        if ($host === $h || (strlen($host) > strlen($h) && substr($host, -strlen($h)) === $h)) {
            $isAllowed = true;
            break;
        }
    }
    if (!$isAllowed) return '';

    return $src;
}

function statusBadge($status) {
    $colors = [
        'Pending'     => '#e67e22',
        'In Process'  => '#2980b9',
        'Completed'   => '#27ae60',
        'Delivered'   => '#7f8c8d',
        'Return'      => '#c0392b',
        'Scheduled'   => '#8e44ad',
        'In Progress' => '#2980b9',
        'Paid'        => '#27ae60',
        'Partial'     => '#e67e22',
        'Owner'       => '#c0392b',
        'Staff'       => '#2980b9',
        'Technician'  => '#16a085',
    ];
    $color = $colors[$status] ?? '#555';
    return '<span class="badge" style="background:' . $color . '">'
         . htmlspecialchars($status) . '</span>';
}

function whatsappLink($phone, $message) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10) $phone = '91' . $phone;
    return 'whatsapp://send?phone=' . $phone . '&text=' . rawurlencode($message);
}
