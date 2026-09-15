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
    // Mockup uses Nunito Sans; also load any admin-selected Google font.
    $families = ['Nunito Sans'];
    $font = theme_aulav2_resolve_site_font(get_config('theme_aulav2'));
    if (!empty($font['google']) && !in_array($font['google'], $families, true)) {
        $families[] = $font['google'];
    }
    $html = "\n<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">\n";
    $html .= "<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>\n";
    foreach ($families as $family) {
        $url = 'https://fonts.googleapis.com/css2?family=' . rawurlencode($family) . ':wght@400;500;600;700;800&display=swap';
        $html .= '<link rel="stylesheet" href="' . s($url) . '">' . "\n";
    }
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
 * Overview image URL for a course, or empty string.
 *
 * @param stdClass $course
 * @return string
 */
function theme_aulav2_course_image_url(stdClass $course): string {
    try {
        $list = new \core_course_list_element($course);
        foreach ($list->get_course_overviewfiles() as $file) {
            if (!$file->is_valid_image()) {
                continue;
            }
            return (string) \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                null,
                $file->get_filepath(),
                $file->get_filename()
            );
        }
    } catch (\Throwable $e) {
        return '';
    }
    return '';
}

/**
 * Visible top-level categories for the frontpage.
 *
 * @param int $limit
 * @return array<int, array{id:int,name:string,desc:string,url:string,count:int,countlabel:string}>
 */
function theme_aulav2_get_frontpage_categories(int $limit = 8): array {
    $out = [];
    try {
        $top = \core_course_category::top();
        foreach ($top->get_children() as $cat) {
            if (!$cat->is_uservisible()) {
                continue;
            }
            $desc = '';
            if (!empty($cat->description)) {
                $desc = content_to_text($cat->description, $cat->descriptionformat ?? FORMAT_HTML);
                $desc = trim($desc);
                if (\core_text::strlen($desc) > 140) {
                    $desc = \core_text::substr($desc, 0, 137) . '…';
                }
            }
            $count = (int) $cat->get_courses_count();
            $out[] = [
                'id' => (int) $cat->id,
                'name' => $cat->get_formatted_name(),
                'desc' => $desc,
                'url' => (new \moodle_url('/course/index.php', ['categoryid' => $cat->id]))->out(false),
                'count' => $count,
                'countlabel' => get_string('recursos_n', 'theme_aulav2', $count),
            ];
            if (count($out) >= $limit) {
                break;
            }
        }
    } catch (\Throwable $e) {
        return [];
    }
    return $out;
}

/**
 * Visible courses for the frontpage (excludes site course).
 *
 * @param int $limit
 * @return array<int, array{id:int,name:string,shortname:string,summary:string,url:string,imageurl:string,category:string,hasimage:bool}>
 */
