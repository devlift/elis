<?php
/**
 * Common page class for role assignments
 *
 * ELIS(TM): Enterprise Learning Intelligence Suite
 * Copyright (C) 2008-2010 Remote-Learner.net Inc (http://www.remote-learner.net)
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
 * @package    elis
 * @subpackage curriculummanagement
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2010 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once elispm::lib('lib.php');
require_once elispm::lib('page.class.php');
require_once elispm::file('form/engineform.class.php');

abstract class enginepage extends pm_page {
    const LANG_FILE = 'elis_program';

    protected $parent_page;
    protected $section;

    public function __construct($params = null) {
        parent::__construct($params);
        $this->section = $this->get_parent_page()->section;
    }

    abstract protected function get_context();

    abstract protected function get_parent_page();

    abstract protected function get_course_id();

    /**
     * Return the engine form
     *
     * @return object The engine form
     */
    protected function get_engine_form() {
        return new cmEngineForm(null, array('courseid' => $this->get_course_id()));
    }

    function get_tab_page() {
        return $this->get_parent_page();
    }

    function get_page_title_default() {
        return print_context_name($this->get_context(), false);
    }

    function build_navbar_default() {
        global $DB;

        //obtain the base of the navbar from the parent page class
        $parent_template = $this->get_parent_page()->get_new_page();
        $parent_template->build_navbar_view();
        $this->_navbar = $parent_template->navbar;

        //add a link to the first role screen where you select a role
        $id = $this->required_param('id', PARAM_INT);
        $page = $this->get_new_page(array('id' => $id), true);
        $this->navbar->add(get_string('results_engine', self::LANG_FILE), $page->url);
    }

    function print_tabs() {
        $id = $this->required_param('id', PARAM_INT);
        $this->get_parent_page()->print_tabs(get_class($this), array('id' => $id));
    }

    /**
     * Return the page parameters for the page.  Used by the constructor for
     * calling $this->set_url().
     *
     * @return array
     */
    protected function _get_page_params() {
        $params = parent::_get_page_params();

        $id = $this->required_param('id', PARAM_INT);
        $params['id'] = $id;

        $page = $this->optional_param('page', 0, PARAM_INT);
        if ($page != 0) {
            $params['page'] = $page;
        }

        return $params;
    }

    function can_do_default() {
        return has_capability('moodle/role:assign', $this->get_context());
    }

    /**
     * Counts all the users assigned this role in this context or higher
     * (can be used by child classes to override the counting with other criteria)
     *
     * @param mixed $roleid either int or an array of ints
     * @param object $context
     * @param bool $parent if true, get list of users assigned in higher context too
     * @return int Returns the result count
     */
    function count_role_users($roleid, $context, $parent = false) {
        //default behaviour of counting role assignments
        return count_role_users($roleid, $context, $parent);
    }

    /**
     * Display the default page
     */
    function display_default() {
        $form = $this->get_engine_form();

        $this->print_tabs();
        $form->display_html();

    }

    protected function get_base_params() {
        $params = parent::get_base_params();
        $params['role'] = $this->required_param('role', PARAM_INT);
        return $params;
    }

    // ELISAT-349: Part 1
    function get_extra_page_params() {
        $extra_params = array();
        $sort = optional_param('sort', 'name', PARAM_ACTION);
        $order = optional_param('dir', 'ASC', PARAM_ACTION);
        if ($order != 'DESC') {
            $order = 'ASC';
        }
        $extra_params['sort'] = $sort;
        $extra_params['dir'] = $order;
        return $extra_params;
    }

    protected function get_selection_form() {
        if ($this->is_assigning()) {
            return new addroleform();
        } else {
            return new removeroleform();
        }
    }

    protected function process_assignment($data) {
        $context = $this->get_context();

        //make sure the current user can assign roles on the current context
        $assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
        $roleids = array_keys($assignableroles);
        if (!in_array($data->role, $roleids)) {
            print_error('nopermissions', 'error');
        }

        //perform the role assignments
        foreach ($data->_selection as $user) {
            role_assign($data->role, cm_get_moodleuserid($user), $context->id);
        }

        //set up the redirect to the appropriate page
        $id = $this->required_param('id', PARAM_INT);
        $role = $this->required_param('role', PARAM_INT);
        $tmppage = $this->get_new_page(array('_assign' => 'assign',
                                             'id'      => $id,
                                             'role'    => $role));
        redirect($tmppage->url, get_string('users_assigned_to_role','elis_program',count($data->_selection)));
    }

    protected function process_unassignment($data) {
        $context = $this->get_context();

        //make sure the current user can assign roles on the current context
        $assignableroles = get_assignable_roles($context, ROLENAME_BOTH);
        $roleids = array_keys($assignableroles);
        if (!in_array($data->role, $roleids)) {
            print_error('nopermissions', 'error');
        }

        //perform the role unassignments
        foreach ($data->_selection as $user) {
            role_unassign($data->role, cm_get_moodleuserid($user), $context->id);
        }

        //set up the redirect to the appropriate page
        $id = $this->required_param('id', PARAM_INT);
        $role = $this->required_param('role', PARAM_INT);
        $tmppage = $this->get_new_page(array('_assign' => 'unassign',
                                             'id'      => $id,
                                             'role'    => $role));
        redirect($tmppage->url, get_string('users_removed_from_role','elis_program',count($data->_selection)));
    }

    protected function get_selection_filter() {
        $post = $_POST;
        $filter = new pm_user_filtering(null, 'index.php', array('s' => $this->pagename) + $this->get_base_params());
        $_POST = $post;
        return $filter;
    }

    protected function print_selection_filter($filter) {
        $filter->display_add();
        $filter->display_active();
    }

    protected function get_assigned_records($filter) {
        global $CFG, $DB;

        $context = $this->get_context();
        $roleid = $this->required_param('role', PARAM_INT);

        $pagenum = optional_param('page', 0, PARAM_INT);
        $perpage = 30;

        $sort = optional_param('sort', 'name', PARAM_ACTION);
        $order = optional_param('dir', 'ASC', PARAM_ACTION);
        if ($order != 'DESC') {
            $order = 'ASC';
        }

        static $sortfields = array(
            'name' => array('lastname', 'firstname'),
            'idnumber' => 'idnumber',
            );
        if (!array_key_exists($sort, $sortfields)) {
            $sort = key($sortfields);
        }
        if (is_array($sortfields[$sort])) {
            $sortclause = implode(', ', array_map(create_function('$x', "return \"\$x $order\";"), $sortfields[$sort]));
        } else {
            $sortclause = "{$sortfields[$sort]} $order";
        }

        $where = "idnumber IN (SELECT mu.idnumber
                                 FROM {user} mu
                                 JOIN {role_assignments} ra
                                      ON ra.userid = mu.id
                                WHERE ra.contextid = :contextid
                                  AND ra.roleid = :roleid
                                  AND mu.mnethostid = :mnethostid)";

        $params = array('contextid' => $context->id,
                        'roleid' => $roleid,
                        'mnethostid' => $CFG->mnet_localhost_id);

        list($extrasql, $extraparams) = $filter->get_sql_filter();
        if ($extrasql) {
            $where .= " AND $extrasql";
            $params = array_merge($params, $extraparams);
        }

        $count = $DB->count_records_select(user::TABLE, $where, $params);
        $users = $DB->get_records_select(user::TABLE, $where, $params, $sortclause, '*', $pagenum*$perpage, $perpage);

        return array($users, $count);
    }

    protected function get_available_records($filter) {
        global $CFG, $DB;

        $context = $this->get_context();
        $roleid = required_param('role', PARAM_INT);

        $pagenum = optional_param('page', 0, PARAM_INT);
        $perpage = 30;

        $sort = optional_param('sort', 'name', PARAM_ACTION);
        $order = optional_param('dir', 'ASC', PARAM_ACTION);
        if ($order != 'DESC') {
            $order = 'ASC';
        }

        static $sortfields = array(
            'name' => array('lastname', 'firstname'),
            'lastname' => array('lastname', 'firstname'),
            'firstname' => array('firstname', 'lastname'),
            'idnumber' => 'idnumber',
            );
        if (!array_key_exists($sort, $sortfields)) {
            $sort = key($sortfields);
        }
        if (is_array($sortfields[$sort])) {
            $sortclause = implode(', ', array_map(create_function('$x', "return \"\$x $order\";"), $sortfields[$sort]));
        } else {
            $sortclause = "{$sortfields[$sort]} $order";
        }

        $where = "idnumber NOT IN (SELECT mu.idnumber
                                     FROM {user} mu
                                LEFT JOIN {role_assignments} ra
                                          ON ra.userid = mu.id
                                    WHERE ra.contextid = :contextid
                                      AND ra.roleid = :roleid
                                      AND mu.mnethostid = :mnethostid)";

        $params = array('contextid' => $context->id,
                        'roleid' => $roleid,
                        'mnethostid' => $CFG->mnet_localhost_id);

        list($extrasql, $extraparams) = $filter->get_sql_filter();

        if ($extrasql) {
            $where .= " AND $extrasql";
            $params = array_merge($params, $extraparams);
        }

        $count = $DB->count_records_select(user::TABLE, $where, $params);
        $users = $DB->get_records_select(user::TABLE, $where, $params, $sortclause, '*', $pagenum*$perpage, $perpage);

        return array($users, $count);
    }

    function get_records_from_selection($record_ids) {
        global $DB;

        //figure out the body if the equals or in clause
        list($idtest, $params) = $DB->get_in_or_equal($record_ids);

        //apply the condition to the user id
        $where = "id {$idtest}";

        $records = $DB->get_records_select(user::TABLE, $where, $params);
        return $records;
    }

    protected function print_record_count($count) {
        print_string('usersfound','elis_program',$count);
    }

    protected function create_selection_table($records, $baseurl) {
        $pagenum = optional_param('page', 0, PARAM_INT);
        $baseurl .= "&page={$pagenum}"; // ELISAT-349: part 2

        //persist our specific parameters
        $id = $this->required_param('id', PARAM_INT);
        $baseurl .= "&id={$id}";
        $assign = $this->optional_param('_assign', 'unassign', PARAM_ACTION);
        $baseurl .= "&_assign={$assign}";
        $role = $this->required_param('role', PARAM_INT);
        $baseurl .= "&role={$role}";

        $records = $records ? $records : array();
        $columns = array('_selection' => array('header' => ''),
                         'idnumber'   => array('header' => get_string('idnumber')),
                         'name'       => array('header' => array('firstname' => array('header' => get_string('firstname')),
                                                                 'lastname' => array('header' => get_string('lastname'))
                                                                 ),
                                               'display_function' => 'user_table_fullname'

                        )
        );

        //determine sort order
        $sort = optional_param('sort', 'lastname', PARAM_ALPHA);
        $dir  = optional_param('dir', 'ASC', PARAM_ALPHA);
        if ($dir !== 'DESC') {
            $dir = 'ASC';
        }

        if (isset($columns[$sort])) {
            $columns[$sort]['sortable'] = $dir;
        } elseif (isset($columns['name']['header'][$sort])) {
            $columns['name']['header'][$sort]['sortable'] = $dir;
        } else {
            $sort = 'lastname';
            $columns['name']['header']['lastname']['sortable'] = $dir;
        }

        return new user_selection_table($records, $columns, new moodle_url($baseurl));
    }
}

