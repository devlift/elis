<?php
/**
 * ELIS(TM): Enterprise Learning Intelligence Suite
 * Copyright (C) 2008-2011 Remote-Learner.net Inc (http://www.remote-learner.net)
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
 * @subpackage programmanagement
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2011 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once elis::lib('data/data_object_with_custom_fields.class.php');
require_once elis::lib('data/customfield.class.php');
require_once elispm::lib('data/curriculum.class.php');
require_once elispm::lib('data/curriculumcourse.class.php');

/* Add these back in as they are migrated
require_once CURMAN_DIRLOCATION . '/lib/datarecord.class.php';          // ok
require_once CURMAN_DIRLOCATION . '/lib/environment.class.php';         // not used
require_once CURMAN_DIRLOCATION . '/lib/curriculum.class.php';          // ok
require_once CURMAN_DIRLOCATION . '/lib/curriculumcourse.class.php';    // ok
require_once CURMAN_DIRLOCATION . '/lib/customfield.class.php';         // ok
*/


class course extends data_object_with_custom_fields {
    const TABLE = 'crlm_course';

    static $config_default_prefix = 'crsdft';

    static $associations = array(
        'pmclass' => array(
            'class' => 'pmclass',
            'foreignidfield' => 'courseid'
        ),
        'coursetemplate' => array(
            'class' => 'coursetemplate',
            'foreignidfield' => 'courseid'
        ),
        'coursecompletion' => array(
            'class' => 'coursecompletion',
            'foreignidfield' => 'courseid'
        ),
        'coursecorequisite' => array(
            'class' => 'coursecorequisite',
            'foreignidfield' => 'courseid'
        ),
        'courseprerequisite' => array(
            'class' => 'courseprerequisite',
            'foreignidfield' => 'courseid'
        ),
        'curriculumcourse' => array(
            'class' => 'curriculumcourse',
            'foreignidfield' => 'courseid'
        ),
        'trackclass' => array(
            'class' => 'trackclass',
            'foreignidfield' => 'courseid'
        ),
    );

    protected $_dbfield_name;
    protected $_dbfield_code;
    protected $_dbfield_idnumber;
    protected $_dbfield_syllabus;
    protected $_dbfield_documents;
    protected $_dbfield_lengthdescription;
    protected $_dbfield_length;
    protected $_dbfield_credits;
    protected $_dbfield_completion_grade;
    protected $_dbfield_environmentid;
    protected $_dbfield_cost;
    protected $_dbfield_timecreated;
    protected $_dbfield_timemodified;
    protected $_dbfield_version;

    private $location;
    private $templateclass;

    /**
     * Contructor.
     *
     * @param $coursedata int/object/array The data id of a data record or data elements to load manually.
     *
     */
    /*
    function course($coursedata = false) {
        parent::datarecord();

        if (is_numeric($coursedata)) {
            $this->data_load_record($coursedata);
        } else if (is_array($coursedata)) {
            $this->data_load_array($coursedata);
        } else if (is_object($coursedata)) {
            $this->data_load_array(get_object_vars($coursedata));
        }

        if (!empty($this->environmentid)) {
            $this->environment = new environment($this->environmentid);
        }

        // FIXME: this should be done every time the template is updated, i.e. through getter/setter methods
        if (!empty($this->id)) {
            $template = new coursetemplate($this->id);
            $course = $this->_db->get_record('course', 'id', $template->location);

            if (!empty($course)) {
                $this->locationlabel = $course->fullname . ' ' . $course->shortname;
            }

            // custom fields
            $level = context_level_base::get_custom_context_level('course', 'elis_program');
            if ($level) {
                $fielddata = field_data::get_for_context(get_context_instance($level,$this->id));
                $fielddata = $fielddata ? $fielddata : array();
                foreach ($fielddata as $name => $value) {
                    $this->{"field_{$name}"} = $value;
                }
            }
        }
    }
    */

    protected function get_field_context_level() {
        return context_level_base::get_custom_context_level('course', 'elis_program');
    }

    /*
     * Remove specified environment from all courses.
     *
     * @param $envid int Environment id.
     * @return bool Status of operation.
     */
    public static function remove_environment($envid) {
    	$sql = 'UPDATE {'.course::TABLE.'} SET environmentid=0 where environmentid=' . $envid;
    	return $this->_db->execute_sql($sql, "");
    }

