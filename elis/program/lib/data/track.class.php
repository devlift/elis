<?php
/**
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
 * @subpackage programmanagement
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2010 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once elis::lib('data/data_object_with_custom_fields.class.php');

/* Add these back in as they are migrated
require_once CURMAN_DIRLOCATION . '/lib/datarecord.class.php';
require_once CURMAN_DIRLOCATION . '/lib/curriculum.class.php';
require_once CURMAN_DIRLOCATION . '/lib/course.class.php';
require_once CURMAN_DIRLOCATION . '/lib/cmclass.class.php';

require_once CURMAN_DIRLOCATION . '/lib/coursetemplate.class.php';
require_once CURMAN_DIRLOCATION . '/lib/curriculumcourse.class.php';
require_once CURMAN_DIRLOCATION . '/lib/moodlecourseurl.class.php';
require_once CURMAN_DIRLOCATION . '/lib/classmoodlecourse.class.php';
require_once CURMAN_DIRLOCATION . '/lib/usertrack.class.php';
require_once CURMAN_DIRLOCATION . '/lib/clustercurriculum.class.php';
require_once CURMAN_DIRLOCATION . '/lib/student.class.php';
require_once CURMAN_DIRLOCATION . '/lib/clustercurriculum.class.php';
*/
require_once elis::lib('data/classmoodlecourse.class.php');
require_once elis::lib('data/course.class.php');
require_once elis::lib('data/coursetemplate.class.php');
require_once elis::lib('data/pmclass.class.php');
require_once elis::lib('data/user.class.php');

//define ('TABLE', 'crlm_track');
//define ('CLASSTABLE', 'crlm_track_class');

class track extends data_object_with_custom_fields {
    const TABLE = 'crlm_track';

     /**
     * User ID-number
     * @var    char
     * @length 255
     */
    protected $_dbfield_id;
    protected $_dbfield_curid;
    protected $_dbfield_idnumber;
    protected $_dbfield_name;
    protected $_dbfield_description;
    protected $_dbfield_startdate;
    protected $_dbfield_enddate;
    protected $_dbfield_defaulttrack;

    static $associations = array(
        'clustertrack' => array(
            'class' => 'clustertrack',
            'foreignidfield' => 'trackid'
        ),
        'usertrack' => array(
            'class' => 'usertrack',
            'foreignidfield' => 'trackid'
        ),
        'trackassignment' => array(
            'class' => 'trackassignment',
            'foreignidfield' => 'trackid'
        ),
        'curriculum' => array(
            'class' => 'curriculum',
            'idfield' => 'trackid'
        )
    );

    /**
     * Contructor.
     */
    /*
      FIXME: add support for custom fields
    function __construct($src=false, $field_map=null, array $associations=array(), moodle_database $database=null) {
        parent::datarecord($src, $field_map, $associations, $database);

        if (!empty($this->id)) {
            /// Load any other data we may want that is associated with the id number...
            // custom fields
            $level = context_level_base::get_custom_context_level('user', 'elis_program');
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
    /*function track($track = false) {
        parent::datarecord();
        $this->set_table(TABLE);
        $this->add_property('id', 'int');
        $this->add_property('curid', 'int', true);
        $this->add_property('idnumber', 'string', true);
        $this->add_property('name', 'string', true);
        $this->add_property('description', 'string');
        $this->add_property('startdate', 'int');
        $this->add_property('enddate', 'int');
        $this->add_property('defaulttrack', 'int');

        if (is_numeric($track)) {
            $this->data_load_record($track);
        } else if (is_array($track)) {
            $this->data_load_array($track);
        } else if (is_object($track)) {
            $this->data_load_array(get_object_vars($track));
        }

        if (!empty($this->curid)) {
            $this->curriculum = new curriculum($this->curid);
        }

        if (!empty($this->id)) {
            // custom fields
            $level = context_level_base::get_custom_context_level('track', 'block_curr_admin');
            if ($level) {
                $fielddata = field_data::get_for_context(get_context_instance($level,$this->id));
                $fielddata = $fielddata ? $fielddata : array();
                foreach ($fielddata as $name => $value) {
                    $this->{"field_{$name}"} = $value;
                }
            }
        }
    }*/

    static $delete_is_complex = true;

    protected function get_field_context_level() {
        return context_level_base::get_custom_context_level('track', 'elis_program');
    }

    public static function delete_for_curriculum($id) {
        $result = true;

        //look up and delete associated tracks
        if($tracks = track_get_listing('name', 'ASC', 0, 0, '', '', $id)) {
            foreach ($tracks as $track) {
                $record = new track($track->id);
                $result = $record->delete() && $result;
            }
        }

        return $result;
    }

