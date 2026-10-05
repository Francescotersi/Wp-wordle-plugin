<?php
/**
 * Handles the [wp_wordle] shortcode on the frontend
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Wordle_Shortcode {

    public function __construct() {
        add_shortcode('wp_wordle', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    public function register_assets() {
        wp_register_style(
            'wp-wordle-style',
            WP_WORDLE_URL . 'assets/css/wordle.css',
            [],
            WP_WORDLE_VERSION
        );

        wp_register_script(
            'wp-wordle-script',
            WP_WORDLE_URL . 'assets/js/wordle.js',
            [],
            WP_WORDLE_VERSION,
            true // Load in the footer
        );
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts([
            'mode' => 'daily',
            'lang' => 'it',
        ], $atts, 'wp_wordle');

        wp_enqueue_style('wp-wordle-style');
        wp_enqueue_script('wp-wordle-script');

        wp_localize_script('wp-wordle-script', 'wpWordleConfig', [
            'apiUrl' => esc_url_raw(rest_url('wp-wordle/v1/')),
            'nonce' => wp_create_nonce('wp_rest'),
            'mode' => sanitize_text_field($atts['mode']),
            'lang' => sanitize_text_field($atts['lang']),
            'i18n' => [
                'winTitle' => __('Splendid!', 'wp-wordle'),
                'lossTitle' => __('Game Over', 'wp-wordle'),
                'solutionLabel' => __('The word was:', 'wp-wordle'),
                'copied' => __('Copied to clipboard', 'wp-wordle'),
                'notEnoughLetters' => __('Not enough letters', 'wp-wordle'),
                'invalidWord' => __('Not in word list', 'wp-wordle'),
                'share' => __('Share', 'wp-wordle'),
                'newGame' => __('Play Again', 'wp-wordle'),
            ],
        ]);

        ob_start();
        ?>
        <div class="wp-wordle-wrapper" id="wp-wordle-app"
             data-api="<?php echo esc_url( rest_url( 'wp-wordle/v1/' ) ); ?>" 
             data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>" 
             data-mode="<?php echo esc_attr( $atts['mode'] ); ?>" 
             data-lang="<?php echo esc_attr( $atts['lang'] ); ?>">
            
            <header class="wp-wordle-header">
                <div class="wp-wordle-modes">
                    <button type="button" class="wp-wordle-mode-btn active" data-mode="daily">
                       <?php esc_html_e('Word of the Day', 'wp-wordle'); ?>
                    </button>
                    <button type="button" class="wp-wordle-mode-btn" data-mode="practice">
                        <?php esc_html_e('Practice', 'wp-wordle'); ?>
                    </button>
                </div>

                <div class="wp-wordle-actions">
                    <button type="button" class="wp-wordle-icon-btn" id="wp-wordle-help-btn" title="<?php esc_attr_e('How to play', 'wp-wordle'); ?>">❓</button>
                    <button type="button" class="wp-wordle-icon-btn" id="wp-wordle-stats-btn" title="<?php esc_attr_e('Statistics', 'wp-wordle'); ?>">📊</button>
                </div>
            </header>

            <div class="wp-wordle-toast-container" id="wp-wordle-toast"></div>

            <main class="wp-wordle-board-container">
                <div class="wp-wordle-board" id="wp-wordle-board">
                    <?php for ($row = 0; $row < 6; $row++) : ?>
                        <div class="wp-wordle-row" data-row="<?php echo esc_attr($row); ?>">
                            <?php for ($col = 0; $col < 5; $col++) : ?>
                                <div class="wp-wordle-tile" data-col="<?php echo esc_attr($col); ?>"></div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </main>

            <!-- On-screen virtual keyboard -->
            <footer class="wp-wordle-keyboard" id="wp-wordle-keyboard">
                <div class="wp-wordle-kb-row">
                    <?php foreach ( [ 'Q', 'W', 'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P' ] as $key ) : ?>
                        <button type="button" class="wp-wordle-key" data-key="<?php echo esc_attr($key); ?>"><?php echo esc_html( $key ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="wp-wordle-kb-row">
                    <?php foreach ( [ 'A', 'S', 'D', 'F', 'G', 'H', 'J', 'K', 'L' ] as $key ) : ?>
                        <button type="button" class="wp-wordle-key" data-key="<?php echo esc_attr($key); ?>"><?php echo esc_html( $key ); ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="wp-wordle-kb-row">
                    <button type="button" class="wp-wordle-key wp-wordle-key-action" data-key="ENTER"><?php esc_html_e( 'ENTER', 'wp-wordle' ); ?></button>
                    <?php foreach ( [ 'Z', 'X', 'C', 'V', 'B', 'N', 'M' ] as $key ) : ?>
                        <button type="button" class="wp-wordle-key" data-key="<?php echo esc_attr($key); ?>"><?php echo esc_html( $key ); ?></button>
                    <?php endforeach; ?>
                    <button type="button" class="wp-wordle-key wp-wordle-key-action" data-key="BACKSPACE">⌫</button>
                </div>
            </footer>

            <div class="wp-wordle-modal-backdrop" id="wp-wordle-modal-help" style="display: none;">
                <div class="wp-wordle-modal">
                    <button type="button" class="wp-wordle-modal-close" data-close="help">&times;</button>
                    <h3><?php esc_html_e('How to Play', 'wp-wordle'); ?></h3>
                    <p><?php esc_html_e('Guess the Wordle in 6 tries.', 'wp-wordle'); ?></p>
                    <p><?php esc_html_e('Each guess must be a valid 5-letter word. After each guess, the color of the tiles will change to show how close your guess was:', 'wp-wordle'); ?></p>
                    
                    <div class="wp-wordle-help-examples">
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile correct">P</span>
                            <span><?php esc_html_e('The letter is in the word and in the correct spot.', 'wp-wordle'); ?></span>
                        </div>
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile present">O</span>
                            <span><?php esc_html_e('The letter is in the word but in the wrong spot.', 'wp-wordle'); ?></span>
                        </div>
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile absent">R</span>
                            <span><?php esc_html_e('The letter is not in the word in any spot.', 'wp-wordle'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wp-wordle-modal-backdrop" id="wp-wordle-modal-stats" style="display: none;">
                <div class="wp-wordle-modal">
                    <button type="button" class="wp-wordle-modal-close" data-close="stats">&times;</button>
                    <h3 id="wp-wordle-stats-title"><?php esc_html_e('Statistics', 'wp-wordle'); ?></h3>
                    
                    <div id="wp-wordle-solution-container" style="display:none; margin: 15px 0;">
                        <span class="wp-wordle-solution-label"><?php esc_html_e('The word was:', 'wp-wordle'); ?></span>
                        <strong class="wp-wordle-solution-word" id="wp-wordle-solution-text"></strong>
                    </div>

                    <div class="wp-wordle-stats-grid">
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-played">0</span>
                            <span class="wp-wordle-stat-label"><?php esc_html_e('Played', 'wp-wordle'); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-winrate">0%</span>
                            <span class="wp-wordle-stat-label"><?php esc_html_e('Win %', 'wp-wordle'); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-streak">0</span>
                            <span class="wp-wordle-stat-label"><?php esc_html_e('Current Streak', 'wp-wordle'); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-maxstreak">0</span>
                            <span class="wp-wordle-stat-label"><?php esc_html_e('Max Streak', 'wp-wordle'); ?></span>
                        </div>
                    </div>

                    <div class="wp-wordle-stats-actions">
                        <button type="button" class="wp-wordle-btn wp-wordle-btn-share" id="wp-wordle-btn-share" style="display: none;">
                            <?php esc_html_e('Share', 'wp-wordle'); ?>
                        </button>
                        <button type="button" class="wp-wordle-btn wp-wordle-btn-new" id="wp-wordle-btn-new">
                            <?php esc_html_e('Play Again', 'wp-wordle'); ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}