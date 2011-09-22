<?php
/**
 *  ELIS(TM): Enterprise Learning Intelligence Suite
 *
 *  Copyright (C) 2008-2011 Remote-Learner.net Inc (http://www.remote-learner.net)
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package    elis
 *  @subpackage programmanagement
 *  @author     Remote-Learner.net Inc
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 *  @copyright  (C) 2008-2011 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

require_once elispm::lib('lib.php');
require_once elispm::file('form/cmform.class.php');
require_once elispm::file('plugins/results_engine/lib.php');

/**
 * the form element for curriculum
 */
class cmEngineForm extends cmform {
    const LANG_FILE = 'pmplugins_results_engine';

    // Form html
    protected $_html = array();

    // Layout switching
    protected $_layout = 'default';

    /**
     * defines items in the form
     */
    public function definition() {
        $configData = array('title');

        $this->defineActivation();
        $this->defineResults();

        $submitlabel = get_string('savechanges');
        $mform =& $this->_form;
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }

    /**
     * Define the activation section of the form.
     *
     * @uses $DB
     */
    protected function defineActivation() {
        global $DB;

        $grades = array(0 => get_string('class_grade', self::LANG_FILE));
        $dates  = array(
            RESULTS_ENGINE_AFTER_START => get_string('after_class_start', self::LANG_FILE),
            RESULTS_ENGINE_BEFORE_END  => get_string('before_class_end', self::LANG_FILE),
            RESULTS_ENGINE_AFTER_END   => get_string('after_class_end', self::LANG_FILE)
        );

        $conditions = array('courseid' => $this->_customdata['courseid']);

        $completions = $DB->get_records(coursecompletion::TABLE, $conditions);

        foreach ($completions as $completion) {
            $grades[$completion->id] = $completion->name;
        }

        $activaterule    = get_string('activate_this_rule', self::LANG_FILE);
        $activationrules = get_string('activation_rules', self::LANG_FILE);
        $criterion       = get_string('criterion', self::LANG_FILE);
        $days            = get_string('days');
        $eventtrigger    = get_string('event_trigger', self::LANG_FILE);
        $executemanually = get_string('execute_manually', self::LANG_FILE);
        $gradeset        = get_string('when_student_grade_set', self::LANG_FILE);
        $on              = get_string('on', self::LANG_FILE);
        $manualtrigger   = get_string('manual_trigger', self::LANG_FILE);
        $selectgrade     = get_string('select_grade', self::LANG_FILE);
        $uselocked       = get_string('use_locked_grades',self::LANG_FILE);
        $result          = get_string('result', self::LANG_FILE);
        $assigntotrack   = get_string('assign_to_track', self::LANG_FILE);
        $assigntoclass   = get_string('assign_to_class', self::LANG_FILE);
        $assigntoprofile = get_string('assign_to_profile', self::LANG_FILE);
        $addscorerange   = get_string('add_another_score_btn', self::LANG_FILE);

        if ($this->_layout == 'custom') {
            // Setup an alternate html output so we can make the form user friendly.
            $html = array();
            $html[] = '<form>';
            $html[] = '<fieldset class="engineform">';
            $html[] = '<legend>'. $activationrules .'</legend>';
            $html[] = '<input type="hidden" name="action" value="edit" />';
            $html[] = '<input type="hidden" name="id" value="'. $this->_customdata['id'] .'" />';
            $html[] = '<input type="hidden" name="contextid" value="'. $this->_customdata['contextid'] .'" />';
            $html[] = '<input type="hidden" name="s" value="'. $this->_customdata['s'] .'" />';
            $html[] = $activaterule .' <input type="checkbox" name="active" value="1" /><br />';

            $html[] = '<fieldset class="engineform">';
            $html[] = '<legend>'. $eventtrigger .'</legend>';
            $html[] = '<input type="radio" name="eventtriggertype" value="grade" />'. $gradeset
                    .' <input type="checkbox" name="lockedgrade" value="1" />'. $uselocked .'<br />';

            $html[] = '<input type="radio" name="eventtriggertype" value="date" />'. $on;
            $html[] = ' <input type="text" name="days" value="" /> '. $days;

            $html[] = '<select name="triggerstartdate">';
            foreach ($dates as $id => $string) {
                $html[] = "<option value=\"{$id}\">{$string}</option>";
            }
            $html[] = '</select><br />';

            $html[] = '<input type="radio" name="eventtriggertype" value="manual" />'. $manualtrigger;
            $html[] = '</fieldset>';

            $html[] = '<fieldset class="engineform">';
            $html[] = '<legend>'. $criterion .'</legend>';
            $html[] = $selectgrade .':<br />';

            $html[] = '<select name="grade">';
            foreach ($grades as $id => $string) {
                $html[] = "<option value=\"{$id}\">{$string}</option>";
            }
            $html[] = '</select><br />';

            $html[] = '</fieldset>';

            $html[] = '<fieldset class="engineform">';
            $html[] = '<legend>'.$result.'</legend>';
            // TODO: ADD HTML FOR RESULTS FIELDSET
            $html[] = '</fieldset>';
            $html[] = '</fieldset>';

            $this->_html = $html;
        } else {
            $mform =& $this->_form;


            $mform->addElement('header', 'activationrules', $activationrules);

            $mform->addElement('hidden', 'rid', $this->_customdata['rid']);
            $mform->addElement('hidden', 'contextid', $this->_customdata['contextid']);

            $active= array();
            $active[] = $mform->createElement('advcheckbox', 'active', '', $activaterule);
            $mform->addGroup($active, '', '', ' ', false);
            $mform->setType('active', PARAM_BOOL);

            $exists = array_key_exists('eventtriggertype', $this->_customdata);
            if ($exists && ($this->_customdata['eventtriggertype'] == RESULTS_ENGINE_MANUAL)) {
                $execute = array();
                $execute[] = $mform->createElement('submit', 'executebutton', $executemanually);
                $mform->addGroup($execute, '', '', ' ', false);
            }

            $mform->addElement('html', '<fieldset class="engineform">');
            $mform->addElement('html', '<legend>'. $eventtrigger .'</legend>');

            $grade = array();
            $grade[] = $mform->createElement('radio', 'eventtriggertype', '', $gradeset, RESULTS_ENGINE_GRADE_SET);
            $grade[] = $mform->createElement('advcheckbox', 'lockedgrade', '', $uselocked);

            $date = array();
            $date[] = $mform->createElement('radio', 'eventtriggertype', '', $on, RESULTS_ENGINE_SCHEDULED);
            $date[] = $mform->createElement('text', 'days', '', 'size="2"');
            $date[] = $mform->createElement('select', 'triggerstartdate', '', $dates);

            $manual = array();
            $manual[] = $mform->createElement('radio', 'eventtriggertype', '', $manualtrigger, RESULTS_ENGINE_MANUAL);

            $mform->setDefaults(array('eventtriggertype' => RESULTS_ENGINE_MANUAL));

            $mform->addGroup($grade, '', '', ' ', false);
            $mform->addGroup($date, '', '', array(' ', ' '. $days .' '), false);
            $mform->addGroup($manual, '', '', ' ', false);

            $mform->setType('locked', PARAM_BOOL);
            $mform->addElement('html', '</fieldset>');

            $mform->addElement('html', '<fieldset class="engineform">');
            $mform->addElement('html', '<legend>'. $criterion .'</legend>');

            $grade = array();
            $grade[] = $mform->createElement('select', 'criteriatype', '', $grades);

            $mform->addElement('html', $selectgrade .'<br />');
            $mform->addGroup($grade);

            $mform->addElement('html', '</fieldset>');

            $mform->addElement('html', '<div id = class="engineform">');
            $mform->addElement('html', '<fieldset>');
            $mform->addElement('html', '<legend>'.$result.'</lengend>');

            // Accordion implementation
            $mform->addElement('html', '<div id="accordion">');

            // Add track score range
            $mform->addElement('html', '<div>');
            $mform->addElement('html', '<h3>');
            $mform->addElement('html', '<a href="#">'.$assigntotrack.'</a>');
            $mform->addElement('html', '</h3>');


            // Hidden elemenets to store minimum, maximum and selected track/class/profile field
            $mform->addElement('hidden', 'range_cache_minimum');
            $mform->addElement('hidden', 'range_cache_maximum');
            $mform->addElement('hidden', 'range_cache_selection1');
            $mform->addElement('hidden', 'range_cache_selection2');

            $mform->addElement('hidden', 'range_minimum');
            $mform->addElement('hidden', 'range_maximum');
            $mform->addElement('hidden', 'range_selection1');
            $mform->addElement('hidden', 'range_selection2');

            $mform->addElement('html', '<div>' . $this->get_assign_to_table(0, false, false, 'track'));

            $attributes = array('onclick' => 'add_range_selection("track");');
            $mform->addElement('submit', 'track_assignment', $addscorerange, $attributes);
            $mform->addElement('html', '</div>');

            $mform->addElement('html', '</div>');

            // Add class score range
            $mform->addElement('html', '<div>');
            $mform->addElement('html', '<h3>');
            $mform->addElement('html', '<a href="#">'.$assigntoclass.'</a>');
            $mform->addElement('html', '</h3>');
            $mform->addElement('html', '<div>' . $this->get_assign_to_table(0, false, false, 'class'));
            $mform->addElement('submit', 'class_assignment', $addscorerange);
            $mform->addElement('html', '</div>');

            $mform->addElement('html', '</div>');

            // Add profile field score range
            $mform->addElement('html', '<div>');
            $mform->addElement('html', '<h3>');
            $mform->addElement('html', '<a href="#">'.$assigntoprofile.'</a>');
            $mform->addElement('html', '</h3>');
            $mform->addElement('html', '<div>Some more content in div');
            $mform->addElement('submit', 'profile_assignment', $addscorerange);
            $mform->addElement('html', '</div>');
            $mform->addElement('html', '</div>');

            $mform->addElement('html', '</fieldset>');

            $mform->addElement('html', '</div>');

            $mform->addElement('html', '</fieldset>');
        }
    }

