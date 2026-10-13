# WP Wordle

Plugin WordPress leggero e reattivo per integrare un gioco Wordle bilingue (Italiano e Inglese) direttamente nel tuo sito, con supporto per sfide giornaliere e allenamento libero.

## 🚀 Installazione Rapida

1. Copia la cartella del plugin in `/wp-content/plugins/wp-wordle`.
2. Accedi alla bacheca di WordPress, vai su **Plugin** > **Plugin installati** e clicca su **Attiva**.

## 🎮 Uso dello Shortcode

Inserisci lo shortcode in qualsiasi pagina, articolo o blocco:

```text
[wp_wordle]
```

### Parametri Essenziali

| Parametro | Valori | Default | Descrizione |
| :--- | :--- | :--- | :--- |
| `theme` | slug del tema | `"brutalist"` | Stile grafico dell'interfaccia |
| `lang` | `"it"` \| `"en"` | `"en"` | Lingua del gioco e del vocabolario |
| `mode` | `"daily"` \| `"practice"` | `"daily"` | Sfida del giorno condivisa o allenamento illimitato |

**Esempio combinato:**
```text
[wp_wordle theme="gameboy" lang="it" mode="daily"]
```

## 🎨 Temi e Validazione Vocabolario

- **60 Temi CSS modulari (caricati on-demand):**  
  `brutalist` (default), `eink`, `editorial`, `swiss`, `nordic`, `blueprint`, `matrix`, `typewriter`, `matcha`, `monokai`, `terracotta`, `dracula`, `solarized-light`, `solarized-dark`, `chalkboard`, `synthwave`, `teletext`, `gameboy`, `desert`, `midnight-slate`, `leather`, `cyberpunk`, `vaporwave`, `steampunk`, `bauhaus`, `art-deco`, `amber-crt`, `win95`, `commodore64`, `cassette-futurism`, `stained-glass`, `parchment`, `retro-mac`, `lofi-chill`, `botanical`, `deep-ocean`, `volcanic`, `arctic`, `autumn`, `cherry-blossom`, `coffee-roast`, `comic-book`, `neo-noir`, `galaxy`, `claymation`, `graph-paper`, `postage-stamp`, `moroccan`, `tarot`, `wireframe`, `arcade-cabinet`, `newspaper`, `neon-tokyo`, `radium`, `origami`, `morse-telegraph`, `solar-flare`, `glitch`, `polaroid`, `baroque`.

### 🖼️ Galleria Anteprime Temi

