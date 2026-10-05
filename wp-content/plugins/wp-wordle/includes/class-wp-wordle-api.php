<?php
/**
 * Gestore REST API per WP Wordle
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Wordle_API {
    private $namespace = 'wp-wordle/v1';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/start', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_start'],
            'permission_callback' => '__return_true',
        ] );

        register_rest_route($this->namespace, '/guess', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_guess'],
            'permission_callback' => '__return_true',
        ] );
    }

    public function handle_start(WP_REST_Request $request) {
        $mode = sanitize_text_field($request->get_param('mode') ?: 'daily');
        $lang = sanitize_text_field($request->get_param('lang') ?: 'it');

        if (!in_array($lang, ['it', 'en'], true)) {
            $lang = 'it';
        }

        if ( $mode === 'practice' ) {
            $token       = wp_generate_password(24, false);
            $secret_word = WP_Wordle_Game::get_random_word($lang);

            set_transient('wp_wordle_p_' . $token, [
                'word' => $secret_word,
                'lang' => $lang,
            ], 4 * HOUR_IN_SECONDS);

            return rest_ensure_response([
                'success'      => true,
                'mode'         => 'practice',
                'token'        => $token,
                'max_attempts' => 6,
                'word_length'  => 5,
            ]);
        }

        return rest_ensure_response( [
            'success'      => true,
            'mode'         => 'daily',
            'date'         => current_time( 'Y-m-d' ),
            'max_attempts' => 6,
            'word_length'  => 5,
        ] );
    }

    public function handle_guess(WP_REST_Request $request) {
        $guess = strtoupper(trim( sanitize_text_field($request->get_param('guess'))));
        $mode = sanitize_text_field($request->get_param('mode') ?: 'daily');
        $lang = sanitize_text_field($request->get_param('lang') ?: 'it');
        $token = sanitize_text_field($request->get_param('token'));
        $attempt = (int) $request->get_param( 'attempt' );
        if (!in_array($lang, ['it', 'en'], true)) {
            $lang = 'it';
        }

        if (strlen($guess) !== 5 || !ctype_alpha($guess)) {
            return new WP_Error(
                'invalid_length',
                __( 'The word must be composed of 5  letters.', 'wp-wordle' ),
                ['status' => 400]
            );
        }

        if (!WP_Wordle_Game::is_valid_word($guess, $lang)) {
            return new WP_Error(
                'not_in_word_list',
                __( 'Word missing in the dictionary.', 'wp-wordle' ),
                ['status' => 400]
            );
        }
        $secret_word = '';

        if ($mode === 'practice') {
            if (empty($token)) {
                return new WP_Error(
                    'missing_token',
                    __('Invalid session.', 'wp-wordle'),
                    ['status' => 400]
                );
            }

            $session = get_transient('wp_wordle_p_' . $token);
            if (!$session || empty( $session['word'])) {
                return new WP_Error(
                    'session_expired',
                    __( 'Game session expired. Start a new game.', 'wp-wordle' ),
                    [ 'status' => 404 ]
                );
            }
            $secret_word = $session['word'];
        } else {
            $secret_word = WP_Wordle_Game::get_daily_word($lang);
        }

        $evaluation = WP_Wordle_Game::evaluate_guess($guess, $secret_word);
        $is_win = ($guess === $secret_word);
        $is_game_over = $is_win || ($attempt >= 6);

        $solution = $is_game_over ? $secret_word : null;

        if ( $mode === 'practice' && $is_game_over ) {
            delete_transient( 'wp_wordle_p_' . $token );
        }

        return rest_ensure_response( [
            'success'      => true,
            'guess'        => $guess,
            'evaluation'   => $evaluation,
            'is_win'       => $is_win,
            'is_game_over' => $is_game_over,
            'solution'     => $solution,
            'attempt'      => $attempt,
            'max_attempts' => 6,
        ] );
    }
}