/**
 * Engine page for courses
 *
 * @author Tyler Bannister <tyler.bannister@remote-learner.net>
 */
class course_enginepage extends enginepage {
    public $pagename = 'crsengine';

    /**
     * Get context
     *
     * @return object The context
     */
    protected function get_context() {
        if (!isset($this->context)) {
            $id = $this->required_param('id', PARAM_INT);

            $context_level = context_level_base::get_custom_context_level('course', 'elis_program');
            $context_instance = get_context_instance($context_level, $id);
            $this->set_context($context_instance);
        }
        return $this->context;
    }

    /**
     * Get the course id.
     *
     * @return int The course id
     */
    protected function get_course_id() {
        return $this->required_param('id', PARAM_INT);
    }

    /**
     * Get parent page object
     *
     * @return object An object of the same type as the parent page
     * @uses $CFG
     * @uses $CURMAN
     */
    protected function get_parent_page() {
        if (!isset($this->parent_page)) {
            global $CFG, $CURMAN;
            require_once elispm::file('coursepage.class.php');
            $id = $this->required_param('id', PARAM_INT);
            $this->parent_page = new coursepage(array('id' => $id,
                                                      'action' => 'view'));
        }
        return $this->parent_page;
    }
}

/**
 * Engine page for classes
 *
 * Classes have an extra form field that courses don't have.
 *
 * @author Tyler Bannister <tyler.bannister@remote-learner.net>
 */
class class_enginepage extends enginepage {
    public $pagename = 'clsengine';

    /**
     * Get context
     *
     * @return object The context
     */
    protected function get_context() {
        if (!isset($this->context)) {
            $id = $this->required_param('id', PARAM_INT);

            $context_level = context_level_base::get_custom_context_level('class', 'elis_program');
            $context_instance = get_context_instance($context_level, $id);
            $this->set_context($context_instance);
        }
        return $this->context;
    }

    /**
     * Get the course id.
     *
     * @return int The course id
     * @uses $DB
     */
    protected function get_course_id() {
        global $DB;

        $classid  = $this->required_param('id', PARAM_INT);
        $courseid = $DB->get_field('courseid', 'crlm_class', array('id' => $classid));
        return $courseid;
    }

    /**
     * Get parent page object
     *
     * @return object An object of the same type as the parent page
     * @uses $CFG
     * @uses $CURMAN
     */
    protected function get_parent_page() {
        if (!isset($this->parent_page)) {
            global $CFG, $CURMAN;
            require_once elispm::file('pmclasspage.class.php');
            $id = $this->required_param('id');
            $this->parent_page = new pmclasspage(array('id' => $id,
                                                       'action' => 'view'));
        }
        return $this->parent_page;
    }
}