    /**
     * Define the results section of the form.
     */
    protected function defineResults() {
    }

    function check_unique($table, $field, $value, $id) {
        global $DB;
        return !$DB->record_exists_select($table, "$field = ? AND id <> ?", array($value, $id));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if(!empty($data['timetocomplete'])) {
            $datedelta = new datedelta($data['timetocomplete']);

            if(!$datedelta->getDateString()) {
                $errors['timetocomplete'] = get_string('error_not_timeformat', 'elis_program');
            }
        }

        if(!empty($data['frequency'])) {
            $datedelta = new datedelta($data['frequency']);

            if(!$datedelta->getDateString()) {
                $errors['frequency'] = get_string('error_not_durrationformat', 'elis_program');
            }
        }

        if (!empty($data['idnumber'])) {
            if (!$this->check_unique(curriculum::TABLE, 'idnumber', $data['idnumber'], $data['id'])) {
                $errors['idnumber'] = get_string('badidnumber', 'elis_program');
            }
        }


        // Add another track score range button
        if (array_key_exists('track_assignment', $data)) {

        }

        if (array_key_exists('class_assignment', $data)) {
        }

        if (array_key_exists('profile_assignment', $data)) {
        }

        return $errors;
    }

    /**
     * Display HTML
     *
     * This function works around the limitations of the moodle forms by printing html
     * directly.  This allows for more custom designed forms.
     */
    function display() {
        if ($this->_layout == 'custom') {
            print(implode("\n", $this->_html));
        } else {
            parent::display();
        }
    }

