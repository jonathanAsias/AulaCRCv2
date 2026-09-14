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
 * Core renderer — logos, favicon, runtime CSS.
 *
 * @package    theme_aulav2
 * @copyright  2026 AulaCRC
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_aulav2\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Extends Boost renderer for AulaV2 brand assets.
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * @return string
     */
    public function standard_head_html() {
        $html = parent::standard_head_html();
        $html .= theme_aulav2_google_fonts_head_html();
        $css = theme_aulav2_get_runtime_css();
        if ($css !== '') {
            $html .= "\n<style id=\"theme-aulav2-runtime\">\n" . $css . "</style>\n";
        }
        return $html;
    }

    /**
     * @param string|array $additionalclasses
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', (string) $additionalclasses);
        }
        $additionalclasses[] = 'theme-aulav2';
        $sitefont = theme_aulav2_resolve_site_font(get_config('theme_aulav2'));
        if (!empty($sitefont['apply'])) {
            $additionalclasses[] = 'aulav2-site-font';
        }
        if ($this->page->pagelayout === 'frontpage') {
            $additionalclasses[] = 'aulav2-frontpage';
        }
        return parent::body_attributes($additionalclasses);
    }

    /**
     * @param int|null $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        $url = theme_aulav2_setting_moodle_url($this->page->theme, 'logo');
        if ($url) {
            return $url;
        }
        return parent::get_logo_url($maxwidth, $maxheight);
    }

    /**
     * @param int $maxwidth
     * @param int $maxheight
     * @return \moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        $url = theme_aulav2_setting_moodle_url($this->page->theme, 'logocompact');
        if ($url) {
            return $url;
        }
        $url = theme_aulav2_setting_moodle_url($this->page->theme, 'logo');
        if ($url) {
            return $url;
        }
        return parent::get_compact_logo_url($maxwidth, $maxheight);
    }

    /**
     * @return bool
     */
    public function should_display_navbar_logo() {
        $show = get_config('theme_aulav2', 'show_logo');
        if ($show !== false && (string) $show === '0') {
            return false;
        }
        if (theme_aulav2_setting_moodle_url($this->page->theme, 'logocompact')
                || theme_aulav2_setting_moodle_url($this->page->theme, 'logo')) {
            return true;
        }
        return parent::should_display_navbar_logo();
    }

    /**
     * @return \moodle_url
     */
    public function favicon() {
        $url = theme_aulav2_setting_moodle_url($this->page->theme, 'favicon');
        if ($url) {
            return $url;
        }
        return parent::favicon();
    }
}
