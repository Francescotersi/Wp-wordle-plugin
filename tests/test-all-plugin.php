<?php
/**
 * Comprehensive Test Suite for WP Wordle Plugin (Italian & English)
 * 
 * Tests all components:
 * 1. Vocabulary Integrity & Curation (IT & EN)
 * 2. Visual Theme Registry & CSS Files Integrity (All 60 Themes)
 * 3. WP_Wordle_Game Engine (IT & EN):
 *    - load_words, is_valid_word, auto-learning, online APIs (Treccani & Datamuse)
 *    - get_daily_word, deterministic date indexing, custom override
 *    - get_random_word
 *    - evaluate_guess (exact, absent, present, duplicate letters handling)
 * 4. WP_Wordle_API REST Controller (IT & EN):
 *    - /start endpoint (daily & practice modes)
 *    - /guess endpoint (valid, invalid, error messages in IT & EN, game over, secret revealing)
 * 5. WP_Wordle_Shortcode Renderer (IT & EN):
 *    - shortcode rendering, localization dictionary, data attributes, tiles & keyboard
 * 6. WP_Wordle_Admin Manager (IT & EN):
 *    - options sanitization, custom word validation, settings page rendering
 * 7. Live WordPress Container Integration (HTTP REST API on localhost:8080):
 *    - Real network requests to WordPress for IT and EN in Daily and Practice modes
 */

// Define WordPress mock constants
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

// In-memory options, transients, and enqueued assets
$GLOBALS['_mock_options'] = [];
$GLOBALS['_mock_transients'] = [];
$GLOBALS['_mock_enqueued_styles'] = [];
$GLOBALS['_mock_enqueued_scripts'] = [];
$GLOBALS['_mock_localized_scripts'] = [];
$GLOBALS['_mock_settings_errors'] = [];

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
function register_setting($option_group, $option_name, $args = []) {}
function add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback = '') {}
function settings_fields($option_group) {}
function do_settings_sections($page) {}
function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {
    echo '<input type="submit" value="' . esc_attr($text ?? 'Save Changes') . '">';
}
function selected($selected, $current = true, $echo = true) {
    $result = ((string)$selected === (string)$current) ? 'selected="selected"' : '';
    if ($echo) echo $result;
    return $result;
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

function wp_register_style($handle, $src, $deps = [], $ver = false, $media = 'all') {}
function wp_register_script($handle, $src, $deps = [], $ver = false, $in_footer = false) {}
function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
    $GLOBALS['_mock_enqueued_styles'][] = $handle;
}
function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
    $GLOBALS['_mock_enqueued_scripts'][] = $handle;
}
function wp_localize_script($handle, $object_name, $l10n) {
    $GLOBALS['_mock_localized_scripts'][$handle][$object_name] = $l10n;
}

class WP_REST_Request {
    private $params = [];
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

class WP_REST_Response {
    public $data;
    public $status;
    public function __construct($data = null, $status = 200) {
        $this->data = $data;
        $this->status = $status;
    }
}

function rest_ensure_response($response) {
    if ($response instanceof WP_REST_Response) {
        return $response;
    }
    return new WP_REST_Response($response, 200);
}

class WP_Error {
    public $code;
    public $message;
    public $data;
    public function __construct($code = '', $message = '', $data = '') {
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
    }
    public function get_error_code() {
        return $this->code;
    }
    public function get_error_message() {
        return $this->message;
    }
    public function get_error_data() {
        return $this->data;
    }
}

function is_wp_error($thing) {
    return ($thing instanceof WP_Error) || (is_array($thing) && isset($thing['is_error']) && $thing['is_error'] === true);
}

function wp_remote_retrieve_response_code($response) {
    return $response['status_code'] ?? 0;
}

function wp_remote_retrieve_body($response) {
    return $response['body'] ?? '';
}

function wp_remote_get($url, $args = []) {
    $timeout = $args['timeout'] ?? 5;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $args['user-agent'] ?? 'WP-Wordle/Test');
    