    /**
     * Creates and associates a class with a track for every course that
     * belongs to the track curriculum
     *
     * TODO: return some data
     */
    function track_auto_create() {
        global $DB;

        if (empty($this->curid) or
            empty($this->id)) {
            cm_error('trackid and curid have not been properly initialized');
            return false;
        }

        $autoenrol = false;
        $usetemplate = false;

        // Pull up the curricula assignment record(s)
        //        $curcourse = curriculumcourse_get_list_by_curr($this->curid);
        $sql = 'SELECT ccc.*, cc.idnumber ' .
            'FROM ' . curriculumcourse::TABLE . ' ccc ' .
            'INNER JOIN ' . course::TABLE . ' cc ON cc.id = ccc.courseid '.
            'WHERE ccc.curriculumid = ' .$this->curid;
        $curcourse = $DB->get_records_sql($sql);
        if (empty($curcourse)) {
            $curcourse = array();
        }

        // For every course of the curricula determine which ones need -
        // to have their auto enrol flag set
        foreach ($curcourse as $recid => $curcourec) {
            $idnumber = (!empty($curcourec->idnumber) ? $curcourec->idnumber.'-' : '') . $this->idnumber;
            $classojb = new cmclass(array('courseid' => $curcourec->courseid,
                                          'idnumber' => $idnumber));

            // Course is required
            if ($curcourec->required) {
                $autoenrol = true;
            }

            $cortemplate = new coursetemplate($curcourec->courseid);

            // Course is using a Moodle template
            if (!empty($cortemplate->location)) {
                // Parse the course id from the template location
                $classname = $cortemplate->templateclass;
                $templateobj = new $classname();
                $templatecorid = $cortemplate->location;
                $usetemplate = true;
            }

            // Create class
            if (!($classid = $classojb->auto_create_class(array('courseid'=>$curcourec->courseid)))) {
                cm_error('Could not create class');
                return false;
            }

            // attach course to moodle template
            if ($usetemplate) {
                moodle_attach_class($classid, 0, '', false, false, true);
            }

            $trackclassobj = new trackassignment(array('courseid' => $curcourec->courseid,
                                                            'trackid'  => $this->id,
                                                            'classid' => $classojb->id));

            // Set auto-enrol flag
            if ($autoenrol) {
                $trackclassobj->autoenrol = 1;
            }

            // Assign class to track
            $trackclassobj->save();

            // Create and assign class to default system track
            // TODO: commented out until we know what to do with CURMAN->config
            /*
            if (!empty($CURMAN->config->userdefinedtrack)) {
                $trkid = $this->create_default_track();

                $trackclassobj = new trackassignment(array('courseid' => $curcourec->courseid,
                                                                'trackid'  => $trkid,
                                                                'classid' => $classojb->id));

                // Set auto-enrol flag
                if ($autoenrol) {
                    $trackclassobj->autoenrol = 1;
                }

                // Assign class to default system track
                $trackclassobj->save();

            }*/
            $usetemplate = false;
            $autoenrol = false;
        }
    }

    /**
     * Creates a default track
     * @return mixed id of new track or false if error
     */
    function create_default_track() {

        $time = time();
        $trackid = 0;

        $trackid = $this->get_default_track();

        if (false !== $trackid) {
            return $trackid;
        }

        $param = array('curid' => $this->curid,
                       'idnumber' => 'default.'.$time,
                       'name' => 'DT.CURID.'.$this->curid,
                       'description' => 'Default Track',
                       'defaulttrack' => 1,
                       'startdate' => $time,
                       'enddate' => $time,
                       'defaulttrack' => 1,
            );

        $newtrk = new track($param);

        if ($newtrk->save()) {
            return $newtrk->id;
        }

        return false;
    }

    /**
     * Returns the track id of the default track for a curriculum
     *
     * @return mixed $trackid Track id or false if an error occured
     */
    function get_default_track() {
        global $DB;

        $trackid = $DB->get_field(TABLE, 'id', array('curid'=> $this->curid,
                                                     'defaulttrack'=> 1));
        return $trackid;
    }

    /**
     * Removes all associations with a track, this entails removing
     * user track, cluster track and class track associations
     * @param none
     * @return none
     */
    function delete() {
        // Cascade
        $level = context_level_base::get_custom_context_level('track', 'block_curr_admin');
        $result = usertrack::delete_for_track($this->id);
        $result = $result && clustertrack::delete_for_track($this->id);
        $result = $result && trackassignment::delete_for_track($this->id);
        $result = $result && delete_context($level,$this->id);

        return $result && $this->data_delete_record();
    }

    function __toString() {
        return $this->name . ' (' . $this->idnumber . ')';
    }

