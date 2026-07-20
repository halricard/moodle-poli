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
 * Admin page to re-apply the Poliedro baseline with a confirmation step.
 *
 * @package    local_polisetup
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/polisetup/locallib.php');

admin_externalpage_setup('local_polisetup');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$confirm = optional_param('confirm', 0, PARAM_BOOL);
$pageurl = new moodle_url('/local/polisetup/index.php');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_polisetup'));

if ($confirm && confirm_sesskey()) {
    $log = local_polisetup_apply();
    echo $OUTPUT->notification(get_string('applied', 'local_polisetup'), 'notifysuccess');
    echo html_writer::start_tag('ul');
    foreach ($log as $line) {
        echo html_writer::tag('li', s($line));
    }
    echo html_writer::end_tag('ul');
    echo $OUTPUT->continue_button(new moodle_url('/admin/search.php'));
} else {
    echo html_writer::tag('p', get_string('applyintro', 'local_polisetup'));
    echo html_writer::start_tag('ul');
    foreach (['applyitem_theme', 'applyitem_login', 'applyitem_lang', 'applyitem_sitename', 'applyitem_langpack'] as $item) {
        echo html_writer::tag('li', get_string($item, 'local_polisetup'));
    }
    echo html_writer::end_tag('ul');

    $applyurl = new moodle_url($pageurl, ['confirm' => 1, 'sesskey' => sesskey()]);
    echo $OUTPUT->single_button($applyurl, get_string('applybutton', 'local_polisetup'), 'post');
}

echo $OUTPUT->footer();