    /////////////////////////////////////////////////////////////////////
    //                                                                 //
    //  FORM FUNCTIONS:                                                //
    //                                                                 //
    /////////////////////////////////////////////////////////////////////

    public function setUrl($url = null, $action = array()) {
        if(!($url instanceof noodle_url)) {
            $url = new moodle_url($url, $action);
        }

        $this->form_url = $url;
    }

    public function create_edit_form($formid='', $rows=2, $cols=40) {
        $configdata = array();
        $configdata['formid'] = $formid;
        $configdata['rows'] = $rows;
        $configdata['cols'] = $cols;
        $configdata['curassignment'] = $this->get_assigned_curricula();
        $configdata['id'] = $this->id;

        if($this->course_exists()) {
            $crsForm = new cmCourseEditForm(null, $configdata);
        } else {
            $crsForm = new cmCourseAddForm(null, $configdata);
        }

        if (!empty($this->id)) {
            $template = $this->coursetemplate->current();
            $course = $this->_db->get_record('course', 'id', $template->location);

            if (!empty($course)) {
                $this->locationlabel = $course->fullname . ' ' . $course->shortname;
            }
        }

        $crsForm->set_data($this);

        return $crsForm;
    }


    /**
     * Return the HTML to edit a specific course.
     * This could be extended to allow for application specific editing, for example
     * a Moodle interface to its formslib.
     *
     * @param $formid string A suffix to put on all 'id' and index for all 'name' attributes.
     *                       This should be unique if being used more than once in a form.
     * @param $extraclass string Any extra class information to add to the output.
     *
     * @return string The form HTML, without the form.
     */
    public function edit_form_html($formid='', $rows='2', $cols='40') {
        $output = '';

        if (!empty($this->id)) {
            $template = $this->coursetemplate->current();
            $output .= $this->add_js_function($template->location);
        } else {
            $output .= $this->add_js_function();
        }

        $crsForm = $this->create_edit_form($formid, $rows, $cols);

        $crsForm->focus();

        ob_start();
        $crsForm->display();
        $output .= ob_get_contents();
        ob_end_clean();

        return $output;
    }

    public function create_completion_form($elemid=0, $formid='', $extraclass='', $rows='2', $cols='40') {
        if ($elemid != 0) {
            $elem = $this->_db->get_record(coursecompletion::TABLE, 'id', $elemid);
        } else {
            $elem = new Object();
            $elem->idnumber = '';
            $elem->name = '';
            $elem->description = '';
            $elem->completion_grade = 0;
            $elem->required = 1;
        }

        $config_data = array();

        $config_data['elem'] = $elem;
        $config_data['elemid'] = $elemid;
        $config_data['formid'] = $formid;
        $config_data['rows'] = $rows;
        $config_data['cols'] = $cols;
        $config_data['id'] = $this->id;

        return new completionform($this->form_url, $config_data);
    }

    /**
     * Return the HTML to edit a course's completion elements.
     * This could be extended to allow for application specific editing, for example
     * a Moodle interface to its formslib.
     *
     * @param $formid string A suffix to put on all 'id' and index for all 'name' attributes.
     *                       This should be unique if being used more than once in a form.
     * @param $extraclass string Any extra class information to add to the output.
     *
     * @return string The form HTML, without the form.
     */
    function edit_completion_form_html($elemid=0, $formid='', $extraclass='', $rows='2', $cols='40') {
        $completionForm = $this->create_completion_form($elemid);

        ob_start();
        $completionForm->display();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }


    /////////////////////////////////////////////////////////////////////
    //                                                                 //
    //  DATA FUNCTIONS:                                                //
    //                                                                 //
    /////////////////////////////////////////////////////////////////////


    /**
     * Get a list of the course completion elements for the current course.
     *
     * @param none
     * @return array The list of course IDs.
     */
    function get_completion_elements() {
        if (!$this->id) {
            return false;
        }

        return $this->_db->get_records(coursecompletion::TABLE, 'courseid', $this->id);
    }

