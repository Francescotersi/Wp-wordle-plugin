<?php
/**
 * Modern Test Suite Runner for WP Wordle Plugin
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/Unit/VocabularyTest.php';
require_once __DIR__ . '/Unit/ThemeRegistryTest.php';
require_once __DIR__ . '/Unit/GameEngineTest.php';
require_once __DIR__ . '/Unit/DictionaryValidationTest.php';
require_once __DIR__ . '/Unit/RestApiTest.php';
require_once __DIR__ . '/Unit/ShortcodeTest.php';
require_once __DIR__ . '/Unit/AdminTest.php';
require_once __DIR__ . '/Integration/DockerWordPressTest.php';

$suites = [
    'vocabulary' => new VocabularyTest(),
    'themes'     => new ThemeRegistryTest(),
    'engine'     => new GameEngineTest(),
    'dictionary' => new DictionaryValidationTest(),
    'api'        => new RestApiTest(),
    'shortcode'  => new ShortcodeTest(),
    'admin'      => new AdminTest(),
    'docker'     => new DockerWordPressTest(),
];

$filter = $argv[1] ?? 'all';

echo "\n\033[1;36m======================================================================\033[0m\n";
echo "\033[1;36m   WP WORDLE PLUGIN AUTOMATED TEST SUITE (MODULAR ARCHITECTURE)\033[0m\n";
echo "\033[1;36m======================================================================\033[0m\n";

$start_time = microtime(true);
$total_passed = 0;
$total_failed = 0;
$total_skipped = 0;

foreach ($suites as $key => $suite) {
    if ($filter !== 'all' && stripos($key, $filter) === false) {
        continue;
    }
    $suite->run();
    $total_passed += $suite->passed;
    $total_failed += $suite->failed;
    $total_skipped += $suite->skipped;
}

$elapsed = round(microtime(true) - $start_time, 2);

echo "\n\033[1;36m======================================================================\033[0m\n";
if ($total_failed === 0) {
    echo "\033[1;32m  ✔ TEST RUN SUCCESSFUL: {$total_passed} assertions passed ({$total_skipped} skipped) in {$elapsed}s\033[0m\n";
} else {
    echo "\033[1;31m  ✖ TEST RUN FAILED: {$total_failed} assertions failed, {$total_passed} passed in {$elapsed}s\033[0m\n";
}
echo "\033[1;36m======================================================================\033[0m\n\n";

exit($total_failed === 0 ? 0 : 1);
