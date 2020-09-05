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
$ADMIN->add('root', new admin_category('schools', new lang_string('schools','local_school')));
$ADMIN->add('schools', new admin_category('temp', new lang_string('temp','local_school')));
$ADMIN->add('schools', new admin_externalpage('school', get_string('school','local_school'), "$CFG->wwwroot/local/school/schools.php"));
$ADMIN->add('schools', new admin_externalpage('class', get_string('class','local_school'), "$CFG->wwwroot/local/school/classes.php"));
$ADMIN->add('schools', new admin_externalpage('department', get_string('department','local_school'), "$CFG->wwwroot/local/school/departments.php"));
$ADMIN->add('temp', new admin_externalpage('teacher', get_string('temporary_teacher','local_school'), "$CFG->wwwroot/local/school/temp_teachers.php"));
$ADMIN->add('temp', new admin_externalpage('student', get_string('temporary_student','local_school'), "$CFG->wwwroot/local/school/temp_students.php"));
$ADMIN->add('temp', new admin_externalpage('upload', get_string('upload','local_school'), "$CFG->wwwroot/local/school/upload.php"));
$ADMIN->add('temp', new admin_externalpage('upload_result', get_string('upload_result','local_school'), "$CFG->wwwroot/local/school/upload_result.php"));
$ADMIN->add('temp', new admin_externalpage('export', get_string('export','local_school'), "$CFG->wwwroot/local/school/export.php"));
$ADMIN->add('schools', new admin_category('core', new lang_string('core','local_school')));
$ADMIN->add('core', new admin_externalpage('change_student', get_string('students','local_school'), "$CFG->wwwroot/local/school/change_student.php"));
$ADMIN->add('core', new admin_externalpage('teachers_assignment', get_string('teachers_assignment','local_school'), "$CFG->wwwroot/local/school/teachers_assignment.php"));
$ADMIN->add('core', new admin_externalpage('teachers', get_string('teachers','local_school'), "$CFG->wwwroot/local/school/teachers.php"));
$ADMIN->add('core', new admin_externalpage('migrate', get_string('migrate','local_school'), "$CFG->wwwroot/local/school/migrate_old_user.php"));
$ADMIN->add('core', new admin_externalpage('outside_teacher', get_string('outside_teachers','local_school'), "$CFG->wwwroot/local/school/outside_teachers.php"));
$ADMIN->add('courses', new admin_externalpage('course_desc', get_string('course_desc','local_school'), "$CFG->wwwroot/local/school/course/courses_desc.php"));

//$ADMIN->add('schools', new admin_externalpage('school_admins', get_string('school_admins','local_school'), "$CFG->wwwroot/local/school/school_admins.php"));

