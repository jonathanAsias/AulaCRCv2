<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme AulaV2 callbacks (SCSS, pluginfile, frontpage context).
 *
 * @package    theme_aulav2
 * @copyright  2026 AulaCRC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Purge theme CSS after settings change.
 *
 * @return void
 */
function theme_aulav2_invalidate_caches(): void {
    theme_reset_all_caches();
}

/**
 * Serve theme setting files.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_aulav2_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $validareas = [
        'logo',
        'logocompact',
        'favicon',
        'backgroundimage',
    ];
    if ($context->contextlevel == CONTEXT_SYSTEM && in_array($filearea, $validareas, true)) {
        $theme = theme_config::load('aulav2');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}

/**
 * Setting file as moodle_url, or null.
 *
 * @param theme_config $theme
 * @param string $setting
 * @return \moodle_url|null
 */
function theme_aulav2_setting_moodle_url(theme_config $theme, string $setting): ?\moodle_url {
    $componentfile = $theme->setting_file_url($setting, $setting);
    if (empty($componentfile)) {
        return null;
    }
    return new \moodle_url($componentfile);
}

/**
 * Validate hex colour.
 *
 * @param mixed $value
 * @param string $default
 * @return string
 */
function theme_aulav2_sanitize_color($value, string $default = '#000000'): string {
    if ($value === null || $value === false || $value === '') {
        return $default;
    }
    $value = trim((string) $value);
    if (preg_match('/^([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
        $value = '#' . $value;
    }
    if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
        return $value;
    }
    return $default;
}

/**
 * Sanitize URL for use inside SCSS url("...").
 *
 * @param string $url
 * @return string
 */
function theme_aulav2_scss_url(string $url): string {
    if (strpos($url, '//') === 0) {
        $url = 'https:' . $url;
    }
    return str_replace(['"', "'", '(', ')', '\\'], '', $url);
}

/**
 * Allowed site font choices.
 *
 * @return array<string, string>
 */
function theme_aulav2_site_font_choices(): array {
    return [
        'system' => '',
        'inter' => 'Inter',
        'roboto' => 'Roboto',
        'opensans' => 'Open Sans',
        'nunitosans' => 'Nunito Sans',
        'poppins' => 'Poppins',
        'custom' => '',
    ];
}

/**
 * @param mixed $value
 * @return string
 */
function theme_aulav2_sanitize_google_font_name($value): string {
    if ($value === null || $value === false) {
        return '';
    }
    $value = trim((string) $value);
    if ($value === '' || !preg_match('/^[A-Za-z][A-Za-z0-9 \-]{0,60}$/', $value)) {
        return '';
    }
    return $value;
}

/**
 * @param stdClass|null $config
 * @return array{stack: string, google: ?string, apply: bool}
 */
function theme_aulav2_resolve_site_font($config): array {
    $system = 'system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    $choice = (is_object($config) && isset($config->site_font)) ? trim((string) $config->site_font) : '';
    if ($choice === '') {
        return ['stack' => $system, 'google' => null, 'apply' => false];
    }

    $choices = theme_aulav2_site_font_choices();
    if (!array_key_exists($choice, $choices)) {
        return ['stack' => $system, 'google' => null, 'apply' => false];
    }

    if ($choice === 'system') {
        return ['stack' => $system, 'google' => null, 'apply' => true];
    }

    if ($choice === 'custom') {
        $custom = theme_aulav2_sanitize_google_font_name($config->site_font_custom ?? '');
        if ($custom === '') {
            return ['stack' => $system, 'google' => null, 'apply' => true];
        }
        return [
            'stack' => '"' . $custom . '", ' . $system,
            'google' => $custom,
            'apply' => true,
        ];
    }

    $family = $choices[$choice];
    return [
        'stack' => '"' . $family . '", ' . $system,
        'google' => $family,
        'apply' => true,
    ];
}

/**
 * Google Fonts link tags.
 *
 * @return string
 */
function theme_aulav2_google_fonts_head_html(): string {
    $font = theme_aulav2_resolve_site_font(get_config('theme_aulav2'));
    if (empty($font['google'])) {
        return '';
    }
    $family = rawurlencode($font['google']);
    $url = 'https://fonts.googleapis.com/css2?family=' . $family . ':wght@400;600;700;800&display=swap';
    $html = "\n<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n";
    $html .= "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
    $html .= '<link rel="stylesheet" href="' . s($url) . '">' . "\n";
    return $html;
}

/**
 * Runtime CSS variables (navbar + home palette + font).
 *
 * @return string
 */
