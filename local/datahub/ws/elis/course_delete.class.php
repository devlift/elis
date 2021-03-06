<?php
/**
 * ELIS(TM): Enterprise Learning Intelligence Suite
 * Copyright (C) 2008-2013 Remote-Learner.net Inc (http://www.remote-learner.net)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    local_datahub
 * @copyright  (C) 2008-2013 Remote Learner.net Inc http://www.remote-learner.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Delete course webservices method.
 */
class local_datahub_elis_course_delete extends external_api {

    /**
     * Require ELIS dependencies if ELIS is installed, otherwise return false.
     * @return bool Whether ELIS dependencies were successfully required.
     */
    public static function require_elis_dependencies() {
        global $CFG;
        if (file_exists($CFG->dirroot.'/local/elisprogram/lib/setup.php')) {
            require_once($CFG->dirroot.'/local/elisprogram/lib/setup.php');
            require_once(elispm::lib('data/course.class.php'));
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets a description of the course object for use in the parameter and return functions.
     * @return array An array of external_value objects describing a user record in webservice terms.
     */
    public static function get_course_object_description() {
        global $DB;
        $params = array(
            'idnumber' => new external_value(PARAM_TEXT, 'Course idnumber', VALUE_REQUIRED)
        );

        return $params;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters The parameters object for this webservice method.
     */
    public static function course_delete_parameters() {
        $params = array('data' => new external_single_structure(static::get_course_object_description()));
        return new external_function_parameters($params);
    }

    /**
     * Performs course delete
     * @throws moodle_exception If there was an error in passed parameters.
     * @throws data_object_exception If there was an error creating the entity.
     * @param array $data The incoming data parameter.
     * @return array An array of parameters, if successful.
     */
    public static function course_delete(array $data) {
        global $USER, $DB;

        if (static::require_elis_dependencies() !== true) {
            throw new moodle_exception('ws_function_requires_elis', 'local_datahub');
        }

        // Parameter validation.
        $params = self::validate_parameters(self::course_delete_parameters(), array('data' => $data));

        // Context validation.
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        $data = (object)$data;

        // Validate course exists.
        if (!($crsid = $DB->get_field(course::TABLE, 'id', array('idnumber' => $data->idnumber)))) {
            throw new data_object_exception('ws_course_delete_fail_invalid_idnumber', 'local_datahub', '', $data);
        }

        // Capability checking.
        require_capability('local/elisprogram:course_delete', \local_elisprogram\context\course::instance($crsid));

        $course = new course($crsid);
        $course->delete();

        // Confirm delete & respond.
        if (!$DB->record_exists(course::TABLE, array('id' => $crsid))) {
            return array(
                'messagecode' => get_string('ws_course_delete_success_code', 'local_datahub'),
                'message' => get_string('ws_course_delete_success_msg', 'local_datahub')
            );
        } else {
            throw new data_object_exception('ws_course_delete_fail', 'local_datahub');
        }
    }

    /**
     * Returns description of method result value
     * @return external_single_structure Object describing return parameters for this webservice method.
     */
    public static function course_delete_returns() {
        return new external_single_structure(
                array(
                    'messagecode' => new external_value(PARAM_TEXT, 'Response Code'),
                    'message' => new external_value(PARAM_TEXT, 'Response')
                )
        );
    }
}
