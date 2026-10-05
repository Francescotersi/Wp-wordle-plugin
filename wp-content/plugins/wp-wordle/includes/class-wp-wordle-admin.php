<?php
/**
 * WP Wordle Admin Panel Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Wordle_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_menu_page() {
        add_options_page(
            __('WP Wordle Settings', 'wp-wordle'),
            'WP Wordle',
            'manage_options',
            'wp-wordle',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Registra le opzioni nel database tramite le Settings API di WordPress
     */
    public function register_settings() {
        register_setting('wp_wordle_settings_group', 'wp_wordle_default_lang', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'it',
        ]);

        register_setting('wp_wordle_settings_group', 'wp_wordle_custom_daily_word_it', [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_custom_word'],
            'default' => '',
        ]);

        register_setting('wp_wordle_settings_group', 'wp_wordle_custom_daily_word_en', [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_custom_word'],
            'default' => '',
        ]);
    }

    /**
     * Sanitize custom word (exactly 5 letters, uppercase)
     */
    public function sanitize_custom_word($word) {
        $word = strtoupper(trim(sanitize_text_field($word)));
        if (!empty($word) && (strlen($word) !== 5 || !ctype_alpha($word))) {
            add_settings_error(
                'wp_wordle_custom_word',
                'invalid_word',
                __('The custom word must be exactly 5 alphabetical letters.', 'wp-wordle')
            );
            return '';
        }
        return $word;
    }

    /**
     * Render the HTML settings page in WordPress admin
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $default_lang = get_option('wp_wordle_default_lang', 'it');
        $custom_word_it = get_option('wp_wordle_custom_daily_word_it', '');
        $custom_word_en = get_option('wp_wordle_custom_daily_word_en', '');

        $it_data = WP_Wordle_Game::load_words('it');
        $en_data = WP_Wordle_Game::load_words('en');
        $today_word_it = WP_Wordle_Game::get_daily_word('it');
        $today_word_en = WP_Wordle_Game::get_daily_word('en');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Wordle - Game Settings', 'wp-wordle'); ?></h1>
            <p><?php esc_html_e('Manage Wordle game settings for your site.', 'wp-wordle'); ?></p>

            <!-- Box informativo Shortcode -->
            <div class="notice notice-info" style="padding: 12px; margin: 20px 0;">
                <h3 style="margin-top: 0;"><?php esc_html_e('How to display the game on your site', 'wp-wordle'); ?></h3>
                <p><?php esc_html_e('Place this shortcode on any page or post:', 'wp-wordle'); ?></p>
                <code style="font-size: 1.1rem; padding: 4px 8px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">[wp_wordle]</code>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #555;">
                    <?php esc_html_e('Advanced options:', 'wp-wordle'); ?>
                    <code>[wp_wordle lang="en"]</code> (inglese) &bull;
                    <code>[wp_wordle mode="practice"]</code> (avvio in modalità allenamento)
                </p>
            </div>

            <!-- Settings Form -->
            <form method="post" action="options.php">
                <?php
                settings_fields('wp_wordle_settings_group');
                do_settings_sections('wp_wordle_settings_group');
                ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="wp_wordle_default_lang"><?php esc_html_e('Default Language', 'wp-wordle'); ?></label>
                        </th>
                        <td>
                            <select name="wp_wordle_default_lang" id="wp_wordle_default_lang">
                                <option value="it" <?php selected($default_lang, 'it'); ?>><?php esc_html_e('Italian', 'wp-wordle'); ?></option>
                                <option value="en" <?php selected($default_lang, 'en'); ?>><?php esc_html_e('English', 'wp-wordle'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="wp_wordle_custom_daily_word_it"><?php esc_html_e('Force Daily Word (Italian)', 'wp-wordle'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="wp_wordle_custom_daily_word_it" id="wp_wordle_custom_daily_word_it" value="<?php echo esc_attr($custom_word_it); ?>" maxlength="5" style="text-transform: uppercase; width: 120px;" placeholder="<?php echo esc_attr($today_word_it); ?>">
                            <p class="description"><?php esc_html_e('Leave empty for automatic daily change. If you enter a 5-letter word, it will be used.', 'wp-wordle'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="wp_wordle_custom_daily_word_en"><?php esc_html_e('Force Daily Word (English)', 'wp-wordle'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="wp_wordle_custom_daily_word_en" id="wp_wordle_custom_daily_word_en" value="<?php echo esc_attr($custom_word_en); ?>" maxlength="5" style="text-transform: uppercase; width: 120px;" placeholder="<?php echo esc_attr($today_word_en); ?>">
                            <p class="description"><?php esc_html_e('Leave empty for automatic daily change.', 'wp-wordle'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'wp-wordle')); ?>
            </form>

            <hr style="margin: 30px 0;">

            <!-- Dictionaries Status -->
            <h3><?php esc_html_e('Vocabulary Status & Current Word', 'wp-wordle'); ?></h3>
            <table class="widefat striped" style="max-width: 600px;">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Language', 'wp-wordle'); ?></th>
                        <th><?php esc_html_e('Target Words', 'wp-wordle'); ?></th>
                        <th><?php esc_html_e('Guess Vocabulary', 'wp-wordle'); ?></th>
                        <th><?php esc_html_e('Today\'s Word (Spoiler)', 'wp-wordle'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Italian (IT)</strong></td>
                        <td><?php echo esc_html(count($it_data['targets'])); ?></td>
                        <td><?php echo esc_html(count($it_data['valid'])); ?></td>
                        <td><code><?php echo esc_html($today_word_it); ?></code></td>
                    </tr>
                    <tr>
                        <td><strong>English (EN)</strong></td>
                        <td><?php echo esc_html(count($en_data['targets'])); ?></td>
                        <td><?php echo esc_html(count($en_data['valid'])); ?></td>
                        <td><code><?php echo esc_html($today_word_en); ?></code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}