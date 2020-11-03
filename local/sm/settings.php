
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
 * Report settings
 *
 * @package    report
 * @subpackage configlog
 * @copyright  2009 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// $ADMIN->add('users', new admin_externalpage('remove_user_from_cohort', new lang_string('title','local_sm'), "$CFG->wwwroot/local/sm/remove_cohort.php"));
$ADMIN->add('root', new admin_category('schools', new lang_string('schools','local_school')));
$ADMIN->add('schools', new admin_externalpage('course_desc', get_string('course_desc','local_school'), "$CFG->wwwroot/local/school/course/courses_desc.php"));
$ADMIN->add('schools', new admin_category('custom_users', get_string('users','admin')));
$ADMIN->add('schools', new admin_category('class_regist', get_string('class_regist','local_sm')));
$ADMIN->add('custom_users', new admin_externalpage('remove_user_from_cohort', new lang_string('remove_user_from_cohort','local_sm'), "$CFG->wwwroot/local/sm/remove_cohort.php"));
$ADMIN->add('custom_users', new admin_externalpage('registed_user_chart', new lang_string('registed_user_chart','local_sm'), "$CFG->wwwroot/local/sm/registed_user_chart.php"));

$ADMIN->add('schools', new admin_category('custom_course', new lang_string('courses','admin')));

$ADMIN->add('custom_course', new admin_externalpage('restrict_access', new lang_string('restrict_access','local_sm'), "$CFG->wwwroot/local/sm/restrict_access.php"));
$ADMIN->add('schools', new admin_externalpage('home_popup_management', new lang_string('home_popup_management','local_sm'), "$CFG->wwwroot/local/sm/home_popup_management.php"));
$ADMIN->add('class_regist', new admin_externalpage('list_class', new lang_string('list_class','local_sm'), "$CFG->wwwroot/local/class_regist/list_class.php"));