    /*
     * Returns an aggregate of enrolment completion statuses for all classes created from this course.
     */
    public function get_completion_counts() {
        $sql = 'SELECT cce.completestatusid status, COUNT(cce.completestatusid) count
        FROM {'.student::TABLE.'} cce
        INNER JOIN {'.pmclass::TABLE.'} cc ON cc.id = cce.classid
        INNER JOIN {'.course::TABLE.'} cco ON cco.id = cc.courseid
        WHERE cco.id = '.$this->id.'
        GROUP BY cce.completestatusid';

        $rows = $this->_db->get_records_sql($sql);

        $ret = array(STUSTATUS_NOTCOMPLETE=>0, STUSTATUS_FAILED=>0, STUSTATUS_PASSED=>0);

        if (empty($rows)) {
            return $ret;
        }

        foreach($rows as $row) {
            // We add the counts to the existing array, which should be as good as an assignment
            // because we never have duplicate statuses.  Of course, stranger things have happened.

            $ret[$row->status] += $row->count;
        }

        return $ret;
    }

    /**
     * Save an element.
     *
     * @param none
     * @return array The list of course IDs.
     */
    function save_completion_element($elemrecord) {
        if (!$this->id || !$this->_dbloaded) {
            return false;
        }

        $elemrecord->courseid = $this->id;
        if (empty($elemrecord->id)) {
            return $this->_db->insert_record(coursecompletion::TABLE, $elemrecord);
        } else {
            return $this->_db->update_record(coursecompletion::TABLE, $elemrecord);
        }
    }

    /**
     * Delete an element.
     *
     * @param none
     * @return array The list of course IDs.
     */
    function delete_completion_element($elemid) {
        if (!$this->id || !$this->_dbloaded) {
            return false;
        }

        return $this->_db->delete_records(coursecompletion::TABLE, 'id', $elemid);
    }

    /**
     * Retrieve the curricula that are affiliated with this course
     *
     * @param none
     * @return array The list of curricula IDs.
     */
    function get_assigned_curricula() {
      $assigned = array();

      if (!$this->id) {
          return false;
      }

      $result = $this->_db->get_records(curriculumcourse::TABLE, 'courseid', $this->id);

      if ($result) {
          foreach ($result as $data) {
            $assigned[$data->curriculumid] = $data->id;
          }
      }

      return $assigned;
    }

    /**
     * Add a course to a curricula
     *
     * @param array $curriculums array value is the curriculum id
     * @return nothing
     * TODO: need to add some error checking
     */
    function add_course_to_curricula($curriculums = array()) {
        $curcourse = new curriculumcourse();

        // Add course to curricula (one or more)
        $curcrsrecord = array();
        $curcrsrecord['id']           = 0;
        $curcrsrecord['courseid']     = $this->id;
        $curcrsrecord['required']     = 0;
        $curcrsrecord['frequency']    = 0;
        $curcrsrecord['timeperiod']   = key($curcourse->timeperiod_values);
        $curcrsrecord['position']     = 0;

        if (is_array($curriculums)) {
            foreach ($curriculums as $curr) {
              $curcrsrecord['curriculumid'] = $curr;
              $newcurcrs = new curriculumcourse($curcrsrecord);
              $status = $newcurcrs->data_insert_record();
              if ($status !== true) {
                  if (!empty($status->message)) {
                      //$output .= cm_error('Record not created. Reason: '.$status->message);
                  } else {
                      //echo cm_error('Record not created.');
                  }
              } else {
                  //echo 'New record created.';
              }
            }
        }
    }

    /**
     * Remove course curriculum assignments
     */
    function remove_course_curricula() {
        $currassigned = $this->get_assigned_curricula();

        foreach($currassigned as $currid => $rowid) {
                // Remove
                $curcrs = new curriculumcourse($rowid);
                $curcrs->data_delete_record();
        }
    }

