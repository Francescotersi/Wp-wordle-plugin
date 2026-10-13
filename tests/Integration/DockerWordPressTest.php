<?php
/**
 * Live WordPress Docker Integration Tests (localhost:8080)
 */

class DockerWordPressTest extends TestCase {
    protected $base_url = 'http://localhost:8080';
    protected $use_rest_route = false;

    public function __construct() {
        parent::__construct('8. Live Docker WordPress Integration');
    }

    protected function http_request($route, $params = [], $method = 'POST') {
        if ($this->use_rest_route) {
            $url = $this->base_url . '/?rest_route=' . $route;
        } else {
            $url = $this->base_url . '/wp-json' . $route;
        }

        $ch = curl_init();
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        } else {
            if (!empty($params)) {
                $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($params);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'code' => $code,
            'body' => json_decode($res, true) ?: $res,
        ];
    }

    public function testLiveDockerIntegration() {
        // Ping localhost:8080
        $ch = curl_init($this->base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 0) {
            $this->skip("Live WordPress container (localhost:8080) is not currently running. Skipping live HTTP integration tests.");
            return;
        }

        // Detect REST route style (/wp-json vs /?rest_route=)
        $check_pretty = $this->http_request('/wp-wordle/v1/start', ['mode' => 'daily', 'lang' => 'it'], 'POST');
        if ($check_pretty['code'] === 404) {
            $this->use_rest_route = true;
        }

        // Live Start Daily IT
        $live_it = $this->http_request('/wp-wordle/v1/start', ['mode' => 'daily', 'lang' => 'it'], 'POST');
        $this->assertEquals(200, $live_it['code'], "Live WP Start Daily IT returns HTTP 200");
        $this->assertEquals('daily', $live_it['body']['mode'] ?? '', "Live WP Start Daily IT returns mode=daily");

        // Live Start Daily EN
        $live_en = $this->http_request('/wp-wordle/v1/start', ['mode' => 'daily', 'lang' => 'en'], 'POST');
        $this->assertEquals(200, $live_en['code'], "Live WP Start Daily EN returns HTTP 200");
        $this->assertEquals('daily', $live_en['body']['mode'] ?? '', "Live WP Start Daily EN returns mode=daily");

        // Live Start Practice IT
        $live_p_it = $this->http_request('/wp-wordle/v1/start', ['mode' => 'practice', 'lang' => 'it'], 'POST');
        $this->assertNotEmpty($live_p_it['body']['token'] ?? '', "Live WP Start Practice IT returns token");

        $token_it = $live_p_it['body']['token'] ?? '';
        if (!empty($token_it)) {
            // Live Guess valid IT
            $res_guess_it = $this->http_request('/wp-wordle/v1/guess', [
                'guess' => 'AMORE',
                'lang'  => 'it',
                'mode'  => 'practice',
                'token' => $token_it,
            ], 'POST');
            $this->assertEquals(200, $res_guess_it['code'], "Live WP Guess IT ('AMORE') returns HTTP 200");
            $this->assertCount(5, $res_guess_it['body']['evaluation'] ?? [], "Live WP Guess IT returns 5 evaluation tiles");

            // Live Guess invalid IT
            $res_inv_it = $this->http_request('/wp-wordle/v1/guess', [
                'guess' => 'XZZZZ',
                'lang'  => 'it',
                'mode'  => 'practice',
                'token' => $token_it,
            ], 'POST');
            $this->assertEquals(400, $res_inv_it['code'], "Live WP Guess IT invalid word returns HTTP 400");
            $this->assertEquals('Parola non valida', $res_inv_it['body']['message'] ?? '', "Live WP Guess IT invalid word message is 'Parola non valida'");
        }

        // Live Start Practice EN
        $live_p_en = $this->http_request('/wp-wordle/v1/start', ['mode' => 'practice', 'lang' => 'en'], 'POST');
        $this->assertNotEmpty($live_p_en['body']['token'] ?? '', "Live WP Start Practice EN returns token");

        $token_en = $live_p_en['body']['token'] ?? '';
        if (!empty($token_en)) {
            $res_guess_en = $this->http_request('/wp-wordle/v1/guess', [
                'guess' => 'APPLE',
                'lang'  => 'en',
                'mode'  => 'practice',
                'token' => $token_en,
            ], 'POST');
            $this->assertEquals(200, $res_guess_en['code'], "Live WP Guess EN ('APPLE') returns HTTP 200");
            $this->assertCount(5, $res_guess_en['body']['evaluation'] ?? [], "Live WP Guess EN returns 5 evaluation tiles");

            $res_inv_en = $this->http_request('/wp-wordle/v1/guess', [
                'guess' => 'QWXYZ',
                'lang'  => 'en',
                'mode'  => 'practice',
                'token' => $token_en,
            ], 'POST');
            $this->assertEquals(400, $res_inv_en['code'], "Live WP Guess EN invalid word returns HTTP 400");
            $this->assertEquals('Not in word list', $res_inv_en['body']['message'] ?? '', "Live WP Guess EN invalid word message is 'Not in word list'");
        }
    }
}
