<?php
/**
 * Admin Panel & Settings Tests
 */

class AdminTest extends TestCase {
    protected $admin;

    public function __construct() {
        parent::__construct('7. WP_Wordle_Admin Settings & Sanitization');
        $this->admin = new WP_Wordle_Admin();
    }

    public function testThemeSanitization() {
        $this->assertEquals('matrix', $this->admin->sanitize_theme('matrix'), "Admin sanitize_theme('matrix') is 'matrix'");
        $this->assertEquals('brutalist', $this->admin->sanitize_theme('invalid_slug_xyz'), "Admin sanitize_theme('invalid_slug_xyz') falls back to 'brutalist'");
    }

    public function testCustomWordSanitization() {
        $this->assertEquals('APPLE', $this->admin->sanitize_custom_word('apple'), "Admin sanitize_custom_word('apple') uppercases to 'APPLE'");
        $this->assertEquals('AMORE', $this->admin->sanitize_custom_word('AMORE'), "Admin sanitize_custom_word('AMORE') is 'AMORE'");
        $this->assertEquals('', $this->admin->sanitize_custom_word(''), "Admin sanitize_custom_word('') allows empty string");

        $GLOBALS['_mock_settings_errors'] = [];
        $short = $this->admin->sanitize_custom_word('CASA');
        $this->assertEquals('', $short, "Admin sanitize_custom_word('CASA') (4 chars) returns empty string");
        $this->assertTrue(count($GLOBALS['_mock_settings_errors']) > 0, "Admin logs error when word is not 5 letters");
    }

    public function testSettingsPageHtmlRendering() {
        ob_start();
        $this->admin->render_settings_page();
        $html = ob_get_clean();

        $this->assertContains("WP Wordle", $html, "Admin settings HTML renders title");
        $this->assertContains("Italian", $html, "Admin settings HTML contains Italian language option");
        $this->assertContains("English", $html, "Admin settings HTML contains English language option");
        $this->assertContains("wp_wordle_custom_daily_word_it", $html, "Admin settings HTML contains custom daily word fields");
        $this->assertContains("Vocabulary Status", $html, "Admin settings HTML renders Vocabulary Status table");
    }
}