    public function set_from_data($data) {
        $this->autocreate = !empty($data->autocreate) ? $data->autocreate : 0;

        $fields = field::get_for_context_level('track', 'block_curr_admin');
        $fields = $fields ? $fields : array();
        foreach ($fields as $field) {
            $fieldname = "field_{$field->shortname}";
            if (isset($data->$fieldname)) {
                $this->$fieldname = $data->$fieldname;
            }
        }

        parent::set_from_data($data);
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
    public function save() { //add
        $status = parent::save(); //add

        if ($status && $this->autocreate) {
            $this->track_auto_create();
        }

        $status = $status && field_data::set_for_context_from_datarecord('track', $this);

        return $status;
    }

    /*function update() {
        $result = parent::update();

        $result = $result && field_data::set_for_context_from_datarecord('track', $this);

        return $result;
    } */

    public function duplicate_check($record=null) {
        global $DB;

        if(empty($record)) {
            $record = $this;
        }

        /// Check for valid idnumber - it can't already exist in the user table.
        if ($DB->record_exists($this->table, 'idnumber', $record->idnumber)) {
            return true;
        }

        return false;
    }

    public static function get_by_idnumber($idnumber) {
        global $CURMAN;

        $retval = $DB->get_record(TABLE, 'idnumber', $idnumber);

        if(!empty($retval)) {
            $retval = new track($retval->id);
        } else {
            $retval = null;
        }

        return $retval;
    }

    /**
     * Check whether a student should be auto-enrolled in more class.
     *
     * This function gets called when a student completes a class, and checks
     * whether that class was a prerequisite for other courses in a track,
     * and whether the student should now be enrolled in the other classes.
     *
     * Strategy:
     * - find tracks that the student is in, and get their curricula
     * - find the course that the class is associated with
     * - see if the course is a prerequisite in any of the curricula
     * - if yes, see if there are any autoenrollable classes in the tracks that
     *   the student can be autoenrolled in
     */
    public static function check_autoenrol_after_course_completion($enrolment) {
        global $DB;

        $userid = $enrolment->userid;
        $courseid = $enrolment->cmclass->courseid;

        // this query will give us all the classes that had course $courseid as
        // a prerequisite, that are autoenrollable in tracks that user $userid
        // is in
        $sql = "SELECT trkcls.*, trk.curid, cls.courseid
                  FROM {". courseprerequisite::TABLE ."} prereq
            INNER JOIN {". curriculumcourse::TABLE ."} curcrs
                       ON curcrs.id = prereq.curriculumcourseid
            INNER JOIN {". pmclass::TABLE ."} cls
                       ON curcrs.courseid = cls.courseid
            INNER JOIN {". trackassignment::TABLE. "} trkcls
                       ON trkcls.classid = cls.id AND trkcls.autoenrol = 1
            INNER JOIN {". track::TABLE. "} trk
                       ON trk.id = trkcls.trackid AND trk.curid = curcrs.curriculumid
            INNER JOIN {". usertrack::TABLE. "} usrtrk
                       ON usrtrk.trackid = trk.id
                 WHERE prereq.courseid = ? AND usrtrk.userid = ?";
        $params = array($courseid, $userid);
        $recs = $DB->get_records_sql($sql, $params);

        // now we just need to loop through them, and enrol them
        $recs = $recs ? $recs : array();
        foreach ($recs as $trkcls) {
            $curcrs = new curriculumcourse();
            $curcrs->courseid = $trkcls->courseid;
            $curcrs->curriculumid = $trkcls->curid;
            if ($curcrs->prerequisites_satisfied($userid)) {
                // no unsatisfied prereqs -- enrol the student
                $newenrolment = new student();
                $newenrolment->userid = $userid;
                $newenrolment->classid = $trkcls->classid;
                $newenrolment->enrolmenttime = time();
                $newenrolment->save(array('waitlist' => 1));
            }
        }

        return true;
    }

    /**
     * Clone a track
     * @param array $options options for cloning.  Valid options are:
     * - 'targetcurriculum': the curriculum id to associate the clones with
     *   (default: same as original track)
     * - 'classmap': a mapping of class IDs to use from the original track to
     *   the cloned track.  If a class from the original track is not mapped, a
     *   new class will be created
     * - 'moodlecourse': whether or not to clone Moodle courses (if they were
     *   autocreated).  Values can be (default: "copyalways"):
     *   - "copyalways": always copy course
     *   - "copyautocreated": only copy autocreated courses
     *   - "autocreatenew": autocreate new courses from course template
     *   - "link": link to existing course
     * @return array array of array of object IDs created.  Key in outer array
     * is type of object (plural).  Key in inner array is original object ID,
     * value is new object ID.  Outer array also has an entry called 'errors',
     * which is an array of any errors encountered when duplicating the
     * object.
     */
    function duplicate($options=array()) {

        $objs = array('errors' => array());
        if (isset($options['targetcluster'])) {
            $cluster = $options['targetcluster'];
            if (!is_object($cluster) || !is_a($cluster, 'cluster')) {
                $options['targetcluster'] = $cluster = new cluster($cluster);
            }
        }

        // clone main track object
        $clone = new track($this);
        unset($clone->id);

        if (isset($options['targetcurriculum'])) {
            $clone->curid = $options['targetcurriculum'];
        }
        if (isset($cluster)) {
            // if cluster specified, append cluster's name to track
            $clone->idnumber = $clone->idnumber . ' - ' . $cluster->name;
            $clone->name = $clone->name . ' - ' . $cluster->name;
        }
        $clone = new track(addslashes_recursive($clone));
        $clone->autocreate = false; // avoid warnings
        if (!$clone->save()) {
            $objs['errors'][] = get_string('failclustcpytrk', 'block_curr_admin', $this);
            return $objs;
        }
        $objs['tracks'] = array($this->id => $clone->id);

        // associate with target cluster (if any)
        if (isset($cluster)) {
            clustertrack::associate($cluster->id, $clone->id);
        }

        // copy classes
        $clstrks = track_assignment_get_listing();
        if (!empty($clstrks)) {
            $objs['classes'] = array();
            if (!isset($options['classmap'])) {
                $options['classmap'] = array();
            }
            foreach ($clstrks as $clstrkdata) {
                $newclstrk = new trackassignment($clstrkdata);
                $newclstrk->trackid = $clone->id;
                unset($newclstrk->id);
                if (isset($options['classmap'][$clstrkdata->clsid])) {
                    // use existing duplicate class
                    $class = new cmclass($options['classmap'][$clstrkdata->clsid]);
                } else {
                    // no existing duplicate -> duplicate class
                    $class = new cmclass($clstrkdata->clsid);
                    $rv = $class->duplicate($options);
                    if (isset($rv['errors']) && !empty($rv['errors'])) {
                        $objs['errors'] = array_merge($objs['errors'], $rv['errors']);
                    }
                    if (isset($rv['classes'])) {
                        $objs['classes'] = $objs['classes'] + $rv['classes'];
                    }
                }
                $newclstrk->classid = $class->id;
                $newclstrk->courseid = $class->courseid;
                $newclstrk->save();
            }
        }
        return $objs;
    }
}

/** ------ trackassignment class ------ **/
class trackassignment extends data_object_with_custom_fields {
    const TABLE = 'crlm_track_class';


