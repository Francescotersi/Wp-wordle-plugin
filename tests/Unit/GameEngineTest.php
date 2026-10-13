<?php
/**
 * Core Game Engine Logic Tests
 */

class GameEngineTest extends TestCase {
    public function __construct() {
        parent::__construct('3. WP_Wordle_Game Engine Logic');
    }

    public function testWordLoading() {
        $words_it = WP_Wordle_Game::load_words('it');
        $this->assertTrue(isset($words_it['targets']) && count($words_it['targets']) >= 2000, "load_words('it') loads targets correctly");
        $this->assertTrue(isset($words_it['valid']) && count($words_it['valid']) >= 2000, "load_words('it') loads valid list correctly");

        $words_en = WP_Wordle_Game::load_words('en');
        $this->assertTrue(isset($words_en['targets']) && count($words_en['targets']) >= 2000, "load_words('en') loads targets correctly");
        $this->assertTrue(isset($words_en['valid']) && count($words_en['valid']) >= 2000, "load_words('en') loads valid list correctly");
    }

    public function testRandomWord() {
        $rand_it = WP_Wordle_Game::get_random_word('it');
        $this->assertTrue((bool)preg_match('/^[A-Z]{5}$/', $rand_it), "get_random_word('it') returns 5-letter alpha word ({$rand_it})");

        $rand_en = WP_Wordle_Game::get_random_word('en');
        $this->assertTrue((bool)preg_match('/^[A-Z]{5}$/', $rand_en), "get_random_word('en') returns 5-letter alpha word ({$rand_en})");
    }

    public function testDailyWordDeterminism() {
        $daily_1 = WP_Wordle_Game::get_daily_word('en');
        $this->assertTrue((bool)preg_match('/^[A-Z]{5}$/', $daily_1), "get_daily_word('en') returns valid uppercase 5-letter word");

        $daily_2 = WP_Wordle_Game::get_daily_word('en');
        $this->assertEquals($daily_1, $daily_2, "get_daily_word('en') is strictly deterministic for same day");
    }

    public function testDailyWordOverrides() {
        update_option('wp_wordle_custom_daily_word_it', 'FIORE');
        $word_it = WP_Wordle_Game::get_daily_word('it');
        $this->assertEquals('FIORE', $word_it, "Custom daily word for IT overrides automatic rotation");

        update_option('wp_wordle_custom_daily_word_en', 'PLANT');
        $word_en = WP_Wordle_Game::get_daily_word('en');
        $this->assertEquals('PLANT', $word_en, "Custom daily word for EN overrides automatic rotation");

        // Clean up
        delete_transient('wp_wordle_custom_daily_word_it');
        update_option('wp_wordle_custom_daily_word_it', '');
        update_option('wp_wordle_custom_daily_word_en', '');
    }

    public function testWordValidationConstraints() {
        $this->assertTrue(WP_Wordle_Game::is_valid_word('AMORE', 'it'), "is_valid_word('AMORE', 'it') is true");
        $this->assertTrue(WP_Wordle_Game::is_valid_word('amore', 'it'), "is_valid_word('amore', 'it') lowercase is true");
        $this->assertTrue(WP_Wordle_Game::is_valid_word('APPLE', 'en'), "is_valid_word('APPLE', 'en') is true");
        $this->assertTrue(WP_Wordle_Game::is_valid_word('apple', 'en'), "is_valid_word('apple', 'en') lowercase is true");

        $this->assertFalse(WP_Wordle_Game::is_valid_word('CASA', 'it'), "is_valid_word('CASA', 'it') (4 letters) is false");
        $this->assertFalse(WP_Wordle_Game::is_valid_word('GRANDEE', 'en'), "is_valid_word('GRANDEE', 'en') (7 letters) is false");
        $this->assertFalse(WP_Wordle_Game::is_valid_word('12345', 'en'), "is_valid_word('12345', 'en') (numbers) is false");
        $this->assertFalse(WP_Wordle_Game::is_valid_word('A!CDE', 'it'), "is_valid_word('A!CDE', 'it') (symbols) is false");
        $this->assertFalse(WP_Wordle_Game::is_valid_word('XZZZZ', 'it'), "is_valid_word('XZZZZ', 'it') is false");
        $this->assertFalse(WP_Wordle_Game::is_valid_word('QWXZY', 'en'), "is_valid_word('QWXZY', 'en') is false");
    }

    public function testEvaluateGuess() {
        // Exact match
        $eval_exact = WP_Wordle_Game::evaluate_guess('APPLE', 'APPLE');
        $this->assertCount(5, $eval_exact, "evaluate_guess returns 5 elements");
        $all_correct = true;
        foreach ($eval_exact as $tile) {
            if ($tile['status'] !== 'correct') $all_correct = false;
        }
        $this->assertTrue($all_correct, "evaluate_guess exact match produces 5 'correct' tiles");

        // Disjoint words
        $eval_absent = WP_Wordle_Game::evaluate_guess('BLACK', 'WHITE');
        $all_absent = true;
        foreach ($eval_absent as $tile) {
            if ($tile['status'] !== 'absent') $all_absent = false;
        }
        $this->assertTrue($all_absent, "evaluate_guess disjoint words produces 5 'absent' tiles");

        // Anagram letters
        $eval_ana = WP_Wordle_Game::evaluate_guess('ACTOR', 'COATR');
        $this->assertEquals('present', $eval_ana[0]['status'], "Letter A in ACTOR is present");
        $this->assertEquals('present', $eval_ana[1]['status'], "Letter C in ACTOR is present");
        $this->assertEquals('present', $eval_ana[2]['status'], "Letter T in ACTOR is present");
        $this->assertEquals('present', $eval_ana[3]['status'], "Letter O in ACTOR is present");
        $this->assertEquals('correct', $eval_ana[4]['status'], "Letter R in ACTOR is correct");

        // Duplicate letters: Secret PUPPY (3 P's), Guess APPLE (2 P's)
        // APPLE vs PUPPY:
        // A: absent
        // P (pos 1): correct (matches PUPPY pos 1? No, PUPPY is P, U, P, P, Y -> pos 1 is U! So pos 1 is present!)
        // P (pos 2): correct (PUPPY pos 2 is P!)
        // L: absent
        // E: absent
        $eval_dup = WP_Wordle_Game::evaluate_guess('APPLE', 'PUPPY');
        $this->assertEquals('absent', $eval_dup[0]['status'], "A in APPLE vs PUPPY is absent");
        $this->assertEquals('present', $eval_dup[1]['status'], "First P in APPLE vs PUPPY is present");
        $this->assertEquals('correct', $eval_dup[2]['status'], "Second P in APPLE vs PUPPY is correct");

        // Italian: FORTE vs FIORE
        $eval_it = WP_Wordle_Game::evaluate_guess('FORTE', 'FIORE');
        $this->assertEquals('correct', $eval_it[0]['status'], "FORTE vs FIORE [0: F] is correct");
        $this->assertEquals('present', $eval_it[1]['status'], "FORTE vs FIORE [1: O] is present");
        $this->assertEquals('present', $eval_it[2]['status'], "FORTE vs FIORE [2: R] is present");
        $this->assertEquals('absent', $eval_it[3]['status'], "FORTE vs FIORE [3: T] is absent");
        $this->assertEquals('correct', $eval_it[4]['status'], "FORTE vs FIORE [4: E] is correct");
    }
}
