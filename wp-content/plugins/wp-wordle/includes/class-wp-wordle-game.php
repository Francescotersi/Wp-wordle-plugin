<?php
/**
 * Game Engine for WP Wordle Plugin with Online Dictionary Integration (Treccani IT & Datamuse EN)
 */

if (! defined('ABSPATH')) {
    exit;
}

class WP_Wordle_Game {
    private static $words_cache = [];

    public function __construct() {}

    /**
     * Elenco di tutti i temi registrati nel plugin
     */
    public static function get_registered_themes() {
        return [
            'brutalist' => ['name' => 'Brutalist Monospace (Default)', 'desc' => 'Terminale ad alto contrasto B&W, Safety Amber, bordi spessi e ombra 90°'],
            'eink' => ['name' => 'E-Ink / Paperwhite', 'desc' => 'Schermo Kindle a 16 livelli di grigio opaco per zero affaticamento visivo'],
            'editorial' => ['name' => 'Tipografia & Inchiostro', 'desc' => 'Cruciverba d’altri tempi su carta avorio vergata e inchiostro nero piombo'],
            'swiss' => ['name' => 'Swiss International Style', 'desc' => 'Modernismo svizzero 1957, rigore geometrico, grigio ghiaccio e colori primari'],
            'nordic' => ['name' => 'Nordic Minimal', 'desc' => 'Estetica scandinava essenziale con verde pino boreale e blu fiordo profondo'],
            'blueprint' => ['name' => 'Architectural Blueprint', 'desc' => 'Cianografia tecnica da disegno CAD, blu scuro navale e linee millimetriche ciano'],
            'matrix' => ['name' => 'Matrix Terminal', 'desc' => 'Fosfori verdi CRT su sfondo nero abisso, estetica cyberpunk da console Unix'],
            'typewriter' => ['name' => 'Antique Typewriter', 'desc' => 'Tasti meccanici pesanti stile macchina da scrivere e nastro inchiostrato seppia'],
            'matcha' => ['name' => 'Matcha Zen', 'desc' => 'Armonia naturale ispirata al tè matcha, bambù e toni organici della terra'],
            'monokai' => ['name' => 'Monokai Pro', 'desc' => 'Il leggendario tema dark per programmatori con verde menta e arancio solare'],
            'terracotta' => ['name' => 'Terracotta & Clay', 'desc' => 'Ceramiche mediterranee, argilla cotta al sole e calda polvere di mattone'],
            'dracula' => ['name' => 'Dracula Gothic', 'desc' => 'Dark mode gotica con sfondo ardesia violaceo, verde elettrico e arancio caldo'],
            'solarized-light' => ['name' => 'Solarized Light', 'desc' => 'Palette scientifica bilanciata di Ethan Schoonover, crema e giallo ocra d’archivio'],
            'solarized-dark' => ['name' => 'Solarized Dark', 'desc' => 'Il classico solarized notturno per terminali con fondo petrolio e ciano puro'],
            'chalkboard' => ['name' => 'Vintage Chalkboard', 'desc' => 'Lavagna scolastica in ardesia scura, polvere di gesso bianco e tocchi di legno'],
            'synthwave' => ['name' => 'Synthwave 80s', 'desc' => 'Retrofuturismo arcade anni 80 con griglie al neon magenta e ciano laser'],
            'teletext' => ['name' => 'Teletext / Ceefax', 'desc' => 'La grafica televisiva a blocchi degli anni ’80, nero assoluto e primari puri'],
            'gameboy' => ['name' => 'Game Boy 1989', 'desc' => 'L’originale schermo monocromatico DMG a matrice di punti in 4 toni di verde'],
            'desert' => ['name' => 'Sahara Dune', 'desc' => 'Sabbie dorate del deserto, ocra e tonalità calde di arenaria e lino naturale'],
            'midnight-slate' => ['name' => 'Midnight Slate', 'desc' => 'Titanio notturno e grafite scurissima con contrasti precisi in argento e blu cielo'],
            'leather' => ['name' => 'Leather Journal', 'desc' => 'Diario da viaggio in cuoio brunito, caratteri impressi a caldo e fogli antichi'],
            'cyberpunk' => ['name' => 'Cyberpunk Night City', 'desc' => 'High-tech low-life: giallo acido di Night City, ciano neon glitch e contrasto nero'],
            'vaporwave' => ['name' => 'Vaporwave 1995', 'desc' => 'Nostalgia Windows 95, statue greche, tramonti al neon rosa pastello e ciano lilla'],
            'steampunk' => ['name' => 'Victorian Steampunk', 'desc' => 'Ingranaggi di rame lucido, manometri a vapore e ottone brunito dell’era vittoriana'],
            'bauhaus' => ['name' => 'Bauhaus Weimar 1919', 'desc' => 'Rigore modernista, forme pure e i tre colori primari di Kandinsky e Walter Gropius'],
            'art-deco' => ['name' => '1920s Art Déco Gatsby', 'desc' => 'Grand Gatsby ruggenti anni ’20: nero ossidiana lucido e filigrana in oro zecchino'],
            'amber-crt' => ['name' => 'Amber Phosphor CRT', 'desc' => 'I leggendari monitor a fosfori ambra arancioni DEC VT220 degli anni ’80'],
            'win95' => ['name' => 'Windows 95 Redmond', 'desc' => 'L’inconfondibile desktop verde acqua #008080 con finestre beveled grigie a rilievo'],
            'commodore64' => ['name' => 'Commodore 64 1982', 'desc' => 'Bordo blu royal, schermata azzurra PETSCII e 64K di memoria RAM indimenticabile'],
            'cassette-futurism' => ['name' => 'Cassette Futurism', 'desc' => 'Stile Nostromo 1979 e NASA: plastica beige opaca, tasti gommati e hardware tattile'],
            'stained-glass' => ['name' => 'Gothic Stained Glass', 'desc' => 'Vetrata policroma di cattedrale gotica con toni rubino, zaffiro e saldature in piombo'],
            'parchment' => ['name' => 'Medieval Parchment', 'desc' => 'Manoscritto miniato del XIII secolo: pergamena vellutata e inchiostri amanuensi'],
            'retro-mac' => ['name' => 'Classic Mac OS 1984', 'desc' => 'System 7 e Macintosh originale: grigio platino, bordi netti e bitmap font Chicago'],
            'lofi-chill' => ['name' => 'Lo-Fi Study Room', 'desc' => 'Atmosfera studio notturna: caffè caldo, cuffie, lilla polveroso e lampada da tavolo'],
            'botanical' => ['name' => 'Vintage Botanical', 'desc' => 'Erbario scientifico ottocentesco con salvia essiccata, felci e carta di cotone'],
            'deep-ocean' => ['name' => 'Abyssal Deep Ocean', 'desc' => 'Fossa delle Marianne: blu marino abissale e bioluminescenza turchese idrotermale'],
            'volcanic' => ['name' => 'Volcanic Obsidian', 'desc' => 'Ossidiana nera basaltica e vene incandescenti di lava vulcanica in eruzione'],
            'arctic' => ['name' => 'Arctic Glacial Ice', 'desc' => 'Ghiacciai polari, riflessi azzurri di iceberg millenari e gelo cristallino'],
            'autumn' => ['name' => 'Autumn Leaves', 'desc' => 'Fogliame autunnale, caldarroste, sciroppo d’acero rosso e zucca speziata'],
            'cherry-blossom' => ['name' => 'Kyoto Sakura', 'desc' => 'Fioritura dei ciliegi primaverile a Kyoto: petali rosa soffice e inchiostro sumi'],
            'coffee-roast' => ['name' => 'Artisan Coffee Roastery', 'desc' => 'Espresso napoletano crema nocciola, arabica tostata scura e vapore di latte caldo'],
            'comic-book' => ['name' => 'Pop Art Comic Book', 'desc' => 'Stile Roy Lichtenstein e fumetti americani anni ’60: giallo splash e onomatopee'],
            'neo-noir' => ['name' => 'Neo-Noir Detective', 'desc' => 'Asfalto bagnato sotto i lampioni alogeni, fumo di sigaretta e impercettibile pioggia'],
            'galaxy' => ['name' => 'Cosmic Nebula', 'desc' => 'Spazio profondo: nebulosa viola, polvere di stelle magenta e lampo supernova'],
            'claymation' => ['name' => 'Claymation Studio', 'desc' => 'Plastilina artigianale modellata a mano in stop-motion con angoli morbidissimi'],
            'graph-paper' => ['name' => 'Millimeter Graph Paper', 'desc' => 'Quaderno di matematica con reticolo millimetrato blu e inchiostro stilografico'],
            'postage-stamp' => ['name' => 'Vintage Postage Stamp', 'desc' => 'Collezione filatelica d’epoca: bordi traforati da francobollo e inchiostri postali'],
            'moroccan' => ['name' => 'Moroccan Zellij', 'desc' => 'Mosaico geometrico arabo-andaluso: blu cobalto di Fez, zellij e terracotta smaltata'],
            'tarot' => ['name' => 'Mystic Tarot', 'desc' => 'Carte dei tarocchi esoteriche: velluto nero mezzanotte e lamina dorata celestiale'],
            'wireframe' => ['name' => 'Tron Vector Wireframe', 'desc' => 'Grafica vettoriale arcade 1982: linee laser al ciano elettrico nel vuoto nero'],
            'arcade-cabinet' => ['name' => 'Coin-Op Arcade 1984', 'desc' => 'Mobile arcade da sala giochi anni ’80: gettoniera nera, marquee e neon lampone'],
            'newspaper' => ['name' => 'Broadsheet Daily', 'desc' => 'Quotidiano stampato del mattino: grigio carta di giornale e inchiostro rotativa'],
            'neon-tokyo' => ['name' => 'Neon Tokyo Rain', 'desc' => 'Shinjuku sotto la pioggia battente: riflessi asfalto scuro e insegne kanji al neon'],
            'radium' => ['name' => 'Radium Dial 1930', 'desc' => 'Orologio militare vintage con quadrante al radio luminescente e numeri verde brillante'],
            'origami' => ['name' => 'Washi Origami', 'desc' => 'Carta giapponese washi a fibra naturale, pieghe nette a losanga e lacca vermiglia'],
            'morse-telegraph' => ['name' => 'Maritime Morse 1844', 'desc' => 'Apparato telegrafico da nave: bachelite nera, tasto in ottone massiccio e impulsi radio'],
            'solar-flare' => ['name' => 'Solar Flare Infrared', 'desc' => 'Telescopio James Webb: plasma solare dorato, brillamenti coronali ed eruzioni solari'],
            'glitch' => ['name' => 'VHS Tape Glitch', 'desc' => 'Nastro magnetico VHS logorato: tracciamento video fuori asse, ciano e aberrazione cromatica'],
            'polaroid' => ['name' => 'Vintage Polaroid SX-70', 'desc' => 'Pellicola fotografica istantanea: classica cornice bianca Polaroid e toni desaturati'],
            'baroque' => ['name' => 'Royal Baroque & Velvet', 'desc' => 'Lusso regale di Versailles: velluto bordeaux imperiale, oro lucido e cornici dorate'],
        ];
    }