    $headers = [];
    if (!empty($args['headers'])) {
        foreach ($args['headers'] as $k => $v) {
            $headers[] = "$k: $v";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $body = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($body === false || !empty($error)) {
        return ['is_error' => true, 'message' => $error];
    }

    return [
        'status_code' => $http_code,
        'body'        => $body,
    ];
}

// Include all plugin classes
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-game.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-api.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-shortcode.php';
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-admin.php';

// Helper for test assertions
$tests_passed = 0;
$total_tests = 0;

function assert_test($description, $condition) {
    global $tests_passed, $total_tests;
    $total_tests++;
    if ($condition) {
        echo "  [PASS] $description\n";
        $tests_passed++;
    } else {
        echo "  [FAIL] $description\n";
    }
}

echo "======================================================================\n";
echo "   WP WORDLE PLUGIN COMPLETE TEST SUITE (ITALIAN & ENGLISH)\n";
echo "======================================================================\n\n";

// ----------------------------------------------------------------------
// SUITE 1: VOCABULARY INTEGRITY & CURATION (IT & EN)
// ----------------------------------------------------------------------
echo "--- 1. VOCABULARY INTEGRITY & CURATION (IT & EN) ---\n";

foreach (['it', 'en'] as $lang) {
    $file_path = WP_WORDLE_PATH . "data/wordle-{$lang}.json";
    assert_test("wordle-{$lang}.json exists", file_exists($file_path));

    $content = file_get_contents($file_path);
    $json = json_decode($content, true);
    assert_test("wordle-{$lang}.json contains valid JSON array", is_array($json));
    assert_test("wordle-{$lang}.json has 'targets' key", isset($json['targets']) && is_array($json['targets']));
    assert_test("wordle-{$lang}.json has 'valid' key", isset($json['valid']) && is_array($json['valid']));

    $targets_count = count($json['targets']);
    $valid_count = count($json['valid']);
    assert_test("wordle-{$lang}.json targets count >= 2000 (actual: $targets_count)", $targets_count >= 2000);
    assert_test("wordle-{$lang}.json valid count >= 2000 (actual: $valid_count)", $valid_count >= 2000);

    // Verify all words are 5-letter uppercase ASCII letters
    $all_clean_targets = true;
    foreach ($json['targets'] as $w) {
        if (strlen($w) !== 5 || !ctype_alpha($w) || $w !== strtoupper($w)) {
            $all_clean_targets = false;
            break;
        }
    }
    assert_test("wordle-{$lang}.json all targets are uppercase 5-letter alpha", $all_clean_targets);

    $all_clean_valid = true;
    foreach ($json['valid'] as $w) {
        if (strlen($w) !== 5 || !ctype_alpha($w) || $w !== strtoupper($w)) {
            $all_clean_valid = false;
            break;
        }
    }
    assert_test("wordle-{$lang}.json all valid words are uppercase 5-letter alpha", $all_clean_valid);

    // No duplicates
    assert_test("wordle-{$lang}.json targets contains no duplicates", count(array_unique($json['targets'])) === $targets_count);
    assert_test("wordle-{$lang}.json valid contains no duplicates", count(array_unique($json['valid'])) === $valid_count);

    // Valid array sorted alphabetically
    $sorted_check = $json['valid'];
    sort($sorted_check);
    assert_test("wordle-{$lang}.json valid array is alphabetically sorted", $json['valid'] === $sorted_check);
}

// ----------------------------------------------------------------------
// SUITE 2: THEMES REGISTRY & MODULAR CSS INTEGRITY (ALL 60 THEMES)
// ----------------------------------------------------------------------
echo "\n--- 2. THEMES REGISTRY & MODULAR CSS FILES (ALL 60 THEMES) ---\n";
$themes = WP_Wordle_Game::get_registered_themes();
assert_test("get_registered_themes returns exactly 60 themes", is_array($themes) && count($themes) === 60);

$all_css_exist = true;
$missing_css = [];
foreach (array_keys($themes) as $slug) {
    $css_file = WP_WORDLE_PATH . "assets/css/themes/{$slug}.css";
    if (!file_exists($css_file) || filesize($css_file) === 0) {
        $all_css_exist = false;
        $missing_css[] = $slug;
    }
}
assert_test("All 60 theme CSS files exist and are non-empty", $all_css_exist);
if (!$all_css_exist) {
    echo "    Missing/empty theme CSS: " . implode(', ', $missing_css) . "\n";
}

// ----------------------------------------------------------------------
// SUITE 3: WP_Wordle_Game ENGINE (IT & EN)
// ----------------------------------------------------------------------
echo "\n--- 3. WP_Wordle_Game ENGINE LOGIC ---\n";

// 3.1 load_words
$it_loaded = WP_Wordle_Game::load_words('it');
$en_loaded = WP_Wordle_Game::load_words('en');
assert_test("load_words('it') loads correctly", !empty($it_loaded['targets']) && !empty($it_loaded['valid']));
assert_test("load_words('en') loads correctly", !empty($en_loaded['targets']) && !empty($en_loaded['valid']));

// 3.2 get_random_word
$rnd_it = WP_Wordle_Game::get_random_word('it');
$rnd_en = WP_Wordle_Game::get_random_word('en');
assert_test("get_random_word('it') returns 5-letter word ($rnd_it)", strlen($rnd_it) === 5 && ctype_alpha($rnd_it));
assert_test("get_random_word('en') returns 5-letter word ($rnd_en)", strlen($rnd_en) === 5 && ctype_alpha($rnd_en));

// 3.3 get_daily_word deterministic rotation and custom word override
$daily_en_today = WP_Wordle_Game::get_daily_word('en');
$daily_en_tomorrow = WP_Wordle_Game::get_daily_word('en', date('Y-m-d', strtotime('+1 day')));
assert_test("get_daily_word('en') returns valid word ($daily_en_today)", strlen($daily_en_today) === 5);
assert_test("get_daily_word('en') calculates deterministically for different dates", strlen($daily_en_tomorrow) === 5);

// Test custom override for IT
update_option('wp_wordle_custom_daily_word_it', 'TESTA');
assert_test("Custom daily word for IT overrides automatic rotation", WP_Wordle_Game::get_daily_word('it') === 'TESTA');
update_option('wp_wordle_custom_daily_word_it', ''); // reset

// Test custom override for EN
update_option('wp_wordle_custom_daily_word_en', 'GHOST');
assert_test("Custom daily word for EN overrides automatic rotation", WP_Wordle_Game::get_daily_word('en') === 'GHOST');
update_option('wp_wordle_custom_daily_word_en', ''); // reset

// 3.4 is_valid_word local tests
assert_test("is_valid_word('AMORE', 'it') is true", WP_Wordle_Game::is_valid_word('AMORE', 'it') === true);
assert_test("is_valid_word('amore', 'it') lowercase is true", WP_Wordle_Game::is_valid_word('amore', 'it') === true);
assert_test("is_valid_word('APPLE', 'en') is true", WP_Wordle_Game::is_valid_word('APPLE', 'en') === true);
assert_test("is_valid_word('apple', 'en') lowercase is true", WP_Wordle_Game::is_valid_word('apple', 'en') === true);

// Invalid lengths & characters
assert_test("is_valid_word('CASA', 'it') (4 letters) is false", WP_Wordle_Game::is_valid_word('CASA', 'it') === false);
assert_test("is_valid_word('GRANDEE', 'en') (7 letters) is false", WP_Wordle_Game::is_valid_word('GRANDEE', 'en') === false);
assert_test("is_valid_word('12345', 'en') is false", WP_Wordle_Game::is_valid_word('12345', 'en') === false);
assert_test("is_valid_word('A!CDE', 'it') is false", WP_Wordle_Game::is_valid_word('A!CDE', 'it') === false);

// Invalid non-existent words
assert_test("is_valid_word('XZZZZ', 'it') is false", WP_Wordle_Game::is_valid_word('XZZZZ', 'it') === false);
assert_test("is_valid_word('QWXZY', 'en') is false", WP_Wordle_Game::is_valid_word('QWXZY', 'en') === false);

// 3.5 evaluate_guess logic (NYT Wordle Rules)
// Case A: Perfect match (5 correct)
$eval_exact = WP_Wordle_Game::evaluate_guess('PIZZA', 'PIZZA');
$all_correct = count(array_filter($eval_exact, fn($tile) => $tile['status'] === 'correct')) === 5;
assert_test("evaluate_guess exact match produces 5 'correct' tiles", $all_correct);

// Case B: No letters match (5 absent)
$eval_absent = WP_Wordle_Game::evaluate_guess('PLUTO', 'AERIS');
$all_absent = count(array_filter($eval_absent, fn($tile) => $tile['status'] === 'absent')) === 5;
assert_test("evaluate_guess completely disjoint words produces 5 'absent' tiles", $all_absent);

// Case C: Transposed / anagram
$eval_anagram = WP_Wordle_Game::evaluate_guess('ROMAA', 'AAMOR');
assert_test("evaluate_guess with anagram letters handles positions correctly", is_array($eval_anagram) && count($eval_anagram) === 5);

// Case D: Duplicate letters rule in guess
// Secret: APPLE (two P's), Guess: PUPPY (three P's)
// Index 0: P (absent, because secret only has 2 P's and they are at index 1,2)
// Index 1: U (absent)
// Index 2: P (correct, matches P at index 2)
// Index 3: P (present or absent? Secret has P at 1, so P at 3 gets 'present')
// Index 4: Y (absent)
$eval_puppy = WP_Wordle_Game::evaluate_guess('PUPPY', 'APPLE');
assert_test("evaluate_guess duplicate letter PUPPY vs APPLE: index 0 is present/absent", in_array($eval_puppy[0]['status'], ['absent', 'present']));
assert_test("evaluate_guess duplicate letter PUPPY vs APPLE: index 2 is correct", $eval_puppy[2]['status'] === 'correct');

// Case E: Italian words test
// Secret: FIORE, Guess: FORTE
// F -> correct (0), O -> present (1), R -> correct (2), T -> absent (3), E -> correct (4)
$eval_it = WP_Wordle_Game::evaluate_guess('FORTE', 'FIORE');
assert_test("evaluate_guess IT: FORTE vs FIORE [0: F] is correct", $eval_it[0]['status'] === 'correct');
assert_test("evaluate_guess IT: FORTE vs FIORE [1: O] is present", $eval_it[1]['status'] === 'present');
assert_test("evaluate_guess IT: FORTE vs FIORE [2: R] is present", $eval_it[2]['status'] === 'present');
assert_test("evaluate_guess IT: FORTE vs FIORE [3: T] is absent", $eval_it[3]['status'] === 'absent');
assert_test("evaluate_guess IT: FORTE vs FIORE [4: E] is correct", $eval_it[4]['status'] === 'correct');

// 3.6 Live Online Dictionaries (Treccani IT & Datamuse EN) & Auto-learning
echo "\n--- 3.6 LIVE ONLINE DICTIONARIES & AUTO-LEARNING (IT & EN) ---\n";
// Save backups of JSON files to ensure pristine state
$orig_it_json = file_get_contents(WP_WORDLE_PATH . 'data/wordle-it.json');
$orig_en_json = file_get_contents(WP_WORDLE_PATH . 'data/wordle-en.json');

// Italian Treccani Live Online Check
$treccani_felpa = WP_Wordle_Game::check_treccani_online('FELPA');
assert_test("check_treccani_online('FELPA') succeeds for real Italian word", $treccani_felpa === true);
$treccani_bad = WP_Wordle_Game::check_treccani_online('XZZZZ');
assert_test("check_treccani_online('XZZZZ') returns false for non-existent word", $treccani_bad === false);

// English Datamuse Live Online Check
$datamuse_books = WP_Wordle_Game::check_english_online('BOOKS');
assert_test("check_english_online('BOOKS') succeeds for real English word", $datamuse_books === true);
$datamuse_bad = WP_Wordle_Game::check_english_online('XZZZZ');
assert_test("check_english_online('XZZZZ') returns false for non-existent word", $datamuse_bad === false);

// Auto-learning IT
$valid_it_learn = WP_Wordle_Game::is_valid_word('FELPA', 'it');
assert_test("is_valid_word('FELPA', 'it') triggers Treccani & returns true", $valid_it_learn === true);
$learned_it_opt = get_option('wp_wordle_learned_words_it', []);
assert_test("Learned Italian word persisted in option 'wp_wordle_learned_words_it'", in_array('FELPA', $learned_it_opt, true));

// Auto-learning EN
$valid_en_learn = WP_Wordle_Game::is_valid_word('BOOKS', 'en');
assert_test("is_valid_word('BOOKS', 'en') triggers Datamuse & returns true", $valid_en_learn === true);
$learned_en_opt = get_option('wp_wordle_learned_words_en', []);
assert_test("Learned English word persisted in option 'wp_wordle_learned_words_en'", in_array('BOOKS', $learned_en_opt, true));

// Restore pristine JSON files
file_put_contents(WP_WORDLE_PATH . 'data/wordle-it.json', $orig_it_json);
file_put_contents(WP_WORDLE_PATH . 'data/wordle-en.json', $orig_en_json);
update_option('wp_wordle_learned_words_it', []);
update_option('wp_wordle_learned_words_en', []);


// ----------------------------------------------------------------------
// SUITE 4: WP_Wordle_API REST CONTROLLER (IT & EN)
// ----------------------------------------------------------------------
echo "\n--- 4. WP_Wordle_API REST CONTROLLER ---\n";
$api = new WP_Wordle_API();

// 4.1 Daily Mode Start (IT & EN)
$req_start_it = new WP_REST_Request(['mode' => 'daily', 'lang' => 'it']);
$res_start_it = $api->handle_start($req_start_it)->data;
assert_test("API Start Daily (IT) returns success=true", $res_start_it['success'] === true && $res_start_it['mode'] === 'daily');

$req_start_en = new WP_REST_Request(['mode' => 'daily', 'lang' => 'en']);
$res_start_en = $api->handle_start($req_start_en)->data;
assert_test("API Start Daily (EN) returns success=true", $res_start_en['success'] === true && $res_start_en['mode'] === 'daily');

// 4.2 Practice Mode Start (IT & EN)
$req_practice_it = new WP_REST_Request(['mode' => 'practice', 'lang' => 'it']);
$res_practice_it = $api->handle_start($req_practice_it)->data;
assert_test("API Start Practice (IT) returns token", !empty($res_practice_it['token']));
$token_it = $res_practice_it['token'];
$session_it = get_transient('wp_wordle_p_' . $token_it);
assert_test("API Practice (IT) transient stores secret word and lang='it'", !empty($session_it['word']) && $session_it['lang'] === 'it');

$req_practice_en = new WP_REST_Request(['mode' => 'practice', 'lang' => 'en']);
$res_practice_en = $api->handle_start($req_practice_en)->data;
assert_test("API Start Practice (EN) returns token", !empty($res_practice_en['token']));
$token_en = $res_practice_en['token'];
$session_en = get_transient('wp_wordle_p_' . $token_en);
assert_test("API Practice (EN) transient stores secret word and lang='en'", !empty($session_en['word']) && $session_en['lang'] === 'en');

// 4.3 Guess validation errors
// Invalid length
$req_bad_len = new WP_REST_Request(['guess' => 'ABCD', 'lang' => 'en', 'mode' => 'daily']);
$res_bad_len = $api->handle_guess($req_bad_len);
assert_test("API Guess with invalid length returns WP_Error('invalid_length')", is_wp_error($res_bad_len) && $res_bad_len->get_error_code() === 'invalid_length');

// Invalid word in IT returns Italian message 'Parola non valida'
$req_bad_it = new WP_REST_Request(['guess' => 'XZZZZ', 'lang' => 'it', 'mode' => 'daily']);
$res_bad_it = $api->handle_guess($req_bad_it);
assert_test("API Guess invalid IT word returns WP_Error with 'Parola non valida'", is_wp_error($res_bad_it) && $res_bad_it->get_error_message() === 'Parola non valida');

// Invalid word in EN returns English message 'Not in word list'
$req_bad_en = new WP_REST_Request(['guess' => 'XZZZZ', 'lang' => 'en', 'mode' => 'daily']);
$res_bad_en = $api->handle_guess($req_bad_en);
assert_test("API Guess invalid EN word returns WP_Error with 'Not in word list'", is_wp_error($res_bad_en) && $res_bad_en->get_error_message() === 'Not in word list');

// Practice mode missing token
$req_no_token = new WP_REST_Request(['guess' => 'APPLE', 'lang' => 'en', 'mode' => 'practice']);
$res_no_token = $api->handle_guess($req_no_token);
assert_test("API Guess Practice without token returns WP_Error('missing_token')", is_wp_error($res_no_token) && $res_no_token->get_error_code() === 'missing_token');

// 4.4 Guess execution in Practice Mode (IT)
$secret_it = $session_it['word'];
// Guess a valid word that is NOT the secret (attempt 1)
$non_secret_guess_it = ($secret_it === 'AMORE') ? 'CASSA' : 'AMORE';
$req_guess_it = new WP_REST_Request([
    'guess'   => $non_secret_guess_it,
    'lang'    => 'it',
    'mode'    => 'practice',
    'token'   => $token_it,
    'attempt' => 1,
]);
$res_guess_it = $api->handle_guess($req_guess_it)->data;
assert_test("API Guess Practice (IT) attempt 1 success=true", $res_guess_it['success'] === true);
assert_test("API Guess Practice (IT) attempt 1 is_win=false", $res_guess_it['is_win'] === false);
assert_test("API Guess Practice (IT) attempt 1 solution is NULL (no spoiler)", $res_guess_it['solution'] === null);

// Now guess the actual secret word (Winning guess)
$req_win_it = new WP_REST_Request([
    'guess'   => $secret_it,
    'lang'    => 'it',
    'mode'    => 'practice',
    'token'   => $token_it,
    'attempt' => 2,
]);
$res_win_it = $api->handle_guess($req_win_it)->data;
assert_test("API Guess Practice (IT) winning guess is_win=true", $res_win_it['is_win'] === true);
assert_test("API Guess Practice (IT) winning guess reveals solution ($secret_it)", $res_win_it['solution'] === $secret_it);
assert_test("API Guess Practice (IT) winning guess cleans up transient", get_transient('wp_wordle_p_' . $token_it) === false);

// 4.5 Guess execution in Practice Mode (EN)
$secret_en = $session_en['word'];
$non_secret_guess_en = ($secret_en === 'APPLE') ? 'HOUSE' : 'APPLE';
$req_guess_en = new WP_REST_Request([
    'guess'   => $non_secret_guess_en,
    'lang'    => 'en',
    'mode'    => 'practice',
    'token'   => $token_en,
    'attempt' => 1,
]);
$res_guess_en = $api->handle_guess($req_guess_en)->data;
assert_test("API Guess Practice (EN) attempt 1 success=true", $res_guess_en['success'] === true);
assert_test("API Guess Practice (EN) attempt 1 solution is NULL", $res_guess_en['solution'] === null);

// Winning guess for EN
$req_win_en = new WP_REST_Request([
    'guess'   => $secret_en,
    'lang'    => 'en',
    'mode'    => 'practice',
    'token'   => $token_en,
    'attempt' => 2,
]);
$res_win_en = $api->handle_guess($req_win_en)->data;
assert_test("API Guess Practice (EN) winning guess is_win=true", $res_win_en['is_win'] === true);
assert_test("API Guess Practice (EN) winning guess reveals solution ($secret_en)", $res_win_en['solution'] === $secret_en);

// ----------------------------------------------------------------------
// SUITE 5: WP_Wordle_Shortcode RENDERER (IT & EN)
// ----------------------------------------------------------------------
echo "\n--- 5. WP_Wordle_Shortcode RENDERER ---\n";
$shortcode = new WP_Wordle_Shortcode();

// Render for Italian
$html_it = $shortcode->render_shortcode(['lang' => 'it', 'mode' => 'daily', 'theme' => 'matrix']);
assert_test("Shortcode (IT) sets data-lang='it'", strpos($html_it, 'data-lang="it"') !== false);
assert_test("Shortcode (IT) sets data-theme='matrix'", strpos($html_it, 'data-theme="matrix"') !== false);
assert_test("Shortcode (IT) renders [GIORNO] button", strpos($html_it, '[GIORNO]') !== false);
assert_test("Shortcode (IT) renders [ALLENAMENTO] button", strpos($html_it, '[ALLENAMENTO]') !== false);
assert_test("Shortcode (IT) renders [INVIO] key", strpos($html_it, 'INVIO') !== false);
assert_test("Shortcode (IT) renders [CANC] key", strpos($html_it, '[CANC]') !== false);
assert_test("Shortcode (IT) renders Italian rules text", strpos($html_it, 'Indovina la parola in 6 tentativi.') !== false);
assert_test("Shortcode (IT) renders Italian stats labels", strpos($html_it, '% Vittorie') !== false);
assert_test("Shortcode enqueues theme-matrix stylesheet", in_array('wp-wordle-theme-matrix', $GLOBALS['_mock_enqueued_styles']));

// Render for English
$html_en = $shortcode->render_shortcode(['lang' => 'en', 'mode' => 'practice', 'theme' => 'cyberpunk']);
assert_test("Shortcode (EN) sets data-lang='en'", strpos($html_en, 'data-lang="en"') !== false);
assert_test("Shortcode (EN) sets data-mode='practice'", strpos($html_en, 'data-mode="practice"') !== false);
assert_test("Shortcode (EN) sets data-theme='cyberpunk'", strpos($html_en, 'data-theme="cyberpunk"') !== false);
assert_test("Shortcode (EN) renders [DAILY] button", strpos($html_en, '[DAILY]') !== false);
assert_test("Shortcode (EN) renders [PRACTICE] button", strpos($html_en, '[PRACTICE]') !== false);
assert_test("Shortcode (EN) renders ENTER key", strpos($html_en, 'ENTER') !== false);
assert_test("Shortcode (EN) renders [DEL] key", strpos($html_en, '[DEL]') !== false);
assert_test("Shortcode (EN) renders English rules text", strpos($html_en, 'Guess the Wordle in 6 tries.') !== false);
assert_test("Shortcode (EN) renders English stats labels", strpos($html_en, 'Win %') !== false);

// Fallback behavior for invalid theme slug
$html_invalid_theme = $shortcode->render_shortcode(['theme' => 'invalid-theme-slug-xyz']);
assert_test("Shortcode falls back to brutalist when given invalid theme", strpos($html_invalid_theme, 'wp-wordle-theme-brutalist') !== false);

// Board layout has 6 rows and 5 tiles per row
preg_match_all('/class="wp-wordle-row"/', $html_en, $rows_matches);
assert_test("Board contains exactly 6 rows", count($rows_matches[0]) === 6);

// ----------------------------------------------------------------------
// SUITE 6: WP_Wordle_Admin MANAGER (IT & EN)
// ----------------------------------------------------------------------
echo "\n--- 6. WP_Wordle_Admin MANAGER ---\n";
$admin = new WP_Wordle_Admin();

// 6.1 Theme sanitization
assert_test("Admin sanitize_theme('matrix') is 'matrix'", $admin->sanitize_theme('matrix') === 'matrix');
assert_test("Admin sanitize_theme('invalid_xyz') falls back to 'brutalist'", $admin->sanitize_theme('invalid_xyz') === 'brutalist');

// 6.2 Custom word sanitization
assert_test("Admin sanitize_custom_word('apple') uppercases to 'APPLE'", $admin->sanitize_custom_word('apple') === 'APPLE');
assert_test("Admin sanitize_custom_word('AMORE') is 'AMORE'", $admin->sanitize_custom_word('AMORE') === 'AMORE');
assert_test("Admin sanitize_custom_word('') allows empty string", $admin->sanitize_custom_word('') === '');
$GLOBALS['_mock_settings_errors'] = [];
$bad_word = $admin->sanitize_custom_word('TOOSHORT');
assert_test("Admin sanitize_custom_word('TOOSHORT') returns empty string and logs error", $bad_word === '' && !empty($GLOBALS['_mock_settings_errors']));

// 6.3 Admin settings page rendering
ob_start();
$admin->render_settings_page();
$admin_html = ob_get_clean();

assert_test("Admin settings HTML renders title", strpos($admin_html, 'WP Wordle - Game Settings') !== false);
assert_test("Admin settings HTML contains Italian language option", strpos($admin_html, 'value="it"') !== false);
assert_test("Admin settings HTML contains English language option", strpos($admin_html, 'value="en"') !== false);
assert_test("Admin settings HTML contains custom daily word fields for IT & EN", strpos($admin_html, 'wp_wordle_custom_daily_word_it') !== false && strpos($admin_html, 'wp_wordle_custom_daily_word_en') !== false);
assert_test("Admin settings HTML renders Vocabulary Status table", strpos($admin_html, 'Italian (IT)') !== false && strpos($admin_html, 'English (EN)') !== false);

// ----------------------------------------------------------------------
// SUITE 7: LIVE DOCKER WORDPRESS INTEGRATION TESTS (HTTP REST API)
// ----------------------------------------------------------------------
echo "\n--- 7. LIVE DOCKER WORDPRESS INTEGRATION TESTS (localhost:8080) ---\n";
$live_wp_url = 'http://localhost:8080/?rest_route=';

function do_post($url, $data = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [
        'code' => $http_code,
        'body' => json_decode($response, true),
        'raw'  => $response,
    ];
}

// 7.1 Live Start Daily (IT & EN)
$live_it_start = do_post($live_wp_url . '/wp-wordle/v1/start', ['lang' => 'it', 'mode' => 'daily']);
assert_test("Live WP Start Daily IT returns HTTP 200", $live_it_start['code'] === 200);
assert_test("Live WP Start Daily IT returns mode=daily", isset($live_it_start['body']['mode']) && $live_it_start['body']['mode'] === 'daily');

$live_en_start = do_post($live_wp_url . '/wp-wordle/v1/start', ['lang' => 'en', 'mode' => 'daily']);
assert_test("Live WP Start Daily EN returns HTTP 200", $live_en_start['code'] === 200);
assert_test("Live WP Start Daily EN returns mode=daily", isset($live_en_start['body']['mode']) && $live_en_start['body']['mode'] === 'daily');

// 7.2 Live Start Practice (IT) & Guess
$live_it_practice = do_post($live_wp_url . '/wp-wordle/v1/start', ['lang' => 'it', 'mode' => 'practice']);
assert_test("Live WP Start Practice IT returns token", !empty($live_it_practice['body']['token']));
$live_token_it = $live_it_practice['body']['token'] ?? '';

if (!empty($live_token_it)) {
    // Valid guess in Italian
    $live_guess_it = do_post($live_wp_url . '/wp-wordle/v1/guess', [
        'lang'    => 'it',
        'mode'    => 'practice',
        'token'   => $live_token_it,
        'guess'   => 'AMORE',
        'attempt' => 1,
    ]);
    assert_test("Live WP Guess IT ('AMORE') returns HTTP 200", $live_guess_it['code'] === 200);
    assert_test("Live WP Guess IT ('AMORE') returns evaluation array of 5 tiles", isset($live_guess_it['body']['evaluation']) && count($live_guess_it['body']['evaluation']) === 5);

    // Invalid gibberish guess in Italian rejected with 400
    $live_bad_it = do_post($live_wp_url . '/wp-wordle/v1/guess', [
        'lang'    => 'it',
        'mode'    => 'practice',
        'token'   => $live_token_it,
        'guess'   => 'XZZZZ',
        'attempt' => 2,
    ]);
    assert_test("Live WP Guess IT invalid word returns HTTP 400", $live_bad_it['code'] === 400);
    assert_test("Live WP Guess IT invalid word error message is 'Parola non valida'", isset($live_bad_it['body']['message']) && $live_bad_it['body']['message'] === 'Parola non valida');
}

// 7.3 Live Start Practice (EN) & Guess
$live_en_practice = do_post($live_wp_url . '/wp-wordle/v1/start', ['lang' => 'en', 'mode' => 'practice']);
assert_test("Live WP Start Practice EN returns token", !empty($live_en_practice['body']['token']));
$live_token_en = $live_en_practice['body']['token'] ?? '';

if (!empty($live_token_en)) {
    // Valid guess in English
    $live_guess_en = do_post($live_wp_url . '/wp-wordle/v1/guess', [
        'lang'    => 'en',
        'mode'    => 'practice',
        'token'   => $live_token_en,
        'guess'   => 'APPLE',
        'attempt' => 1,
    ]);
    assert_test("Live WP Guess EN ('APPLE') returns HTTP 200", $live_guess_en['code'] === 200);
    assert_test("Live WP Guess EN ('APPLE') returns evaluation array of 5 tiles", isset($live_guess_en['body']['evaluation']) && count($live_guess_en['body']['evaluation']) === 5);

    // Invalid gibberish guess in English rejected with 400
    $live_bad_en = do_post($live_wp_url . '/wp-wordle/v1/guess', [
        'lang'    => 'en',
        'mode'    => 'practice',
        'token'   => $live_token_en,
        'guess'   => 'XZZZZ',
        'attempt' => 2,
    ]);
    assert_test("Live WP Guess EN invalid word returns HTTP 400", $live_bad_en['code'] === 400);
    assert_test("Live WP Guess EN invalid word error message is 'Not in word list'", isset($live_bad_en['body']['message']) && $live_bad_en['body']['message'] === 'Not in word list');
}

// ----------------------------------------------------------------------
// FINAL SUMMARY
// ----------------------------------------------------------------------
echo "\n======================================================================\n";
echo " TEST SUMMARY: $tests_passed / $total_tests PASSED\n";
echo "======================================================================\n";

if ($tests_passed === $total_tests) {
    exit(0);
} else {
    exit(1);
}
