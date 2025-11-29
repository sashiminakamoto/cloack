<?php
/**
 * YOURLS with Cloaker Integration
 * Bot detection wrapper for YOURLS URL shortener
 */

// Allow admin and API access without cloaker check
$request_uri = $_SERVER['REQUEST_URI'];

if (strpos($request_uri, '/admin') !== false ||
    strpos($request_uri, 'yourls-api.php') !== false ||
    strpos($request_uri, 'yourls-infos.php') !== false) {
    require_once __DIR__ . '/yourls-loader.php';
    exit;
}

// Load cloaker core
require_once __DIR__ . '/core.php';
require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/db.php';

// Initialize cloaker
$cloaker = new Cloaker(
    $os_white,
    $country_white,
    $lang_white,
    $ip_black_filename,
    $ip_black_cidr,
    $tokens_black,
    $url_should_contain,
    $ua_black,
    $isp_black,
    $block_without_referer,
    $referer_stopwords,
    $block_vpnandtor
);

// Check if full cloak mode is enabled
if ($tds_mode == 'full') {
    add_white_click($cloaker->detect, ['fullcloak']);
    header('Location: https://tinhte.vn');
    exit;
}

// Check visitor
$check_result = $cloaker->check();

if ($check_result == 0 || $tds_mode === 'off') {
    // Real user → Allow YOURLS access
    add_black_click('', $cloaker->detect, 'links', 'yourls');
    require_once __DIR__ . '/yourls-loader.php';
} else {
    // Bot detected → Redirect to Tinhte.vn
    add_white_click($cloaker->detect, $cloaker->result);
    header('Location: https://tinhte.vn');
    exit;
}
