<?php
/**
 * Online Dictionary Integration & Auto-learning Tests
 */

class DictionaryValidationTest extends TestCase {
    public function __construct() {
        parent::__construct('4. Online Dictionaries & Auto-Learning (Treccani & Datamuse)');
    }

    public function testTreccaniOnlineValidation() {
        $real_it = WP_Wordle_Game::check_treccani_online('FELPA');
        $this->assertTrue($real_it, "check_treccani_online('FELPA') succeeds for real Italian word");

        $fake_it = WP_Wordle_Game::check_treccani_online('XZZZZ');
        $this->assertFalse($fake_it, "check_treccani_online('XZZZZ') returns false for non-existent word");
    }

    public function testEnglishDatamuseOnlineValidation() {
        $real_en = WP_Wordle_Game::check_english_online('BOOKS');
        $this->assertTrue($real_en, "check_english_online('BOOKS') succeeds for real English word");

        $fake_en = WP_Wordle_Game::check_english_online('XZZZZ');
        $this->assertFalse($fake_en, "check_english_online('XZZZZ') returns false for non-existent word");
    }

    public function testAutoLearningPersistence() {
        // Reset learned words
        update_option('wp_wordle_learned_words_it', []);
        update_option('wp_wordle_learned_words_en', []);

        // Test auto-learning persistence for Italian
        WP_Wordle_Game::add_valid_word('TESTA', 'it');
        $learned_it = get_option('wp_wordle_learned_words_it', []);
        $this->assertTrue(in_array('TESTA', $learned_it, true), "add_valid_word persists word in option 'wp_wordle_learned_words_it'");

        // Test auto-learning persistence for English
        WP_Wordle_Game::add_valid_word('HELLO', 'en');
        $learned_en = get_option('wp_wordle_learned_words_en', []);
        $this->assertTrue(in_array('HELLO', $learned_en, true), "add_valid_word persists word in option 'wp_wordle_learned_words_en'");
    }
}
