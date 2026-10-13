<?php
/**
 * Test Suite Bootstrap & Test Runner for WP Wordle Plugin
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}
if (!defined('WP_WORDLE_PATH')) {
    define('WP_WORDLE_PATH', dirname(__DIR__) . '/wp-content/plugins/wp-wordle/');
}
if (!defined('WP_WORDLE_URL')) {
    define('WP_WORDLE_URL', 'http://example.com/wp-content/plugins/wp-wordle/');
}
if (!defined('WP_WORDLE_VERSION')) {
    define('WP_WORDLE_VERSION', '1.0.0');
}
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}
if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

// In-memory mock storage
$GLOBALS['_mock_options'] = [];
$GLOBALS['_mock_transients'] = [];
$GLOBALS['_mock_enqueued_styles'] = [];
$GLOBALS['_mock_enqueued_scripts'] = [];
$GLOBALS['_mock_localized_scripts'] = [];
$GLOBALS['_mock_settings_errors'] = [];

// WordPress core functions mocks
function get_option($key, $default = false) {
    return $GLOBALS['_mock_options'][$key] ?? $default;
}

function update_option($key, $value, $autoload = false) {
    $GLOBALS['_mock_options'][$key] = $value;
    return true;
}

function get_transient($key) {
    return $GLOBALS['_mock_transients'][$key] ?? false;
}

function set_transient($key, $value, $expiration = 0) {
    $GLOBALS['_mock_transients'][$key] = $value;
    return true;
}

function delete_transient($key) {
    unset($GLOBALS['_mock_transients'][$key]);
    return true;
}

function home_url() {
    return 'http://example.com';
}

function rest_url($path = '') {
    return 'http://example.com/wp-json/' . ltrim($path, '/');
}

function current_time($type = 'mysql') {
    if ($type === 'Y-m-d') {
        return date('Y-m-d');
    }
    return date('Y-m-d H:i:s');
}

function sanitize_text_field($str) {
    return is_string($str) ? trim(strip_tags($str)) : '';
}

function sanitize_key($key) {
    return strtolower(preg_replace('/[^a-z0-9_\-]/', '', (string)$key));
}

function wp_create_nonce($action = -1) {
    return 'mock_nonce_' . $action;
}

function wp_generate_password($length = 12, $special_chars = true) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $res;
}

function esc_attr($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function esc_html($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function esc_html_e($text, $domain = 'default') {
    echo esc_html($text);
}

function esc_url($url) {
    return htmlspecialchars((string)$url, ENT_QUOTES, 'UTF-8');
}

function esc_url_raw($url) {
    return (string)$url;
}

function __($text, $domain = 'default') {
    return $text;
}

function shortcode_atts($pairs, $atts, $shortcode = '') {
    $atts = (array)$atts;
    $out = [];
    foreach ($pairs as $name => $default) {
        if (array_key_exists($name, $atts)) {
            $out[$name] = $atts[$name];
        } else {
            $out[$name] = $default;
        }
    }
    return $out;
}

function add_shortcode($tag, $callback) {}
function add_action($hook, $callback) {}
function register_rest_route($namespace, $route, $args) {}
function add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback) {}
function register_setting($option_group, $option_name, $args = []) {}
function add_settings_section($id, $title, $callback, $page) {}
function add_settings_field($id, $title, $callback, $page, $section, $args = []) {}
function settings_fields($option_group) {}
function do_settings_sections($page) {}
function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {}

function selected($selected, $current = true, $echo = true) {
    $res = ((string)$selected === (string)$current) ? ' selected="selected"' : '';
    if ($echo) echo $res;
    return $res;
}

function checked($checked, $current = true, $echo = true) {
    $res = ((string)$checked === (string)$current) ? ' checked="checked"' : '';
    if ($echo) echo $res;
    return $res;
}

function disabled($disabled, $current = true, $echo = true) {
    $res = ((string)$disabled === (string)$current) ? ' disabled="disabled"' : '';
    if ($echo) echo $res;
    return $res;
}

function current_user_can($capability) {
    return true;
}

function add_settings_error($setting, $code, $message, $type = 'error') {
    $GLOBALS['_mock_settings_errors'][] = [
        'setting' => $setting,
        'code'    => $code,
        'message' => $message,
        'type'    => $type,
    ];
}

function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
    $GLOBALS['_mock_enqueued_styles'][$handle] = $src;
}

function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
    $GLOBALS['_mock_enqueued_scripts'][$handle] = $src;
}

function wp_localize_script($handle, $object_name, $l10n) {
    $GLOBALS['_mock_localized_scripts'][$handle][$object_name] = $l10n;
}

function is_wp_error($thing) {
    return ($thing instanceof WP_Error);
}

function wp_remote_get($url, $args = []) {
    $timeout = $args['timeout'] ?? 5;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $args['headers']['User-Agent'] ?? 'Mozilla/5.0 WP-Wordle-Test');
    $body = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error || $http_code === 0) {
        return new WP_Error('http_request_failed', $error ?: 'Connection failed');
    }

    return [
        'response' => ['code' => $http_code],
        'body'     => $body,
    ];
}

function wp_remote_retrieve_response_code($response) {
    if (is_wp_error($response) || !isset($response['response']['code'])) {
        return '';
    }
    return $response['response']['code'];
}

function wp_remote_retrieve_body($response) {
    if (is_wp_error($response) || !isset($response['body'])) {
        return '';
    }
    return $response['body'];
}

// Mock WordPress Classes
class WP_Error {
    protected $errors = [];
    protected $error_data = [];

    public function __construct($code = '', $message = '', $data = '') {
        if (!empty($code)) {
            $this->errors[$code][] = $message;
            if (!empty($data)) {
                $this->error_data[$code] = $data;
            }
        }
    }

    public function get_error_code() {
        $codes = array_keys($this->errors);
        return $codes[0] ?? '';
    }

    public function get_error_message($code = '') {
        if (empty($code)) {
            $code = $this->get_error_code();
        }
        return $this->errors[$code][0] ?? '';
    }
}

class WP_REST_Request {
    protected $params = [];

    public function __construct($params = []) {
        $this->params = $params;
    }

    public function get_param($key) {
        return $this->params[$key] ?? null;
    }

    public function set_param($key, $value) {
        $this->params[$key] = $value;
    }
}

class WP_REST_Response implements ArrayAccess {
    public $data;
    public $status;

    public function __construct($data = null, $status = 200) {
        $this->data = $data;
        $this->status = $status;
    }

    public function get_data() {
        return $this->data;
    }

    public function get_status() {
        return $this->status;
    }

    public function offsetExists($offset): bool {
        return is_array($this->data) && isset($this->data[$offset]);
    }

    public function offsetGet($offset): mixed {
        return is_array($this->data) ? ($this->data[$offset] ?? null) : null;
    }

    public function offsetSet($offset, $value): void {
        if (is_array($this->data)) {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void {
        if (is_array($this->data)) {
            unset($this->data[$offset]);
        }
    }
}

function rest_ensure_response($response) {
    if (is_wp_error($response)) {
        return $response;
    }
    if ($response instanceof WP_REST_Response) {
        return $response;
    }
    return new WP_REST_Response($response, 200);
}

// Require plugin classes
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-game.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-api.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-shortcode.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-admin.php';

/**
 * Lightweight, elegant TestCase base class
 */
