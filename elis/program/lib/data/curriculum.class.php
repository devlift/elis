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
require_once elispm::lib('data/course.class.php');
require_once elispm::lib('data/curriculumcourse.class.php');
require_once elispm::lib('data/user.class.php');
require_once elispm::lib('datedelta.class.php');

/*
require_once CURMAN_DIRLOCATION . '/lib/datarecord.class.php';          // ok
require_once CURMAN_DIRLOCATION . '/lib/course.class.php';              // ok
require_once CURMAN_DIRLOCATION . '/lib/curriculumcourse.class.php';    // ok
require_once CURMAN_DIRLOCATION . '/lib/curriculumstudent.class.php';   // missing
require_once CURMAN_DIRLOCATION . '/lib/user.class.php';                // ok
require_once CURMAN_DIRLOCATION . '/lib/datedelta.class.php';           // ok
require_once CURMAN_DIRLOCATION . '/lib/customfield.class.php';         // ok
*/

class curriculum extends data_object_with_custom_fields {
    const TABLE = 'crlm_curriculum';

    static $associations = array(
        'clustercurriculum' => array(
            'class' => 'clustercurriculum',
            'foreignidfield' => 'curriculumid'
        ),
        'curriculumassignment' => array(
            'class' => 'curriculumassignment',
            'foreignidfield' => 'curriculumid'
        ),
        'curriculumcourse' => array(
            'class' => 'curriculumcourse',
            'foreignidfield' => 'curriculumid'
        ),
        'track' => array(
            'class' => 'track',
            'foreignidfield' => 'curid'
        ),
    );

    protected $_dbfield_idnumber;
    protected $_dbfield_name;
    protected $_dbfield_description;
    protected $_dbfield_reqcredits;
    protected $_dbfield_iscustom;
    protected $_dbfield_timecreated;
    protected $_dbfield_timemodified;
    protected $_dbfield_timetocomplete;
    protected $_dbfield_frequency;
    protected $_dbfield_priority;

    /**
     * Contructor.
     *
     * @param $curriculumdata int/object/array The data id of a data record or data elements to load manually.
     *
     */
    /*
    function curriculum($curriculumdata=false) {
        parent::datarecord();

        $this->set_table(CURTABLE);
        $this->add_property('id', 'int');
        $this->add_property('idnumber', 'string', true);
        $this->add_property('name', 'string', true);
        $this->add_property('description', 'string');
        $this->add_property('reqcredits', 'int');
        $this->add_property('iscustom', 'int');
        $this->add_property('timecreated', 'int');
        $this->add_property('timemodified', 'int');
        $this->add_property('timetocomplete', 'string');
        $this->add_property('frequency', 'string');
        $this->add_property('priority', 'int');

        if (is_numeric($curriculumdata)) {
            $this->data_load_record($curriculumdata);
        } else if (is_array($curriculumdata)) {
            $this->data_load_array($curriculumdata);
        } else if (is_object($curriculumdata)) {
            $this->data_load_array(get_object_vars($curriculumdata));
        }

        if (!empty($this->userid)) {
            $this->user = new user($this->userid);
        }

        if (!empty($this->id)) {
            // custom fields
            $level = context_level_base::get_custom_context_level('curriculum', 'elis_program');
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
        return context_level_base::get_custom_context_level('curriculum', 'elis_program');
    }

    public function set_from_data($data) {
        // TO-DO: re-enable when custom field stuff is done
        /*
        $fields = field::get_for_context_level('curriculum', 'elis_program');
        $fields = $fields ? $fields : array();
        foreach ($fields as $field) {
            $fieldname = "field_{$field->shortname}";
            if (isset($data->$fieldname)) {
                $this->$fieldname = $data->$fieldname;
            }
        }
        */

