<?php
/**
 * Game Engine for Wordpress-Wordle-Plugin
 */

if (! defined('ABSPATH')) {
    exit;
}

class WP_Wordle_Game {
    private static $words_cache = [];

    public function __construct() {}

    public static function load_words($lang = 'it') {
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'it';

        if (isset(self::$words_cache[$lang])) {
            return self::$words_cache[$lang];
        }

        $file_path = WP_WORDLE_PATH . "data/wordle-{$lang}.json";
        if (!file_exists($file_path)) {
            return ['targets' => [], 'valid' => []];
        }

        $content = file_get_contents($file_path);
        $data = json_decode($content, true);

        if (!is_array($data) || empty($data['targets'])) {
            return ['targets' => [], 'valid' => []];
        }
        self::$words_cache[$lang] = $data;
        return $data;
    }

    public static function get_daily_word($lang = 'it', $date = null) {
        $custom_word = get_option( 'wp_wordle_custom_daily_word_' . $lang );
        if ( ! empty( $custom_word ) && strlen( $custom_word ) === 5 ) {
            return strtoupper( $custom_word );
        }

        $words_data = self::load_words($lang);
        $targets = $words_data['targets'];
        if (empty($targets)) {
            return 'WORDL';
        }
        if (empty($date)) {
            $date = current_time('Y-m-d');
        }

        $start_epoch = strtotime('2020-01-01');
        $current = strtotime($date);
        $days_diff = (int)floor(($current - $start_epoch) / DAY_IN_SECONDS);
        $index = abs($days_diff) % count($targets);
        return strtoupper($targets[$index]);
    }

    public static function get_random_word($lang = 'it') {
        $words_data = self::load_words($lang);
        $targets = $words_data['targets'];
        if (empty($targets)) {
            return 'WORDL';
        }
        
        $random_key = array_rand($targets);
        return strtoupper($targets[$random_key]);
    }

    public static function is_valid_word($word, $lang = 'it') {
        $word = strtoupper(trim($word));
        if (strlen($word) !== 5 || !ctype_alpha($word)) {
            return false;
        }
        $words_data = self::load_words($lang);
        return in_array( $word, $words_data['valid'], true ) || in_array( $word, $words_data['targets'], true );
    }

    public static function evaluate_guess($guess, $secret) {
        $guess = strtoupper(trim($guess));
        $secret = strtoupper(trim($secret));
        $result = array_fill(0, 5, null);

        $secret_letter_counts = [];
        for ( $i = 0; $i < 5; $i++ ) {
            $char = $secret[ $i ];
            $secret_letter_counts[ $char ] = ( $secret_letter_counts[ $char ] ?? 0 ) + 1;
        }

        for ($i = 0; $i < 5; $i++) {
            $g_char = $guess[$i];
            $s_char = $secret[$i];
            if ($g_char === $s_char) {
                $result[$i] = [
                    'letter' => $g_char,
                    'status' => 'correct', //good char good spot
                ];
                $secret_letter_counts[$g_char]--;
            }
        }

        for ($i = 0; $i < 5; $i++) {
            if ( $result[ $i ] !== null ) {
                continue;
            }
            $g_char = $guess[$i];
            if (!empty($secret_letter_counts[$g_char]) && $secret_letter_counts[$g_char] > 0) {
                $result[ $i ] = [
                    'letter' => $g_char,
                    'status' => 'present', // good char wrong spot
                ];
                $secret_letter_counts[ $g_char ]--;
            } else {
                $result[ $i ] = [
                    'letter' => $g_char,
                    'status' => 'absent', //wrong char
                ];
            }
        }

        return $result;
    }
}