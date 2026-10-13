<?php
/**
 * Standalone Test Script for WP Wordle English Dictionary & Live Online Validation
 */

// Define WordPress mock environment
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}
if (!defined('WP_WORDLE_PATH')) {
    define('WP_WORDLE_PATH', dirname(__DIR__) . '/wp-content/plugins/wp-wordle/');
}
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}
if (!defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}

// In-memory options and transients storage
$GLOBALS['_mock_options'] = [];
$GLOBALS['_mock_transients'] = [];

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

function home_url() {
    return 'http://example.com';
}

function is_wp_error($thing) {
    return is_array($thing) && isset($thing['is_error']) && $thing['is_error'] === true;
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

// Load WP Wordle Game Engine
require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-game.php';

echo "========================================================\n";
echo " WP WORDLE ENGLISH DICTIONARY & LIVE VALIDATION TESTS\n";
echo "========================================================\n\n";

$tests_passed = 0;
$total_tests = 0;

function assert_test($description, $condition) {
    global $tests_passed, $total_tests;
    $total_tests++;
    if ($condition) {
        echo " [PASS] $description\n";
        $tests_passed++;
    } else {
        echo " [FAIL] $description\n";
    }
}

// ----------------------------------------------------------------------
// Test 1: Verify wordle-en.json structure and counts
// ----------------------------------------------------------------------
echo "--- Test Suite 1: File Structure and Vocabulary Curation ---\n";
$json_path = WP_WORDLE_PATH . 'data/wordle-en.json';
assert_test("wordle-en.json exists", file_exists($json_path));

$original_file_content = file_get_contents($json_path);
$data = json_decode($original_file_content, true);

assert_test("wordle-en.json is valid JSON", is_array($data));
assert_test("targets contains exactly 2000 words", isset($data['targets']) && count($data['targets']) === 2000);
assert_test("valid contains exactly 2000 words", isset($data['valid']) && count($data['valid']) === 2000);

$targets_unique = count(array_unique($data['targets'])) === 2000;
assert_test("targets has no duplicate words", $targets_unique);

$valid_unique = count(array_unique($data['valid'])) === 2000;
assert_test("valid has no duplicate words", $valid_unique);

$all_uppercase_alpha = true;
foreach ($data['targets'] as $w) {
    if (strlen($w) !== 5 || !ctype_alpha($w) || $w !== strtoupper($w)) {
        $all_uppercase_alpha = false;
        break;
    }
}
assert_test("All target words are 5-letter uppercase ASCII A-Z", $all_uppercase_alpha);

$valid_sorted = $data['valid'];
$expected_sorted = $valid_sorted;
sort($expected_sorted);
assert_test("valid words array is sorted alphabetically", $valid_sorted === $expected_sorted);

// Check targets vs valid relationship (same set of curated words)
$diff1 = array_diff($data['targets'], $data['valid']);
$diff2 = array_diff($data['valid'], $data['targets']);
assert_test("targets and valid have the exact same 2000 vocabulary words", empty($diff1) && empty($diff2));

// ----------------------------------------------------------------------
// Test 2: WP_Wordle_Game::load_words('en')
// ----------------------------------------------------------------------
echo "\n--- Test Suite 2: WP_Wordle_Game::load_words('en') ---\n";
$loaded = WP_Wordle_Game::load_words('en');
assert_test("load_words('en') returns array with targets and valid", is_array($loaded) && isset($loaded['targets'], $loaded['valid']));
assert_test("load_words('en') targets count is 2000", count($loaded['targets']) === 2000);
assert_test("load_words('en') valid count is 2000", count($loaded['valid']) === 2000);

// ----------------------------------------------------------------------
// Test 3: is_valid_word with known local word
// ----------------------------------------------------------------------
echo "\n--- Test Suite 3: Local Vocabulary Validation ---\n";
assert_test("is_valid_word('APPLE', 'en') is TRUE", WP_Wordle_Game::is_valid_word('APPLE', 'en') === true);
assert_test("is_valid_word('apple', 'en') (lowercase input) is TRUE", WP_Wordle_Game::is_valid_word('apple', 'en') === true);
assert_test("is_valid_word('HOUSE', 'en') is TRUE", WP_Wordle_Game::is_valid_word('HOUSE', 'en') === true);

// ----------------------------------------------------------------------
// Test 4: is_valid_word with invalid gibberish word
// ----------------------------------------------------------------------
echo "\n--- Test Suite 4: Invalid Gibberish Rejection ---\n";
assert_test("is_valid_word('XZZZZ', 'en') is FALSE", WP_Wordle_Game::is_valid_word('XZZZZ', 'en') === false);
assert_test("is_valid_word('QWXZY', 'en') is FALSE", WP_Wordle_Game::is_valid_word('QWXZY', 'en') === false);
assert_test("is_valid_word('12345', 'en') is FALSE", WP_Wordle_Game::is_valid_word('12345', 'en') === false);
assert_test("is_valid_word('SHORT', 'en') with length != 5 is handled properly", WP_Wordle_Game::is_valid_word('LONGWORD', 'en') === false);

// ----------------------------------------------------------------------
// Test 5: Live online check and auto-learning
// ----------------------------------------------------------------------
echo "\n--- Test Suite 5: Live Online Check & Auto-learning ---\n";
// Choose a real English word known NOT to be in the 2000 curated list (e.g. 'BOOKS' or 'TREES')
$unseen_word = 'BOOKS';
assert_test("Precondition: '$unseen_word' is NOT in initial 2000 valid list", !in_array($unseen_word, $data['valid'], true));

// Test check_english_online directly
$online_check = WP_Wordle_Game::check_english_online($unseen_word);
assert_test("check_english_online('$unseen_word') succeeds", $online_check === true);
assert_test("check_english_online transient was cached", get_transient('wp_wordle_dict_en_' . strtolower($unseen_word)) === 'valid');

// Test is_valid_word with unseen word triggers auto-learning
$valid_result = WP_Wordle_Game::is_valid_word($unseen_word, 'en');
assert_test("is_valid_word('$unseen_word', 'en') returns TRUE via online check", $valid_result === true);

// Verify auto-learning persistence
$learned_option = get_option('wp_wordle_learned_words_en', []);
assert_test("Learned word persisted in option 'wp_wordle_learned_words_en'", is_array($learned_option) && in_array($unseen_word, $learned_option, true));

$file_json = json_decode(file_get_contents($json_path), true);
assert_test("Learned word persisted in wordle-en.json file", is_array($file_json['valid']) && in_array($unseen_word, $file_json['valid'], true));

// Test merging of learned words on fresh load_words
// Reset the static cache reflection to simulate a new request
$reflection = new ReflectionClass('WP_Wordle_Game');
$cache_prop = $reflection->getProperty('words_cache');
$cache_prop->setAccessible(true);
$cache_prop->setValue(null, []); // clear in-memory cache

$reloaded = WP_Wordle_Game::load_words('en');
assert_test("Reloaded vocabulary merges learned word from database option", in_array($unseen_word, $reloaded['valid'], true));
assert_test("Reloaded valid count incremented to 2001", count($reloaded['valid']) === 2001);

// ----------------------------------------------------------------------
// Cleanup: Restore pristine 2000 words file
// ----------------------------------------------------------------------
file_put_contents($json_path, $original_file_content);
echo "\n[CLEANUP] wordle-en.json restored to pristine 2000 curated words.\n";

echo "\n========================================================\n";
echo " TEST SUMMARY: $tests_passed / $total_tests PASSED\n";
echo "========================================================\n";

if ($tests_passed === $total_tests) {
    exit(0);
} else {
    exit(1);
}
