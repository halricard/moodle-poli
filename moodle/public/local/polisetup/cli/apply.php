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
 * Re-apply the Poliedro baseline from the command line.
 *
 * Usage: php local/polisetup/cli/apply.php
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/local/polisetup/locallib.php');

[$options, $unrecognised] = cli_get_params(['help' => false], ['h' => 'help']);

if ($options['help']) {
    cli_writeln("Apply the Poliedro corporate baseline (theme, login, language, site name).\n");
    cli_writeln("Usage: php local/polisetup/cli/apply.php");
    exit(0);
}

cli_heading('Poliedro setup');
foreach (local_polisetup_apply() as $line) {
    cli_writeln('  ' . $line);
}
exit(0);