    /**
     * Carica il dizionario da file locale e fonde le parole apprese dinamicamente
     */
    public static function load_words($lang = 'en') {
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'en';

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

        // Fonde le parole apprese e salvate nel database (italiano ed inglese)
        if ($lang === 'it' || $lang === 'en') {
            $learned = get_option('wp_wordle_learned_words_' . $lang, []);
            if (!empty($learned) && is_array($learned)) {
                $data['valid'] = array_values(array_unique(array_merge($data['valid'], $learned)));
            }
        }

        self::$words_cache[$lang] = $data;
        return $data;
    }

    public static function reset_words_cache() {
        self::$words_cache = [];
    }

    /**
     * Valida una parola inglese tramite API online (Datamuse API) con transient caching
     */
    public static function check_english_online($word) {
        if (!is_string($word)) {
            return false;
        }
        $word = strtolower(trim($word));
        if (strlen($word) !== 5 || !ctype_alpha($word)) {
            return false;
        }

        $transient_key = 'wp_wordle_dict_en_' . $word;
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            return ($cached === 'valid');
        }

        $url = 'https://api.datamuse.com/words?sp=' . rawurlencode($word) . '&max=1';

        $response = wp_remote_get($url, [
            'timeout'    => 4,
            'user-agent' => 'WP-Wordle/1.0; ' . (function_exists('home_url') ? home_url() : 'https://wordpress.org'),
            'headers'    => [
                'Accept' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = (int) wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            set_transient($transient_key, 'invalid', HOUR_IN_SECONDS);
            return false;
        }

        $data = json_decode($body, true);
        if (is_array($data) && !empty($data)) {
            if (isset($data[0]['word']) && strtolower($data[0]['word']) === $word) {
                set_transient($transient_key, 'valid', DAY_IN_SECONDS);
                return true;
            }
        }

        set_transient($transient_key, 'invalid', HOUR_IN_SECONDS);
        return false;
    }

    public static function check_treccani_online($word) {
        if (!is_string($word)) {
            return false;
        }
        $word = strtolower(trim($word));
        if (strlen($word) !== 5 || !ctype_alpha($word)) {
            return false;
        }

        $url = 'https://www.treccani.it/vocabolario/ricerca/' . rawurlencode($word) . '/';

        $response = wp_remote_get($url, [
            'timeout'    => 4,
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'headers'    => [
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ],
        ]);

        if (is_wp_error($response)) {
            return false;
        }

        $status_code = (int) wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return false;
        }

        if (preg_match('/"totalfound"\s*:\s*([0-9]+)/', $body, $matches)) {
            $total_found = (int) $matches[1];
            return ($total_found > 0);
        }

        if (stripos($body, 'nessun risultato') !== false) {
            return false;
        }

        return false;
    }