    function add_js_function($id = 0) {
        $id = empty($id) ? 0 : $id;

        return '<script language=javascript>
                    function openNewWindow() {
                        var clsTemplate = document.getElementById("id_templateclass");
                        var classname = clsTemplate.value;
                        var x = window.open(\'coursetemplatepage.php?class=\' + classname + \'&selected=\' + '.$id.', \'newWindow\', \'height=500,width=500,resizable,scrollbars\');
                    }

                    function cleartext() {
                        var crslabel = document.getElementById("id_locationlabel");
                        crslabel.value = "";

                        var location = document.getElementById("id_location");
                        location.value = "";
                    }
                </script>';
    }

    function course_exists($id=null) {
        if(empty($id)){
            return record_exists(course::TABLE, 'id', $this->id);
        } else {
            return record_exists(course::TABLE, 'id', $id);
        }
    }

    /////////////////////////////////////////////////////////////////////
    //                                                                 //
    //  STATIC FUNCTIONS:                                              //
    //    These functions can be used without instatiating an object.  //
    //    Usage: student::[function_name([args])]                      //
    //                                                                 //
    /////////////////////////////////////////////////////////////////////

    public static function get_default() {
        $default_values = array();
        $prefix = self::$config_default_prefix;
        $prefixlen = strlen($prefix);

        /*
        foreach ($CURMAN->config as $key => $data) {

          if (false !== strpos($key, $prefix)) {

              $index = substr($key, $prefixlen);

              $default_values[$index] = $data;
          }
        }
        */

        return $default_values;
    }

    /**
     * Check for any course nags that need to be handled.
     *
     */
    function check_for_nags() {
        global $CFG;

        /*
        $sendtouser =       $CURMAN->config->notify_courserecurrence_user;
        $sendtorole =       $CURMAN->config->notify_courserecurrence_role;
        $sendtosupervisor = $CURMAN->config->notify_courserecurrence_supervisor;

        /// If nobody receives a notification, we're done.
        if (!$sendtouser && !$sendtorole && !$sendtosupervisor) {
            return true;
        }
        */

        /// Course Recurrence:
        /// A course needs to recur if,
        ///     - The user has previously taken a class of this course,
        ///     - The frequency time has passed from when they last completed a class of this course

        /// currenttime > completetime + (frequency * timeperiod_in_seconds)


        mtrace("Checking course notifications<br />\n");

        /// Get all curriculum courses with a frequency greater than zero.
        /// LEFT JOIN Moodle course and Moodle user info, since they may not have records.
        /// LEFT JOIN notification log where there isn't a notification record for the course and user and 'class_notstarted'.
        $day   = 60 * 60 * 24;
        $week  = $day * 7;
        $month = $day * 30;
        $year  = $day * 365;
        $timenow = time();

        /// Enrolment id will be the one that won't repeat, so it will be the unique index.
        $select = 'SELECT cce.id, ccc.frequency, ccc.timeperiod, ' .
                  'cc.name as coursename, ' .
                  'c.name as curriculumname, ' .
                  'cu.id as userid, cu.idnumber as useridnumber, cu.firstname as firstname, cu.lastname as lastname, ' .
                  'cce.id as enrolmentid, cce.completetime as completetime, ' .
                  'u.id as muserid ';
        $from   = 'FROM {'.curriculumcourse::TABLE.'} ccc ';
        $join   = 'INNER JOIN {'.course::TABLE.'} cc ON cc.id = ccc.courseid ' .
                  'INNER JOIN {'.curriculum::TABLE.'} c ON c.id = ccc.curriculumid ' .
                  'INNER JOIN {'.curriculumassignment::TABLE.'} cca ON cca.curriculumid = c.id ' .
                  'INNER JOIN {'.user::TABLE.'} cu ON cu.id = cca.userid ' .
                  'INNER JOIN {'.pmclass::TABLE.'} ccl ON ccl.courseid = cc.id ' .
                  'INNER JOIN {'.classenrolment::TABLE.'} cce ON cce.classid = ccl.id ' .
                  'LEFT JOIN {'.$CFG->prefix.'user u ON u.idnumber = cu.idnumber ' .
                  'LEFT JOIN {'.notificationlog::TABLE.' cnl ON cnl.userid = cu.id AND cnl.instance = cce.id AND cnl.event = \'course_recurrence\' ';
        $where  = 'WHERE (cce.completestatusid != '.STUSTATUS_NOTCOMPLETE.') AND (ccc.frequency > 0) '.
                  'AND ((cce.completetime + ' .
            /// This construct is to select the number of seconds to add to determine the delta frequency based on the timeperiod
                  '(CASE ccc.timeperiod WHEN \'year\' THEN (ccc.frequency * '.$year.')
                                        WHEN \'month\' THEN (ccc.frequency * '.$month.')
                                        WHEN \'week\' THEN (ccc.frequency * '.$week.')
                                        WHEN \'day\' THEN (ccc.frequency * '.$day.')
                                        ELSE 0 END)' .
            ///
                  ') < '.$timenow.') AND (cnl.id IS NULL) ';
        $order  = 'ORDER BY cce.id ASC ';
        $sql    = $select . $from . $join . $where . $order;

        $usertempl = new user(); // used just for its properties.

        $rs = get_recordset_sql($sql);
        if ($rs) {
            while ($rec = rs_fetch_next_record($rs)) {
                /// Load the student...
                $userdata = array();
                foreach ($usertempl->properties as $prop => $type) {
                    if (isset($rec->$prop)) {
                        $userdata[$prop] = $rec->$prop;
                    }
                }
                /// Do this AFTER copying properties to prevent accidentially stomping on the user id
                $userdata['id'] = $rec->userid;
                $user = new user($userdata);
                /// Add the moodleuserid to the user record so we can use it in the event handler.
                $user->moodleuserid = $rec->muserid;
                $user->coursename = $rec->coursename;
                $user->enrolmentid = $rec->enrolmentid;

                mtrace("Triggering course_recurrence event.\n");
                events_trigger('course_recurrence', $user);
            }
        }
        return true;
    }