    /**
     * User ID-number
     * @var    char
     * @length 255
     */
    protected $_dbfield_id;
    protected $_dbfield_trackid;
    protected $_dbfield_classid;
    protected $_dbfield_courseid;
    protected $_dbfield_autoenrol;

    static $associations = array(
        'track' => array(
            'class' => 'track',
            'idfield' => 'trackid'
        ),
        'pmclass' => array(
            'class' => 'pmclass',
            'idfield' => 'courseid'
        ),
        'course' => array(
            'class' => 'course',
            'foreignkey' => 'courseid'
        ),
    );
    /*function trackassignment($trackassign = false) {
    //    parent::datarecord();
    //    $this->set_table(CLASSTABLE);
        if (is_numeric($trackassign)) {
            $this->data_load_record($trackassign);
        } else if (is_array($trackassign)) {
            $this->data_load_array($trackassign);
        } else if (is_object($trackassign)) {
            $this->data_load_array(get_object_vars($trackassign));
        }
    }*/

    public static function delete_for_class($id) {

        return $this->_db->delete_records(trackassignment::TABLE, array('classid'=> $id));
    }

    public static function delete_for_track($id) {

        //TODO: Remove users from crlm_user_track table that use -
        // this track

        return $this->_db->delete_records(trackassignment::TABLE, array('trackid'=> $id));
    }

    function count_assigned_classes_from_track() {

        return $this->_db->count_records(trackassignment::TABLE, array('trackid'=> $this->trackid));
    }

    /**
     * Retrieve records of tracks that have been assigned to
     * the class id
     *
     * @return mixed Returns an array key - track id, value - id of record
     * in crlm_track_class table
     */
    function get_assigned_tracks() {
        global $DB;

        $assigned   = array();

        $result = $DB->get_records(trackassignment::TABLE, array('classid'=> $this->classid));

        if ($result) {
            foreach ($result as $data) {
                $assigned[$data->trackid] = $data->id;
            }
        }

        return $assigned;
    }

    /**
     * Returns true if class is assigned to a track
     *
     * @return mixed true if record exits, otherwise fale
     */
    function is_class_assigned_to_track() {

        // check if assignment already exists
        return $DB->record_exists(trackassignment::TABLE, array('classid'=> $this->classid,
                                                                'trackid'=> $this->trackid));
    }

