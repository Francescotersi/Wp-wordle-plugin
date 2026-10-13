<?php
/**
 * Theme Registry & Modular CSS Files Test
 */

class ThemeRegistryTest extends TestCase {
    public function __construct() {
        parent::__construct('2. Theme Registry & Modular CSS Files');
    }

    public function testThemesCountAndFiles() {
        $themes = WP_Wordle_Game::get_registered_themes();
        $this->assertCount(60, $themes, "get_registered_themes returns exactly 60 themes");

        $all_css_exist = true;
        $all_previews_exist = true;
        $missing_css = [];
        $missing_previews = [];

        foreach ($themes as $slug => $data) {
            $css_path = WP_WORDLE_PATH . "assets/css/themes/{$slug}.css";
            $preview_path = WP_WORDLE_PATH . "assets/themes/theme-{$slug}.png";

            if (!file_exists($css_path) || filesize($css_path) === 0) {
                $all_css_exist = false;
                $missing_css[] = $slug;
            }

            if (!file_exists($preview_path) || filesize($preview_path) === 0) {
                $all_previews_exist = false;
                $missing_previews[] = $slug;
            }
        }

        $this->assertTrue($all_css_exist, "All 60 theme CSS files exist and are non-empty");
        $this->assertTrue($all_previews_exist, "All 60 theme preview thumbnails exist and are non-empty");
    }

    public function testThemeCssVariables() {
        // Sample check critical themes for necessary CSS variables
        $sample_themes = ['brutalist', 'matrix', 'gameboy', 'cyberpunk', 'synthwave'];
        foreach ($sample_themes as $slug) {
            $css_path = WP_WORDLE_PATH . "assets/css/themes/{$slug}.css";
            $content = file_get_contents($css_path);
            $has_bg = strpos($content, '--theme-bg') !== false;
            $has_tile = strpos($content, '--theme-tile-') !== false;
            $this->assertTrue($has_bg && $has_tile, "Theme '{$slug}' defines core CSS custom properties");
        }
    }
}
