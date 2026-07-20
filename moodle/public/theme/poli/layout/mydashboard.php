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
 * Poli dashboard layout: adds a personalised welcome header above the
 * standard Boost dashboard.
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

$primary = new core\navigation\output\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

// Personalised welcome.
$hour = (int) userdate(time(), '%H');
if ($hour < 12) {
    $greeting = get_string('goodmorning', 'theme_poli');
} else if ($hour < 18) {
    $greeting = get_string('goodafternoon', 'theme_poli');
} else {
    $greeting = get_string('goodevening', 'theme_poli');
}

$mycourses = enrol_get_my_courses('id', 'visible DESC');
$coursecount = 0;
$completedcount = 0;
$inprogresscount = 0;
foreach ($mycourses as $c) {
    if ($c->id == SITEID) {
        continue;
    }
    $coursecount++;
    $pct = \core_completion\progress::get_course_progress_percentage($c);
    if ($pct !== null) {
        if ($pct >= 100) {
            $completedcount++;
        } else if ($pct > 0) {
            $inprogresscount++;
        }
    }
}

// "Continue where you left off": the learner's most recently accessed course,
// deep-linked to the next activity. Computed in lib.php (needs $DB, which is
// not in the layout include scope).
$continue = theme_poli_dashboard_continue($mycourses);

$welcome = [
    'greeting' => $greeting,
    'firstname' => format_string($USER->firstname),
    'date' => userdate(time(), get_string('strftimedaydate', 'langconfig')),
    'coursecount' => $coursecount,
    'completedcount' => $completedcount,
    'inprogresscount' => $inprogresscount,
    'mycoursesurl' => (new \core\url('/my/courses.php'))->out(false),
    // Deep-link the progress/completed stats into the course overview block,
    // pre-selecting its grouping so the list opens already filtered.
    'inprogressurl' => (new \core\url('/my/courses.php', ['grouping' => 'inprogress']))->out(false),
    'completedurl' => (new \core\url('/my/courses.php', ['grouping' => 'past']))->out(false),
    'continue' => $continue,
    'hascontinue' => (bool) $continue,
];

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true,
        ['context' => context_course::instance(SITEID), 'escape' => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'welcome' => $welcome,
];

$templatecontext += theme_poli_navbar_context();
$templatecontext += theme_poli_footer_context();
echo $OUTPUT->render_from_template('theme_poli/mydashboard', $templatecontext);