    /**
     * Assign a class to a track, this function also creates
     * and assigns the class to the curriculum default track
     *
     * @return TODO: add meaningful return value
     */
    function save() { //add()
        global $DB;

        if (empty($this->courseid)) {
            $this->courseid = $DB->get_field(pmclass::TABLE, 'courseid', array('id'=> $this->classid));
        }
        // TODO: Comment out until we know what to do with $CURMAN->config
        // Also, how to convert to save :)
        /*
        if ((empty($this->trackid) or
             empty($this->classid) or
             empty($this->courseid)) and
            empty($CURMAN->config->userdefinedtrack)) {
            cm_error('trackid and classid have not been properly initialized');
            return false;
        } elseif ((empty($this->courseid) or
                   empty($this->classid)) and
                  $CURMAN->config->userdefinedtrack) {
            cm_error('courseid has not been properly initialized');
        }

        if (empty($CURMAN->config->userdefinedtrack)) {
            if ($this->is_class_assigned_to_track()) {
                return false;
            }

            // Determine whether class is required
            $curcrsobj = new curriculumcourse(
                array('curriculumid'    => $this->track->curid,
                      'courseid'        => $this->classid));

            // insert assignment record
            $parent::save(); //updated for ELIS2 from $this->data_insert_record()

            if ($this->autoenrol && $this->is_autoenrollable()) {
                // autoenrol all users in the track
                $users = usertrack::get_users($this->trackid);
                foreach ($users as $user) {
                    $stu_record = new object();
                    $stu_record->userid = $user->userid;
                    $stu_record->user_idnumber = $user->idnumber;
                    $stu_record->classid = $this->classid;
                    $stu_record->enrolmenttime = time();

                    $enrolment = new student($stu_record);
                    // check prerequisites and enrolment limits
                    $enrolment->save(array('prereq' => 1, 'waitlist' => 1));
                }
            }
        } else {
            // Look for all the curricula course is linked to -
            // then pull up the default system track for each curricula -
            // and add class to each default system track
            $currculums = curriculumcourse_get_list_by_course($this->courseid);

            $currculums = is_array($currculums) ? $currculums : array();

            foreach ($currculums as $recid => $record) {
                // Create default track for curriculum
                $trkojb = new track(array('curid'=>$record->curriculumid));
                $trkid = $trkojb->create_default_track();

                // Create track assignment object
                $trkassign = new trackassignment(array('trackid' => $trkid,
                                                            'classid' => $this->classid,
                                                            'courseid'=> $this->courseid));

                // Check if class is already assigned to default track
                if (!$trkassign->is_class_assigned_to_default_track()) {
                    // Determine whether class is required
                    $curcrsobj = new curriculumcourse(
                        array('curriculumid'    => $trkassign->track->curid,
                              'courseid'        => $trkassign->courseid));

                    // Get required field and determine if class is autoenrol eligible
                    $trkassign->autoenrol =
                        (1 == $trkassign->cmclass->count_course_assignments($trkassign->cmclass->courseid) and
                         true === $curcrsobj->is_course_required()) ? 1 : 0;

                    // assign class to the curriculum's default track
                    $trkassign->assign_class_to_default_track();
                }
            }
        }
*/
        events_trigger('crlm_track_class_associated', $this);

        return true;
    }

    /**
     * Determines whether a class can be autoenrolled.   To be autoenrollable,
     * the class:
     * - must have the autoenrol flag set
     * - must be the only class in the track for its course
     */
    function is_autoenrollable() {
        global $DB;

        if (!$this->autoenrol) {
            return false;
        }
        if (empty($this->courseid)) {
            $this->courseid = $DB->get_field(pmclass::TABLE, 'courseid', array('id'=> $this->classid));
        }
        $select = "trackid = ? AND courseid = ? AND classid != ?";
        $params = array($this->trackid, $this->courseid, $this->classid);
        return !$DB->record_exists_select(trackassignment::TABLE, $select, $params);
    }

    //TODO: document function and return something meaningful
    function assign_class_to_default_track() {
        $trackid = $this->track->create_default_track();
        if (false !== $trackid) {
            $this->trackid = $trackid;
            $this->save();
        }
    }

    /**
     * Returns whether a class has already been assigned to the curriculum -
     * default track
     *
     * @return boolean true if record exists, otherwise false
     */
    function is_class_assigned_to_default_track() {
        global $DB;

        // Get the curriculum id
        // check if default track exists
        $exists = $DB->record_exists(TABLE, 'curid', $this->track->curid,
                                             array('defaulttrack'=> 1));

        if (!$exists) {
            return false;
        }

        // Retrieve track id
        $trackid = $DB->get_field(TABLE, 'id', array('curid'=> $this->track->curid,
                                                            'defaulttrack'=> 1));
        if (false === $trackid) {
            cm_error('Error #1001: selecting field from crlm_track table');
        }

        // Check if class is assigned to default track
        return $DB->record_exists(trackassignment::TABLE, array('classid'=> $this->classid,
                                                           'trackid'=> $trackid));
    }

