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
 * Poli drawers layout — Boost's drawer layout plus the shared navbar context
 * (auth buttons + dark-only logo) so every internal/admin page renders the Poli
 * navbar correctly.
 *
 * @package   theme_poli
 * @copyright 2026 Poliedro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $selectmenu = new \core\output\select_menu(
            'tertiarynavigation',
            $overflowdata->urls,
            $overflowdata->selected,
        );
        $selectmenu->set_label($overflowdata->label, $overflowdata->labelattributes);
        $overflow = $selectmenu->export_for_template($OUTPUT);
    }
}

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$coursefullname = ($PAGE->course?->fullname) ? format_string(
    $PAGE->course->fullname,
    true,
    ['context' => context_course::instance($PAGE->course->id), 'escape' => false],
) : '';
$courseurl = $PAGE->course ? new \core\url('/course/view.php', ['id' => $PAGE->course->id]) : null;

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'coursefullname' => $coursefullname,
    'courseurl' => $courseurl ? $courseurl->out(false) : null,
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => theme_poli_langtoggle_context(),
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
];

$templatecontext += theme_poli_navbar_context();
$templatecontext += theme_poli_footer_context();

// Grade reports (user report at /grade/report/user and the participant report
// at /course/user.php, both pagelayout 'report'): turn each percentage cell into
// a colour-banded visual bar so a student reads their standing at a glance
// instead of scanning a wall of numbers. Vanilla JS, so no AMD build step; the
// JS itself only touches grade tables + pure-"NN %" cells, so it's a no-op on
// any other report page.
if ($PAGE->pagelayout === 'report' || strpos($PAGE->pagetype, 'grade') !== false) {
    // Localised "Percentage" column header, injected so the JS bars ONLY that
    // column (not the weight / contribution columns, which are also "NN %").
    $pctjs = 'window.poliPctLabel = ' . json_encode(get_string('percentage', 'grades')) . ';';
    $PAGE->requires->js_init_code($pctjs . <<<'JS'

(function() {
    var norm = function(s) { return (s || '').replace(/\u00a0/g, ' ').trim(); };
    var LABEL = norm(window.poliPctLabel).toLowerCase();
    var run = function() {
        document.querySelectorAll('table.user-grade').forEach(function(table) {
            // Locate the percentage column by its header text.
            var idx = -1;
            var heads = table.querySelectorAll('thead th, thead td');
            if (!heads.length) { heads = table.querySelectorAll('tr:first-child th, tr:first-child td'); }
            heads.forEach(function(h) {
                if (idx < 0 && norm(h.textContent).toLowerCase() === LABEL) { idx = h.cellIndex; }
            });
            if (idx < 0) { return; }
            table.querySelectorAll('tbody tr').forEach(function(tr) {
                var cell = tr.cells && tr.cells[idx];
                if (!cell || cell.dataset.poliGraded) { return; }
                var m = norm(cell.textContent).match(/^(\d+(?:[.,]\d+)?)\s*%$/);
                if (!m) { return; }
                var pct = Math.max(0, Math.min(100, parseFloat(m[1].replace(',', '.'))));
                var band = pct >= 70 ? 'good' : (pct >= 50 ? 'mid' : 'low');
                cell.dataset.poliGraded = '1';
                cell.classList.add('poli-grade-cell', 'poli-grade--' + band);
                var bar = document.createElement('span');
                bar.className = 'poli-grade-bar';
                bar.setAttribute('aria-hidden', 'true');
                var fill = document.createElement('span');
                fill.style.width = pct + '%';
                bar.appendChild(fill);
                cell.appendChild(bar);
            });
        });
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', run);
    } else {
        run();
    }
})();
JS, true);
}

echo $OUTPUT->render_from_template('theme_boost/drawers', $templatecontext);
