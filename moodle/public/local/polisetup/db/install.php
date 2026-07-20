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
 * Runs the Poliedro baseline once, when the plugin is installed.
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Install hook: apply the corporate baseline.
 *
 * @return bool
 */
function xmldb_local_polisetup_install() {
    global $CFG;
    require_once($CFG->dirroot . '/local/polisetup/locallib.php');

    $log = local_polisetup_apply();
    foreach ($log as $line) {
        mtrace('  [polisetup] ' . $line);
    }

    return true;
}