    /*
     * ---------------------------------------------------------------------------------------
     * EVENT HANDLER FUNCTIONS:
     *
     * These functions handle specific student events.
     *
     */

    /**
     * Function to handle course recurrence events.
     *
     * @param   user      $user  CM user object representing the user in the course
     *
     * @return  boolean          TRUE is successful, otherwise FALSE
     */

    public static function course_recurrence_handler($user) {
        global $CFG;
        require_once($CFG->dirroot.'/curriculum/lib/notifications.php');

        /// Does the user receive a notification?
        /*
        $sendtouser       = $CURMAN->config->notify_courserecurrence_user;
        $sendtorole       = $CURMAN->config->notify_courserecurrence_role;
        $sendtosupervisor = $CURMAN->config->notify_courserecurrence_supervisor;

        /// If nobody receives a notification, we're done.
        if (!$sendtouser && !$sendtorole && !$sendtosupervisor) {
            return true;
        }
        */

        $context = get_system_context();

        /// Make sure this is a valid user.
        $enroluser = new user($user->id);
        if (empty($enroluser->id)) {
            print_error('nouser', 'elis_program');
            return true;
        }

        $message = new notification();

        /// Set up the text of the message
        /*
        $text = empty($CURMAN->config->notify_courserecurrence_message) ?
                    get_string('notifycourserecurrencemessagedef', 'elis_program') :
                    $CURMAN->config->notify_courserecurrence_message;
        $search = array('%%userenrolname%%', '%%coursename%%');
        $replace = array(fullname($user), $user->coursename);
        $text = str_replace($search, $replace, $text);

        $eventlog = new Object();
        $eventlog->event = 'course_recurrence';
        $eventlog->instance = $user->enrolmentid;
        if ($sendtouser) {
            $message->send_notification($text, $user, null, $eventlog);
        }

        $users = array();

        if ($sendtorole) {
            /// Get all users with the notify_courserecurrence capability.
            if ($roleusers = get_users_by_capability($context, 'block/curr_admin:notify_courserecurrence')) {
                $users = $users + $roleusers;
            }
        }

        if ($sendtosupervisor) {
            /// Get parent-context users.
            if ($supervisors = cm_get_users_by_capability('user', $user->id, 'block/curr_admin:notify_courserecurrence')) {
                $users = $users + $supervisors;
            }
        }

        foreach ($users as $u) {
            $message->send_notification($text, $u, $enroluser);
        }
        */

        return true;
    }

	public function delete() {
        $level = context_level_base::get_custom_context_level('course', 'elis_program');
		$return = curriculumcourse::delete_for_course($this->id);
		$return = $return && cmclass::delete_for_course($this->id);
		$return = $return && taginstance::delete_for_course($this->id);
        $return = $return && coursetemplate::delete_for_course($this->id);
        $return = $return && delete_context($level,$this->id);

    	return $return && $this->data_delete_record();
    }

