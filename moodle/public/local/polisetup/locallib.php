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
 * Apply logic for the Poliedro corporate-university baseline.
 *
 * Every step is idempotent and guarded, so the routine is safe to run on
 * install and to re-run later (CLI or admin page). Nothing here is destructive
 * beyond flipping documented site settings — all reversible from Admin.
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Default site display name applied to the front page course.
 */
const LOCAL_POLISETUP_SITENAME = 'Poliedro';

/**
 * Full site name applied to the front page course.
 */
const LOCAL_POLISETUP_SITEFULLNAME = 'Poliedro — Universidade Corporativa';

/**
 * Apply the Poliedro corporate baseline to the site configuration.
 *
 * @return string[] Human-readable log lines describing what happened.
 */
function local_polisetup_apply(): array {
    global $CFG, $DB;

    $log = [];
    $set = function (string $name, $value, ?string $plugin = null) use (&$log) {
        set_config($name, $value, $plugin);
        $log[] = get_string('logset', 'local_polisetup', [
            'name' => ($plugin ? $plugin . '/' : '') . $name,
            'value' => is_scalar($value) ? (string) $value : json_encode($value),
        ]);
    };

    // 1. Make Poli the active theme (site + all device types).
    $set('theme', 'poli');
    $set('themelegacy', 'poli');
    $set('themetablet', '');
    $set('themephone', '');

    // 2. Corporate login: no self-registration, no guest button, no autologin.
    //    The theme templates already respect these flags; here we flip the site
    //    config so the buttons actually disappear. Reversible in Admin.
    $set('registerauth', '');
    $set('guestloginbutton', 0);
    $set('autologinguests', 0);

    // 3. Default interface language.
    $set('lang', 'pt_br');

    // 4. Site name / short name on the front page course.
    try {
        $siteid = (int) get_site()->id;
        $DB->set_field('course', 'fullname', LOCAL_POLISETUP_SITEFULLNAME, ['id' => $siteid]);
        $DB->set_field('course', 'shortname', LOCAL_POLISETUP_SITENAME, ['id' => $siteid]);
        $log[] = get_string('logsitename', 'local_polisetup', LOCAL_POLISETUP_SITEFULLNAME);
    } catch (\Throwable $e) {
        $log[] = get_string('logskip', 'local_polisetup', 'sitename: ' . $e->getMessage());
    }

    // 5. Best-effort: install the Brazilian Portuguese language pack. Needs
    //    outbound access to download.moodle.org; failure is non-fatal.
    try {
        if (get_string_manager()->translation_exists('pt_br', false)) {
            $log[] = get_string('loglangpresent', 'local_polisetup');
        } else {
            require_once($CFG->dirroot . '/admin/tool/langimport/classes/controller.php');
            $controller = new \tool_langimport\controller();
            // Returns the number of packs installed; throws on download error.
            $installed = (int) $controller->install_languagepacks(['pt_br']);
            $log[] = $installed > 0
                ? get_string('loglanginstalled', 'local_polisetup')
                : get_string('loglangfailed', 'local_polisetup');
        }
    } catch (\Throwable $e) {
        $log[] = get_string('logskip', 'local_polisetup', 'langpack: ' . $e->getMessage());
    }

    // Record that the baseline ran (used to avoid re-running silently on every
    // upgrade, while still allowing a manual re-apply).
    $set('lastapplied', time(), 'local_polisetup');

    purge_all_caches();
    $log[] = get_string('logdone', 'local_polisetup');

    return $log;
}
