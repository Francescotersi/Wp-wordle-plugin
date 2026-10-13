<?php
/**
 * REST API Controller & Anti-Spoiler Security Tests
 */

class RestApiTest extends TestCase {
    protected $api;

    public function __construct() {
        parent::__construct('5. WP_Wordle_API Controller & Security');
        $this->api = new WP_Wordle_API();
    }

    public function testStartEndpoint() {
        // Daily IT
        $req_daily_it = new WP_REST_Request(['mode' => 'daily', 'lang' => 'it']);
        $res_daily_it = $this->api->handle_start($req_daily_it);
        $this->assertTrue(isset($res_daily_it['success']) && $res_daily_it['success'] === true, "API Start Daily (IT) returns success=true");
        $this->assertEquals('daily', $res_daily_it['mode'] ?? '', "API Start Daily (IT) mode is 'daily'");

        // Daily EN
        $req_daily_en = new WP_REST_Request(['mode' => 'daily', 'lang' => 'en']);
        $res_daily_en = $this->api->handle_start($req_daily_en);
        $this->assertTrue(isset($res_daily_en['success']) && $res_daily_en['success'] === true, "API Start Daily (EN) returns success=true");

        // Practice IT
        $req_p_it = new WP_REST_Request(['mode' => 'practice', 'lang' => 'it']);
        $res_p_it = $this->api->handle_start($req_p_it);
        $this->assertNotEmpty($res_p_it['token'] ?? '', "API Start Practice (IT) returns token");
        $session_it = get_transient('wp_wordle_p_' . $res_p_it['token']);
        $this->assertTrue(!empty($session_it['word']) && $session_it['lang'] === 'it', "API Practice (IT) transient stores secret word");

        // Practice EN
        $req_p_en = new WP_REST_Request(['mode' => 'practice', 'lang' => 'en']);
        $res_p_en = $this->api->handle_start($req_p_en);
        $this->assertNotEmpty($res_p_en['token'] ?? '', "API Start Practice (EN) returns token");
        $session_en = get_transient('wp_wordle_p_' . $res_p_en['token']);
        $this->assertTrue(!empty($session_en['word']) && $session_en['lang'] === 'en', "API Practice (EN) transient stores secret word");
    }

    public function testGuessValidationErrors() {
        // Invalid length
        $req_len = new WP_REST_Request(['guess' => 'TEST', 'lang' => 'en', 'mode' => 'daily']);
        $res_len = $this->api->handle_guess($req_len);
        $this->assertTrue(is_wp_error($res_len) && $res_len->get_error_code() === 'invalid_length', "API Guess with invalid length returns WP_Error('invalid_length')");

        // Invalid word IT
        $req_inv_it = new WP_REST_Request(['guess' => 'XZZZZ', 'lang' => 'it', 'mode' => 'daily']);
        $res_inv_it = $this->api->handle_guess($req_inv_it);
        $this->assertTrue(is_wp_error($res_inv_it), "API Guess invalid IT word returns WP_Error");
        $this->assertContains('Parola non valida', $res_inv_it->get_error_message(), "API Guess invalid IT word returns localized error 'Parola non valida'");

        // Invalid word EN
        $req_inv_en = new WP_REST_Request(['guess' => 'QWXYZ', 'lang' => 'en', 'mode' => 'daily']);
        $res_inv_en = $this->api->handle_guess($req_inv_en);
        $this->assertTrue(is_wp_error($res_inv_en), "API Guess invalid EN word returns WP_Error");
        $this->assertContains('Not in word list', $res_inv_en->get_error_message(), "API Guess invalid EN word returns localized error 'Not in word list'");

        // Practice missing token
        $req_no_tok = new WP_REST_Request(['guess' => 'APPLE', 'lang' => 'en', 'mode' => 'practice']);
        $res_no_tok = $this->api->handle_guess($req_no_tok);
        $this->assertTrue(is_wp_error($res_no_tok) && $res_no_tok->get_error_code() === 'missing_token', "API Guess Practice without token returns WP_Error('missing_token')");
    }

    public function testPracticeGameProgressionAndAntiSpoiler() {
        // Start Practice IT
        $req_start = new WP_REST_Request(['mode' => 'practice', 'lang' => 'it']);
        $res_start = $this->api->handle_start($req_start);
        $token = $res_start['token'];
        $session = get_transient('wp_wordle_p_' . $token);
        $secret = $session['word'];

        // Non-winning guess
        $wrong_guess = ($secret === 'AMORE') ? 'CASSA' : 'AMORE';
        $req_wrong = new WP_REST_Request([
            'guess' => $wrong_guess,
            'lang'  => 'it',
            'mode'  => 'practice',
            'token' => $token,
        ]);
        $res_wrong = $this->api->handle_guess($req_wrong);

        $this->assertTrue($res_wrong['success'], "API Guess attempt 1 success=true");
        $this->assertFalse($res_wrong['is_win'], "API Guess attempt 1 is_win=false");
        $this->assertNull($res_wrong['solution'], "API Guess attempt 1 solution is strictly NULL (Anti-spoiler active)");

        // Winning guess
        $req_win = new WP_REST_Request([
            'guess' => $secret,
            'lang'  => 'it',
            'mode'  => 'practice',
            'token' => $token,
        ]);
        $res_win = $this->api->handle_guess($req_win);

        $this->assertTrue($res_win['is_win'], "API Guess winning guess is_win=true");
        $this->assertEquals($secret, $res_win['solution'], "API Guess winning guess reveals secret solution ({$secret})");
        $this->assertFalse(get_transient('wp_wordle_p_' . $token), "API Guess winning guess removes practice transient");
    }
}
