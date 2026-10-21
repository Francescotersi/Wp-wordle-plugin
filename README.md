# WP Wordle

A lightweight, responsive WordPress plugin to embed a bilingual Wordle game (Italian & English) directly into your site, featuring daily challenges and unlimited practice mode.

## 🚀 Quick Installation

1. Copy the plugin directory to `/wp-content/plugins/wp-wordle`.
2. Go to WordPress Dashboard > **Plugins** > **Installed Plugins** and click **Activate**.

## 🎮 Shortcode Usage

Embed the game into any post, page, or widget block:

```text
[wp_wordle]
```

### Essential Parameters

| Parameter | Values | Default | Description |
| :--- | :--- | :--- | :--- |
| `theme` | theme slug | `"brutalist"` | Visual theme for the game UI |
| `lang` | `"it"` \| `"en"` | `"en"` | Vocabulary & game language |
| `mode` | `"daily"` \| `"practice"` | `"daily"` | Shared daily challenge or unlimited practice mode |

**Combined example:**
```text
[wp_wordle theme="gameboy" lang="it" mode="daily"]
```

### 🖼️ Theme Preview Gallery
- **60 Modular CSS Themes (loaded on-demand):**  

<table width="100%">
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-brutalist.png" width="100%" alt="Brutalist Monospace" /><br/>
      <b>Brutalist Monospace</b><br/><code>brutalist</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-eink.png" width="100%" alt="E-Ink / Paperwhite" /><br/>
      <b>E-Ink / Paperwhite</b><br/><code>eink</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-editorial.png" width="100%" alt="Tipografia & Inchiostro" /><br/>
      <b>Tipografia & Inchiostro</b><br/><code>editorial</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-swiss.png" width="100%" alt="Swiss International Style" /><br/>
      <b>Swiss International Style</b><br/><code>swiss</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-nordic.png" width="100%" alt="Nordic Minimal" /><br/>
      <b>Nordic Minimal</b><br/><code>nordic</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-blueprint.png" width="100%" alt="Architectural Blueprint" /><br/>
      <b>Architectural Blueprint</b><br/><code>blueprint</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-matrix.png" width="100%" alt="Matrix Terminal" /><br/>
      <b>Matrix Terminal</b><br/><code>matrix</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-typewriter.png" width="100%" alt="Antique Typewriter" /><br/>
      <b>Antique Typewriter</b><br/><code>typewriter</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-matcha.png" width="100%" alt="Matcha Zen" /><br/>
      <b>Matcha Zen</b><br/><code>matcha</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-monokai.png" width="100%" alt="Monokai Pro" /><br/>
      <b>Monokai Pro</b><br/><code>monokai</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-terracotta.png" width="100%" alt="Terracotta & Clay" /><br/>
      <b>Terracotta & Clay</b><br/><code>terracotta</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-dracula.png" width="100%" alt="Dracula Gothic" /><br/>
      <b>Dracula Gothic</b><br/><code>dracula</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solarized-light.png" width="100%" alt="Solarized Light" /><br/>
      <b>Solarized Light</b><br/><code>solarized-light</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solarized-dark.png" width="100%" alt="Solarized Dark" /><br/>
      <b>Solarized Dark</b><br/><code>solarized-dark</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-chalkboard.png" width="100%" alt="Vintage Chalkboard" /><br/>
      <b>Vintage Chalkboard</b><br/><code>chalkboard</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-synthwave.png" width="100%" alt="Synthwave 80s" /><br/>
      <b>Synthwave 80s</b><br/><code>synthwave</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-teletext.png" width="100%" alt="Teletext / Ceefax" /><br/>
      <b>Teletext / Ceefax</b><br/><code>teletext</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-gameboy.png" width="100%" alt="Game Boy 1989" /><br/>
      <b>Game Boy 1989</b><br/><code>gameboy</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-desert.png" width="100%" alt="Sahara Dune" /><br/>
      <b>Sahara Dune</b><br/><code>desert</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-midnight-slate.png" width="100%" alt="Midnight Slate" /><br/>
      <b>Midnight Slate</b><br/><code>midnight-slate</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-leather.png" width="100%" alt="Leather Journal" /><br/>
      <b>Leather Journal</b><br/><code>leather</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cyberpunk.png" width="100%" alt="Cyberpunk Night City" /><br/>
      <b>Cyberpunk Night City</b><br/><code>cyberpunk</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-vaporwave.png" width="100%" alt="Vaporwave 1995" /><br/>
      <b>Vaporwave 1995</b><br/><code>vaporwave</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-steampunk.png" width="100%" alt="Victorian Steampunk" /><br/>
      <b>Victorian Steampunk</b><br/><code>steampunk</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-bauhaus.png" width="100%" alt="Bauhaus Weimar 1919" /><br/>
      <b>Bauhaus Weimar 1919</b><br/><code>bauhaus</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-art-deco.png" width="100%" alt="1920s Art Déco Gatsby" /><br/>
      <b>1920s Art Déco Gatsby</b><br/><code>art-deco</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-amber-crt.png" width="100%" alt="Amber Phosphor CRT" /><br/>
      <b>Amber Phosphor CRT</b><br/><code>amber-crt</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-win95.png" width="100%" alt="Windows 95 Redmond" /><br/>
      <b>Windows 95 Redmond</b><br/><code>win95</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-commodore64.png" width="100%" alt="Commodore 64 1982" /><br/>
      <b>Commodore 64 1982</b><br/><code>commodore64</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cassette-futurism.png" width="100%" alt="Cassette Futurism" /><br/>
      <b>Cassette Futurism</b><br/><code>cassette-futurism</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-stained-glass.png" width="100%" alt="Gothic Stained Glass" /><br/>
      <b>Gothic Stained Glass</b><br/><code>stained-glass</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-parchment.png" width="100%" alt="Medieval Parchment" /><br/>
      <b>Medieval Parchment</b><br/><code>parchment</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-retro-mac.png" width="100%" alt="Classic Mac OS 1984" /><br/>
      <b>Classic Mac OS 1984</b><br/><code>retro-mac</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-lofi-chill.png" width="100%" alt="Lo-Fi Study Room" /><br/>
      <b>Lo-Fi Study Room</b><br/><code>lofi-chill</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-botanical.png" width="100%" alt="Vintage Botanical" /><br/>
      <b>Vintage Botanical</b><br/><code>botanical</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-deep-ocean.png" width="100%" alt="Abyssal Deep Ocean" /><br/>
      <b>Abyssal Deep Ocean</b><br/><code>deep-ocean</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-volcanic.png" width="100%" alt="Volcanic Obsidian" /><br/>
      <b>Volcanic Obsidian</b><br/><code>volcanic</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-arctic.png" width="100%" alt="Arctic Glacial Ice" /><br/>
      <b>Arctic Glacial Ice</b><br/><code>arctic</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-autumn.png" width="100%" alt="Autumn Leaves" /><br/>
      <b>Autumn Leaves</b><br/><code>autumn</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-cherry-blossom.png" width="100%" alt="Kyoto Sakura" /><br/>
      <b>Kyoto Sakura</b><br/><code>cherry-blossom</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-coffee-roast.png" width="100%" alt="Artisan Coffee Roastery" /><br/>
      <b>Artisan Coffee Roastery</b><br/><code>coffee-roast</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-comic-book.png" width="100%" alt="Pop Art Comic Book" /><br/>
      <b>Pop Art Comic Book</b><br/><code>comic-book</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-neo-noir.png" width="100%" alt="Neo-Noir Detective" /><br/>
      <b>Neo-Noir Detective</b><br/><code>neo-noir</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-galaxy.png" width="100%" alt="Cosmic Nebula" /><br/>
      <b>Cosmic Nebula</b><br/><code>galaxy</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-claymation.png" width="100%" alt="Claymation Studio" /><br/>
      <b>Claymation Studio</b><br/><code>claymation</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-graph-paper.png" width="100%" alt="Millimeter Graph Paper" /><br/>
      <b>Millimeter Graph Paper</b><br/><code>graph-paper</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-postage-stamp.png" width="100%" alt="Vintage Postage Stamp" /><br/>
      <b>Vintage Postage Stamp</b><br/><code>postage-stamp</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-moroccan.png" width="100%" alt="Moroccan Zellij" /><br/>
      <b>Moroccan Zellij</b><br/><code>moroccan</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-tarot.png" width="100%" alt="Mystic Tarot" /><br/>
      <b>Mystic Tarot</b><br/><code>tarot</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-wireframe.png" width="100%" alt="Tron Vector Wireframe" /><br/>
      <b>Tron Vector Wireframe</b><br/><code>wireframe</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-arcade-cabinet.png" width="100%" alt="Coin-Op Arcade 1984" /><br/>
      <b>Coin-Op Arcade 1984</b><br/><code>arcade-cabinet</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-newspaper.png" width="100%" alt="Broadsheet Daily" /><br/>
      <b>Broadsheet Daily</b><br/><code>newspaper</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-neon-tokyo.png" width="100%" alt="Neon Tokyo Rain" /><br/>
      <b>Neon Tokyo Rain</b><br/><code>neon-tokyo</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-radium.png" width="100%" alt="Radium Dial 1930" /><br/>
      <b>Radium Dial 1930</b><br/><code>radium</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-origami.png" width="100%" alt="Washi Origami" /><br/>
      <b>Washi Origami</b><br/><code>origami</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-morse-telegraph.png" width="100%" alt="Maritime Morse 1844" /><br/>
      <b>Maritime Morse 1844</b><br/><code>morse-telegraph</code>
    </td>
  </tr>
  <tr>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-solar-flare.png" width="100%" alt="Solar Flare Infrared" /><br/>
      <b>Solar Flare Infrared</b><br/><code>solar-flare</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-glitch.png" width="100%" alt="VHS Tape Glitch" /><br/>
      <b>VHS Tape Glitch</b><br/><code>glitch</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-polaroid.png" width="100%" alt="Vintage Polaroid SX-70" /><br/>
      <b>Vintage Polaroid SX-70</b><br/><code>polaroid</code>
    </td>
    <td align="center" width="25%" valign="top">
      <img src="wp-content/plugins/wp-wordle/assets/themes/theme-baroque.png" width="100%" alt="Royal Baroque & Velvet" /><br/>
      <b>Royal Baroque & Velvet</b><br/><code>baroque</code>
    </td>
  </tr>
