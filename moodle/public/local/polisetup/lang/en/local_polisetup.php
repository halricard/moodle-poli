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
 * Strings for local_polisetup (English).
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Poliedro setup';
$string['applyintro'] = 'This applies the Poliedro corporate-university baseline to your site. It runs automatically when the plugin is installed; use the button below to re-apply it. Everything below is a standard site setting and can be reverted in Admin.';
$string['applybutton'] = 'Apply Poliedro baseline';
$string['applied'] = 'Poliedro baseline applied.';
$string['applyitem_theme'] = 'Set Poli as the active theme (all devices).';
$string['applyitem_login'] = 'Corporate login: disable self-registration, the guest login button and guest autologin.';
$string['applyitem_lang'] = 'Set Brazilian Portuguese (pt_br) as the default language.';
$string['applyitem_sitename'] = 'Set the site name to Poliedro.';
$string['applyitem_langpack'] = 'Download the pt_br language pack (requires internet; skipped if unavailable).';

$string['logset'] = 'Set {$a->name} = {$a->value}';
$string['logsitename'] = 'Site name set to “{$a}”';
$string['loglangpresent'] = 'Language pack pt_br already installed';
$string['loglanginstalled'] = 'Language pack pt_br installed';
$string['loglangfailed'] = 'Could not download the pt_br language pack — install it manually under Language packs';
$string['logskip'] = 'Skipped: {$a}';
$string['logdone'] = 'Done. Caches purged.';

$string['privacy:metadata'] = 'The Poliedro setup plugin does not store any personal data.';
