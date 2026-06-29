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
 * Poli theme library.
 *
 * @package    theme_poli
 * @copyright  2026 Poliedro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the main SCSS content (the Boost preset chain).
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_poli_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();
    $context = context_system::instance();

    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_poli', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    return $scss;
}

/**
 * Get SCSS to prepend (brand variables, before Bootstrap).
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_poli_get_pre_scss($theme) {
    global $CFG;

    $scss = '';

    // Map theme settings to SCSS variables.
    $configurable = [
        // Setting key => [variable names].
        'brandcolor' => ['primary', 'poli-brand-primary'],
        'brandaccent' => ['poli-brand-accent'],
        'brandtertiary' => ['poli-brand-tertiary'],
    ];

    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Load the brand token defaults / variable overrides.
    $scss .= file_get_contents($CFG->dirroot . '/theme/poli/scss/pre.scss');

    if (defined('BEHAT_SITE_RUNNING')) {
        $scss .= "\$behatsite: true;\n";
    }

    // Prepend any admin-defined raw pre SCSS.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Inject additional SCSS (Poli components + background images, after Bootstrap).
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_poli_get_extra_scss($theme) {
    global $CFG;

    $content = file_get_contents($CFG->dirroot . '/theme/poli/scss/post.scss');

    // Site-wide background image.
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (!empty($imageurl)) {
        $content .= '@media (min-width: 768px) {';
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover; background-attachment: fixed;";
        $content .= ' } }';
    }

    // Hero background image (optional override of the gradient).
    $herourl = $theme->setting_file_url('herobackgroundimage', 'herobackgroundimage');
    if (!empty($herourl)) {
        $content .= '.poli-hero { ';
        $content .= "--poli-hero-image: url('$herourl');";
        $content .= ' }';
    }

    // Login background image (inherits Boost behaviour).
    $loginbackgroundimageurl = $theme->setting_file_url('loginbackgroundimage', 'loginbackgroundimage');
    if (!empty($loginbackgroundimageurl)) {
        $content .= 'body.pagelayout-login #page .login-layout-left { ';
        $content .= "background-image: url('$loginbackgroundimageurl'); background-size: cover; background-position: center;";
        $content .= ' }';
    }

    // Configurable navbar accent / fill (settings-driven CSS).
    $content .= theme_poli_navbar_style_css($theme);
    $content .= theme_poli_enrol_category_badge_css();

    // Admin-defined raw post SCSS.
    if (!empty($theme->settings->scss)) {
        $content .= "\n" . $theme->settings->scss;
    }

    return $content;
}

/**
 * Build category badge labels for the enrolment landing page.
 *
 * Moodle exposes the current course category as body.category-{id}; generated
 * CSS lets the core enrol page show the same category chip as the course hero.
 *
 * @return string CSS
 */
function theme_poli_enrol_category_badge_css(): string {
    global $DB;

    try {
        $categories = $DB->get_records('course_categories', null, 'id ASC', 'id,name');
    } catch (\Throwable $e) {
        return '';
    }

    $css = '';
    foreach ($categories as $category) {
        $name = trim(strip_tags(format_string($category->name, true,
            ['context' => context_coursecat::instance($category->id)])));
        if ($name === '') {
            continue;
        }
        $label = \core_text::strtoupper($name);
        $css .= 'body.path-enrol.category-' . (int) $category->id . ' .coursebox .info::before{';
        $css .= 'content:' . theme_poli_css_string($label) . ';}';
    }

    return $css;
}

/**
 * Escapes a value for a CSS string literal.
 *
 * @param string $value
 * @return string
 */
function theme_poli_css_string(string $value): string {
    return '"' . addcslashes($value, "\\\"\n\r\f") . '"';
}

/**
 * Build the navbar appearance CSS from the theme settings (style + colour).
 *
 * @param theme_config $theme
 * @return string CSS
 */