    function enrol_all_track_users_in_class() {
        global $DB;

        // find all users who are not enrolled in the class
        // TODO: validate this...
        $sql = "NOT EXISTS (SELECT 'x'
                                FROM {".student::TABLE. "} s
                                WHERE s.classid = ? AND s.userid = ?)
                  AND trackid = ?";
        $params = array($this->classid, '{' .usertrack::TABLE .'}'.userid, $this->trackid);
        $users = $DB->get_records_select(usertrack::TABLE, $sql, $params, 'userid');

        if ($users) {
            $timenow = time();
            $count = 0;
            $waitlisted = 0;
            $prereq = 0;
            foreach ($users as $user) {
                // enrol user in track
                $enrol = new student();
                $enrol->classid = $this->classid;
                $enrol->userid = $user->userid;
                $enrol->enrolmenttime = $timenow;
                $result = $enrol->save(array('prereq' => 1, 'waitlist' => 1));
                if ($result === true) {
                    $count++;
                } elseif (is_object($result)) {
                    if ($result->code = 'user_waitlisted') {
                        $waitlisted++;
                    } elseif ($result->code = 'user_waitlisted') {
                        $prereq++;
                    }
                }
            }

            print_string('n_users_enrolled', 'block_curr_admin', $count);
            if ($waitlisted) {
                print_string('n_users_waitlisted', 'block_curr_admin', $waitlisted);
            }
            if ($prereq) {
                print_string('n_users_unsatisfied_prereq', 'block_curr_admin', $prereq);
            }
        } else {
            print_string('all_users_already_enrolled', 'block_curr_admin');
        }
    }
}
/// Non-class supporting functions. (These may be able to replaced by a generic container/listing class)


/**
 * Gets a track listing with specific sort and other filters.
 *
 * @param   string          $sort          Field to sort on
 * @param   string          $dir           Direction of sort
 * @param   int             $startrec      Record number to start at
 * @param   int             $perpage       Number of records per page
 * @param   string          $namesearch    Search string for curriculum name
 * @param   string          $alpha         Start initial of curriculum name filter
 * @param   int             $curriculumid  Necessary associated curriculum
 * @param   int             $clusterid     Necessary associated cluster
 * @param   cm_context_set  $contexts      Contexts to provide permissions filtering, of null if none
 * @param   int             $userid        The id of the user we are assigning to tracks
 *
 * @return  object array                   Returned records
 */
function track_get_listing($sort='name', $dir='ASC', $startrec=0, $perpage=0, $namesearch='',
                           $alpha='', $curriculumid = 0, $parentclusterid = 0, $contexts = null, $userid = 0) {
    global $USER, $DB;

    //$LIKE = $this->_db->sql_compare();
    $params = array();
    $NAMESEARCH_LIKE = $DB->sql_like('trk.name', ':search_namesearch');
    $ALPHA_LIKE = $DB->sql_like('trk.name', ':search_alpha');

    $select = 'SELECT trk.*, cur.name AS parcur, (SELECT COUNT(*) ' .
              'FROM {' . CLASSTABLE . '} '.
              'WHERE trackid = :trkid ) as class ';
    $params['trkid'] = 'trk.id';
    $tables = 'FROM {' . TABLE . '} trk '.
              'JOIN {' .curriculum::TABLE . '} cur ON trk.curid = cur.id ';
    $join   = '';
    $on     = '';

    $where = array('trk.defaulttrack = 0');

    if (!empty($namesearch)) {
        $namesearch = trim($namesearch);
        //$where[] = "(trk.name $LIKE  '%$namesearch%')";
        $where[] = $NAMESEARCH_LIKE;
        $params['search_namesearch'] = "%{$namesearch}%";
    }

    if ($alpha) {
        //$where[] = "(trk.name $LIKE '$alpha%')";
        $where[] = $ALPHA_LIKE;
        $params['search_lastname'] = "{$alpha}%";
    }

    if ($curriculumid) {
        $where[] = "(trk.curid = :curid)";
        $params['curid'] = $curriculumid;
    }

    if ($parentclusterid) {
        $where[] = "(trk.id IN (SELECT trackid FROM {".CLASSTABLE."}
                            WHERE clusterid = :parentclusterid))";
        $params['parentclusterid'] = $parentclusterid;
    }

    if ($contexts !== null) {
        //$where[] = $contexts->sql_filter_for_context_level('trk.id', 'track');
        $filter_object = $contexts->filter_for_context_level('trk.id', 'track');
        $where[] = $filter_object->get_sql();
    }

    if(!empty($userid)) {
        //get the context for the "indirect" capability
        $context = cm_context_set::for_user_with_capability('cluster', 'block/curr_admin:track:enrol_cluster_user', $USER->id);

        $allowed_clusters = array();

        $clusters = cluster_get_user_clusters($userid);
        $allowed_clusters = $context->get_allowed_instances($clusters, 'cluster', 'clusterid');

        $curriculum_context = cm_context_set::for_user_with_capability('cluster', 'block/curr_admin:track:enrol', $USER->id);
        $curriculum_filter_object = $curriculum_context->sql_filter_for_context_level('trk.id', 'track');
        $curriculum_filter = $curriculum_filter_object->get_sql();

        if(empty($allowed_clusters)) {
            $where[] = $curriculum_filter;
        } else {
            //this allows both the indirect capability and the direct track filter to work

            $allowed_clusters_list = implode(',', $allowed_clusters);
            $where[] = "(
                          trk.id IN (
                            SELECT clsttrk.trackid
                            FROM {". class_track::TABLE. "} clsttrk
                            WHERE clsttrk.clusterid IN (:allowed_clusters)
                          )
                          OR
                          {$curriculum_filter}
                        )";
           $params['allowed_clusters'] = $allowed_clusters_list;
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

    if (!empty($perpage)) {
        if ($this->_db->get_dbfamily() == 'postgres') {
            $limit = 'LIMIT ' . $perpage . ' OFFSET ' . $startrec;
        } else {
            $limit = 'LIMIT '.$startrec.', '.$perpage;
        }
    } else {
        $limit = '';
    }

    $sql = $select.$tables.$join.$on.$where.$sort.$limit;

    return $DB->get_records_sql($sql, $params);
}