function theme_aulav2_get_runtime_css(): string {
    $cfg = get_config('theme_aulav2');
    $css = ':root {';
    $css .= '--aulav2-navbar-bg: ' . theme_aulav2_sanitize_color($cfg->navbarcolor ?? null, '#FFFFFF') . ';';
    $css .= '--aulav2-navbar-color: ' . theme_aulav2_sanitize_color($cfg->navbartextcolor ?? null, '#1D2433') . ';';
    $css .= '--aulav2-govco: ' . theme_aulav2_sanitize_color($cfg->color_govco ?? null, '#0943b5') . ';';
    $css .= '--aulav2-navy: ' . theme_aulav2_sanitize_color($cfg->color_navy ?? null, '#27348b') . ';';
    $css .= '--aulav2-ink: ' . theme_aulav2_sanitize_color($cfg->color_ink ?? null, '#091e3f') . ';';
    $css .= '--aulav2-hero-bg: ' . theme_aulav2_sanitize_color($cfg->color_hero_bg ?? null, '#e5ecf8') . ';';
    $css .= '--aulav2-border: ' . theme_aulav2_sanitize_color($cfg->color_border ?? null, '#b5c7e9') . ';';

    $font = theme_aulav2_resolve_site_font($cfg);
    if (!empty($font['apply'])) {
        $css .= '--aulav2-font-family: ' . $font['stack'] . ';';
    }
    $css .= '}';

    if (!empty($font['apply'])) {
        $css .= 'body.aulav2-site-font, body.aulav2-site-font .aulav2-home { font-family: var(--aulav2-font-family); }';
    }

    // Override home SCSS defaults with admin palette.
    $css .= 'body.pagelayout-frontpage {';
    $css .= '--aulav2-govco: ' . theme_aulav2_sanitize_color($cfg->color_govco ?? null, '#0943b5') . ';';
    $css .= '--aulav2-navy: ' . theme_aulav2_sanitize_color($cfg->color_navy ?? null, '#27348b') . ';';
    $css .= '--aulav2-ink: ' . theme_aulav2_sanitize_color($cfg->color_ink ?? null, '#091e3f') . ';';
    $css .= '--aulav2-hero-bg: ' . theme_aulav2_sanitize_color($cfg->color_hero_bg ?? null, '#e5ecf8') . ';';
    $css .= '--aulav2-border: ' . theme_aulav2_sanitize_color($cfg->color_border ?? null, '#b5c7e9') . ';';
    $css .= '}';

    return $css;
}

/**
 * Main SCSS content.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aulav2_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    if ($filename === 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && $filename !== 'default.scss'
            && ($presetfile = $fs->get_file($context->id, 'theme_aulav2', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    $scss .= file_get_contents($CFG->dirroot . '/theme/aulav2/scss/post.scss');
    $home = $CFG->dirroot . '/theme/aulav2/scss/aulav2/_home.scss';
    if (is_readable($home)) {
        $scss .= file_get_contents($home);
    }

    return $scss;
}

/**
 * @return string
 */
function theme_aulav2_get_precompiled_css() {
    return theme_boost_get_precompiled_css();
}

/**
 * Pre-SCSS (Boost variables).
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aulav2_get_pre_scss($theme) {
    global $CFG;

    try {
        $scss = '';
        $map = [
            'brandcolor' => ['primary', '#0943b5'],
            'secondarycolor' => ['secondary', '#27348b'],
            'bodybgcolor' => ['body-bg', '#ffffff'],
        ];
        foreach ($map as $configkey => [$sassvar, $default]) {
            $raw = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
            $value = theme_aulav2_sanitize_color($raw, $default);
            $scss .= '$' . $sassvar . ': ' . $value . ";\n";
        }

        $scss .= "\$body-color: #091e3f;\n";
        $scss .= "\$link-color: \$primary;\n";

        $settings = (isset($theme->settings) && is_object($theme->settings)) ? $theme->settings : null;
        $font = theme_aulav2_resolve_site_font($settings);
        if (!empty($font['apply'])) {
            $scss .= '$font-family-base: ' . $font['stack'] . ";\n";
            $scss .= "\$headings-font-family: \$font-family-base;\n";
        }

        $pre = $CFG->dirroot . '/theme/aulav2/scss/pre.scss';
        if (is_readable($pre)) {
            $scss .= file_get_contents($pre);
        }

        if (!empty($theme->settings->scsspre)) {
            $scss .= "\n" . (string) $theme->settings->scsspre . "\n";
        }

        return $scss;
    } catch (\Throwable $e) {
        debugging('theme_aulav2_get_pre_scss failed: ' . $e->getMessage(), DEBUG_NORMAL);
        return "\$primary: #0943b5;\n\$secondary: #27348b;\n";
    }
}

/**
 * Extra SCSS after compilation.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aulav2_get_extra_scss($theme) {
    try {
        $content = '';
        $config = $theme->settings ?? null;
        $bgtype = (!empty($config->site_bg_type)) ? (string) $config->site_bg_type : 'color';

        if ($bgtype === 'image') {
            $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
            if (!empty($imageurl)) {
                $safe = theme_aulav2_scss_url((string) $imageurl);
                $content .= 'body { background-image: url("' . $safe . '"); ';
                $content .= 'background-size: cover; background-attachment: fixed; background-position: center; }';
            }
        }

        if (!empty($theme->settings->scss)) {
            $content = (string) $theme->settings->scss . "\n" . $content;
        }

        return $content;
    } catch (\Throwable $e) {
        debugging('theme_aulav2_get_extra_scss failed: ' . $e->getMessage(), DEBUG_NORMAL);
        return '';
    }
}

/**
 * Pix URL under theme/aulav2/pix/.
 *
 * @param string $name
 * @return string
 */
