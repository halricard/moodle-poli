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
 * Poli front page layout: curated student home (hero + categories + courses).
 *
 * @package   theme_poli
 * @copyright 2026 Poliedro
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

// Add block button in editing mode.
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

$extraclasses = ['uses-drawers', 'pagelayout-poli-frontpage'];
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

// -----------------------------------------------------------------------------
// Poli home content.
// -----------------------------------------------------------------------------
$theme = theme_config::load('poli');
$settings = $theme->settings;

$herobuttonurl = !empty($settings->herobuttonurl) ? $settings->herobuttonurl : '/my/courses.php';
if (strpos($herobuttonurl, 'http') !== 0) {
    $herobuttonurl = (new \core\url($herobuttonurl))->out(false);
}

$showcategories = !isset($settings->showcategories) || $settings->showcategories;
$showcourses = !isset($settings->showcourses) || $settings->showcourses;
$courselimit = !empty($settings->courselimit) ? (int) $settings->courselimit : 8;

$categories = $showcategories ? theme_poli_get_categories(8) : [];
$courses = $showcourses ? theme_poli_get_courses($courselimit) : [];

// Decorate categories/courses with the CSS-variable colour strings used in the
// template (keeps presentation logic out of the mustache).
foreach ($categories as &$cat) {
    $cat['coursecountlabel'] = ($cat['coursecount'] == 1)
        ? get_string('onecourse', 'theme_poli')
        : get_string('coursecount', 'theme_poli', $cat['coursecount']);
}
unset($cat);

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
    'langmenu' => theme_poli_langtoggle_context(),
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,

    // Poli home.
    'heroheading' => format_string($settings->heroheading ?? get_string('heroheading_default', 'theme_poli')),
    'herosubheading' => format_text($settings->herosubheading ?? get_string('herosubheading_default', 'theme_poli'),
        FORMAT_PLAIN),
    'herobuttontext' => format_string($settings->herobuttontext ?? get_string('herobuttontext_default', 'theme_poli')),
    'herobuttonurl' => $herobuttonurl,
    'hasherobutton' => isloggedin() && !isguestuser(),
    'loginurl' => (new \core\url('/login/index.php'))->out(false),
    'showcategories' => $showcategories && !empty($categories),
    'categories' => array_values($categories),
    'showcourses' => $showcourses,
    'courses' => array_values($courses),
    'hascourses' => !empty($courses),
    'allcoursesurl' => (new \core\url('/course/index.php'))->out(false),
];

$templatecontext += theme_poli_navbar_context();
$templatecontext += theme_poli_footer_context();
echo $OUTPUT->render_from_template('theme_poli/frontpage', $templatecontext);
