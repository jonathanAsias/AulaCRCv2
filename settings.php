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
 * Administration settings for theme AulaV2.
 *
 * @package    theme_aulav2
 * @copyright  2026 AulaCRC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/lib.php');

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs(
        'themesettingaulav2',
        get_string('configtitle', 'theme_aulav2')
    );

    // --- General ---
    $page = new admin_settingpage('theme_aulav2_general', get_string('generalsettings', 'theme_aulav2'));

    $page->add(new admin_setting_heading(
        'theme_aulav2/general_heading_brand',
        get_string('settings_heading_brand', 'theme_aulav2'),
        get_string('settings_heading_branddesc', 'theme_aulav2')
    ));

    $setting = new admin_setting_configstoredfile(
        'theme_aulav2/logo',
        get_string('logo', 'theme_aulav2'),
        get_string('logodesc', 'theme_aulav2'),
        'logo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif', '.webp']]
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile(
        'theme_aulav2/logocompact',
        get_string('logocompact', 'theme_aulav2'),
        get_string('logocompactdesc', 'theme_aulav2'),
        'logocompact',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif', '.webp']]
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile(
        'theme_aulav2/favicon',
        get_string('favicon', 'theme_aulav2'),
        get_string('favicondesc', 'theme_aulav2'),
        'favicon',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.ico', '.png', '.svg']]
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcheckbox(
        'theme_aulav2/show_logo',
        get_string('show_logo', 'theme_aulav2'),
        get_string('show_logodesc', 'theme_aulav2'),
        1
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_aulav2/brandcolor',
        get_string('brandcolor', 'theme_aulav2'),
        get_string('brandcolordesc', 'theme_aulav2'),
        '#0943b5'
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_aulav2/secondarycolor',
        get_string('secondarycolor', 'theme_aulav2'),
        get_string('secondarycolordesc', 'theme_aulav2'),
        '#27348b'
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_aulav2/navbarcolor',
        get_string('navbarcolor', 'theme_aulav2'),
        get_string('navbarcolordesc', 'theme_aulav2'),
        '#FFFFFF'
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_aulav2/navbartextcolor',
        get_string('navbartextcolor', 'theme_aulav2'),
        get_string('navbartextcolordesc', 'theme_aulav2'),
        '#1D2433'
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_aulav2/bodybgcolor',
        get_string('bodybgcolor', 'theme_aulav2'),
        get_string('bodybgcolordesc', 'theme_aulav2'),
        '#FFFFFF'
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configselect(
        'theme_aulav2/site_bg_type',
        get_string('site_bg_type', 'theme_aulav2'),
        get_string('site_bg_typedesc', 'theme_aulav2'),
        'color',
        [
            'color' => get_string('site_bg_type_color', 'theme_aulav2'),
            'image' => get_string('site_bg_type_image', 'theme_aulav2'),
        ]
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile(
        'theme_aulav2/backgroundimage',
        get_string('backgroundimage', 'theme_aulav2'),
        get_string('backgroundimagedesc', 'theme_aulav2'),
        'backgroundimage',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg']]
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $page->add(new admin_setting_heading(
        'theme_aulav2/general_heading_typography',
        get_string('settings_heading_typography', 'theme_aulav2'),
        get_string('settings_heading_typographydesc', 'theme_aulav2')
    ));

    $fontchoices = [];
    foreach (array_keys(theme_aulav2_site_font_choices()) as $key) {
        $strkey = ($key === 'custom') ? 'site_font_customoption' : ('site_font_' . $key);
        $fontchoices[$key] = get_string($strkey, 'theme_aulav2');
    }
    $setting = new admin_setting_configselect(
        'theme_aulav2/site_font',
        get_string('site_font', 'theme_aulav2'),
        get_string('site_fontdesc', 'theme_aulav2'),
        'system',
        $fontchoices
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configtext(
        'theme_aulav2/site_font_custom',
        get_string('site_font_custom', 'theme_aulav2'),
        get_string('site_font_customdesc', 'theme_aulav2'),
        '',
        PARAM_TEXT
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $settings->add($page);

    // --- Home colours ---
    $page = new admin_settingpage('theme_aulav2_colors', get_string('colorsettings', 'theme_aulav2'));

    $page->add(new admin_setting_heading(
        'theme_aulav2/colors_heading',
        get_string('settings_heading_home_colors', 'theme_aulav2'),
        get_string('settings_heading_home_colorsdesc', 'theme_aulav2')
    ));

    $homecolors = [
        'color_govco' => '#0943b5',
        'color_navy' => '#27348b',
        'color_ink' => '#091e3f',
        'color_hero_bg' => '#e5ecf8',
        'color_border' => '#b5c7e9',
    ];
    foreach ($homecolors as $key => $default) {
        $setting = new admin_setting_configcolourpicker(
            'theme_aulav2/' . $key,
            get_string($key, 'theme_aulav2'),
            get_string($key . 'desc', 'theme_aulav2'),
            $default
        );
        $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
        $page->add($setting);
    }

    $settings->add($page);

    // --- Advanced ---
    $page = new admin_settingpage('theme_aulav2_advanced', get_string('advancedsettings', 'theme_aulav2'));

    $setting = new admin_setting_configtextarea(
        'theme_aulav2/scsspre',
        get_string('rawscsspre', 'theme_aulav2'),
        get_string('rawscsspre_desc', 'theme_aulav2'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $setting = new admin_setting_configtextarea(
        'theme_aulav2/scss',
        get_string('rawscss', 'theme_aulav2'),
        get_string('rawscss_desc', 'theme_aulav2'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_aulav2_invalidate_caches');
    $page->add($setting);

    $settings->add($page);
}