abstract class TestCase {
    public $name = '';
    public $passed = 0;
    public $failed = 0;
    public $skipped = 0;
    public $results = [];

    public function __construct($name = '') {
        $this->name = $name ?: get_class($this);
    }

    public function setUp() {}
    public function tearDown() {}

    public function assert($condition, $description) {
        if ($condition) {
            $this->passed++;
            $this->results[] = ['status' => 'PASS', 'message' => $description];
            echo "  \033[32m[PASS]\033[0m {$description}\n";
        } else {
            $this->failed++;
            $this->results[] = ['status' => 'FAIL', 'message' => $description];
            echo "  \033[31m[FAIL]\033[0m {$description}\n";
        }
    }

    public function skip($description) {
        $this->skipped++;
        $this->results[] = ['status' => 'SKIP', 'message' => $description];
        echo "  \033[33m[SKIP]\033[0m {$description}\n";
    }

    public function assertTrue($condition, $description = 'Expected true') {
        $this->assert($condition === true, $description);
    }

    public function assertFalse($condition, $description = 'Expected false') {
        $this->assert($condition === false, $description);
    }

    public function assertEquals($expected, $actual, $description = '') {
        $desc = $description ?: "Expected " . json_encode($expected) . ", got " . json_encode($actual);
        $this->assert($expected === $actual, $desc);
    }

    public function assertNotEmpty($value, $description = 'Expected non-empty') {
        $this->assert(!empty($value), $description);
    }

    public function assertNull($value, $description = 'Expected null') {
        $this->assert($value === null, $description);
    }

    public function assertNotNull($value, $description = 'Expected not null') {
        $this->assert($value !== null, $description);
    }

    public function assertCount($expected_count, $array, $description = '') {
        $actual_count = is_countable($array) ? count($array) : 0;
        $desc = $description ?: "Expected count {$expected_count}, got {$actual_count}";
        $this->assert($expected_count === $actual_count, $desc);
    }

    public function assertContains($needle, $haystack, $description = '') {
        if (is_string($haystack)) {
            $desc = $description ?: "Expected string to contain '{$needle}'";
            $this->assert(strpos($haystack, $needle) !== false, $desc);
        } elseif (is_array($haystack)) {
            $desc = $description ?: "Expected array to contain element";
            $this->assert(in_array($needle, $haystack, true), $desc);
        } else {
            $this->assert(false, "Invalid haystack for assertContains");
        }
    }

    public function run() {
        $methods = get_class_methods($this);
        $test_methods = array_filter($methods, function($m) {
            return strpos($m, 'test') === 0;
        });

        echo "\n\033[1;34m--- " . strtoupper($this->name) . " ---\033[0m\n";

        foreach ($test_methods as $method) {
            $this->setUp();
            $this->$method();
            $this->tearDown();
        }
    }
}