<table>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-brutalist.png" width="95" alt="Brutalist Monospace" /><br/>
      <sub><b>Brutalist Monospace</b><br/><code>brutalist</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-eink.png" width="95" alt="E-Ink / Paperwhite" /><br/>
      <sub><b>E-Ink / Paperwhite</b><br/><code>eink</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-editorial.png" width="95" alt="Tipografia & Inchiostro" /><br/>
      <sub><b>Tipografia & Inchiostro</b><br/><code>editorial</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-swiss.png" width="95" alt="Swiss International Style" /><br/>
      <sub><b>Swiss International Style</b><br/><code>swiss</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-nordic.png" width="95" alt="Nordic Minimal" /><br/>
      <sub><b>Nordic Minimal</b><br/><code>nordic</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-blueprint.png" width="95" alt="Architectural Blueprint" /><br/>
      <sub><b>Architectural Blueprint</b><br/><code>blueprint</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-matrix.png" width="95" alt="Matrix Terminal" /><br/>
      <sub><b>Matrix Terminal</b><br/><code>matrix</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-typewriter.png" width="95" alt="Antique Typewriter" /><br/>
      <sub><b>Antique Typewriter</b><br/><code>typewriter</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-matcha.png" width="95" alt="Matcha Zen" /><br/>
      <sub><b>Matcha Zen</b><br/><code>matcha</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-monokai.png" width="95" alt="Monokai Pro" /><br/>
      <sub><b>Monokai Pro</b><br/><code>monokai</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-terracotta.png" width="95" alt="Terracotta & Clay" /><br/>
      <sub><b>Terracotta & Clay</b><br/><code>terracotta</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-dracula.png" width="95" alt="Dracula Gothic" /><br/>
      <sub><b>Dracula Gothic</b><br/><code>dracula</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solarized-light.png" width="95" alt="Solarized Light" /><br/>
      <sub><b>Solarized Light</b><br/><code>solarized-light</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solarized-dark.png" width="95" alt="Solarized Dark" /><br/>
      <sub><b>Solarized Dark</b><br/><code>solarized-dark</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-chalkboard.png" width="95" alt="Vintage Chalkboard" /><br/>
      <sub><b>Vintage Chalkboard</b><br/><code>chalkboard</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-synthwave.png" width="95" alt="Synthwave 80s" /><br/>
      <sub><b>Synthwave 80s</b><br/><code>synthwave</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-teletext.png" width="95" alt="Teletext / Ceefax" /><br/>
      <sub><b>Teletext / Ceefax</b><br/><code>teletext</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-gameboy.png" width="95" alt="Game Boy 1989" /><br/>
      <sub><b>Game Boy 1989</b><br/><code>gameboy</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-desert.png" width="95" alt="Sahara Dune" /><br/>
      <sub><b>Sahara Dune</b><br/><code>desert</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-midnight-slate.png" width="95" alt="Midnight Slate" /><br/>
      <sub><b>Midnight Slate</b><br/><code>midnight-slate</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-leather.png" width="95" alt="Leather Journal" /><br/>
      <sub><b>Leather Journal</b><br/><code>leather</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cyberpunk.png" width="95" alt="Cyberpunk Night City" /><br/>
      <sub><b>Cyberpunk Night City</b><br/><code>cyberpunk</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-vaporwave.png" width="95" alt="Vaporwave 1995" /><br/>
      <sub><b>Vaporwave 1995</b><br/><code>vaporwave</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-steampunk.png" width="95" alt="Victorian Steampunk" /><br/>
      <sub><b>Victorian Steampunk</b><br/><code>steampunk</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-bauhaus.png" width="95" alt="Bauhaus Weimar 1919" /><br/>
      <sub><b>Bauhaus Weimar 1919</b><br/><code>bauhaus</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-art-deco.png" width="95" alt="1920s Art Déco Gatsby" /><br/>
      <sub><b>1920s Art Déco Gatsby</b><br/><code>art-deco</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-amber-crt.png" width="95" alt="Amber Phosphor CRT" /><br/>
      <sub><b>Amber Phosphor CRT</b><br/><code>amber-crt</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-win95.png" width="95" alt="Windows 95 Redmond" /><br/>
      <sub><b>Windows 95 Redmond</b><br/><code>win95</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-commodore64.png" width="95" alt="Commodore 64 1982" /><br/>
      <sub><b>Commodore 64 1982</b><br/><code>commodore64</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cassette-futurism.png" width="95" alt="Cassette Futurism" /><br/>
      <sub><b>Cassette Futurism</b><br/><code>cassette-futurism</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-stained-glass.png" width="95" alt="Gothic Stained Glass" /><br/>
      <sub><b>Gothic Stained Glass</b><br/><code>stained-glass</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-parchment.png" width="95" alt="Medieval Parchment" /><br/>
      <sub><b>Medieval Parchment</b><br/><code>parchment</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-retro-mac.png" width="95" alt="Classic Mac OS 1984" /><br/>
      <sub><b>Classic Mac OS 1984</b><br/><code>retro-mac</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-lofi-chill.png" width="95" alt="Lo-Fi Study Room" /><br/>
      <sub><b>Lo-Fi Study Room</b><br/><code>lofi-chill</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-botanical.png" width="95" alt="Vintage Botanical" /><br/>
      <sub><b>Vintage Botanical</b><br/><code>botanical</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-deep-ocean.png" width="95" alt="Abyssal Deep Ocean" /><br/>
      <sub><b>Abyssal Deep Ocean</b><br/><code>deep-ocean</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-volcanic.png" width="95" alt="Volcanic Obsidian" /><br/>
      <sub><b>Volcanic Obsidian</b><br/><code>volcanic</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-arctic.png" width="95" alt="Arctic Glacial Ice" /><br/>
      <sub><b>Arctic Glacial Ice</b><br/><code>arctic</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-autumn.png" width="95" alt="Autumn Leaves" /><br/>
      <sub><b>Autumn Leaves</b><br/><code>autumn</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cherry-blossom.png" width="95" alt="Kyoto Sakura" /><br/>
      <sub><b>Kyoto Sakura</b><br/><code>cherry-blossom</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-coffee-roast.png" width="95" alt="Artisan Coffee Roastery" /><br/>
      <sub><b>Artisan Coffee Roastery</b><br/><code>coffee-roast</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-comic-book.png" width="95" alt="Pop Art Comic Book" /><br/>
      <sub><b>Pop Art Comic Book</b><br/><code>comic-book</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-neo-noir.png" width="95" alt="Neo-Noir Detective" /><br/>
      <sub><b>Neo-Noir Detective</b><br/><code>neo-noir</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-galaxy.png" width="95" alt="Cosmic Nebula" /><br/>
      <sub><b>Cosmic Nebula</b><br/><code>galaxy</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-claymation.png" width="95" alt="Claymation Studio" /><br/>
      <sub><b>Claymation Studio</b><br/><code>claymation</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-graph-paper.png" width="95" alt="Millimeter Graph Paper" /><br/>
      <sub><b>Millimeter Graph Paper</b><br/><code>graph-paper</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-postage-stamp.png" width="95" alt="Vintage Postage Stamp" /><br/>
      <sub><b>Vintage Postage Stamp</b><br/><code>postage-stamp</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-moroccan.png" width="95" alt="Moroccan Zellij" /><br/>
      <sub><b>Moroccan Zellij</b><br/><code>moroccan</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-tarot.png" width="95" alt="Mystic Tarot" /><br/>
      <sub><b>Mystic Tarot</b><br/><code>tarot</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-wireframe.png" width="95" alt="Tron Vector Wireframe" /><br/>
      <sub><b>Tron Vector Wireframe</b><br/><code>wireframe</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-arcade-cabinet.png" width="95" alt="Coin-Op Arcade 1984" /><br/>
      <sub><b>Coin-Op Arcade 1984</b><br/><code>arcade-cabinet</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-newspaper.png" width="95" alt="Broadsheet Daily" /><br/>
      <sub><b>Broadsheet Daily</b><br/><code>newspaper</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-neon-tokyo.png" width="95" alt="Neon Tokyo Rain" /><br/>
      <sub><b>Neon Tokyo Rain</b><br/><code>neon-tokyo</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-radium.png" width="95" alt="Radium Dial 1930" /><br/>
      <sub><b>Radium Dial 1930</b><br/><code>radium</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-origami.png" width="95" alt="Washi Origami" /><br/>
      <sub><b>Washi Origami</b><br/><code>origami</code></sub>
    </td>
  </tr>
  <tr>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-morse-telegraph.png" width="95" alt="Maritime Morse 1844" /><br/>
      <sub><b>Maritime Morse 1844</b><br/><code>morse-telegraph</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solar-flare.png" width="95" alt="Solar Flare Infrared" /><br/>
      <sub><b>Solar Flare Infrared</b><br/><code>solar-flare</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-glitch.png" width="95" alt="VHS Tape Glitch" /><br/>
      <sub><b>VHS Tape Glitch</b><br/><code>glitch</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-polaroid.png" width="95" alt="Vintage Polaroid SX-70" /><br/>
      <sub><b>Vintage Polaroid SX-70</b><br/><code>polaroid</code></sub>
    </td>
    <td align="center" width="20%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-baroque.png" width="95" alt="Royal Baroque & Velvet" /><br/>
      <sub><b>Royal Baroque & Velvet</b><br/><code>baroque</code></sub>
    </td>
  </tr>