function theme_aulav2_get_frontpage_courses(int $limit = 12): array {
    global $CFG;
    require_once($CFG->dirroot . '/course/lib.php');

    $out = [];
    try {
        $courses = get_courses('all', 'c.sortorder ASC', 'c.id,c.fullname,c.shortname,c.summary,c.visible,c.category,c.summaryformat');
        foreach ($courses as $course) {
            if ((int) $course->id === (int) SITEID) {
                continue;
            }
            $context = \context_course::instance($course->id);
            if (empty($course->visible) && !has_capability('moodle/course:viewhiddencourses', $context)) {
                continue;
            }
            $summary = '';
            if (!empty($course->summary)) {
                $summary = format_string($course->summary, true, ['context' => $context]);
                $summary = trim(html_to_text($summary, 0, false));
                if (\core_text::strlen($summary) > 160) {
                    $summary = \core_text::substr($summary, 0, 157) . '…';
                }
            }
            $catname = '';
            try {
                $cat = \core_course_category::get($course->category, IGNORE_MISSING);
                if ($cat) {
                    $catname = $cat->get_formatted_name();
                }
            } catch (\Throwable $e) {
                $catname = '';
            }
            $image = theme_aulav2_course_image_url($course);
            $out[] = [
                'id' => (int) $course->id,
                'name' => format_string($course->fullname, true, ['context' => $context]),
                'shortname' => format_string($course->shortname, true, ['context' => $context]),
                'summary' => $summary,
                'url' => (new \moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
                'imageurl' => $image,
                'hasimage' => $image !== '',
                'category' => $catname,
            ];
            if (count($out) >= $limit) {
                break;
            }
        }
    } catch (\Throwable $e) {
        return [];
    }
    return $out;
}

/**
 * Mustache context for the mockup frontpage (live categories + courses).
 *
 * @return array
 */
function theme_aulav2_get_frontpage_context(): array {
    global $CFG;

    $webcrc = get_config('theme_aulav2', 'webcrc_url');
    if ($webcrc === false || trim((string) $webcrc) === '') {
        $webcrc = 'https://www.crcom.gov.co';
    }

    $catalog = get_config('theme_aulav2', 'catalog_url');
    if ($catalog === false || trim((string) $catalog) === '') {
        $catalog = $CFG->wwwroot . '/course/index.php';
    }

    $headerlogo = '';
    try {
        $theme = \theme_config::load('aulav2');
        $logourl = theme_aulav2_setting_moodle_url($theme, 'logocompact');
        if (!$logourl) {
            $logourl = theme_aulav2_setting_moodle_url($theme, 'logo');
        }
        if ($logourl) {
            $headerlogo = (string) $logourl;
        }
    } catch (\Throwable $e) {
        $headerlogo = '';
    }

    $categories = theme_aulav2_get_frontpage_categories(8);
    $courses = theme_aulav2_get_frontpage_courses(12);

    // Audiences = top categories (up to 5).
    $audiences = [];
    foreach (array_slice($categories, 0, 5) as $cat) {
        $audiences[] = [
            'label' => $cat['name'],
            'url' => $cat['url'],
        ];
    }
    // Fallback labels if no categories yet.
    if ($audiences === []) {
        $audiences = [
            ['label' => get_string('audience_ciudadania', 'theme_aulav2'), 'url' => $catalog],
            ['label' => get_string('audience_industria', 'theme_aulav2'), 'url' => $catalog],
            ['label' => get_string('audience_equipo', 'theme_aulav2'), 'url' => $catalog],
            ['label' => get_string('audience_entidades', 'theme_aulav2'), 'url' => $catalog],
            ['label' => get_string('audience_academia', 'theme_aulav2'), 'url' => $catalog],
        ];
    }

    // Temas = categories with counts.
    $temas = [];
    foreach ($categories as $cat) {
        $temas[] = [
            'title' => $cat['name'],
            'desc' => $cat['desc'] !== '' ? $cat['desc'] : get_string('categorycourses', 'theme_aulav2', $cat['count']),
            'count' => $cat['countlabel'],
            'url' => $cat['url'],
        ];
    }
    if ($temas === []) {
        $temas = [
            [
                'title' => get_string('tema_proteccion', 'theme_aulav2'),
                'desc' => get_string('tema_proteccion_desc', 'theme_aulav2'),
                'count' => get_string('recursos_n', 'theme_aulav2', 0),
                'url' => $catalog,
            ],
        ];
    }

    // Search tags from category names.
    $searchtags = [];
    foreach (array_slice($categories, 0, 4) as $cat) {
        $searchtags[] = $cat['name'];
    }
    if ($searchtags === []) {
        $searchtags = [
            get_string('tag_calidad', 'theme_aulav2'),
            get_string('tag_pqr', 'theme_aulav2'),
            get_string('tag_fraude', 'theme_aulav2'),
            get_string('tag_portabilidad', 'theme_aulav2'),
        ];
    }

    $featured = $courses[0] ?? null;
    $series = array_slice($courses, 1, 3);
    $recommended = array_slice($courses, 0, 3);
    if (count($courses) > 3) {
        $recommended = array_slice($courses, 3, 3);
        if (count($recommended) < 3) {
            $recommended = array_slice($courses, 0, 3);
        }
    }

    // Ruta steps: first courses as learning path, else static strings.
    $rutasteps = [];
    foreach (array_slice($courses, 0, 4) as $i => $course) {
        $rutasteps[] = [
            'label' => ($i + 1) . '. ' . $course['name'],
            'url' => $course['url'],
            'imageurl' => $course['imageurl'],
            'hasimage' => $course['hasimage'],
        ];
    }
    if ($rutasteps === []) {
        $rutasteps = [
            ['label' => get_string('ruta_1', 'theme_aulav2'), 'url' => $catalog, 'imageurl' => '', 'hasimage' => false],
            ['label' => get_string('ruta_2', 'theme_aulav2'), 'url' => $catalog, 'imageurl' => '', 'hasimage' => false],
            ['label' => get_string('ruta_3', 'theme_aulav2'), 'url' => $catalog, 'imageurl' => '', 'hasimage' => false],
            ['label' => get_string('ruta_4', 'theme_aulav2'), 'url' => $catalog, 'imageurl' => '', 'hasimage' => false],
        ];
    }

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
            'header_logo_url' => $headerlogo,
            'search_placeholder' => get_string('search_placeholder', 'theme_aulav2'),
            'webcrc_url' => $webcrc,
            'catalog_url' => $catalog,
            'has_courses' => $courses !== [],
            'has_categories' => $categories !== [],
            'audiences' => $audiences,
            'search_tags' => $searchtags,
            'featured' => $featured,
            'series' => $series,
            'temas' => $temas,
            'ruta_steps' => $rutasteps,
            'ruta_cta_url' => $featured['url'] ?? $catalog,
            'recommended' => $recommended,
            'faq_answer' => get_string('faq_answer', 'theme_aulav2'),
        ],
    ];
}