    public static function add_valid_word($word, $lang = 'en') {
        if (!is_string($word)) {
            return;
        }
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'en';
        $word = strtoupper(trim($word));
        if (strlen($word) !== 5 || !ctype_alpha($word)) {
            return;
        }

        if (isset(self::$words_cache[$lang])) {
            if (!in_array($word, self::$words_cache[$lang]['valid'], true)) {
                self::$words_cache[$lang]['valid'][] = $word;
            }
        }

        if ($lang === 'it' || $lang === 'en') {
            $option_key = 'wp_wordle_learned_words_' . $lang;
            $learned = get_option($option_key, []);
            if (!is_array($learned)) {
                $learned = [];
            }
            if (!in_array($word, $learned, true)) {
                $learned[] = $word;
                update_option($option_key, $learned, false);
            }
        }

        $file_path = WP_WORDLE_PATH . "data/wordle-{$lang}.json";
        if (file_exists($file_path) && is_writable($file_path)) {
            $file_content = file_get_contents($file_path);
            $json = json_decode($file_content, true);
            if (is_array($json) && isset($json['valid']) && !in_array($word, $json['valid'], true)) {
                $json['valid'][] = $word;
                sort($json['valid']);
                file_put_contents($file_path, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /**
     * Tenta di recuperare la "Parola del Giorno" dalla homepage di Treccani.
     * Se è casualmente di 5 lettere, la restituisce. Altrimenti restituisce stringa vuota.
     */
    public static function get_treccani_daily_word() {
        $today = current_time('Y-m-d');
        $transient_key = 'wp_wordle_treccani_daily_' . $today;
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            return $cached;
        }

        $candidate = '';
        $response = wp_remote_get('https://www.treccani.it/', [
            'timeout'    => 4,
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ]);

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            // Cerca il blocco "parola del giorno" con relativo link vocabolario
            if (preg_match('/parola[- ]del[- ]giorno.*?<a[^>]*href=["\'][^"\']*vocabolario\/([a-zA-Z]+)[^"\']*["\']/is', $body, $matches)) {
                $term = strtoupper(trim($matches[1]));
                if (strlen($term) === 5 && ctype_alpha($term)) {
                    $candidate = $term;
                }
            }
        }

        set_transient($transient_key, $candidate, DAY_IN_SECONDS);
        return $candidate;
    }

    /**
     * Recupera la parola del giorno
     */
    public static function get_daily_word($lang = 'en', $date = null) {
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'en';
        $custom_word = get_option('wp_wordle_custom_daily_word_' . $lang);
        if (!empty($custom_word) && strlen($custom_word) === 5 && ctype_alpha($custom_word)) {
            return strtoupper($custom_word);
        }

        // Se lingua italiana, tenta prima la parola del giorno ufficiale Treccani (se di 5 lettere)
        if ($lang === 'it' && empty($date)) {
            $treccani_word = self::get_treccani_daily_word();
            if (!empty($treccani_word) && strlen($treccani_word) === 5 && ctype_alpha($treccani_word)) {
                return $treccani_word;
            }
        }

        $words_data = self::load_words($lang);
        $targets = $words_data['targets'];
        if (empty($targets)) {
            return 'WORDL';
        }
        if (empty($date)) {
            $date = current_time('Y-m-d');
        } else {
            if (is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}/', $date, $m)) {
                $date = $m[0];
            }
        }

        try {
            $start = new DateTimeImmutable('2020-01-01', new DateTimeZone('UTC'));
            $current = new DateTimeImmutable($date, new DateTimeZone('UTC'));
            $days_diff = (int)$start->diff($current)->format('%r%a');
        } catch (Exception $e) {
            $days_diff = (int)floor((strtotime($date) - strtotime('2020-01-01')) / DAY_IN_SECONDS);
        }
        $index = abs($days_diff) % count($targets);
        return strtoupper($targets[$index]);
    }

    public static function get_random_word($lang = 'en') {
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'en';
        $words_data = self::load_words($lang);
        $targets = $words_data['targets'];
        if (empty($targets)) {
            return 'WORDL';
        }

        $random_key = array_rand($targets);
        return strtoupper($targets[$random_key]);
    }

    /**
     * Valida se una parola esiste: prima controllo locale veloce, poi fetch dizionario online con auto-apprendimento
     */
    public static function is_valid_word($word, $lang = 'en') {
        if (!is_string($word)) {
            return false;
        }
        $word = strtoupper(trim($word));
        if (strlen($word) !== 5 || !ctype_alpha($word)) {
            return false;
        }
        $lang = in_array($lang, ['it', 'en'], true) ? $lang : 'en';

        $words_data = self::load_words($lang);
        $is_in_local = in_array($word, $words_data['valid'], true) || in_array($word, $words_data['targets'], true);
        if ($is_in_local) {
            return true;
        }

        // Per l'inglese, se non è nel dizionario locale, verifica tramite online dictionary API
        if ($lang === 'en') {
            if (self::check_english_online($word)) {
                self::add_valid_word($word, 'en');
                return true;
            }
            return false;
        }

        // Per l'italiano, se non è nel dizionario locale, verifica su Treccani
        if ($lang === 'it') {
            // Controlla cache negativa temporanea (evita spam di richieste per parole inesistenti ripetute)
            $transient_neg_key = 'wp_wordle_neg_' . md5($word);
            if (get_transient($transient_neg_key) === 'invalid') {
                return false;
            }

            if (self::check_treccani_online($word)) {
                self::add_valid_word($word, 'it');
                return true;
            } else {
                set_transient($transient_neg_key, 'invalid', HOUR_IN_SECONDS);
                return false;
            }
        }

        return false;
    }

    public static function evaluate_guess($guess, $secret) {
        if (!is_string($guess) || !is_string($secret)) {
            return [];
        }
        $guess = strtoupper(trim($guess));
        $secret = strtoupper(trim($secret));

        if (strlen($guess) !== 5 || strlen($secret) !== 5 || !ctype_alpha($guess) || !ctype_alpha($secret)) {
            return [];
        }

        $result = array_fill(0, 5, null);

        $secret_letter_counts = [];
        for ($i = 0; $i < 5; $i++) {
            $char = $secret[$i];
            $secret_letter_counts[$char] = ($secret_letter_counts[$char] ?? 0) + 1;
        }

        for ($i = 0; $i < 5; $i++) {
            $g_char = $guess[$i];
            $s_char = $secret[$i];
            if ($g_char === $s_char) {
                $result[$i] = [
                    'letter' => $g_char,
                    'status' => 'correct',
                ];
                $secret_letter_counts[$g_char]--;
            }
        }

        for ($i = 0; $i < 5; $i++) {
            if ($result[$i] !== null) {
                continue;
            }
            $g_char = $guess[$i];
            if (!empty($secret_letter_counts[$g_char]) && $secret_letter_counts[$g_char] > 0) {
                $result[$i] = [
                    'letter' => $g_char,
                    'status' => 'present',
                ];
                $secret_letter_counts[$g_char]--;
            } else {
                $result[$i] = [
                    'letter' => $g_char,
                    'status' => 'absent',
                ];
            }
        }

        return $result;
    }
}