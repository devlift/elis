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
 * @package    local_elisprogram
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2008-2013 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once elispm::lib('page.class.php');
require_once elispm::lib('lib.php');
require_once elispm::file('form/resultsconfigform.class.php');

class resultsconfigpage extends pm_page {

    var $pagename = 'resultsconfig';
    var $section = 'admn';
    var $form_class = 'resultsconfigform';
    private $customdata = array();

    function can_do_default() {
        $context = get_context_instance(CONTEXT_SYSTEM);
        return has_capability('local/elisprogram:config', $context);
    }

    function build_navbar_default($who = null) {
        global $CFG;
        parent::build_navbar_default($who);
        $page = $this->get_new_page(array('action' => 'default'), true);
        //$this->navbar->add(get_string('learningplan', 'local_elisprogram'), "{$CFG->wwwroot}/local/elisprogram/");
        $this->navbar->add(get_string('results_engine_defaults_config','local_elisprogram'), $page->url);
    }

    function get_title_default() {
        return get_string('results_engine_defaults_config','local_elisprogram');
    }

    function normalize_submitted_data($raw_submitted_data) {
        $ranges=array();
        foreach ($raw_submitted_data as $k => $v) {
            $kparts=explode('_',$k);
            if ($kparts[0] != 'textgroup' || !is_number($kparts[1])) {
                if (!is_numeric($k) && $k !== 'finalize' && $k !== 'rowcount') {
                    $ranges[$k] = $v; //to allow for additional information like the national average for prepworks
                }
                continue; //numeric keys may overwrite our processed numeric keys, so sorry, you're out of luck
            }
            $i = $kparts[1];
            $ranges[$i]=array(
                'rowid'=>intval($v['rowid']),
                'min'=>$v['mininput'],
                'max'=>$v['maxinput'],
                'name'=>$v['nameinput']
            );
        }
        return $ranges;
    }

    function do_default() {
        global $CFG, $DB;
        //get saved defaults
        $defaults=get_config('local_elisprogram','results_engine_defaults');
        $defaults=(!empty($defaults))?unserialize($defaults):array();
        $saved_row_count=(!empty($defaults))?sizeof($defaults):1;

        $this->customdata=array(
            'nrc' => optional_param('rowcount',$saved_row_count,PARAM_INT),
            'defaults' => $defaults);

        $form = $this->get_form();
        $data = (array)$form->get_data();

        if (!empty($data) && isset($data['finalize'])) {
            //form being saved

            $data = $this->normalize_submitted_data($data);

            //validate submitted info
            $data2 = array();
            foreach ($data as $i => $range) {
                if (is_numeric($i)) {

                    //remove entirely empty rows
                    if (empty($range['min']) && empty($range['max']) && empty($range['name'])) {
                        continue;
                    }

                    //check for empty values
                    if (empty($range['min'])) {
                        $errs[$i][] = get_string('results_engine_defaults_err_no_min', 'local_elisprogram');
                    }
                    if (empty($range['max'])) {
                        $errs[$i][] = get_string('results_engine_defaults_err_no_max', 'local_elisprogram');
                    }
                    if (!isset($range['name'])) {
                        $range['name'] = '';
                    }

                    //check for overlapping values
                    if (empty($errs[$i]) && !empty($data2) && is_array($data2)) {
                        foreach ($data2 as $j => $ex_rng) {
                            if (!isset($ex_rng['min'],$ex_rng['max'])) {
                                continue;
                            }

                            if ($range['min'] >= $ex_rng['min'] && $range['min'] <= $ex_rng['max']) {
                                $errs[$i][] = get_string('results_engine_defaults_err_min_conflict', 'local_elisprogram');
                                break;
                            }
                            if ($range['max'] >= $ex_rng['min'] && $range['max'] <= $ex_rng['max']) {
                                $errs[$i][] = get_string('results_engine_defaults_err_max_conflict', 'local_elisprogram');
                                break;
                            }
                            if ($ex_rng['min'] >= $range['min'] && $ex_rng['max'] <= $range['max']) {
                                $errs[$i][] = get_string('results_engine_defaults_err_enveloped_range', 'local_elisprogram');
                                break;
                            }
                        }
                    }

                    if (empty($errs[$i])) {
                        $data2[$i] = $range;
                    }
                }
            }
            $data = array();

            //index saved info by rowid to make getting extra saved data easy
            $saved_data_rowid_indexed = array();
            $extradata = array();
            foreach ($defaults as $i => $defaultsdata) {
                if (is_numeric($i) && isset($defaultsdata['rowid']) && is_numeric($defaultsdata['rowid'])) {
                    $saved_data_rowid_indexed[$defaultsdata['rowid']] = $defaultsdata;
                } else {
                    $extradata[$i] = $defaultsdata;
                }
            }

            //add back in any extra values
            foreach ($data2 as $i => $row) {
                if (is_numeric($i) && isset($row['rowid']) && is_numeric($row['rowid']) && !empty($saved_data_rowid_indexed[$row['rowid']])) {
                    $data2[$i] = array_merge($saved_data_rowid_indexed[$row['rowid']],$row);
                }
            }
            $data2 = array_merge($extradata,$data2);
            $data2 = serialize($data2);
            pm_set_config('results_engine_defaults', $data2);

            //$target = $this->get_new_page(array('s'=>'resultsconfig','action' => 'default'), false);
            //redirect($target->url);

            $results = '';
            if (!empty($errs) && is_array($errs)) {
                $results .= get_string('results_engine_defaults_errs_encountered', 'local_elisprogram');
                foreach ($errs as $i => $rowerrs) {
                    if (!empty($rowerrs) && is_array($rowerrs)) {
                        $results .= '<ul>';
                        $results .= '<li>'.get_string('results_engine_defaults_err_row','local_elisprogram',($i+1)).'</li>';
                        $results .= '<ul>';
                        foreach ($rowerrs as $err) {
                            $results .= '<li>'.$err.'</li>';
                        }
                        $results .= '</ul></ul>';
                    }
                }
                $results .= get_string('results_engine_defaults_saved_with_errors','local_elisprogram');
            } else {
                $results .= get_string('results_engine_defaults_settings_saved','local_elisprogram');
            }
            $results .= '<br /><br />';
        }

        $this->customdata['results'] = (!empty($results)) ? $results : '';

        $this->display('default');

    }

    function display_default() {

        $form = $this->get_form();
        $form->display();
    }

    function get_form() {
        $target = $this->get_new_page(array('s'=>'resultsconfig','action' => 'default'));
        $form = new $this->form_class($target->url,$this->customdata);
        return $form;
    }
}