function theme_poli_navbar_style_css($theme): string {
    $style = $theme->settings->navbarstyle ?? 'accent-gradient';
    $colourkey = $theme->settings->navbarcolor ?? 'primary';

    $colours = [
        'primary' => $theme->settings->brandcolor ?: '#00AEC7',
        'accent' => $theme->settings->brandaccent ?: '#E6007E',
        'tertiary' => $theme->settings->brandtertiary ?: '#FFC400',
    ];
    $c = $colours[$colourkey] ?? $colours['primary'];
    // Full palette gradient (teal → magenta → amber) for the accent line / fill.
    $palettegrad = "linear-gradient(120deg, {$colours['primary']} 0%, {$colours['accent']} 55%, {$colours['tertiary']} 100%)";

    $css = '';
    switch ($style) {
        case 'none':
            $css .= '.navbar.fixed-top::after{display:none !important;}';
            break;
        case 'accent-solid':
            $css .= ".navbar.fixed-top::after{background:{$c} !important;}";
            break;
        case 'fill-solid':
        case 'fill-gradient':
            $bg = ($style === 'fill-solid') ? $c : $palettegrad;
            $css .= ".navbar.fixed-top{background:{$bg} !important;backdrop-filter:none !important;border-bottom-color:rgba(255,255,255,.15) !important;}";
            $css .= '.navbar.fixed-top::after{display:none !important;}';
            $css .= '.navbar.fixed-top,.navbar.fixed-top .navbar-brand,.navbar.fixed-top .poli-wordmark{color:#fff !important;}';
            $css .= '.navbar.fixed-top .primary-navigation .nav-link{color:rgba(255,255,255,.82) !important;}';
            $css .= '.navbar.fixed-top .primary-navigation .nav-link:hover{color:#fff !important;background:rgba(255,255,255,.16) !important;}';
            $css .= '.navbar.fixed-top .primary-navigation .nav-link.active{color:#fff !important;}';
            $css .= '.navbar.fixed-top .primary-navigation .nav-link.active::after{background:#fff !important;}';
            $css .= '.navbar.fixed-top .btn-icon,.navbar.fixed-top .nav-link,.navbar.fixed-top a{color:#fff !important;}';
            $css .= '.navbar.fixed-top .divider{border-color:rgba(255,255,255,.3) !important;}';
            break;
        case 'accent-gradient':
        default:
            $css .= ".navbar.fixed-top::after{background:{$palettegrad} !important;}";
            break;
    }

    return $css;
}

/**
 * Get precompiled css fallback.
 *
 * @return string compiled css
 */
function theme_poli_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Serves theme settings files (background, hero and login images).
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_poli_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $allowed = ['logo', 'logodark', 'backgroundimage', 'herobackgroundimage', 'loginbackgroundimage', 'preset'];
    if ($context->contextlevel == CONTEXT_SYSTEM && in_array($filearea, $allowed, true)) {
        $theme = theme_config::load('poli');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}

/**
 * Theme user preferences (inherit Boost drawer preferences).
 *
 * @return array[]
 */
function theme_poli_user_preferences(): array {
    return [
        'drawer-open-block' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => false,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
        'drawer-open-index' => [
            'type' => PARAM_BOOL,
            'null' => NULL_NOT_ALLOWED,
            'default' => true,
            'permissioncallback' => [core_user::class, 'is_current_user'],
        ],
    ];
}

/**
 * Authentication context shared with the navbar (login / signup buttons).
 *
 * @return array
 */
function theme_poli_auth_context(): array {
    global $CFG;
    require_once($CFG->libdir . '/authlib.php');
    $loggedin = isloggedin() && !isguestuser();
    return [
        'userloggedin' => $loggedin,
        // Positive flag: only show the login/register buttons when we KNOW the
        // visitor is logged out. On layouts that do not provide this context
        // (undefined → falsy) the navbar safely falls back to the user menu.
        'showauthbuttons' => !$loggedin,
        'loginurl' => (new \core\url('/login/index.php'))->out(false),
        'signupurl' => (new \core\url('/login/signup.php'))->out(false),
        'cansignup' => signup_is_enabled(),
    ];
}

/**
 * Navbar context: auth flags plus the configurable dark-only logo.
 *
 * @return array
 */
function theme_poli_navbar_context(): array {
    $ctx = theme_poli_auth_context();

    $theme = theme_config::load('poli');
    $dark = $theme->setting_file_url('logodark', 'logodark');

    $ctx['haslogo'] = !empty($dark);
    $ctx['logodark'] = $dark;

    return $ctx;
}

