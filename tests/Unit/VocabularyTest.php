<?php
/**
 * Vocabulary Integrity & Format Tests
 */

class VocabularyTest extends TestCase {
    public function __construct() {
        parent::__construct('1. Vocabulary Integrity (Italian & English)');
    }

    public function testItalianVocabulary() {
        $file_it = WP_WORDLE_PATH . 'data/wordle-it.json';
        $this->assertTrue(file_exists($file_it), "wordle-it.json exists on disk");

        $raw_it = file_get_contents($file_it);
        $json_it = json_decode($raw_it, true);
        $this->assertTrue(is_array($json_it), "wordle-it.json is valid JSON array");
        $this->assertTrue(isset($json_it['targets']) && is_array($json_it['targets']), "wordle-it.json has 'targets' array");
        $this->assertTrue(isset($json_it['valid']) && is_array($json_it['valid']), "wordle-it.json has 'valid' array");

        $targets_it = $json_it['targets'];
        $valid_it = $json_it['valid'];

        $this->assertTrue(count($targets_it) >= 2000, "wordle-it.json targets count >= 2000 (actual: " . count($targets_it) . ")");
        $this->assertTrue(count($valid_it) >= 2000, "wordle-it.json valid count >= 2000 (actual: " . count($valid_it) . ")");

        $all_targets_valid = true;
        foreach ($targets_it as $w) {
            if (!preg_match('/^[A-Z]{5}$/', $w)) {
                $all_targets_valid = false;
                break;
            }
        }
        $this->assertTrue($all_targets_valid, "wordle-it.json all targets are uppercase 5-letter alpha");

        $all_valid_words_ok = true;
        foreach ($valid_it as $w) {
            if (!preg_match('/^[A-Z]{5}$/', $w)) {
                $all_valid_words_ok = false;
                break;
            }
        }
        $this->assertTrue($all_valid_words_ok, "wordle-it.json all valid words are uppercase 5-letter alpha");

        $this->assertEquals(count($targets_it), count(array_unique($targets_it)), "wordle-it.json targets has no duplicates");
        $this->assertEquals(count($valid_it), count(array_unique($valid_it)), "wordle-it.json valid has no duplicates");

        $sorted_valid = $valid_it;
        sort($sorted_valid);
        $this->assertEquals($sorted_valid, $valid_it, "wordle-it.json valid array is sorted alphabetically");
    }

    public function testEnglishVocabulary() {
        $file_en = WP_WORDLE_PATH . 'data/wordle-en.json';
        $this->assertTrue(file_exists($file_en), "wordle-en.json exists on disk");

        $raw_en = file_get_contents($file_en);
        $json_en = json_decode($raw_en, true);
        $this->assertTrue(is_array($json_en), "wordle-en.json is valid JSON array");
        $this->assertTrue(isset($json_en['targets']) && is_array($json_en['targets']), "wordle-en.json has 'targets' array");
        $this->assertTrue(isset($json_en['valid']) && is_array($json_en['valid']), "wordle-en.json has 'valid' array");

        $targets_en = $json_en['targets'];
        $valid_en = $json_en['valid'];

        $this->assertTrue(count($targets_en) >= 2000, "wordle-en.json targets count >= 2000 (actual: " . count($targets_en) . ")");
        $this->assertTrue(count($valid_en) >= 2000, "wordle-en.json valid count >= 2000 (actual: " . count($valid_en) . ")");

        $all_targets_valid = true;
        foreach ($targets_en as $w) {
            if (!preg_match('/^[A-Z]{5}$/', $w)) {
                $all_targets_valid = false;
                break;
            }
        }
        $this->assertTrue($all_targets_valid, "wordle-en.json all targets are uppercase 5-letter alpha");

        $all_valid_words_ok = true;
        foreach ($valid_en as $w) {
            if (!preg_match('/^[A-Z]{5}$/', $w)) {
                $all_valid_words_ok = false;
                break;
            }
        }
        $this->assertTrue($all_valid_words_ok, "wordle-en.json all valid words are uppercase 5-letter alpha");

        $this->assertEquals(count($targets_en), count(array_unique($targets_en)), "wordle-en.json targets has no duplicates");
        $this->assertEquals(count($valid_en), count(array_unique($valid_en)), "wordle-en.json valid has no duplicates");

        $sorted_valid = $valid_en;
        sort($sorted_valid);
        $this->assertEquals($sorted_valid, $valid_en, "wordle-en.json valid array is sorted alphabetically");
    }
}