    protected function get_assign_to_table($resultsid = 0, $addrow = false, $cached_data = null, $type = 'track') {

            global $CFG, $OUTPUT;

            $scoreheader        = get_string('score', self::LANG_FILE);
            $assigntype         = get_string("assign_to_{$type}", self::LANG_FILE);
            $selecttype         = get_string("select_{$type}", self::LANG_FILE);
            $deletescoretype    = get_string("delete_score", self::LANG_FILE);

            $attributes = array('class'     => "datatable {$type}assignment",
                                'id'        => "assign_{$type}_table",
                                'border'    => '1');
            $output = html_writer::start_tag('table', $attributes);

            // Header
            $attributes['id'] = "header_assign_{$type}_table";
            $output .= html_writer::start_tag('tr');

            $output .= html_writer::tag('th', $scoreheader);
            $output .= html_writer::tag('th', $assigntype);

            $output .= html_writer::end_tag('tr');

            // data row(s)
            $function = "get_assign_to_{$type}_data";
            $data = $this->$function($resultsid);

            if (empty($data)) {

                $output .= html_writer::start_tag('tr');
                $output .= html_writer::start_tag('td');

                $random_id = html_writer::random_id();

                // Print Minimum
                $attributes = array('id' => "id_minimum_new_{$type}_{$random_id}",
                                    'value' => '',
                                    'type' => 'text',
                                    'size' => '5',
                                    'maxlength' => '5',
                                    'name' => "{$type}_minimum");
                $output .= html_writer::empty_tag('input', $attributes);
                $output .= '&nbsp;&nbsp;';

                // Print Maximum
                $attributes['id'] = "maximum_new_{$type}_{$random_id}";
                $attributes['name'] = "{$type}_maximum";
                $output .= html_writer::empty_tag('input', $attributes);
                $output .= '&nbsp;&nbsp;';

                // Print Delete icon
                // TODO: add onclick event to clear minimum, maximum and track selection
                $attributes = array('title' => $deletescoretype,
                                    'alt' => $deletescoretype,
                                    'src' => $OUTPUT->pix_url('delete', 'elis_program'));

                $image  = html_writer::empty_tag('img', $attributes);
                $output .= html_writer::link('#', $image);


                $output .= html_writer::end_tag('td');
                $output .= html_writer::start_tag('td');

                // Print type selection label
                $attributes = array('id' => "label_new_{$type}_{$random_id}");
                $output  .= html_writer::tag('label', '', $attributes);

                // Print hidden type element to store the type id
                $attributes = array('id' => "id_new_{$type}_{$random_id}");
                $attributes = array('name' => "{$type}_id");
                $output  .= html_writer::tag('hidden', '', $attributes);

                $output .= '&nbsp;&nbsp;';

                // Print track selection link
                $url = "form/{$type}selector.php?id={$type}_{$random_id}";
                $attributes = array('onClick' => 'show_panel("'.$url.'")');

                $output .= html_writer::link('#', $selecttype, $attributes);


                $output .= html_writer::end_tag('td');
                $output .= html_writer::end_tag('tr');
            } else {
                // print data
            }

            if ($cached_data) {
                // Print cached data
            }

            if ($addrow) {
                // Add input row
            }

            $output .= html_writer::start_tag('tr');

            // data column(s)
            $output .= html_writer::tag('td', 'some input');
            $output .= html_writer::tag('td', 'some input2');

            $output .= html_writer::end_tag('tr');

            $output .= html_writer::end_tag('table');

            return $output;
    }

