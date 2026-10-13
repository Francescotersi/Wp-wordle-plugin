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
        $default_theme = get_option('wp_wordle_default_theme', 'brutalist');
        $default_lang = get_option('wp_wordle_default_lang', 'en');
        if (!in_array($default_lang, ['it', 'en'], true)) {
            $default_lang = 'en';
        }
        $registered_themes = WP_Wordle_Game::get_registered_themes();
        if (!isset($registered_themes[$default_theme])) {
            $default_theme = 'brutalist';
        }

        $atts = shortcode_atts([
            'mode' => 'daily',
            'lang' => $default_lang,
            'theme' => $default_theme,
        ], $atts, 'wp_wordle');

        $theme = sanitize_key($atts['theme']);
        if (!isset($registered_themes[$theme])) {
            $theme = $default_theme;
        }
        $atts['theme'] = $theme;
        $atts['lang'] = in_array($atts['lang'], ['it', 'en'], true) ? $atts['lang'] : $default_lang;
        $atts['mode'] = in_array($atts['mode'], ['daily', 'practice'], true) ? $atts['mode'] : 'daily';

        $lang = $atts['lang'];

        // UI String dictionaries (English default, Italian alternative)
        $translations = [
            'en' => [
                'daily'           => 'DAILY',
                'practice'        => 'PRACTICE',
                'how_to_play'     => 'How to play',
                'statistics'      => 'Statistics',
                'close'           => 'Close',
                'rules_title'     => 'How to Play',
                'rules_intro'     => 'Guess the Wordle in 6 tries.',
                'rules_detail'    => 'Each guess must be a valid 5-letter word. After each guess, the color of the tiles will change to show how close your guess was:',
                'ex_correct'      => 'The letter is in the word and in the correct spot.',
                'ex_present'      => 'The letter is in the word but in the wrong spot.',
                'ex_absent'       => 'The letter is not in the word in any spot.',
                'stats_title'     => 'Statistics',
                'solution_label'  => 'The word was:',
                'played'          => 'Played',
                'win_rate'        => 'Win %',
                'current_streak'  => 'Current Streak',
                'max_streak'      => 'Max Streak',
                'share'           => 'Share',
                'play_again'      => 'Play Again',
                'enter'           => 'ENTER',
                'backspace'       => 'Backspace',
                'backspace_key'   => '[DEL]',
                'win_toast'       => 'SPLENDID!',
                'loss_toast'      => 'GAME OVER',
                'copied'          => 'Copied to clipboard',
                'not_enough'      => 'Not enough letters',
                'invalid_word'    => 'Not in word list',
                'connection_err'  => 'Connection error',
            ],
            'it' => [
                'daily'           => 'GIORNO',
                'practice'        => 'ALLENAMENTO',
                'how_to_play'     => 'Come giocare',
                'statistics'      => 'Statistiche',
                'close'           => 'Chiudi',
                'rules_title'     => 'Come giocare',
                'rules_intro'     => 'Indovina la parola in 6 tentativi.',
                'rules_detail'    => 'Ogni tentativo deve essere una parola valida di 5 lettere. Dopo ogni tentativo, il colore dei riquadri cambierà per indicare la correttezza delle lettere:',
                'ex_correct'      => 'La lettera è nella parola e nella posizione corretta.',
                'ex_present'      => 'La lettera è nella parola ma nella posizione errata.',
                'ex_absent'       => 'La lettera non è presente nella parola in nessuna posizione.',
                'stats_title'     => 'Statistiche',
                'solution_label'  => 'La parola era:',
                'played'          => 'Giocate',
                'win_rate'        => '% Vittorie',
                'current_streak'  => 'Serie attuale',
                'max_streak'      => 'Serie max',
                'share'           => 'Condividi',
                'play_again'      => 'Gioca ancora',
                'enter'           => 'INVIO',
                'backspace'       => 'Cancella',
                'backspace_key'   => '[CANC]',
                'win_toast'       => 'SPLENDIDO!',
                'loss_toast'      => 'PARTITA FINITA',
                'copied'          => 'Copiato negli appunti',
                'not_enough'      => 'Lettere insufficienti',
                'invalid_word'    => 'Parola non valida',
                'connection_err'  => 'Errore di connessione',
            ],
        ];

        $t = isset($translations[$lang]) ? $translations[$lang] : $translations['en'];

        wp_enqueue_style('wp-wordle-style');

        // Carica in modo modulare solo il foglio di stile del tema attivo
        $theme_css_file = WP_WORDLE_PATH . 'assets/css/themes/' . $theme . '.css';
        if (file_exists($theme_css_file)) {
            wp_enqueue_style(
                'wp-wordle-theme-' . $theme,
                WP_WORDLE_URL . 'assets/css/themes/' . $theme . '.css',
                ['wp-wordle-style'],
                WP_WORDLE_VERSION
            );
        }

        wp_enqueue_script('wp-wordle-script');

        wp_localize_script('wp-wordle-script', 'wpWordleConfig', [
            'apiUrl' => esc_url_raw(rest_url('wp-wordle/v1/')),
            'nonce'  => wp_create_nonce('wp_rest'),
            'mode'   => sanitize_text_field($atts['mode']),
            'lang'   => sanitize_text_field($atts['lang']),
            'theme'  => sanitize_text_field($atts['theme']),
            'i18n'   => [
                'winTitle'         => $t['win_toast'],
                'lossTitle'        => $t['loss_toast'],
                'solutionLabel'    => $t['solution_label'],
                'copied'           => $t['copied'],
                'notEnoughLetters' => $t['not_enough'],
                'invalidWord'      => $t['invalid_word'],
                'connectionError'  => $t['connection_err'],
                'share'            => $t['share'],
                'newGame'          => $t['play_again'],
            ],
        ]);

        ob_start();
        ?>
        <div class="wp-wordle-wrapper wp-wordle-theme-<?php echo esc_attr( $atts['theme'] ); ?>" id="wp-wordle-app"
             data-api="<?php echo esc_url( rest_url( 'wp-wordle/v1/' ) ); ?>" 
             data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>" 
             data-mode="<?php echo esc_attr( $atts['mode'] ); ?>" 
             data-lang="<?php echo esc_attr( $atts['lang'] ); ?>"
             data-theme="<?php echo esc_attr( $atts['theme'] ); ?>">
            
            <header class="wp-wordle-header">
                <div class="wp-wordle-modes">
                    <button type="button" class="wp-wordle-mode-btn <?php echo $atts['mode'] === 'daily' ? 'active' : ''; ?>" data-mode="daily">
                       [<?php echo esc_html($t['daily']); ?>]
                    </button>
                    <button type="button" class="wp-wordle-mode-btn <?php echo $atts['mode'] === 'practice' ? 'active' : ''; ?>" data-mode="practice">
                        [<?php echo esc_html($t['practice']); ?>]
                    </button>
                </div>

                <div class="wp-wordle-actions">
                    <button type="button" class="wp-wordle-icon-btn" id="wp-wordle-help-btn" title="<?php echo esc_attr($t['how_to_play']); ?>" aria-label="<?php echo esc_attr($t['how_to_play']); ?>">[?]</button>
                    <button type="button" class="wp-wordle-icon-btn" id="wp-wordle-stats-btn" title="<?php echo esc_attr($t['statistics']); ?>" aria-label="<?php echo esc_attr($t['statistics']); ?>">[::]</button>
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
                    <button type="button" class="wp-wordle-key wp-wordle-key-action" data-key="ENTER"><?php echo esc_html($t['enter']); ?></button>
                    <?php foreach ( [ 'Z', 'X', 'C', 'V', 'B', 'N', 'M' ] as $key ) : ?>
                        <button type="button" class="wp-wordle-key" data-key="<?php echo esc_attr($key); ?>"><?php echo esc_html( $key ); ?></button>
                    <?php endforeach; ?>
                    <button type="button" class="wp-wordle-key wp-wordle-key-action" data-key="BACKSPACE" aria-label="<?php echo esc_attr($t['backspace']); ?>"><?php echo esc_html($t['backspace_key']); ?></button>
                </div>
            </footer>

            <div class="wp-wordle-modal-backdrop" id="wp-wordle-modal-help" style="display: none;">
                <div class="wp-wordle-modal">
                    <button type="button" class="wp-wordle-modal-close" data-close="help" aria-label="<?php echo esc_attr($t['close']); ?>">[X]</button>
                    <h3><?php echo esc_html($t['rules_title']); ?></h3>
                    <p><?php echo esc_html($t['rules_intro']); ?></p>
                    <p><?php echo esc_html($t['rules_detail']); ?></p>
                    
                    <div class="wp-wordle-help-examples">
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile correct">P</span>
                            <span><?php echo esc_html($t['ex_correct']); ?></span>
                        </div>
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile present">O</span>
                            <span><?php echo esc_html($t['ex_present']); ?></span>
                        </div>
                        <div class="wp-wordle-example-row">
                            <span class="wp-wordle-tile absent">R</span>
                            <span><?php echo esc_html($t['ex_absent']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wp-wordle-modal-backdrop" id="wp-wordle-modal-stats" style="display: none;">
                <div class="wp-wordle-modal">
                    <button type="button" class="wp-wordle-modal-close" data-close="stats" aria-label="<?php echo esc_attr($t['close']); ?>">[X]</button>
                    <h3 id="wp-wordle-stats-title"><?php echo esc_html($t['stats_title']); ?></h3>
                    
                    <div id="wp-wordle-solution-container" style="display:none; margin: 15px 0;">
                        <span class="wp-wordle-solution-label"><?php echo esc_html($t['solution_label']); ?></span>
                        <strong class="wp-wordle-solution-word" id="wp-wordle-solution-text"></strong>
                    </div>

                    <div class="wp-wordle-stats-grid">
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-played">0</span>
                            <span class="wp-wordle-stat-label"><?php echo esc_html($t['played']); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-winrate">0%</span>
                            <span class="wp-wordle-stat-label"><?php echo esc_html($t['win_rate']); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-streak">0</span>
                            <span class="wp-wordle-stat-label"><?php echo esc_html($t['current_streak']); ?></span>
                        </div>
                        <div class="wp-wordle-stat-item">
                            <span class="wp-wordle-stat-num" id="wp-wordle-stat-maxstreak">0</span>
                            <span class="wp-wordle-stat-label"><?php echo esc_html($t['max_streak']); ?></span>
                        </div>
                    </div>

                    <div class="wp-wordle-stats-actions">
                        <button type="button" class="wp-wordle-btn wp-wordle-btn-share" id="wp-wordle-btn-share" style="display: none;">
                            <?php echo esc_html($t['share']); ?>
                        </button>
                        <button type="button" class="wp-wordle-btn wp-wordle-btn-new" id="wp-wordle-btn-new">
                            <?php echo esc_html($t['play_again']); ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }
}