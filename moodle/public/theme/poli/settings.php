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
 * Poli theme settings.
 *
 * @package    theme_poli
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingpoli', get_string('configtitle', 'theme_poli'));

    // =========================================================================
    // General settings.
    // =========================================================================
    $page = new admin_settingpage('theme_poli_general', get_string('generalsettings', 'theme_poli'));

    // Preset.
    $name = 'theme_poli/preset';
    $title = get_string('preset', 'theme_poli');
    $description = get_string('preset_desc', 'theme_poli');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_poli', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'poli');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files.
    $name = 'theme_poli/presetfiles';
    $title = get_string('presetfiles', 'theme_poli');
    $description = get_string('presetfiles_desc', 'theme_poli');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        ['maxfiles' => 20, 'accepted_types' => ['.scss']]);
    $page->add($setting);

    // Brand colour (primary / teal).
    $name = 'theme_poli/brandcolor';
    $title = get_string('brandcolor', 'theme_poli');
    $description = get_string('brandcolor_desc', 'theme_poli');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#00AEC7');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Brand accent colour (magenta).
    $name = 'theme_poli/brandaccent';
    $title = get_string('brandaccent', 'theme_poli');
    $description = get_string('brandaccent_desc', 'theme_poli');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#E6007E');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Brand tertiary colour (amber).
    $name = 'theme_poli/brandtertiary';
    $title = get_string('brandtertiary', 'theme_poli');
    $description = get_string('brandtertiary_desc', 'theme_poli');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '#FFC400');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Dark-only logo.
    $name = 'theme_poli/logodark';
    $title = get_string('logodark', 'theme_poli');
    $description = get_string('logodark_desc', 'theme_poli');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logodark', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.webp', '.gif']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Navbar appearance style.
    $name = 'theme_poli/navbarstyle';
    $title = get_string('navbarstyle', 'theme_poli');
    $description = get_string('navbarstyle_desc', 'theme_poli');
    $choices = [
        'accent-gradient' => get_string('navbarstyle_accentgradient', 'theme_poli'),
        'accent-solid' => get_string('navbarstyle_accentsolid', 'theme_poli'),
        'fill-gradient' => get_string('navbarstyle_fillgradient', 'theme_poli'),
        'fill-solid' => get_string('navbarstyle_fillsolid', 'theme_poli'),
        'none' => get_string('navbarstyle_none', 'theme_poli'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'accent-gradient', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Navbar accent colour (from palette).
    $name = 'theme_poli/navbarcolor';
    $title = get_string('navbarcolor', 'theme_poli');
    $description = get_string('navbarcolor_desc', 'theme_poli');
    $choices = [
        'primary' => get_string('palette_primary', 'theme_poli'),
        'accent' => get_string('palette_accent', 'theme_poli'),
        'tertiary' => get_string('palette_tertiary', 'theme_poli'),
    ];
    $setting = new admin_setting_configselect($name, $title, $description, 'primary', $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background image.
    $name = 'theme_poli/backgroundimage';
    $title = get_string('backgroundimage', 'theme_poli');
    $description = get_string('backgroundimage_desc', 'theme_poli');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Login background image.
    $name = 'theme_poli/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_poli');
    $description = get_string('loginbackgroundimage_desc', 'theme_poli');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // =========================================================================
    // Front page / hero settings.
    // =========================================================================
    $page = new admin_settingpage('theme_poli_frontpage', get_string('frontpagesettings', 'theme_poli'));

    // Hero heading.
    $name = 'theme_poli/heroheading';
    $title = get_string('heroheading', 'theme_poli');
    $description = get_string('heroheading_desc', 'theme_poli');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('heroheading_default', 'theme_poli'), PARAM_TEXT);
    $page->add($setting);

    // Hero subheading.
    $name = 'theme_poli/herosubheading';
    $title = get_string('herosubheading', 'theme_poli');
    $description = get_string('herosubheading_desc', 'theme_poli');
    $setting = new admin_setting_configtextarea($name, $title, $description,
        get_string('herosubheading_default', 'theme_poli'), PARAM_TEXT);
    $page->add($setting);

    // Hero primary button label.
    $name = 'theme_poli/herobuttontext';
    $title = get_string('herobuttontext', 'theme_poli');
    $description = get_string('herobuttontext_desc', 'theme_poli');
    $setting = new admin_setting_configtext($name, $title, $description,
        get_string('herobuttontext_default', 'theme_poli'), PARAM_TEXT);
    $page->add($setting);

    // Hero primary button URL.
    $name = 'theme_poli/herobuttonurl';
    $title = get_string('herobuttonurl', 'theme_poli');
    $description = get_string('herobuttonurl_desc', 'theme_poli');
    $setting = new admin_setting_configtext($name, $title, $description, '/my/courses.php', PARAM_URL);
    $page->add($setting);

    // Hero background image.
    $name = 'theme_poli/herobackgroundimage';
    $title = get_string('herobackgroundimage', 'theme_poli');
    $description = get_string('herobackgroundimage_desc', 'theme_poli');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'herobackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Show categories section.
    $name = 'theme_poli/showcategories';
    $title = get_string('showcategories', 'theme_poli');
    $description = get_string('showcategories_desc', 'theme_poli');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);

    // Show featured courses section.
    $name = 'theme_poli/showcourses';
    $title = get_string('showcourses', 'theme_poli');
    $description = get_string('showcourses_desc', 'theme_poli');
    $setting = new admin_setting_configcheckbox($name, $title, $description, 1);
    $page->add($setting);

    // Number of featured courses.
    $name = 'theme_poli/courselimit';
    $title = get_string('courselimit', 'theme_poli');
    $description = get_string('courselimit_desc', 'theme_poli');
    $setting = new admin_setting_configtext($name, $title, $description, 8, PARAM_INT);
    $page->add($setting);

    $settings->add($page);

    // =========================================================================
    // Advanced settings (raw SCSS).
    // =========================================================================
    $page = new admin_settingpage('theme_poli_advanced', get_string('advancedsettings', 'theme_poli'));

    $setting = new admin_setting_scsscode('theme_poli/scsspre',
        get_string('rawscsspre', 'theme_poli'), get_string('rawscsspre_desc', 'theme_poli'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_scsscode('theme_poli/scss',
        get_string('rawscss', 'theme_poli'), get_string('rawscss_desc', 'theme_poli'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
