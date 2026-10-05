<?php
/**
 * Plugin Name: Wp Wordle game
 * Description: Add to your site a simple Worlde game
 * Version: 1.0.0
 * Author: Francesco Tersillo
 */

// Blocco di sicurezza: impedisce l'esecuzione diretta del file via URL
if (!defined('ABSPATH')) {
    exit;
}

define('WP_WORDLE_VERSION', '1.0.0');
define('WP_WORDLE_PATH', plugin_dir_path( __FILE__ ));
define('WP_WORDLE_URL', plugin_dir_url( __FILE__ ));

function wp_wordle_init() {
    require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-game.php';
    require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-api.php';
    require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-shortcode.php';

    if (is_admin()) {
        require_once WP_WORDLE_PATH . 'includes/class-wp-wordle-admin.php';
        new WP_Wordle_Admin();
    }

    new WP_Wordle_API();
    new WP_Wordle_Shortcode();
}

add_action('plugins_loaded', 'wp_wordle_init');