        $this->_load_data_from_record($data, true);
    }

	function delete() {
        $level = context_level_base::get_custom_context_level('curriculum', 'elis_program');
		$result = track::delete_for_curriculum($this->id);
		$result = $result && clustercurriculum::delete_for_curriculum($this->id);
		$result = $result && curriculumcourse::delete_for_curriculum($this->id);
		$result = $result && curriculumstudent::delete_for_curriculum($this->id);
        $result = $result && delete_context($level,$this->id);

    	return $result && $this->data_delete_record();
    }

    function __toString() {
        return $this->name;
    }

    /**
     * Check for a duplicate record when doing an insert.
     *
     * @param boolean $record true if a duplicate is found false otherwise
     * note: output is expected and treated as boolean please ensure return values are boolean
     */
    function duplicate_check($record=null) {
        if(empty($record)) {
            $record = $this;
        }

        /// Check for valid idnumber - it can't already exist in the user table.
        if ($this->_db->record_exists($this->table, 'idnumber', $record->idnumber)) {
            return true;
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////
    //                                                                 //
    //  STATIC FUNCTIONS:                                              //
    //                                                                 //
    /////////////////////////////////////////////////////////////////////

    /**
     * Check for any curriculum nags that need to be handled.
     *
     */
    public static function check_for_nags() {
        $status = self::check_for_completed_nags();
        $status = self::check_for_recurrence_nags() && $status;
        return $status;
    }

    /**
     * Check for any curriculum completed nags that need to be handled.
     */
    public static function check_for_completed_nags() {
        global $CFG;

        /// Completed curricula:

        $select  = 'SELECT cce.id as id, cce.credits AS curcredits,
                    cur.id as curid, cur.reqcredits as reqcredits,
                    cca.id as curassid, cca.userid, cca.curriculumid, cca.completed, cca.timecompleted,
                    cca.credits, cca.locked, cca.timecreated, cca.timemodified, cca.timeexpired,
                    ccc.courseid as courseid ';
        /// >* This will return ALL class enrolment records for a user's curriculum assignment.
        $from    = 'FROM {'.curriculumassignment::TABLE.'} cca ';
        $join    = 'INNER JOIN {'.user::TABLE.'} cu ON cu.id = cca.userid
                    INNER JOIN {'.curriculum::TABLE.'} cur ON cca.curriculumid = cur.id
                    INNER JOIN {'.curriculumcourse::TABLE.'} ccc ON ccc.curriculumid = cur.id
                    INNER JOIN {'.course::TABLE.'} cco ON cco.id = ccc.courseid
                    INNER JOIN {'.pmclass::TABLE.'} ccl ON ccl.courseid = cco.id
                    INNER JOIN {'.classenrolment::TABLE.'} cce ON (cce.classid = ccl.id) AND (cce.userid = cca.userid) ';
        /// >*
        $where   = 'WHERE (cca.completed = 0) AND (cce.completestatusid != '.STUSTATUS_NOTCOMPLETE.') ';
        $order   = 'ORDER BY cur.id, cca.id ASC ';
        //$groupby = "GROUP BY cca.id HAVING numcredits > cur.reqcredits "; /// The "HAVING" clause limits the returns to completed CURRICULA only.
        $groupby = '';
        $sql     = $select . $from . $join . $where . $groupby . $order;

        $curassid = 0;
        $curid = 0;
        $numcredits = 0;
        $reqcredits = 10000;    /// Initially so a completion event is not triggered.
        $requiredcourseids = array();
        $checkcourses = $requiredcourseids;
        $context = false;
        $curasstempl = new curriculumstudent(); // used just for its properties.
        $studenttempl = new student(); // used just for its properties.
        $timenow = time();
        $secondsinaday = 60 * 60 * 24;

        $rs = get_recordset_sql($sql);
        if ($rs) {
            while ($rec = rs_fetch_next_record($rs)) {
            /// Loop through enrolment records grouped by curriculum and curriculum assignments,
            /// counting the credits achieved and looking for all required courses to be complete.
            /// Load a new curriculum assignment
                if ($curassid != $rec->curassid) {
                    /// Check for completion - all credits have been earned and all required courses completed
                    if ($curassid && ($numcredits >= $reqcredits) && empty($checkcourses)) {
                        $currstudent->complete($timenow, $numcredits, 1);
                    }

                    $curassid = $rec->curassid;
                    $curassdata = array();

                    foreach ($curasstempl->properties as $prop => $type) {
                        $curassdata[$prop] = $rec->$prop;
                    }
                    $curassdata['id'] = $rec->curassid;
                    $currstudent = new curriculumstudent($curassdata);

                    $numcredits = 0;
                    $checkcourses = $requiredcourseids;
                }


            /// Get a new list of required courses.
                if ($curid != $rec->curid) {
                    $curid = $rec->curid;
                    $reqcredits = $rec->reqcredits;
                    $select = 'curriculumid = '.$curid.' AND required = 1';
                    if (!($requiredcourseids = get_records_select(curriculumcourse::TABLE, $select, '', 'courseid,required'))) {
                        $requiredcourseids = array();
                    }
                    $checkcourses = $requiredcourseids;
                }

            /// Track data for completion...
                $numcredits += $rec->curcredits;
                if (isset($checkcourses[$rec->courseid])) {
                    unset($checkcourses[$rec->courseid]);
                }
            }
        }

        /// Check for last record completion - all credits have been earned and all required courses completed
        if ($curassid && ($numcredits >= $reqcredits) && empty($checkcourses)) {
            $currstudent->complete($timenow, $numcredits, 1);
        }


        /*
        $sendtouser = $CURMAN->config->notify_curriculumnotcompleted_user;
        $sendtorole = $CURMAN->config->notify_curriculumnotcompleted_role;

        /// If nobody receives a notification, we're done.
        if (!$sendtouser && !$sendtorole) {
            return true;
        }
        */

        /// Incomplete curricula:

        $select  = 'SELECT cca.id as id, cca.userid, cca.curriculumid, cca.completed, cca.timecompleted,
                    cca.credits, cca.locked, cca.timecreated, cca.timemodified,
                    cur.id as curid, cur.timetocomplete as timetocomplete ';
        $from    = 'FROM {'.curriculumassignment::TABLE.'} cca ';
        $join    = 'INNER JOIN {'.user::TABLE.'} cu ON cu.id = cca.userid
                    INNER JOIN {'.curriculum::TABLE.'} cur ON cca.curriculumid = cur.id
                    LEFT JOIN {'.notificationlog::TABLE.'} cnl ON cnl.userid = cu.id AND cnl.instance = cca.id AND
                    cnl.event = \'curriculum_notcompleted\' ';
        $where   = 'WHERE (cca.completed = 0) AND (cur.timetocomplete != \'\') AND (cur.timetocomplete NOT LIKE \'0h, 0d, 0w, 0m, 0y%\') AND cnl.id IS NULL ';
        $order   = 'ORDER BY cur.id, cca.id ASC ';
        $groupby = '';
        $sql     = $select . $from . $join . $where . $groupby . $order;

        $context = false;
        $curasstempl = new curriculumstudent(); // used just for its properties.
        $studenttempl = new student(); // used just for its properties.
        $timenow = time();
        $secondsinaday = 60 * 60 * 24;

        $rs = get_recordset_sql($sql);
        if ($rs) {
            while ($rec = rs_fetch_next_record($rs)) {
                /// Loop through curriculum assignments checking for nags.
                $deltad = new datedelta($rec->timetocomplete);

                /// Need to fit this into the SQL instead.
                $reqcompletetime = $rec->timecreated + $deltad->gettimestamp();

                /// If no time to completion set, it has no completion restriction.
                if ($reqcompletetime  == 0) {
                    continue;
                }

                $daysfrom = ($reqcompletetime - $timenow) / $secondsinaday;
                /*
                if ($daysfrom <= $CURMAN->config->notify_curriculumnotcompleted_days) {
                    $curstudent = new curriculumstudent($rec);
                    mtrace("Triggering curriculum_notcompleted event.\n");
                    events_trigger('curriculum_notcompleted', $curstudent);
                }
                */
            }
        }

        return true;
    }

    /**
     * Check for any curriculum recurrence notifications that need to be sent out.
     */
    public static function check_for_recurrence_nags() {
        global $CFG;

        $sendtouser = elis::$config->elis_program->notify_curriculumrecurrence_user;
        $sendtorole = elis::$config->elis_program->notify_curriculumrecurrence_role;

        /// If nobody receives a notification, we're done.
        if (!$sendtouser && !$sendtorole) {
            return true;
        }

        $timenow = time();

        $sql = 'SELECT cca.id AS enrolmentid, cc.name AS curriculumname,
                       cu.id AS userid, cu.idnumber AS useridnumber, cu.firstname AS firstname, cu.lastname AS lastname,
                       mu.id AS muserid
                  FROM {'.curriculumassignment::TABLE.'} cca
                  JOIN {'.curriculum::TABLE.'} cc ON cca.curriculumid = cc.id
                  JOIN {'.user::TABLE.'} cu ON cu.id = cca.userid
                  JOIN {user} mu ON cu.idnumber = mu.idnumber
             LEFT JOIN {'.notificationlog::TABLE.'} cnl ON cnl.userid = cu.id AND cnl.instance = cca.id AND cnl.event = \'curriculum_recurrence\'
                 WHERE cnl.id IS NULL and cca.timeexpired > 0
                  AND cca.timeexpired < $timenow + '.elis::$config->elis_program->notify_curriculumrecurrence_days.'
               ';

        $usertempl = new user(); // used just for its properties.

        $rs = get_recordset_sql($sql);
        if ($rs) {
            while ($rec = rs_fetch_next_record($rs)) {
                /// Load the student...
                $userdata = array();
                $userdata['id'] = $rec->userid;
                foreach ($usertempl->properties as $prop => $type) {
                    if (isset($rec->$prop)) {
                        $userdata[$prop] = $rec->$prop;
                    }
                }
                $user = new user($userdata);
                /// Add the moodleuserid to the user record so we can use it in the event handler.
                $user->moodleuserid = $rec->muserid;
                $user->curriculumname = $rec->curriculumname;
                $user->enrolmentid = $rec->enrolmentid;

                mtrace("Triggering curriculum_recurrence event.\n");
                events_trigger('curriculum_recurrence', $user);
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
     * Function to handle curriculum recurrence events.
     *
     * @param   user     $user  The CM user the curriculum is recurring for
     *
     * @return  boolean         TRUE is successful, otherwise FALSE
     */

    public static function curriculum_recurrence_handler($user) {
        global $CFG;
        //require_once($CFG->dirroot.'/curriculum/lib/notifications.php');

        /// Does the user receive a notification?
        /*
        $sendtouser       = $CURMAN->config->notify_curriculumrecurrence_user;
        $sendtorole       = $CURMAN->config->notify_curriculumrecurrence_role;
        $sendtosupervisor = $CURMAN->config->notify_curriculumrecurrence_supervisor;

        /// If nobody receives a notification, we're done.
        if (!$sendtouser && !$sendtorole && !$sendtosupervisor) {
            return true;
        }
        */

        $context = get_system_context();

        $message = new notification();

        /// Set up the text of the message
        /*
        $text = empty($CURMAN->config->notify_curriculumrecurrence_message) ?
                    get_string('notifycurriculumrecurrencemessagedef', 'elis_program') :
                    $CURMAN->config->notify_curriculumrecurrence_message;
        $search = array('%%userenrolname%%', '%%curriculumname%%');
        $replace = array(fullname($user), $user->curriculumname);
        $text = str_replace($search, $replace, $text);

        $eventlog = new Object();
        $eventlog->event = 'curriculum_recurrence';
        $eventlog->instance = $user->enrolmentid;
        if ($sendtouser) {
            $message->send_notification($text, $user, null, $eventlog);
        }

        $users = array();

        if ($sendtorole) {
            /// Get all users with the notify_curriculumrecurrence capability.
            if ($roleusers = get_users_by_capability($context, 'block/curr_admin:notify_curriculumrecurrence')) {
                $users = $users + $roleusers;
            }
        }

        if ($sendtosupervisor) {
            /// Get parent-context users.
            if ($supervisors = cm_get_users_by_capability('user', $user->id, 'block/curr_admin:notify_curriculumrecurrence')) {
                $users = $users + $supervisors;
            }
        }

        foreach ($users as $u) {
            $message->send_notification($text, $u, $user, $eventlog);
        }
        */

        return true;
    }

    public static function get_by_idnumber($idnumber) {
        $retval = $this->_db->get_record(curriculum::TABLE, array('idnumber'=>$idnumber));

        if(!empty($retval)) {
            $retval = new curriculum($retval->id);
        } else {
            $retval = null;
        }

        return $retval;
    }

    /**
     * Clone a curriculum.
     * @param array $options options for cloning.  Valid options are:
     * - 'tracks': whether or not to clone tracks (default: false)
     * - 'courses': whether or not to clone courses (default: false)
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
    function duplicate($options=array()) {
        require_once elispm::lib('data/track.class.php');

        $objs = array('errors' => array());
        if (isset($options['targetcluster'])) {
            $cluster = $options['targetcluster'];
            if (!is_object($cluster) || !is_a($cluster, 'cluster')) {
                $options['targetcluster'] = $cluster = new cluster($cluster);
            }
        }

        // clone main curriculum object
        $clone = new curriculum($this);
        unset($clone->id);
        if (isset($cluster)) {
            // if cluster specified, append cluster's name to curriculum
            $clone->name = $clone->name . ' - ' . $cluster->name;
            $clone->idnumber = $clone->idnumber . ' - ' . $cluster->name;
        }
        $clone = new curriculum(addslashes_recursive($clone));
        if (!$clone->add()) {
            $objs['errors'][] = get_string('failclustcpycurr', 'elis_program', $this);
            return $objs;
        }
        $objs['curricula'] = array($this->id => $clone->id);
        $options['targetcurriculum'] = $clone->id;

        // associate with target cluster (if any)
        if (isset($cluster)) {
            clustercurriculum::associate($cluster->id, $clone->id);
        }

        if (!empty($options['courses'])) {
            // copy courses
            $currcrs = curriculumcourse_get_list_by_curr($this->id);
            if (!empty($currcrs)) {
                $objs['courses'] = array();
                $objs['classes'] = array();
                foreach ($currcrs as $currcrsdata) {
                    $course = new course($currcrsdata->courseid);
                    $rv = $course->duplicate($options);
                    if (isset($rv['errors']) && !empty($rv['errors'])) {
                        $objs['errors'] = array_merge($objs['errors'], $rv['errors']);
                    }
                    if (isset($rv['courses'])) {
                        $objs['courses'] = $objs['courses'] + $rv['courses'];
                    }
                    if (isset($rv['classes'])) {
                        $objs['classes'] = $objs['classes'] + $rv['classes'];
                    }

                    // associate with curriculum
                    if (isset($rv['courses'][$course->id])) {
                        $curcrs = new curriculumcourse($currcrsdata);
                        unset($curcrs->id);
                        $curcrs->courseid = $rv['courses'][$course->id];
                        $curcrs->curriculumid = $clone->id;
                        $curcrs->add();
                    }
                }
            }
        }

        if (!empty($objs['errors'])) {
            return $objs;
        }

        if (!empty($options['tracks'])) {
            // copy tracks
            $tracks = track_get_listing('name', 'ASC', 0, 0, '', '', $this->id);
            if (isset($objs['courses'])) {
                $options['coursemap'] = $objs['courses'];
            }
            if (!empty($tracks)) {
                $objs['tracks'] = array();
                if (isset($objs['courses'])) {
                    $options['coursemap'] = $objs['courses'];
                }
                if (!isset($objs['classes'])) {
                    $objs['classes'] = array();
                }
                foreach ($tracks as $track) {
                    $track = new track($track);
                    $options['classmap'] = $objs['classes'];
                    $rv = $track->duplicate($options);
                    if (isset($rv['errors']) && !empty($rv['errors'])) {
                        $objs['errors'] = array_merge($objs['errors'], $rv['errors']);
                    }
                    if (isset($rv['tracks'])) {
                        $objs['tracks'] = $objs['tracks'] + $rv['tracks'];
                    }
                    if (isset($rv['classes'])) {
                        $objs['classes'] = $objs['classes'] + $rv['classes'];
                    }
                }
            }
        }
        return $objs;
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
        $isnew = empty($this->id);

        parent::save();

        if (!$isnew) {
            // If this setting is changed, we need to update the existing curriclum expiration values (ELIS-1172)
            // TO-DO: enable this when curriculumassignment is done
            if ($rs = $this->_db->get_recordset_select(curriculumassignment::TABLE, "timeexpired != 0 AND curriculumid = {$this->id}", '', 'id, userid')) {
                $timenow = time();

                foreach ($rs as $rec) {
                    $update = new stdClass;
                    $update->id           = $rec->id;
                    $update->timeexpired  = calculate_curriculum_expiry(NULL, $this->id, $rec->userid);
                    $update->timemodified = $timenow;

                    update_record(curriculumassignment::TABLE, $update);
                 }

                rs_close($rs);
            }
        }

        field_data::set_for_context_from_datarecord('curriculum', $this);
    }

}


/// Non-class supporting functions. (These may be able to replaced by a generic container/listing class)


/**
 * Gets a curriculum listing with specific sort and other filters.
 *
 * @param   string        $sort        Field to sort on.
 * @param   string        $dir         Direction of sort.
 * @param   int           $startrec    Record number to start at.
 * @param   int           $perpage     Number of records per page.
 * @param   string        $namesearch  Search string for curriculum name.
 * @param   string        $alpha       Start initial of curriculum name filter.
 * @param   array         $contexts    Contexts to search (in the form return by
 * @param   int           $userid      The id of the user we are assigning to curricula
 *
 * @return  object array               Returned records.
 */
function curriculum_get_listing($sort='name', $dir='ASC', $startrec=0, $perpage=0, $namesearch='',
                                $alpha='', $contexts = null, $userid = 0) {
    global $USER, $DB;

    $select = 'SELECT cur.*, (SELECT COUNT(*) FROM {'.curriculumcourse::TABLE.'}
               WHERE curriculumid = cur.id ) as courses ';
    $tables = 'FROM {'.curriculum::TABLE.'} cur ';
    $join   = ' ';
    $on     = ' ';

    $where = array("cur.iscustom = '0'");
    $params = array();

    if ($contexts !== null && !empty($namesearch)) {
        $namesearch = trim($namesearch);

        $name_like = $DB->sql_like('name', '?');

        $where[] = "($name_like)";
        $params += array("%$namesearch%");
    }

    if ($alpha) {
        $name_like = $DB->sql_like('name', '?');
        $where[] = "($name_like)";
        $params[] = "$alpha%";
    }

    if ($contexts !== null) {
        $filter_object = $contexts->get_filter('cur.id', 'curriculum');
        $filter_sql = $filter_object->get_sql(false, 'cur');
        if (isset($filter_sql['where'])) {
            $where[] = $filter_sql['where'];
            $params += $filter_sql['where_params'];
        }
    }

    if(!empty($userid)) {
        //get the context for the "indirect" capability
        $context = cm_context_set::for_user_with_capability('cluster', 'block/curr_admin:curriculum:enrol_cluster_user', $USER->id);

        $clusters = cluster_get_user_clusters($userid);
        $allowed_clusters = $context->get_allowed_instances($clusters, 'cluster', 'clusterid');

        $curriculum_context = cm_context_set::for_user_with_capability('curriculum', 'block/curr_admin:curriculum:enrol', $USER->id);
        $filter_object = $curriculum_context->get_filter('cur.id', 'curriculum');
        $filter_sql = $filter_object->get_sql(false, 'cur');
        if (isset($filter_sql['where'])) {
            $curriculum_filter = $filter_sql['where'];
            $curriculum_params = $filter_sql['where_params'];
        }

        if(empty($allowed_clusters)) {
            $where[] = $curriculum_filter;
            $params[] = $curriculum_params;
        } else {
            $allowed_clusters_list = implode(',', $allowed_clusters);

            //this allows both the indirect capability and the direct curriculum filter to work
            $where[] = '(
                          cur.id IN (
                            SELECT clstcur.curriculumid
                            FROM {'.clustercurriculum::TABLE.'} clstcur
                            WHERE clstcur.clusterid IN ('.$allowed_clusters_list.')
                          )
                          OR
                          '.$curriculum_filter.'
                        )';
            $params[] = $curriculum_params;
        }

    }

    if (!empty($where)) {
        $where = 'WHERE '.implode(' AND ',$where).' ';
    } else {
        $where = '';
    }

    if ($sort) {
        $sort = 'ORDER BY '.$sort .' '. $dir.' ';
    }

    $sql = $select.$tables.$join.$on.$where.$sort;

    return $DB->get_records_sql($sql, $params, $startrec, $perpage);
}

/**
 * Gets a curriculum listing suitable for a select box.
 *
 * @return array Returned records.
 */
function curriculum_get_menu() {
    return get_records_menu(curriculum::TABLE, NULL, NULL, 'name', 'id,name');
}

function curriculum_count_records($namesearch = '', $alpha = '', $contexts = null) {
    global $DB;

    $where = array("iscustom = '0'");
    $params = array();

    if (!empty($namesearch)) {
        $name_like = $DB->sql_like('name', '?');

        $where[] = "($name_like)";
        $params += array("%$namesearch%");
    }

    if ($alpha) {
        $name_like = $DB->sql_like('name', '?');
        $where[] = "($name_like)";
        $params[] = "$alpha%";
    }

    if ($contexts != null) {
        $filter_object = $contexts->get_filter('cur.id', 'curriculum');
        $filter_sql = $filter_object->get_sql(false, 'cur');
        if (isset($filter_sql['where'])) {
            $where[] = $filter_sql['where'];
            $params += $filter_sql['where_params'];
        }
    }

    $where = implode(' AND ',$where).' ';

    return $DB->count_records_select(curriculum::TABLE, $where, $params);
}