/**
 * Calculates the number of records in a listing as created by track_get_listing
 *
 * @param   string          $namesearch    Search string for curriculum name
 * @param   string          $alpha         Start initial of curriculum name filter
 * @param   int             $curriculumid  Necessary associated curriculum
 * @param   int             $clusterid     Necessary associated cluster
 * @param   cm_context_set  $contexts      Contexts to provide permissions filtering, of null if none
 * @return  int                            The number of records
 */
function track_count_records($namesearch = '', $alpha = '', $curriculumid = 0, $parentclusterid = 0, $contexts = null) {
    global $DB;

    //$LIKE = $this->_db->sql_compare();
    $params = array();
    $NAMESEARCH_LIKE = $DB->sql_like('name', ':search_namesearch');
    $ALPHA_LIKE = $DB->sql_like('name', ':search_alpha');

    $where = array('defaulttrack = 0');

    if (!empty($namesearch)) {
        //$where[] = "name $LIKE '%$namesearch%'";
        $where[] = $NAMESEARCH_LIKE;
        $params['search_namesearch'] = '%$namesearch%';
    }

    if ($alpha) {
        //$where[] = "(name $LIKE '$alpha%')";
        $where[] = $ALPHA_LIKE;
        $params['search_alpha'] = '$alpha%';
    }

    if ($curriculumid) {
        //$where[] = "(curid = $curriculumid)";
        $where[] = "(curid = :curriculumid)";
        $params['curriculumid'] = $curriculumid;
    }

    if ($parentclusterid) {
        $where[] = "(id IN (SELECT trackid FROM {".class_track::TABLE."}
                            WHERE clusterid = :parentclusterid))";
        $params['parentclusterid'] = $parentclusterid;
    }

    if ($contexts !== null) {
        $filter_object = $contexts->sql_filter_for_context_level('id', 'track');
        $where[] = $filter_object->get_sql();
    }

    $where = implode(' AND ', $where);

    return $DB->count_records_select(TABLE, $where, $params);
}

/**
 * Retrieve a list of tracks based on a curriculum id
 * excluding default tracks
 *
 * @param int curid curriculum id
 *
 * @return mixed array of crlm_track objects or false if
 * nothing was found
 */
function track_get_list_from_curr($curid) {
    global $DB;

    $tracks = $DB->get_records(TABLE, array('curid'=> $curid));

    if (is_array($tracks)) {
        foreach ($tracks as $key => $track) {
            if (1  == $track->defaulttrack) {
                unset($tracks[$key]);
            }
        }
    }
    return $tracks;
}

/**
 * Gets a track assignment listing with specific sort and other filters.
 *
 * @param int $trackid track id
 * @param string $sort Field to sort on.
 * @param string $dir Direction of sort.
 * @param int $startrec Record number to start at.
 * @param int $perpage Number of records per page.
 * @param string $namesearch Search string for curriculum name.
 * @param string $alpha Start initial of curriculum name filter.
 * @return object array Returned records.
 */