    public static function find($filter=null, array $sort=array(), $limitfrom=0, $limitnum=0, moodle_database $db=null) {
        return parent::find($filter, $sort, $limitfrom, $limitnum, $db);
    }

    public function set_from_data($data) {
        if (isset($data->curriculum)) {
            $this->curriculum = $data->curriculum;
        }

        if (isset($data->location)) {
            $this->location = $data->location;
            $this->templateclass = $data->templateclass;
        }

        $fields = field::get_for_context_level('course', 'elis_program');
        $fields = $fields ? $fields : array();
        foreach ($fields as $field) {
            $fieldname = "field_{$field->shortname}";
            if (isset($data->$fieldname)) {
                $this->$fieldname = $data->$fieldname;
            }
        }

        $this->_load_data_from_record($data, true);
    }

    static $validation_rules = array(
        'validate_idnumber_not_empty',
        'validate_unique_idnumber'
    );

    function validate_idnumber_not_empty() {
        return validate_not_empty($this, 'idnumber');
    }

    function validate_unique_idnumber() {
        return validate_is_unique($this, array('idnumber'));
    }

    public function save() {
        parent::save();

        if(isset($this->curriculum)) {
            $this->add_course_to_curricula($this->curriculum);
        }

        // Add moodle course template
        if (isset($this->location)) {
            $template = $this->coursetemplate->current();
            $template->location           = $this->location;
            $template->templateclass      = $this->templateclass;
            $template->courseid           = $this->id;

            $template->save();
        } else {
            coursetemplate::delete_for_course($this->id);
        }

        field_data::set_for_context_from_datarecord('course', $this);
    }

    public function __toString() {
    	return $this->name;
    }

    static public function get_by_idnumber($idnumber) {
        $retval = null;

        $course = $this->_db->get_record(course::TABLE, 'idnumber', $idnumber);


        if(!empty($course)) {
            $retval = new course($course->id);
        }

        return $retval;
    }

    /**
     * Clone a course.
     * @param array $options options for cloning.  Valid options are:
     * - 'classes': whether or not to clone classes (default: false)
     * - 'moodlecourses': whether or not to clone Moodle courses (if they were
     *   autocreated).  Values can be (default: "copyalways"):
     *   - "copyalways": always copy course
     *   - "copyautocreated": only copy autocreated courses
     *   - "autocreatenew": autocreate new courses from course template
     *   - "link": link to existing course
     * - 'targetcluster': the cluster id or cluster object (if any) to
     *   associate the clones with (default: none)
     * @return array array of array of object IDs created.  Key in outer array
     * is type of object (plural).  Key in inner array is original object ID,
     * value is new object ID.  Outer array also has an entry called 'errors',
     * which is an array of any errors encountered when duplicating the
     * object.
     */
    function duplicate($options) {
        require_once elispm::lib('pmclass.class.php');
        require_once elispm::lib('coursetemplate.class.php');
        $objs = array('errors' => array());
        if (isset($options['targetcluster'])) {
            $cluster = $options['targetcluster'];
            if (!is_object($cluster) || !is_a($cluster, 'cluster')) {
                $options['targetcluster'] = $cluster = new cluster($cluster);
            }
        }

        // clone main course object
        $clone = new course($this);
        unset($clone->id);
        if (isset($cluster)) {
            // if cluster specified, append cluster's name to course
            $clone->name = $clone->name . ' - ' . $cluster->name;
            $clone->idnumber = $clone->idnumber . ' - ' . $cluster->name;
        }
        $clone = new course(addslashes_recursive($clone));
        if (!$clone->add()) {
            $objs['errors'][] = get_string('failclustcpycurrcrs', 'elis_program', $this);
            return $objs;
        }

        $objs['courses'] = array($this->id => $clone->id);
        $options['targetcourse'] = $clone->id;

        // copy completion elements
        $compelems = $this->get_completion_elements();
        if (!empty($compelems)) {
            foreach ($compelems as $compelem) {
                $compelem = addslashes_recursive($compelem);
                unset($compelem->id);
                $clone->save_completion_esement($complem);
            }
        }

        // copy template
        $template = $this->_db->get_record(coursetemplate::TABLE, 'courseid', $this->id);
        $template = new coursetemplate($template);
        unset($template->id);
        $template->courseid = $clone->id;
        $template->add();

        // copy the classes
        if (!empty($options['classes'])) {
            $classes = cmclass_get_record_by_courseid($this->id);
            if (!empty($classes)) {
                $objs['classes'] = array();
                foreach ($classes as $class) {
                    $class = new cmclass($class);
                    $rv = $class->duplicate($options);
                    if (isset($rv['errors']) && !empty($rv['errors'])) {
                        $objs['errors'] = array_merge($objs['errors'], $rv['errors']);
                    }
                    if (isset($rv['classes'])) {
                        $objs['classes'] = $objs['classes'] + $rv['classes'];
                    }
                }
            }
        }
        return $objs;
    }
}