/**
 * Brand accent palette used to colour category tiles and card chips when no
 * image is present. Cycles through the Poliedro logo colours.
 *
 * @return array list of [name, start hex, end hex] gradient stops.
 */
function theme_poli_accent_palette(): array {
    return [
        ['teal',    '#00AEC7', '#0093A8'],
        ['magenta', '#E6007E', '#B80064'],
        ['amber',   '#FFC400', '#F59E0B'],
        ['violet',  '#7C3AED', '#5B21B6'],
        ['emerald', '#10B981', '#059669'],
        ['rose',    '#FB7185', '#E11D48'],
    ];
}

/**
 * Build the list of top-level course categories for the front page.
 *
 * @param int $limit Maximum number of categories.
 * @return array template-ready category rows.
 */
function theme_poli_get_categories(int $limit = 8): array {
    $palette = theme_poli_accent_palette();
    $icons = ['fa-graduation-cap', 'fa-flask', 'fa-calculator', 'fa-book-open',
        'fa-laptop-code', 'fa-palette', 'fa-globe', 'fa-music'];

    $result = [];
    $i = 0;

    try {
        $categories = core_course_category::top()->get_children();
    } catch (\Throwable $e) {
        return [];
    }

    foreach ($categories as $category) {
        if (!$category->is_uservisible()) {
            continue;
        }
        // Skip empty categories so the explorer only surfaces real content.
        if ($category->get_courses_count() < 1) {
            continue;
        }
        $accent = $palette[$i % count($palette)];
        $result[] = [
            'id' => $category->id,
            'name' => format_string($category->name, true, ['context' => context_coursecat::instance($category->id)]),
            'url' => (new \core\url('/course/index.php', ['categoryid' => $category->id]))->out(false),
            'coursecount' => $category->get_courses_count(),
            'accentname' => $accent[0],
            'accentstart' => $accent[1],
            'accentend' => $accent[2],
            'icon' => $icons[$i % count($icons)],
        ];
        $i++;
        if ($i >= $limit) {
            break;
        }
    }

    return $result;
}

/**
 * Resolve the overview image for a course, falling back to a generated
 * gradient placeholder keyed on the course id.
 *
 * @param stdClass $course
 * @return array [hasimage, imageurl, accentstart, accentend, initials]
 */
function theme_poli_course_image($course): array {
    global $CFG;
    require_once($CFG->dirroot . '/course/lib.php');

    $imageurl = null;
    $courselistelement = new core_course_list_element($course);
    foreach ($courselistelement->get_course_overviewfiles() as $file) {
        if ($file->is_valid_image()) {
            $imageurl = \core\url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                false
            )->out(false);
            break;
        }
    }

    $palette = theme_poli_accent_palette();
    $accent = $palette[$course->id % count($palette)];

    // Build two-letter initials from the course name.
    $name = trim(format_string($course->fullname));
    $words = preg_split('/\s+/', $name);
    $initials = '';
    foreach ($words as $word) {
        $first = core_text::substr($word, 0, 1);
        if ($first !== '' && preg_match('/\p{L}/u', $first)) {
            $initials .= core_text::strtoupper($first);
        }
        if (core_text::strlen($initials) >= 2) {
            break;
        }
    }
    if ($initials === '') {
        $initials = 'P';
    }

    return [
        'hasimage' => (bool) $imageurl,
        'imageurl' => $imageurl,
        'accentstart' => $accent[1],
        'accentend' => $accent[2],
        'initials' => $initials,
    ];
}

/**
 * Build a list of courses for the front page course-card grid.
 *
 * @param int $limit Maximum number of courses.
 * @return array template-ready course rows.
 */
