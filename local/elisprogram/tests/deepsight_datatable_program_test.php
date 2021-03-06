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
 * @copyright  (C) 2013 Remote Learner.net Inc http://www.remote-learner.net
 * @author     James McQuillan <james.mcquillan@remote-learner.net>
 *
 */

require_once(dirname(__FILE__).'/../../eliscore/test_config.php');
global $CFG;
require_once($CFG->dirroot.'/local/elisprogram/lib/setup.php');
require_once(dirname(__FILE__).'/other/deepsight_testlib.php');

require_once(elispm::lib('data/curriculum.class.php'));

/**
 * Mock program datatable class exposing protected methods and properties
 */
class deepsight_datatable_program_mock extends deepsight_datatable_program {

    /**
     * Magic function to expose protected properties
     * @param string $name The name of the property
     * @return string|int|bool The value of the property
     */
    public function __get($name) {
        return (isset($this->$name)) ? $this->$name : false;
    }

    /**
     * Magic function to expose protected properties
     * @param string $name The name of the property
     * @return string|int|bool The value of the property
     */
    public function __isset($name) {
        return (isset($this->$name)) ? true : false;
    }

    /**
     * Expose protected methods.
     * @param string $name The name of the called method.
     * @param array $args Array of arguments.
     */
    public function __call($name, $args) {
        if (method_exists($this, $name)) {
            return call_user_func_array(array($this, $name), $args);
        }
    }

    /**
     * Expose protected properties.
     * @param string $name The name of the property.
     * @param mixed $val The name to set.
     */
    public function __set($name, $val) {
        $this->$name = $val;
    }
}

/**
 * Tests the base program datatable class.
 * @group local_elisprogram
 * @group deepsight
 */
class deepsight_datatable_program_testcase extends deepsight_datatable_standard_implementation_test {

    /**
     * Construct the datatable we're testing.
     *
     * @return deepsight_datatable The deepsight_datatable object we're testing.
     */
    protected function get_test_table() {
        global $DB;
        return new deepsight_datatable_program_mock($DB, 'test', '', 'testuniqid');
    }

    /**
     * Do any setup before tests that rely on data in the database - i.e. create users/courses/classes/etc or import csvs.
     */
    protected function set_up_tables() {
        $dataset = $this->createCsvDataSet(array(
            curriculum::TABLE => elispm::file('tests/fixtures/deepsight_program.csv')
        ));
        $this->loadDataSet($dataset);
    }

    /**
     * Dataprovider for test_bulklist_get_display.
     *
     * @return array The array of argument arrays.
     */
    public function dataprovider_bulklist_get_display() {
        return array(
            array(array(5, 6), array(6 => 'Test Program 6', 5 => 'Test Program 5'), 2)
        );
    }

    /**
     * Dataprovider for test_get_search_results()
     *
     * @return array The array of argument arrays.
     */
    public function dataprovider_get_search_results() {
        // Parse the csv to get information and create element arrays, indexed by element id.
        $csvdata = file_get_contents(dirname(__FILE__).'/fixtures/deepsight_program.csv');
        $csvdata = explode("\n", $csvdata);
        $keys = explode(',', $csvdata[0]);
        $lines = count($csvdata);
        $csvelements = array();
        for ($i=1; $i<$lines; $i++) {
            $curele = explode(',', $csvdata[$i]);
            $csvelements[$curele[0]] = array_combine($keys, $curele);
        }
        unset($csvdata, $keys);

        // Create search result arrays, indexed by element id.
        $results = array();
        foreach ($csvelements as $id => $element) {
            $results[$id] = array(
                'element_id' => $id,
                'element_name' => $element['name'],
                'element_idnumber' => $element['idnumber'],
                'id' => $id,
                'meta' => array(
                    'label' => $element['name']
                )
            );
        }

        return array(
            // Test Default.
            array(
                array(),
                array('element.name' => 'ASC'),
                0,
                20,
                array($results[5], $results[6], $results[7]),
                3
            ),
            // Test Sorting.
            array(
                array(),
                array('element.name' => 'DESC'),
                0,
                20,
                array($results[7], $results[6], $results[5]),
                3
            ),
            // Test Basic Searching.
            array(
                array('name' => array('Test 5')),
                array('element.name' => 'DESC'),
                0,
                20,
                array($results[5]),
                1
            ),
            // Test limited page results.
            array(
                array(),
                array('element.name' => 'ASC'),
                0,
                2,
                array($results[5], $results[6]),
                3
            ),
        );
    }
}