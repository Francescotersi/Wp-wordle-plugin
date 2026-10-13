<?php
/**
 * Shortcode Renderer & Frontend Component Tests
 */

class ShortcodeTest extends TestCase {
    protected $shortcode;

    public function __construct() {
        parent::__construct('6. WP_Wordle_Shortcode Renderer');
        $this->shortcode = new WP_Wordle_Shortcode();
    }

    public function testItalianShortcodeRendering() {
        $html_it = $this->shortcode->render_shortcode([
            'theme' => 'matrix',
            'lang'  => 'it',
            'mode'  => 'daily',
        ]);

        $this->assertContains("data-lang=\"it\"", $html_it, "Shortcode (IT) sets data-lang='it'");
        $this->assertContains("data-theme=\"matrix\"", $html_it, "Shortcode (IT) sets data-theme='matrix'");
        $this->assertContains("GIORNO", $html_it, "Shortcode (IT) renders [GIORNO] button");
        $this->assertContains("ALLENAMENTO", $html_it, "Shortcode (IT) renders [ALLENAMENTO] button");
        $this->assertContains("INVIO", $html_it, "Shortcode (IT) renders [INVIO] key");
        $this->assertContains("[CANC]", $html_it, "Shortcode (IT) renders [CANC] key");
        $this->assertContains("Indovina la parola", $html_it, "Shortcode (IT) renders Italian rules text");
        $this->assertContains("Giocate", $html_it, "Shortcode (IT) renders Italian stats labels");
        $this->assertTrue(isset($GLOBALS['_mock_enqueued_styles']['wp-wordle-theme-matrix']), "Shortcode enqueues theme-matrix stylesheet");
    }

    public function testEnglishShortcodeRendering() {
        $html_en = $this->shortcode->render_shortcode([
            'theme' => 'cyberpunk',
            'lang'  => 'en',
            'mode'  => 'practice',
        ]);

        $this->assertContains("data-lang=\"en\"", $html_en, "Shortcode (EN) sets data-lang='en'");
        $this->assertContains("data-mode=\"practice\"", $html_en, "Shortcode (EN) sets data-mode='practice'");
        $this->assertContains("data-theme=\"cyberpunk\"", $html_en, "Shortcode (EN) sets data-theme='cyberpunk'");
        $this->assertContains("DAILY", $html_en, "Shortcode (EN) renders [DAILY] button");
        $this->assertContains("PRACTICE", $html_en, "Shortcode (EN) renders [PRACTICE] button");
        $this->assertContains("ENTER", $html_en, "Shortcode (EN) renders ENTER key");
        $this->assertContains("[DEL]", $html_en, "Shortcode (EN) renders [DEL] key");
        $this->assertContains("Guess the Wordle", $html_en, "Shortcode (EN) renders English rules text");
        $this->assertContains("Played", $html_en, "Shortcode (EN) renders English stats labels");
    }

    public function testFallbackThemeAndBoardLayout() {
        $html_fallback = $this->shortcode->render_shortcode(['theme' => 'invalid_theme_xyz']);
        $this->assertContains("data-theme=\"brutalist\"", $html_fallback, "Shortcode falls back to brutalist when given invalid theme");

        // Board layout check: exactly 6 wp-wordle-row instances
        $row_count = substr_count($html_fallback, 'class="wp-wordle-row"');
        $this->assertEquals(6, $row_count, "Wordle board contains exactly 6 rows");
    }
}
