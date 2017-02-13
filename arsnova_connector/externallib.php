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
 * @package     local_arsnova_connector
 * @category    access
 * @copyright   2017, HS Hannover, elc@hs-hannover.de
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_arsnova_connector_external extends external_api {

    const EDITING_TEACHER = 3;
    const TEACHER = 4;
    const STUDENT = 5;

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_users_courses_parameters() {
        return new external_function_parameters(
                array('username' => new external_value(PARAM_TEXT, 'The name of the user whose courses should be returned', VALUE_REQUIRED))
        );
    }

    /**
     * Returns users courses
     * @return array courses
     */
    public static function get_users_courses($username) {

        require_capability('local/arsnova_connector:access_functions', context_system::instance());

        global $CFG;
        global $DB;
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot . "/lib/accesslib.php");

        //Parameter validation
        $params = self::validate_parameters(self::get_users_courses_parameters(), array('username' => $username));

        $user = $DB->get_record('user', array('username' => $params['username']));
        $userdetail = user_get_user_details($user, null, ['enrolledcourses']);
        $result = [];
        foreach ($userdetail['enrolledcourses'] as $course) {
            $highestrole = self::get_relevant_role_in_course($user->id, $course['id']);
            $result[] = array('id' => $course['id'], 'shortname' => $course['shortname'],
                'fullname' => $course['fullname'], 'roleid' => $highestrole);
        }
        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_users_courses_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                array(
            'id' => new external_value(PARAM_INT, 'id of course'),
            'shortname' => new external_value(PARAM_RAW, 'short name of course'),
            'fullname' => new external_value(PARAM_RAW, 'long name of course'),
            'roleid' => new external_value(PARAM_INT, 'roleid in that course')
                )
                )
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_user_role_in_course_parameters() {
        return new external_function_parameters(
                array('username' => new external_value(PARAM_TEXT, 'The name of the user'),
            'courseid' => new external_value(PARAM_INT, 'The id of the course'))
        );
    }

    /**
     * Returns the role the user has in that course
     * @return array courses
     */
    public static function get_user_role_in_course($username, $courseid) {
        require_capability('local/arsnova_connector:access_functions', context_system::instance());

        global $CFG;
        global $DB;
        require_once($CFG->dirroot . "/user/lib.php");
        require_once($CFG->dirroot . "/lib/accesslib.php");

        //Parameter validation
        $params = self::validate_parameters(self::get_user_role_in_course_parameters(), array('username' => $username, 'courseid' => $courseid));

        $user = $DB->get_record('user', array('username' => $params['username']));
        return self::get_relevant_role_in_course($user->id, $courseid);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_user_role_in_course_returns() {
        return new external_value(PARAM_INT, 'roleid of the role the user has in that course; -1 if none');
    }

    /**
     * Looks for relevant roles the user has in that course and returns the one with the most permissions if multiples are defined
     * or -1 if there is no relevant role
     * @param type $userid
     * @param type $courseid
     * @return type roleid
     */
    private static function get_relevant_role_in_course($userid, $courseid) {
        $coursecontext = context_course::instance($courseid);
        self::validate_context($coursecontext);
        $roles = get_user_roles($coursecontext, $userid);
        if (empty($roles)) {
            return -1;  //Not really necessary, just save some computation
        }

        $validRoles = [self::EDITING_TEACHER, self::STUDENT, self::TEACHER];
        $highestrole = -1;
        foreach ($roles as $role) {
            $currrole = $role->roleid;
            if (in_array($currrole, $validRoles) && ($highestrole == -1 || $highestrole == self::STUDENT)) {
                //If we haven't assigned a valid role or if the role is student (in the latter case we can only get more permissions, so just assign it)
                $highestrole = $currrole;
            }
        }
        return $highestrole;
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_course_users_parameters() {
        return new external_function_parameters(
                array('courseid' => new external_value(PARAM_INT, 'The id of the course'))
        );
    }

    /**
     * Returns usernames of the users enrolled in the given course
     * @return array courses
     */
    public static function get_course_users($courseid) {
        require_capability('local/arsnova_connector:access_functions', context_system::instance());

        global $CFG;
        require_once($CFG->dirroot . "/enrol/externallib.php");

        //Parameter validation
        $params = self::validate_parameters(self::get_course_users_parameters(), array('courseid' => $courseid));

        $courseusers = core_enrol_external::get_enrolled_users($params['courseid'], array(array('name' => 'userfields', 'value' => 'username')));
        $result = [];
        foreach ($courseusers as $user) {
            $result[] = $user['username'];
        }
        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_course_users_returns() {
        return new external_multiple_structure(
                new external_value('username', PARAM_TEXT, 'id of the enrolled user')
        );
    }

}