/// Non-class supporting functions. (These may be able to replaced by a generic container/listing class)

/**
 * Gets a course listing with specific sort and other filters.
 *
 * @param string $sort Field to sort on.
 * @param string $dir Direction of sort.
 * @param int $startrec Record number to start at.
 * @param int $perpage Number of records per page.
 * @param string $namesearch Search string for course name.
 * @param string $descsearch Search string for course description.
 * @param string $alpha Start initial of course name filter.
 * @return object array Returned records.
 */

function course_get_listing($sort='crs.name', $dir='ASC', $startrec=0, $perpage=0, $namesearch='', $alpha='', $contexts=null) {
    global $DB;

    //$LIKE = $DB->sql_compare();
    $LIKE = 'LIKE';

    //$select = 'SELECT crs.*, env.name as envname, env.description as envdescription, (SELECT COUNT(*) FROM {'.curriculumcourse::TABLE.'} WHERE courseid = crs.id ) as curricula ';
    $select = 'SELECT crs.*, (SELECT COUNT(*) FROM {'.curriculumcourse::TABLE.'} WHERE courseid = crs.id ) as curricula ';
    $tables = 'FROM {'.course::TABLE.'} crs ';
    //$join   = 'LEFT JOIN {'.enrvironment::TABLE.'} env ';
    //$on     = 'ON env.id = crs.environmentid ';
    $join   = ' ';
    $on     = ' ';

    $where = array();
    if (!empty($namesearch)) {
        $namesearch = trim($namesearch);
        $where[] = "((crs.name $LIKE '%$namesearch%') OR (crs.idnumber $LIKE '%$namesearch%')) ";
    }

    if ($alpha) {
        $where[] = (!empty($where) ? ' AND ' : '') . "(crs.name $LIKE '$alpha%') ";
    }

    if ($contexts !== null) {
        //$where[] = $contexts->sql_filter_for_context_level('crs.id', 'course');

        //$filter_object = $contexts->filter_for_context_level('crs.id', 'course');
        //$where[] = $filter_object->get_sql();
    }

    if (!empty($where)) {
        $where = 'WHERE '.implode(' AND ',$where).' ';
    } else {
        $where = '';
    }

    if ($sort) {
        $sort = 'ORDER BY '.$sort .' '. $dir.' ';
    }

    if (!empty($perpage)) {
        //if ($this->_db->_dbconnection->databaseType == 'postgres7') {
        //    $limit = 'LIMIT ' . $perpage . ' OFFSET ' . $startrec;
        //} else {
            $limit = 'LIMIT '.$startrec.', '.$perpage;
        //}
    } else {
        $limit = '';
    }

    $sql = $select.$tables.$join.$on.$where.$sort.$limit;

    return $DB->get_records_sql($sql);
}


function course_count_records($namesearch = '', $alpha = '', $contexts = null) {
    global $DB;

    $where = array();

    //$LIKE = $this->_db->sql_compare();
    $LIKE = 'LIKE';

    if (!empty($namesearch)) {
        $where[] = "((name $LIKE '%$namesearch%') OR (idnumber $LIKE '%$namesearch%'))";
    }

    if ($alpha) {
        $where[] = "(name $LIKE '$alpha%')";
    }

    if ($contexts !== null) {
        //$where[] = $contexts->sql_filter_for_context_level('id', 'course');

        //$filter_object = $contexts->filter_for_context_level('id', 'course');
        //$where[] = $filter_object->get_sql();
    }

    $where = implode(' AND ', $where);

    return $DB->count_records_select(course::TABLE, $where);
}