function theme_poli_get_courses(int $limit = 8): array {
    global $CFG, $USER;
    require_once($CFG->dirroot . '/course/lib.php');

    $courses = [];

    // Prefer the user's own enrolled courses when logged in.
    if (isloggedin() && !isguestuser()) {
        $enrolled = enrol_get_my_courses('*', 'visible DESC, fullname ASC', $limit);
        foreach ($enrolled as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            $courses[$course->id] = $course;
        }
    }

    // Top up with the most recent visible courses on the site.
    if (count($courses) < $limit) {
        $recent = get_courses('all', 'c.timecreated DESC', 'c.id, c.fullname, c.shortname, c.summary, c.summaryformat, c.category, c.visible');
        foreach ($recent as $course) {
            if ($course->id == SITEID || empty($course->visible)) {
                continue;
            }
            if (isset($courses[$course->id])) {
                continue;
            }
            $courses[$course->id] = $course;
            if (count($courses) >= $limit) {
                break;
            }
        }
    }

    $result = [];
    foreach ($courses as $course) {
        $coursecontext = context_course::instance($course->id);
        $image = theme_poli_course_image($course);
        $category = core_course_category::get($course->category, IGNORE_MISSING, true);

        $summary = '';
        if (!empty($course->summary)) {
            $summary = shorten_text(
                trim(html_to_text(format_text($course->summary, $course->summaryformat ?? FORMAT_HTML,
                    ['context' => $coursecontext, 'noclean' => true]), 0, false)),
                140
            );
        }

        $result[] = array_merge($image, [
            'id' => $course->id,
            'fullname' => format_string($course->fullname, true, ['context' => $coursecontext]),
            'shortname' => format_string($course->shortname, true, ['context' => $coursecontext]),
            'summary' => $summary,
            'categoryname' => $category ? format_string($category->name) : '',
            'url' => (new \core\url('/course/view.php', ['id' => $course->id]))->out(false),
            'enrolled' => is_enrolled($coursecontext, $USER, '', true),
        ]);

        if (count($result) >= $limit) {
            break;
        }
    }

    return $result;
}

/**
 * Build the course presentation hero context for the course layout.
 *
 * @param stdClass $course
 * @return array template context.
 */
function theme_poli_course_hero($course): array {
    global $CFG, $USER, $PAGE;
    require_once($CFG->dirroot . '/course/lib.php');

    $coursecontext = context_course::instance($course->id);
    $image = theme_poli_course_image($course);
    $category = core_course_category::get($course->category, IGNORE_MISSING, true);

    $summary = '';
    if (!empty($course->summary)) {
        $summary = shorten_text(
            trim(html_to_text(format_text($course->summary, $course->summaryformat ?? FORMAT_HTML,
                ['context' => $coursecontext, 'noclean' => true]), 0, false)),
            260
        );
    }

    // Course teachers (editing teachers, by archetype).
    $teachers = [];
    $seen = [];
    $teacherroles = get_archetype_roles('editingteacher');
    foreach ($teacherroles as $role) {
        $users = get_role_users($role->id, $coursecontext, false,
            'u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, '
            . 'u.alternatename, u.picture, u.imagealt, u.email');
        foreach ($users as $teacher) {
            if (isset($seen[$teacher->id])) {
                continue;
            }
            $seen[$teacher->id] = true;
            $userpic = new \core\output\user_picture($teacher);
            $userpic->size = 36;
            $teachers[] = [
                'name' => fullname($teacher),
                'picture' => $userpic->get_url($PAGE)->out(false),
            ];
            if (count($teachers) >= 4) {
                break 2;
            }
        }
    }

    // Progress for enrolled users.
    $progress = null;
    $isenrolled = is_enrolled($coursecontext, $USER, '', true);
    if ($isenrolled && \core_completion\progress::get_course_progress_percentage($course) !== null) {
        $progress = (int) round(\core_completion\progress::get_course_progress_percentage($course));
    }

    return array_merge($image, [
        'id' => $course->id,
        'fullname' => format_string($course->fullname, true, ['context' => $coursecontext]),
        'summary' => $summary,
        'categoryname' => $category ? format_string($category->name) : '',
        'categoryurl' => $category
            ? (new \core\url('/course/index.php', ['categoryid' => $category->id]))->out(false) : null,
        'teachers' => $teachers,
        'hasteachers' => !empty($teachers),
        'isenrolled' => $isenrolled,
        'hasprogress' => $progress !== null,
        'progress' => $progress,
    ]);
}
