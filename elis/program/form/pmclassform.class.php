<?php
/**
 * Form used for editing / displaying a class record.
 *
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

require_once elispm::file('form/cmform.class.php');

MoodleQuickForm::registerElementType('time_selector', "{$CFG->dirroot}/curriculum/form/timeselector.php", 'cm_time_selector');

class pmclassform extends cmform {
    function definition() {
        global $USER, $CFG;

        parent::definition();

        if(!empty($this->_customdata['obj'])) {
            $obj = $this->_customdata['obj'];
            if(empty($obj->startdate) || $obj->startdate == 0) {
                $this->set_data(array('disablestart' => '1'));
            }
            if(empty($obj->enddate) || $obj->enddate == 0) {
                $this->set_data(array('disableend' => '1'));
            }

            if (isset($obj->starttimeminute) && isset($obj->starttimehour)) {
                $this->set_data(array('starttime' => array('minute' => $obj->starttimeminute,
                                                           'hour' => $obj->starttimehour)));
            }
            if (isset($obj->endtimeminute) && isset($obj->endtimehour)) {
                $this->set_data(array('endtime' => array('minute' => $obj->endtimeminute,
                                                         'hour' => $obj->endtimehour)));
            }
        }

        $mform =& $this->_form;

        $mform->addElement('hidden', 'id');

        // If there is no custom data for the course, create some
        if (empty($this->_customdata['obj']->course->name) ||
            empty($this->_customdata['obj']->id)) {
            $courses = array();
            if (!empty($USER->id)) {
                $contexts = get_contexts_by_capability_for_user(
                                'course', 'block/curr_admin:class:create',
                                $USER->id);
                // get listing of available ELIS courses
                $courses = course_get_listing('name', 'ASC', 0, 0, '', '',
                                              $contexts);
            }

            // Add course select
            $attributes = array('onchange'=>'update_trk_multiselect(); ');

            $selections = array();
            if (!empty($courses)) {
                foreach($courses as $course) {
                    $selections[$course->id] = '(' . $course->idnumber . ')'
                                               . $course->name;
                }
            }

            $mform->addElement('select', 'courseid', get_string('course', 'elis_program') . ':', $selections, $attributes);
            $mform->addHelpButton('courseid', 'pmclassform:course', 'elis_program');

            $firstcourse = reset($courses);
            $this->firstcourse = $firstcourse;

            if(false !== $firstcourse && empty($this->_customdata['obj']->id)) {
                $this->add_track_multi_select($firstcourse->id);
            } elseif(!empty($courses)) {
                $this->add_track_multi_select($this->_customdata['obj']->courseid);
            }
        } else {
            $extra_params = array();
            $mform->addElement('static', 'courseid', get_string('course', 'elis_program') . ':');

            // Get current action and set param accordingly
            $current_action    = optional_param('action','view',PARAM_ALPHA);
            $extra_params['action']    = $current_action;
            $extra_params['s']         = 'crs'; // Want to set the url for the course
            $extra_params['id']        = $this->_customdata['obj']->courseid; // Course id
            $course_url = $this->get_moodle_url($extra_params);

            $course_name = '(' . $this->_customdata['obj']->course->idnumber . ')'.'<a href="'.$course_url.'" >'.$this->_customdata['obj']->course->name.'</a>';
            $this->set_data(array('courseid' => $course_name));
            $mform->addHelpButton('courseid', 'pmclassform:course', 'elis_program');

            $this->add_track_multi_select($this->_customdata['obj']->courseid);
        }

        if(!empty($this->_customdata['obj']->courseid)) {
            $mform->freeze('courseid');
        } else {
            $mform->addRule('courseid', get_string('required'), 'required', NULL, 'client');
        }
        // Done adding course select

        $mform->addElement('text', 'idnumber', get_string('class_idnumber', 'elis_program') . ':');
        $mform->addRule('idnumber', get_string('required'), 'required', NULL, 'client');
        $mform->setType('idnumber', PARAM_TEXT);
        $mform->addHelpButton('idnumber', 'pmclassform:class_idnumber', 'elis_program');

        $mform->addElement('date_selector', 'startdate', get_string('class_startdate', 'elis_program') . ':', array('optional'=>true, 'disabled'=>'disabled'));
        $mform->addHelpButton('startdate', 'pmclassform:class_startdate', 'elis_program');

        $mform->addElement('date_selector', 'enddate', get_string('class_enddate', 'elis_program') . ':', array('optional'=>true));

        // They may very likely be a much better way of checking for this...
        /*
        if (empty($obj->starttimehour) and empty($obj->starttimeminute)) {
            $mform->addElement('time_selector', 'starttime', get_string('class_starttime', 'elis_program') . ':',
                               array('optional'=>true, 'checked'=>'checked', 'display_12h'=>$CURMAN->config->time_format_12h));
        } else {
            $mform->addElement('time_selector', 'starttime', get_string('class_starttime', 'elis_program') . ':',
                               array('optional'=>true, 'checked'=>'unchecked', 'display_12h'=>$CURMAN->config->time_format_12h));
        }
        $mform->addHelpButton('starttime', 'pmclassform:class_starttime', 'elis_program');
        */

        // Do the same thing for the endtime
        /*
        if (empty($obj->endtimehour) and empty($obj->endtimeminute)) {
            $mform->addElement('time_selector', 'endtime', get_string('class_endtime', 'elis_program') . ':',
                               array('optional'=>true, 'checked'=>'checked', 'display_12h'=>$CURMAN->config->time_format_12h));
        } else {
            $mform->addElement('time_selector', 'endtime', get_string('class_endtime', 'elis_program') . ':',
                               array('optional'=>true, 'checked'=>'unchecked', 'display_12h'=>$CURMAN->config->time_format_12h));
        }
        */

        $mform->addElement('text', 'maxstudents', get_string('class_maxstudents', 'elis_program') . ':');
        $mform->setType('maxstudents', PARAM_INT);
        $mform->addHelpButton('maxstudents', 'pmclassform:class_maxstudents', 'elis_program');

        // Environment selector
        //$envs = environment_get_listing();
        //$envs = $envs ? $envs : array();

        //$o_envs = array(get_string('none', 'elis_program'));

        //foreach ($envs as $env) {
        //    $o_envs[$env->id] = $env->name;
        //}

        //$mform->addElement('select', 'environmentid', get_string('environment', 'elis_program') . ':',
        //                   $o_envs);
        //$mform->addHelpButton('environmentid', 'pmclassform:environment', 'elis_program');

        // Course selector
        if (empty($this->_customdata['obj']->moodlecourseid)) {
            $this->add_moodle_course_select();
        } else {
            $coursename = $this->_db->get_field('course', 'fullname', 'id', $this->_customdata['obj']->moodlecourseid);
            $mform->addElement('static', 'class_attached_course', get_string('class_attached_course', 'elis_program') . ':',  "<a href=\"$CFG->wwwroot/course/view.php?id={$this->_customdata['obj']->moodlecourseid}\">$coursename</a>");
            $mform->addHelpButton('class_attached_course', 'pmclassform:moodlecourse', 'elis_program');
            $mform->addElement('hidden', 'moodlecourseid');
        }

        $mform->addElement('checkbox', 'enrol_from_waitlist', get_string('waitlistenrol', 'elis_program') . ':');
        $mform->addHelpButton('enrol_from_waitlist', 'pmclassform:waitlistenrol', 'elis_program');

        // custom fields
        $fields = field::get_for_context_level('class');
        $fields = $fields ? $fields : array();

        $lastcat = null;
        $context = isset($this->_customdata['obj']) && isset($this->_customdata['obj']->id)
            ? get_context_instance(context_level_base::get_custom_context_level('class', 'elis_program'), $this->_customdata['obj']->id)
            : get_context_instance(CONTEXT_SYSTEM);
        /*
        require_once elispm::file('plugins/manual/custom_fields.php');
        foreach ($fields as $rec) {
            $field = new field($rec);
            if (!isset($field->owners['manual'])) {
                continue;
            }
            if ($lastcat != $rec->categoryid) {
                $lastcat = $rec->categoryid;
                $mform->addElement('header', "category_{$lastcat}", htmlspecialchars($rec->categoryname));
            }
            manual_field_add_form_element($this, $context, $field);
        }
        */

        $this->add_action_buttons();
    }

    /**
     * Load the options into the track selection boxes based on the
     * selected course.
     */
    function definition_after_data() {
        $mform =& $this->_form;
        $courseid = $mform->getElementValue('courseid');
        if ($courseid) {
            $courseid = (int)array_shift($courseid);
        } else {
            if(!empty($this->_customdata['obj']->courseid)) {
                $courseid = $this->_customdata['obj']->courseid;
            } else if (!empty($this->firstcourse)) {
                $courseid = $this->firstcourse->id;
            }
        }
        if ($courseid) {
            $unassigned = array();
            $assigned = array();

            // Multi select box for choosing tracks
            $tracks = track_get_listing();
            $tracks = empty($tracks) ? array() : $tracks;

            $curcrsassign = curriculumcourse_get_list_by_course($courseid);
            $curcrsassign = empty($curcrsassign) ? array() : $curcrsassign;

            foreach ($curcrsassign as $recid => $curcrsrec) {
                foreach ($tracks as $trackid => $trackrec) {
                    if ($trackrec->curid == $curcrsrec->curriculumid) {
                        if (!empty($this->_customdata['obj']->id)) {
                            $trkobj = new trackassignmentclass(array('classid' => $this->_customdata['obj']->id,
                                                                     'trackid' => $trackid));
                            if (!$trkobj->is_class_assigned_to_track()) {
                                $unassigned[$trackid] = $trackrec->name;
                            } else {
                                // Create list for currently assigned tracks
                                $assigned[$trackid] = $trackrec->name;
                            }
                        } else {
                            $unassigned[$trackid] = $trackrec->name;
                        }
                    }

                }
            }

            $temp = array('assignedtrack'=>array_keys($assigned));
            $this->set_data($temp);

            $track_el =& $mform->getElement('assignedtrack');
            $track_el->load($assigned);
            $track_el =& $mform->getElement('track');
            $track_el->load($unassigned);
        }
    }


    /**
     * Adds the multi-select box for tracks.  This form element
     * contents will change depending on which course was selected
     *
     * @param int $courseid the course id of the first item in the
     * course selection element
     *
     * @return string HTML for a multi-select element
     */
    function add_track_multi_select($courseid) {
        global $CFG;

        //require_js('yui_yahoo');
        //require_js('yui_event');
        //require_js('yui_connection');
        //require_js($CFG->wwwroot.'/curriculum/js/trkmultiselect.js');

        $PAGE->requires->js('yui_yahoo');
        $PAGE->requires->js('yui_event');
        $PAGE->requires->js('yui_connection');
        $PAGE->requires->js('/elis/program/js/trkmultiselect.js');

        $mform =& $this->_form;

        $assignedSelection = &$mform->addElement('select', 'assignedtrack', get_string('class_assigntrackhead', 'elis_program') . ':', array());
        $mform->freeze('assignedtrack');
        $assignedSelection->setMultiple(true);

        $unassignedSelections = &$mform->addElement('select', 'track', get_string('class_unassigntrackhead', 'elis_program') . ':', array());
        $unassignedSelections->setMultiple(true);
        $mform->addHelpButton('track', 'pmclassform:class_unassigntrackhead', 'elis_program');
    }

    /**
     * adds a moodle course selection box to the form
     *
     * @uses $CFG
     * @param $formid string A suffix to put on all 'id' and index for all 'name' attributes.
     *                       This should be unique if being used more than once in a form.
     * @param $extraclass string Any extra class information to add to the output.
     *
     * @return string The form HTML, without the form.
     */
    function add_moodle_course_select() {
        global $CFG, $DB;

        $mform =& $this->_form;

        //$categoryid = $this->_db->get_field('course_categories', 'id', 'parent', '0');
        //$sitename   = $this->_db->get_field('course', 'shortname', 'id', SITEID);

        $select = 'id != \'' . SITEID . '\' AND fullname NOT LIKE \'.%\'';

        $cselect = array(get_string('none', 'elis_program'));

        //$crss = $this->_db->get_records_select('course', $select, 'fullname');
        $params = array();
        $crss = $DB->get_records_select('course', $select, $params, 'fullname');
        if(!empty($crss)) {
            foreach ($crss as $crs) {
                $cselect[$crs->id] = $crs->fullname;
            }
        }

        $moodleCourses = array();
        if (count($cselect) != 1) {
            $moodleCourses[] = $mform->createElement('select', 'moodlecourseid', get_string('moodlecourse', 'elis_program'), $cselect);
        } else {
            $mform->addElement('static', 'no_moodle_courses', get_string('moodlecourse', 'elis_program') . ':', get_string('no_moodlecourse', 'elis_program'));
            $mform->addHelpButton('no_moodle_courses', 'pmclassform:moodlecourse', 'elis_program');
        }

        // Add auto create checkbox if CM course uses a template
        if(empty($this->_customdata['obj']->courseid)) {
            $courseid = 0;
        } else {
            $courseid = $this->_customdata['obj']->courseid;
        }

        $template = new coursetemplate();

        if (empty($courseid) || false !== $template->data_load_record($courseid) && !empty($template->location)) {
            $moodleCourses[] = $mform->createElement('checkbox', 'autocreate', '', get_string('autocreate', 'elis_program'));
        }

        if(count($cselect) != 1) {
            $mform->addGroup($moodleCourses, 'moodleCourses', get_string('moodlecourse', 'elis_program') . ':');
            $mform->disabledIf('moodleCourses', 'moodleCourses[autocreate]', 'checked');
            $mform->addHelpButton('moodleCourses', 'pmclassform:moodlecourse', 'elis_program');
        }
    }

    /**
     *  make sure the start time is before the end time and the start date is before the end date for the class
     * @param array $data
     * @param mixed $files
     * @return array
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($this->_db->record_exists_select(CLSTABLE, "idnumber = '{$data['idnumber']}'".($data['id'] ? " AND id != {$data['id']}" : ''))) {
            $errors['idnumber'] = get_string('idnumber_already_used', 'elis_program');
        }

        if($data['starttime'] > $data['endtime']) {
            $errors['starttime'] = get_string('error_duration', 'elis_program');
        }

        if(!empty($data['startdate']) && !empty($data['enddate']) && !empty($data['disablestart']) && !empty($data['disableend'])) {
            if($data['startdate'] > $data['enddate']) {
                $errors['start'] = get_string('error_date_range', 'elis_program');
            }
        }

        if(!empty($this->_customdata['obj']) && !empty($this->_customdata['obj']->maxstudents)){
            if($data['maxstudents'] < $this->_customdata['obj']->maxstudents && $data['maxstudents'] < student::count_enroled($this->_customdata['obj']->id)) {
                $context = get_context_instance(CONTEXT_SYSTEM);
                if(!has_capability('block/curr_admin:overrideclasslimit', $context)) {
                    $errors['maxstudents'] = get_string('error_n_overenrol', 'elis_program');
                }
            }
        }

        return $errors;
    }

    /**
      * Create a url to the current page.
     *
     * @param	array	extra	array of extra parameters
     * @return moodle_url
     */
    function get_moodle_url($extra = array()) {
        global $CFG;
        $params    = array();

        $url = new moodle_url($CFG->wwwroot . '/curriculum/index.php', $params);

        foreach($extra as $name=>$value) {
            $url->param($name, $value);
        }

        return $url->out();
    }

    function get_data(){
        $data = parent::get_data();

        if (!empty($data)) {
            $mform =& $this->_form;

            if(!empty($mform->_submitValues['disablestart'])) {
                $data->startdate = 0;
            }

            if(!empty($mform->_submitValues['disableend'])) {
                $data->enddate = 0;
            }

            $timezoneoffset = get_user_timezone_offset();
            if ($timezoneoffset > 13) {
                $timezoneoffset = (float)date('Z')/3600;
            }
            if(!empty($data->starttime) and
               !isset($mform->_submitValues['starttime']['timeenable'])) {
                $data->starttimehour = (int)($data->starttime / HOURSECS) + $timezoneoffset;
                $data->starttimeminute = ($data->starttime % HOURSECS) / MINSECS;
            } else {
                $data->starttimehour = $data->starttimeminute = 0;
            }

            if(!empty($data->endtime) and
               !isset($mform->_submitValues['endtime']['timeenable'])) {
                $data->endtimehour = (int)($data->endtime / HOURSECS) + $timezoneoffset;
                $data->endtimeminute = ($data->endtime % HOURSECS) / MINSECS;
            } else {
                $data->endtimehour = $data->endtimeminute = 0;
            }
        }

        return $data;
    }

    function freeze() {
        // Add completion status information
        $counts = $this->_customdata['obj']->get_completion_counts();

        $counttext = "Passed: {$counts[2]}, Failed: {$counts[1]}, In Progress: {$counts[0]}";

        $this->_form->addElement('static', 'test', get_string('completion_status', 'elis_program'), $counttext);

        parent::freeze();
    }

}