function theme_aulav2_pix_url(string $name): string {
    global $OUTPUT;
    return (string) $OUTPUT->image_url($name, 'theme_aulav2');
}

/**
 * Mustache context for the mockup frontpage.
 *
 * @return array
 */
function theme_aulav2_get_frontpage_context(): array {
    global $CFG;

    $webcrc = !empty($CFG->wwwroot) ? $CFG->wwwroot : '#';

    return [
        'aulav2_frontpage' => true,
        'home' => [
            'pix' => [
                'faq_icon' => theme_aulav2_pix_url('faq-icon'),
                'crc_crest' => theme_aulav2_pix_url('crc-crest'),
                'crc_text' => theme_aulav2_pix_url('crc-text'),
                'crc_sub' => theme_aulav2_pix_url('crc-sub'),
                'prai' => theme_aulav2_pix_url('prai'),
                'icon_x' => theme_aulav2_pix_url('icon-x'),
                'icon_fb' => theme_aulav2_pix_url('icon-fb'),
                'icon_ig' => theme_aulav2_pix_url('icon-ig'),
                'icon_yt' => theme_aulav2_pix_url('icon-yt'),
                'icon_in' => theme_aulav2_pix_url('icon-in'),
                'icon_ss' => theme_aulav2_pix_url('icon-ss'),
                'govco_logo' => theme_aulav2_pix_url('govco-logo'),
                'co_logo' => theme_aulav2_pix_url('co-logo'),
            ],
            'search_placeholder' => get_string('search_placeholder', 'theme_aulav2'),
            'webcrc_url' => $webcrc,
            'audiences' => [
                ['label' => get_string('audience_ciudadania', 'theme_aulav2')],
                ['label' => get_string('audience_industria', 'theme_aulav2')],
                ['label' => get_string('audience_equipo', 'theme_aulav2')],
                ['label' => get_string('audience_entidades', 'theme_aulav2')],
                ['label' => get_string('audience_academia', 'theme_aulav2')],
            ],
            'search_tags' => [
                get_string('tag_calidad', 'theme_aulav2'),
                get_string('tag_pqr', 'theme_aulav2'),
                get_string('tag_fraude', 'theme_aulav2'),
                get_string('tag_portabilidad', 'theme_aulav2'),
            ],
            'temas' => [
                [
                    'title' => get_string('tema_proteccion', 'theme_aulav2'),
                    'desc' => get_string('tema_proteccion_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 26),
                    'icon' => 'shield',
                ],
                [
                    'title' => get_string('tema_comunicaciones', 'theme_aulav2'),
                    'desc' => get_string('tema_comunicaciones_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 22),
                    'icon' => 'globe',
                ],
                [
                    'title' => get_string('tema_audiovisuales', 'theme_aulav2'),
                    'desc' => get_string('tema_audiovisuales_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 18),
                    'icon' => 'tv',
                ],
                [
                    'title' => get_string('tema_postal', 'theme_aulav2'),
                    'desc' => get_string('tema_postal_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 11),
                    'icon' => 'mail',
                ],
                [
                    'title' => get_string('tema_competencia', 'theme_aulav2'),
                    'desc' => get_string('tema_competencia_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 26),
                    'icon' => 'chart',
                ],
                [
                    'title' => get_string('tema_datos', 'theme_aulav2'),
                    'desc' => get_string('tema_datos_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 17),
                    'icon' => 'data',
                ],
                [
                    'title' => get_string('tema_innovacion', 'theme_aulav2'),
                    'desc' => get_string('tema_innovacion_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 12),
                    'icon' => 'pulse',
                ],
                [
                    'title' => get_string('tema_gestion', 'theme_aulav2'),
                    'desc' => get_string('tema_gestion_desc', 'theme_aulav2'),
                    'count' => get_string('recursos_n', 'theme_aulav2', 9),
                    'icon' => 'gear',
                ],
            ],
            'ruta_steps' => [
                get_string('ruta_1', 'theme_aulav2'),
                get_string('ruta_2', 'theme_aulav2'),
                get_string('ruta_3', 'theme_aulav2'),
                get_string('ruta_4', 'theme_aulav2'),
            ],
            'faq_answer' => get_string('faq_answer', 'theme_aulav2'),
        ],
    ];
}