</table>

### 🔍 Vocabulary Validation & Anti-Spoiler Protection
- **Italian (IT):** Real-time verification against the official **Treccani** vocabulary with automatic local cache learning (`wp_wordle_learned_words_it`).
- **English (EN):** Built-in curated dictionary with dynamic fallback and learning via the Datamuse API (`wp_wordle_learned_words_en`).
- **Security & Anti-Spoiler:** Word evaluation is handled entirely server-side through the WordPress REST API; the secret solution is never leaked to the client browser prior to puzzle completion.

## ⚙️ Admin Dashboard Settings

Navigate to **Settings** > **WP Wordle** in the WordPress admin panel to:
- Select the global default theme for your site.
- Set the default game language (Italian / English).
- Manually override the daily secret word (for contests, promotions, or special events).
- Inspect dictionary caching status and check active solutions protected by spoiler toggles.

## 🧪 Automated Test Suite

The plugin comes with a comprehensive, modular automated test suite (137 assertions across 8 dedicated test suites) that verifies dictionary integrity, all 60 modular CSS themes, the game engine (IT & EN), live online dictionary services (Treccani & Datamuse), REST API controllers, frontend shortcode rendering, admin dashboard, and containerized Docker environment integration.

To run the complete test suite:
```bash
php tests/run-tests.php
```
*(or via legacy alias `php tests/test-all-plugin.php`)*

To run specific test modules (e.g. dictionary, engine, api, themes, docker):
```bash
php tests/run-tests.php dictionary
```