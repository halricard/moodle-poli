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
 * Poli course category / course listing layout with a brand banner header.
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

// "All courses" view = course/index.php with no specific category selected.
$ispolihome = !optional_param('categoryid', 0, PARAM_INT)
    && strpos($PAGE->url->get_path(), '/course/index.php') !== false;

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}
if ($ispolihome) {
    $extraclasses[] = 'poli-allcourses';
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
        $selectmenu = new \core\output\select_menu('tertiarynavigation', $overflowdata->urls, $overflowdata->selected);
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

// Build the category banner.
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$catbanner = [
    'title' => get_string('allcoursestitle', 'theme_poli'),
    'description' => get_string('allcoursesdesc', 'theme_poli'),
    'coursecountlabel' => '',
    'accentstart' => '#00AEC7',
    'accentend' => '#E6007E',
];
if ($categoryid) {
    $category = core_course_category::get($categoryid, IGNORE_MISSING);
    if ($category) {
        $palette = theme_poli_accent_palette();
        $accent = $palette[$categoryid % count($palette)];
        $count = $category->get_courses_count();
        $catbanner = [
            'title' => format_string($category->name, true, ['context' => context_coursecat::instance($category->id)]),
            'description' => $category->description
                ? shorten_text(trim(html_to_text(format_text($category->description, $category->descriptionformat,
                    ['context' => context_coursecat::instance($category->id), 'noclean' => true]), 0, false)), 200)
                : '',
            'coursecountlabel' => ($count == 1)
                ? get_string('onecourse', 'theme_poli')
                : get_string('coursecount', 'theme_poli', $count),
            'accentstart' => $accent[1],
            'accentend' => $accent[2],
        ];
    }
}

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
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'catbanner' => $catbanner,
    'polihome' => $ispolihome,
    'allcoursesurl' => (new \core\url('/course/index.php'))->out(false),
];

// On the "All courses" view, render the home-style categories + course cards.
if ($ispolihome) {
    $categories = theme_poli_get_categories(12);
    foreach ($categories as &$cat) {
        $cat['coursecountlabel'] = ($cat['coursecount'] == 1)
            ? get_string('onecourse', 'theme_poli')
            : get_string('coursecount', 'theme_poli', $cat['coursecount']);
    }
    unset($cat);
    $courses = theme_poli_get_courses(12);

    $templatecontext['categories'] = array_values($categories);
    $templatecontext['showcategories'] = !empty($categories);
    $templatecontext['courses'] = array_values($courses);
    $templatecontext['hascourses'] = !empty($courses);
    $templatecontext['showcourses'] = true;
}

$templatecontext += theme_poli_navbar_context();
$templatecontext += theme_poli_footer_context();
echo $OUTPUT->render_from_template('theme_poli/coursecategory', $templatecontext);