    protected function get_assign_to_track_data($resultsid = 0) {
        global $DB, $CFG;

        if (empty($resultsid)) {
            return array();
        }

        $sql = "SELECT rea.id, rea.minimum, rea.maximum, rea.trackid AS typeid, t.name ".
               "FROM {$CFG->prefix}crlm_results_engine_action rea ".
               "RIGHT JOIN {$CFG->prefix}crlm_track t ON rea.trackid = t.id ".
               "WHERE rea.resultengineid = :resultsengineid ORDER BY minimum ASC";

        $params = array('resultsengineid' => $resultsid);

        $data = $DB->get_records_sql($sql, $params);

        if (empty($data)) {
            return array();
        }

        return $data;
    }

    protected function get_assign_to_class_data($resultsid = 0) {
        global $DB, $CFG;

        if (empty($resultsid)) {
            return array();
        }

        $sql = "SELECT rea.id, rea.minimum, rea.maximum, rea.classid AS typeid, cls.idnumber AS name".
               "FROM {$CFG->prefix}crlm_results_engine_action rea ".
               "RIGHT JOIN {$CFG->prefix}crlm_class cls ON rea.classid = cls.id ".
               "WHERE rea.resultengineid = :resultsengineid ORDER BY minimum ASC";

        $params = array('resultsengineid' => $resultsid);

        $data = $DB->get_records_sql($sql, $params);

        if (empty($data)) {
            return array();
        }

        return $data;
    }

}
