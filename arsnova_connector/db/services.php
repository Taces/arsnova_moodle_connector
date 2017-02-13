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
 * Web service local plugin template external functions and service definitions.
 *
 * @package     local_arsnova_connector
 * @category    access
 * @copyright   2017, HS Hannover, elc@hs-hannover.de
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$functions = array(
    'local_arsnova_connector_get_users_courses' => array(
        'classname' => 'local_arsnova_connector_external',
        'methodname' => 'get_users_courses',
        'classpath' => 'local/arsnova_connector/externallib.php',
        'description' => 'Returns a list of all courses the user is enrolled in',
        'capabilities' => 'local/arsnova_connector:access_functions, moodle/course:view, moodle/course:viewhiddencourses',
        'type' => 'read'
    ),
    'local_arsnova_connector_get_user_role_in_course' => array(
        'classname' => 'local_arsnova_connector_external',
        'methodname' => 'get_user_role_in_course',
        'classpath' => 'local/arsnova_connector/externallib.php',
        'description' => 'Returns the roleid of the role the user has in that course',
        'capabilities' => 'local/arsnova_connector:access_functions, moodle/course:view, moodle/course:viewhiddencourses',
        'type' => 'read'
    ),
    'local_arsnova_connector_get_course_users' => array(
        'classname' => 'local_arsnova_connector_external',
        'methodname' => 'get_course_users',
        'classpath' => 'local/arsnova_connector/externallib.php',
        'description' => 'Returns only the usernames enrolled in the course',
        'capabilities' => 'local/arsnova_connector:access_functions, moodle/course:view, moodle/course:viewhiddencourses,'
        . ' moodle/course:viewparticipants, moodle/user:viewdetails, moodle/user:viewalldetails',
        'type' => 'read'
    )
);

$services = array(
    'Arsnova Service' => array(
        'functions' => array('local_arsnova_connector_get_users_courses',
            'local_arsnova_connector_get_user_role_in_course',
            'local_arsnova_connector_get_course_users'),
        'restrictedusers' => 0,
        'enabled' => 1,
    ),
);
