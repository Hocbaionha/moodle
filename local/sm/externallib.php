<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->dirroot . "/course/externallib.php");

class local_courses_external extends core_course_external
{

    public static function get_courses_by_field_parameters()
    {
        return new external_function_parameters(
            array(
                'field' => new external_value(PARAM_ALPHA, 'The field to search can be left empty for all courses or:
                    id: course id
                    ids: comma separated course ids
                    shortname: course short name
                    idnumber: course id number
                    category: category id the course belongs to
                ', VALUE_DEFAULT, ''),
                'value' => new external_value(PARAM_RAW, 'The value to match', VALUE_DEFAULT, '')
            )
        );
    }


    /**
     * Get courses matching a specific field (id/s, shortname, idnumber, category)
     *
     * @param string $field field name to search, or empty for all courses
     * @param string $value value to search
     * @return array list of courses and warnings
     * @throws  invalid_parameter_exception
     * @since Moodle 3.2
     */
    public static function get_courses_by_field($field = '', $value = '')
    {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/filterlib.php');

        $params = self::validate_parameters(self::get_courses_by_field_parameters(),
            array(
                'field' => $field,
                'value' => $value,
            )
        );
        $warnings = array();

        if (empty($params['field'])) {
            $courses = $DB->get_records('course', null, 'id ASC');
        } else {
            switch ($params['field']) {
                case 'id':
                case 'category':
                    $value = clean_param($params['value'], PARAM_INT);
                    break;
                case 'ids':
                    $value = clean_param($params['value'], PARAM_SEQUENCE);
                    break;
                case 'shortname':
                    $value = clean_param($params['value'], PARAM_TEXT);
                    break;
                case 'idnumber':
                    $value = clean_param($params['value'], PARAM_RAW);
                    break;
                default:
                    throw new invalid_parameter_exception('Invalid field name');
            }

            if ($params['field'] === 'ids') {
                // Preload categories to avoid loading one at a time.
                $courseids = explode(',', $value);
                list ($listsql, $listparams) = $DB->get_in_or_equal($courseids);
                $categoryids = $DB->get_fieldset_sql("
                        SELECT DISTINCT cc.id
                          FROM {course} c
                          JOIN {course_categories} cc ON cc.id = c.category
                         WHERE c.id $listsql", $listparams);
                core_course_category::get_many($categoryids);

                // Load and validate all courses. This is called because it loads the courses
                // more efficiently.
                list ($courses, $warnings) = external_util::validate_courses($courseids, [],
                    false, true);
            } else {
                $courses = $DB->get_records('course', array($params['field'] => $value), 'id ASC');
            }
        }

        $coursesdata = array();
        foreach ($courses as $course) {
            $context = context_course::instance($course->id);
            $canupdatecourse = has_capability('moodle/course:update', $context);
            $canviewhiddencourses = has_capability('moodle/course:viewhiddencourses', $context);

            // Check if the course is visible in the site for the user.
            if (!$course->visible and !$canviewhiddencourses and !$canupdatecourse) {
                continue;
            }
            // Get the public course information, even if we are not enrolled.
            $courseinlist = new core_course_list_element($course);

            // Now, check if we have access to the course, unless it was already checked.
            try {
                if (empty($course->contextvalidated)) {
                    self::validate_context($context);
                }
            } catch (Exception $e) {
                // User can not access the course, check if they can see the public information about the course and return it.
                if (core_course_category::can_view_course_info($course)) {
                    $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);
                }
                continue;
            }
            $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);
            // Return information for any user that can access the course.
            $coursefields = array('format', 'showgrades', 'newsitems', 'startdate', 'enddate', 'maxbytes', 'showreports', 'visible',
                'groupmode', 'groupmodeforce', 'defaultgroupingid', 'enablecompletion', 'completionnotify', 'lang', 'theme',
                'marker');

            // Course filters.
            $coursesdata[$course->id]['filters'] = filter_get_available_in_context($context);

            // Information for managers only.
            if ($canupdatecourse) {
                $managerfields = array('idnumber', 'legacyfiles', 'calendartype', 'timecreated', 'timemodified', 'requested',
                    'cacherev');
                $coursefields = array_merge($coursefields, $managerfields);
            }

            // Populate fields.
            foreach ($coursefields as $field) {
                $coursesdata[$course->id][$field] = $course->{$field};
            }

            // Clean lang and auth fields for external functions (it may content uninstalled themes or language packs).
            if (isset($coursesdata[$course->id]['theme'])) {
                $coursesdata[$course->id]['theme'] = clean_param($coursesdata[$course->id]['theme'], PARAM_THEME);
            }
            if (isset($coursesdata[$course->id]['lang'])) {
                $coursesdata[$course->id]['lang'] = clean_param($coursesdata[$course->id]['lang'], PARAM_LANG);
            }

            $courseformatoptions = course_get_format($course)->get_config_for_external();
            foreach ($courseformatoptions as $key => $value) {
                $coursesdata[$course->id]['courseformatoptions'][] = array(
                    'name' => $key,
                    'value' => $value
                );
            }
            $coursesdata[$course->id]['topics'] = $DB->get_records('course_sections', ["course" => $course->id ], 'id ASC', 'id,name');
            $activitys = get_array_of_activities(18);
            foreach ($coursesdata[$course->id]['topics'] as $key => $section) {
                foreach ($activitys as $activities) {
                    if ($section->id == $activities->sectionid && !isset($activities->deletioninprogress)) {
                        $coursesdata[$course->id]['topics'][$key]->activities[] = (array)$activities;
                    }
                }
            }
        }
        return array(
            'courses' => $coursesdata,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.2
     */
    public static function get_courses_by_field_returns()
    {
        // Course structure, including not only public viewable fields.
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(self::get_course_structure(false), 'Course'),
                'warnings' => new external_warnings()
            )
        );
    }