</table>

### 🔍 Validazione Vocabolario & Anti-Spoiler
  - **Italiano (IT):** Verifica live sul vocabolario ufficiale **Treccani** con memorizzazione automatica nella cache locale (`wp_wordle_learned_words_it`).
  - **Inglese (EN):** Validazione tramite dizionario integrato e servizio Datamuse.
  - **Sicurezza:** La soluzione è verificata via REST API lato server e non viene mai esposta nel codice sorgente del browser.

## ⚙️ Configurazione in Bacheca

Accedi a **Impostazioni** > **WP Wordle** per:
- Scegliere il tema predefinito globale per il sito.
- Impostare la lingua di default (Italiano / Inglese).
- Forzare manualmente la parola del giorno (per contest o eventi speciali).
- Verificare lo stato del vocabolario e consultare la soluzione attiva protetta da spoiler.

## 🧪 Suite di Test

Il plugin include una suite di test automatizzati completa (125 asserzioni) che verifica l'integrità dei vocabolari, tutti i 60 temi CSS, l'engine di gioco (IT & EN), l'integrazione online (Treccani & Datamuse), i controller REST API e i test d'integrazione containerizzati.

Per eseguire l'intera suite di test:
```bash
php tests/test-all-plugin.php
```

Per il test mirato del dizionario inglese e relative API online:
```bash
php tests/test-english-dictionary.php
```
