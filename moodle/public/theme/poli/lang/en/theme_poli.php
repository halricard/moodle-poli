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
 * Poli theme strings (English).
 *
 * @package    theme_poli
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Poli';
$string['choosereadme'] = 'Poli is a modern, polished child theme of Boost for the Poliedro learning experience. It delivers a curated student home with a hero banner, category explorer, rich course cards and a beautiful course presentation page in a premium dark-only experience.';
$string['configtitle'] = 'Poli settings';

// Setting pages.
$string['generalsettings'] = 'General';
$string['frontpagesettings'] = 'Front page';
$string['advancedsettings'] = 'Advanced';

// Preset.
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Boost_Presets">Boost presets</a> for information on creating and sharing your own preset files.';

// Brand colours.
$string['brandcolor'] = 'Primary brand colour';
$string['brandcolor_desc'] = 'The primary accent colour (Poliedro teal).';
$string['brandaccent'] = 'Accent brand colour';
$string['brandaccent_desc'] = 'The secondary accent colour used for highlights and gradients (Poliedro magenta).';
$string['brandtertiary'] = 'Tertiary brand colour';
$string['brandtertiary_desc'] = 'A third accent colour used sparingly for emphasis (Poliedro amber).';

// Navbar appearance.
$string['navbarstyle'] = 'Navbar style';
$string['navbarstyle_desc'] = 'How the navigation bar is decorated with the brand colour. Choose a filled style for a coloured navbar, an accent style for a branded underline, or None to keep the dark navbar background only.';
$string['navbarstyle_accentgradient'] = 'Accent line — gradient';
$string['navbarstyle_accentsolid'] = 'Accent line — solid colour';
$string['navbarstyle_fillgradient'] = 'Filled navbar — gradient';
$string['navbarstyle_fillsolid'] = 'Filled navbar — solid colour';
$string['navbarstyle_none'] = 'None';
$string['navbarcolor'] = 'Navbar colour';
$string['navbarcolor_desc'] = 'Which palette colour to use for the navbar accent / fill. This has no effect when Navbar style is set to None.';
$string['palette_primary'] = 'Primary (teal)';
$string['palette_accent'] = 'Accent (magenta)';
$string['palette_tertiary'] = 'Tertiary (amber)';

// Logo.
$string['logodark'] = 'Theme logo';
$string['logodark_desc'] = 'Logo shown in the dark-only navbar. Use a logo with light/white text that reads well on a dark background.';

// Images.
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'The image to display as a background of the login page.';
$string['herobackgroundimage'] = 'Hero background image';
$string['herobackgroundimage_desc'] = 'Optional image shown behind the front page hero banner. When empty, a brand gradient is used.';

// Hero.
$string['heroheading'] = 'Hero heading';
$string['heroheading_desc'] = 'The large headline shown in the front page hero banner.';
$string['heroheading_default'] = 'Aprenda sem limites com o Poliedro';
$string['herosubheading'] = 'Hero subheading';
$string['herosubheading_desc'] = 'The supporting text shown below the hero heading.';
$string['herosubheading_default'] = 'Conteúdos de excelência, professores dedicados e uma plataforma feita para você ir além. Comece agora a sua jornada de aprendizado.';
$string['herobuttontext'] = 'Hero button label';
$string['herobuttontext_desc'] = 'The label for the primary call-to-action button in the hero.';
$string['herobuttontext_default'] = 'Explorar meus cursos';
$string['herobuttonurl'] = 'Hero button URL';
$string['herobuttonurl_desc'] = 'The destination of the primary call-to-action button.';

// Section toggles.
$string['showcategories'] = 'Show categories section';
$string['showcategories_desc'] = 'Display the course category explorer on the front page.';
$string['showcourses'] = 'Show featured courses section';
$string['showcourses_desc'] = 'Display the featured course cards on the front page.';
$string['courselimit'] = 'Number of featured courses';
$string['courselimit_desc'] = 'How many course cards to display in the featured courses grid.';

// Raw SCSS.
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';

// Front page section headings.
$string['exploretitle'] = 'Explore by area';
$string['exploresubtitle'] = 'Find the path that fits your goals';
$string['coursestitle'] = 'Featured courses';
$string['coursessubtitle'] = 'Handpicked to get you started';
$string['viewallcourses'] = 'View all courses';
$string['coursecount'] = '{$a} courses';
$string['onecourse'] = '1 course';
$string['nocourses'] = 'No courses available yet.';
$string['searchcourses'] = 'Search courses…';
$string['filterbyarea'] = 'Filter by area';
$string['filterall'] = 'All areas';
$string['noresults'] = 'No courses match your search.';

// Course cards / hero.
$string['enrolled'] = 'Enrolled';
$string['viewcourse'] = 'View course';
$string['continuelearning'] = 'Continue learning';
$string['accesscourse'] = 'Access course';
$string['courseprogress'] = 'Your progress';
$string['taughtby'] = 'Taught by';

// Login panel.
$string['loginpaneltitle'] = 'Your journey of knowledge starts here';
$string['loginpanellead'] = 'Access your courses, track your progress and learn with the quality of Poliedro — anytime, anywhere.';
$string['loginfeature1'] = 'Excellent content and dedicated teachers';
$string['loginfeature2'] = 'Track your progress in real time';
$string['loginfeature3'] = 'Study on any device';

// Course listing / category pages.
$string['allcoursestitle'] = 'All courses';
$string['allcoursesdesc'] = 'Browse the full catalogue and find your next course.';

// Dashboard welcome.
$string['goodmorning'] = 'Good morning';
$string['goodafternoon'] = 'Good afternoon';
$string['goodevening'] = 'Good evening';
$string['welcomesub'] = 'Here is an overview of your learning. Keep it up!';
$string['statcourses'] = 'My courses';
$string['statinprogress'] = 'In progress';
$string['statcompleted'] = 'Completed';

// Footer.
$string['footertagline'] = 'Excellence in education. Learn without limits, anytime and anywhere, with the quality of Poliedro.';
$string['footerexplore'] = 'Explore';
$string['footerhelp'] = 'Support';
$string['footerhome'] = 'Home';
$string['footercourses'] = 'All courses';
$string['footercategories'] = 'Categories';
$string['footermycourses'] = 'My courses';
$string['footercontact'] = 'Contact support';
$string['footerrights'] = 'All rights reserved.';

// Privacy.
$string['privacy:metadata'] = 'The Poli theme does not store any personal data.';