function track_assignment_get_listing($trackid = 0, $sort='cls.idnumber', $dir='ASC', $startrec=0, $perpage=0, $namesearch='',
                                      $alpha='') {
    global $DB;

    //$LIKE = $this->_db->sql_compare();
    $params = array();
    $NAMESEARCH_LIKE = $DB->sql_like('cls.idnumber', ':search_namesearch');
    $ALPHA_LIKE = $DB->sql_like('cls.idnumber', ':search_alpha');

    $select = 'SELECT trkassign.*, cls.idnumber as clsname, cls.id as clsid, enr.enrolments, curcrs.required ';
    $tables = ' FROM {' . trackassignment::TABLE . '} trkassign ';
    $join   = " JOIN {" . track::TABLE ."} trk ON trkassign.trackid = trk.id
                JOIN {". pmclass::TABLE ."} cls ON trkassign.classid = cls.id
                JOIN {". curriculumcourse::TABLE . "} curcrs ON curcrs.curriculumid = trk.curid AND curcrs.courseid = cls.courseid ";
    // get number of users from track who are enrolled
    $join  .= "LEFT JOIN (SELECT s.classid, COUNT(s.userid) AS enrolments
                            FROM {". student::TABLE ."} s
                            JOIN {". usertrack::TABLE ."} t USING(userid)
                           WHERE t.trackid = :trackid
                        GROUP BY s.classid) enr ON enr.classid = cls.id ";
    $params['trackid'] = $trackid;

    //apply the track filter to the outermost query if applicable
    if ($trackid == 0) {
        $where = ' TRUE';
    } else {
        $where = ' trkassign.trackid = :assign_trackid';
        $params['assign_trackid'] = $trackid;
    }

    if (!empty($namesearch)) {
        $namesearch = trim($namesearch);
        $where .= (!empty($where) ? ' AND ' : '') . $NAMESEARCH_LIKE;
        $params['serch_namesearch'] = '%$namesearch%';
    }

    if ($alpha) {
        $where .= (!empty($where) ? ' AND ' : '') . $ALPHA_LIKE;
        $params['search_alpha'] = '$alpha%';
    }

    if (!empty($where)) {
        $where = 'WHERE '.$where.' ';
    }

    if ($sort) {
        $sort = 'ORDER BY '.$sort .' '. $dir.' ';
    }

    if (!empty($perpage)) {
        if ($this->_db->get_dbfamily() == 'postgres') {
            $limit = 'LIMIT ' . $perpage . ' OFFSET ' . $startrec;
        } else {
            $limit = 'LIMIT '.$startrec.', '.$perpage;
        }
    } else {
        $limit = '';
    }

    $sql = $select.$tables.$join.$where.$sort.$limit;

    return $DB->get_records_sql($sql, $params);
}

/**
 * Gets the number of items in the track assignment listing
 *
 * @param   int     $trackid     The id of the track to obtain the listing for
 * @param   string  $namesearch  Search string for curriculum name
 * @param   string  $alpha       Start initial of curriculum name filter
 * @return  int                  The number of appropriate records
 */
function track_assignment_count_records($trackid, $namesearch = '', $alpha = '') {
    global $DB;

    //$LIKE = $this->_db->sql_compare();
    $params = array();
    $NAMESEARCH_LIKE = $DB->sql_like('cls.idnumber', ':search_namesearch');
    $ALPHA_LIKE = $DB->sql_like('cls.idnumber', ':search_alpha');

    $select = 'SELECT COUNT(*) ';
    $tables = ' FROM {' . trackassignment::TABLE . '} trkassign ';
    $join   = ' JOIN {' . track::TABLE. '} trk ON trkassign.trackid = trk.id
                JOIN {' . pmclass::CLSTABLE. '} cls ON trkassign.classid = cls.id
                JOIN {' . curriculumcourse::TABLE. '} curcrs ON curcrs.curriculumid = trk.curid AND curcrs.courseid = cls.courseid ';
    // get number of users from track who are enrolled
    $join  .= 'LEFT JOIN (SELECT s.classid, COUNT(s.userid) AS enrolments
                            FROM {' . student::TABLE. '} s
                            JOIN {' . usertrack::TABLE. '} t USING(userid)
                           WHERE t.trackid = :trackid
                        GROUP BY s.classid) enr ON enr.classid = cls.id ';
    $params['trackid'] = $rackid;

    $where = ' trkassign.trackid = :assign_trackid';
    $params['assign_trackid'] = $trackid;

    if (!empty($namesearch)) {
        $namesearch = trim($namesearch);
        $where .= (!empty($where) ? ' AND ' : '') . $NAMESEARCH_LIKE;
        $params['search_namesearch'] = '%$namesearch%';
    }

    if ($alpha) {
        $where .= (!empty($where) ? ' AND ' : '') . $ALPHA_LIKE;
        $params['search_alpha'] = '$alpha%';
    }

    if (!empty($where)) {
        $where = 'WHERE '.$where.' ';
    }

    $sql = $select.$tables.$join.$where;

    return $DB->count_records_sql($sql, $params);
}