    protected static function get_course_structure($onlypublicdata = true)
    {
        $coursestructure = array(
            'id' => new external_value(PARAM_INT, 'course id'),
            'fullname' => new external_value(PARAM_TEXT, 'course full name'),
            'displayname' => new external_value(PARAM_TEXT, 'course display name'),
            'shortname' => new external_value(PARAM_TEXT, 'course short name'),
            'categoryid' => new external_value(PARAM_INT, 'category id'),
            'categoryname' => new external_value(PARAM_TEXT, 'category name'),
            'sortorder' => new external_value(PARAM_INT, 'Sort order in the category', VALUE_OPTIONAL),
            'summary' => new external_value(PARAM_RAW, 'summary'),
            'summaryformat' => new external_format_value('summary'),
            'summaryfiles' => new external_files('summary files in the summary field', VALUE_OPTIONAL),
            'overviewfiles' => new external_files('additional overview files attached to this course'),
            'contacts' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'contact user id'),
                        'fullname' => new external_value(PARAM_NOTAGS, 'contact user fullname'),
                    )
                ),
                'contact users'
            ),
            'enrollmentmethods' => new external_multiple_structure(
                new external_value(PARAM_PLUGIN, 'enrollment method'),
                'enrollment methods list'
            ),
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                        'shortname' => new external_value(PARAM_RAW,
                            'The shortname of the custom field - to be able to build the field class in the code'),
                        'type' => new external_value(PARAM_ALPHANUMEXT,
                            'The type of the custom field - text field, checkbox...'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                    )
                ), 'Custom fields', VALUE_OPTIONAL),
            'topics' => new external_multiple_structure(
                new external_single_structure(
                array(
                    'activities' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'activities id'),
                                'name' => new external_value(PARAM_RAW, 'activities name'),
                                'mod' => new external_value(PARAM_RAW, 'activities mod')
                            )
                        ),'activities', VALUE_OPTIONAL
                    ),
                    'id' => new external_value(PARAM_INT, 'topic id'),
                    'name' => new external_value(PARAM_NOTAGS, 'topic name'),
                )
            ),
                'topics',VALUE_OPTIONAL)
        );

        if (!$onlypublicdata) {
            $extra = array(
                'idnumber' => new external_value(PARAM_RAW, 'Id number', VALUE_OPTIONAL),
                'format' => new external_value(PARAM_PLUGIN, 'Course format: weeks, topics, social, site,..', VALUE_OPTIONAL),
                'showgrades' => new external_value(PARAM_INT, '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                'newsitems' => new external_value(PARAM_INT, 'Number of recent items appearing on the course page', VALUE_OPTIONAL),
                'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                'maxbytes' => new external_value(PARAM_INT, 'Largest size of file that can be uploaded into', VALUE_OPTIONAL),
                'showreports' => new external_value(PARAM_INT, 'Are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT, '1: available to student, 0:not available', VALUE_OPTIONAL),
                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible', VALUE_OPTIONAL),
                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no', VALUE_OPTIONAL),
                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id', VALUE_OPTIONAL),
                'enablecompletion' => new external_value(PARAM_INT, 'Completion enabled? 1: yes 0: no', VALUE_OPTIONAL),
                'completionnotify' => new external_value(PARAM_INT, '1: yes 0: no', VALUE_OPTIONAL),
                'lang' => new external_value(PARAM_SAFEDIR, 'Forced course language', VALUE_OPTIONAL),
                'theme' => new external_value(PARAM_PLUGIN, 'Fame of the forced theme', VALUE_OPTIONAL),
                'marker' => new external_value(PARAM_INT, 'Current course marker', VALUE_OPTIONAL),
                'legacyfiles' => new external_value(PARAM_INT, 'If legacy files are enabled', VALUE_OPTIONAL),
                'calendartype' => new external_value(PARAM_PLUGIN, 'Calendar type', VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT, 'Time when the course was created', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Last time  the course was updated', VALUE_OPTIONAL),
                'requested' => new external_value(PARAM_INT, 'If is a requested course', VALUE_OPTIONAL),
                'cacherev' => new external_value(PARAM_INT, 'Cache revision number', VALUE_OPTIONAL),
                'filters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'filter' => new external_value(PARAM_PLUGIN, 'Filter plugin name'),
                            'localstate' => new external_value(PARAM_INT, 'Filter state: 1 for on, -1 for off, 0 if inherit'),
                            'inheritedstate' => new external_value(PARAM_INT, '1 or 0 to use when localstate is set to inherit'),
                        )
                    ),
                    'Course filters', VALUE_OPTIONAL
                ),
                'courseformatoptions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'Course format option name.'),
                            'value' => new external_value(PARAM_RAW, 'Course format option value.'),
                        )
                    ),
                    'Additional options for particular course format.', VALUE_OPTIONAL
                ),
            );
            $coursestructure = array_merge($coursestructure, $extra);
        }
        return new external_single_structure($coursestructure);
    }

}
