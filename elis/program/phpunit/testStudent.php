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
 * @subpackage programmanager
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2012 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once(dirname(__FILE__) . '/../../core/test_config.php');
global $CFG;
require_once($CFG->dirroot . '/elis/program/lib/setup.php');
require_once(elis::lib('testlib.php'));
require_once('PHPUnit/Extensions/Database/DataSet/CsvDataSet.php');
require_once(elispm::lib('data/user.class.php'));
require_once(elispm::lib('data/student.class.php'));
require_once(elispm::lib('data/pmclass.class.php'));
require_once(elispm::lib('data/course.class.php'));

class studentTest extends elis_database_test {
    protected $backupGlobalsBlacklist = array('DB');

    protected static function get_overlay_tables() {
        return array(
            'context' => 'moodle',
            'user' => 'moodle',
            'course' => 'moodle',
            'message' => 'moodle',
            user::TABLE => 'elis_program',
            student::TABLE => 'elis_program',
            pmclass::TABLE => 'elis_program',
            course::TABLE => 'elis_program'
        );
    }

    protected function setUp() {
        global $DB;
        parent::setUp();
        $DB = self::$origdb; // setUpContextsTable needs $DB to be the real
                             // database for get_admin()
        $this->setUpContextsTable();
        $DB = self::$overlaydb;
    }

    protected function tearDown() {
        global $DB;
        $DB = self::$origdb;
    }

    /**
     * Set up the contexts table with the minimum that we need.
     */
    private function setUpContextsTable() {
        global $CFG;
        // system context
        $syscontext = self::$origdb->get_record('context', array('contextlevel' => CONTEXT_SYSTEM));
        self::$overlaydb->import_record('context', $syscontext);

        // site (front page) course
        $site = self::$origdb->get_record('course', array('id' => SITEID));
        self::$overlaydb->import_record('course', $site);
        $sitecontext = self::$origdb->get_record('context', array('contextlevel' => CONTEXT_COURSE,
                                                                  'instanceid' => SITEID));
        self::$overlaydb->import_record('context', $sitecontext);

        // primary admin user
        $admin = get_admin();
        if ($admin) {
            self::$overlaydb->import_record('user', $admin);
            $CFG->siteadmins = $admin->id;
            $usercontext = self::$origdb->get_record('context', array('contextlevel' => CONTEXT_USER,
                                                                      'instanceid' => $admin->id));
            self::$overlaydb->import_record('context', $usercontext);

            // copy admin user's ELIS user (if available)
            $elisuser = user::find(new field_filter('idnumber', $admin->idnumber), array(), 0, 0, self::$origdb);
            if ($elisuser->valid()) {
                $elisuser = $elisuser->current();
                self::$overlaydb->import_record(user::TABLE, $elisuser->to_object());
            }
        }
    }

    protected function load_csv_data() {
        $dataset = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $dataset->addTable('user', elis::component_file('program', 'phpunit/mdluser.csv'));
        $dataset->addTable(course::TABLE, elis::component_file('program', 'phpunit/pmcourse.csv'));
        $dataset->addTable(pmclass::TABLE, elis::component_file('program', 'phpunit/pmclass.csv'));
        $dataset->addTable(user::TABLE, elis::component_file('program', 'phpunit/pmuser.csv'));
        $dataset->addTable(student::TABLE, elis::component_file('program', 'phpunit/student.csv'));
        load_phpunit_data_set($dataset, true, self::$overlaydb);
    }

    /**
     * Test validation of empty userid
     *
     * @expectedException data_object_validation_exception
     */
    public function testStudentValidationPreventsEmptyUserid() {
        $this->load_csv_data();

        $student = new student(array('classid' => 100));

        $student->save();
    }

    /**
     * Test validation of empty classid
     *
     * @expectedException data_object_validation_exception
     */
    public function testStudentValidationPreventsEmptyClassid() {
        $this->load_csv_data();

        $student = new student(array('userid' => 103));

        $student->save();
    }

    /**
     * Test validation of invalid userid
     *
     * @expectedException dml_missing_record_exception
     */
    public function testStudentValidationPreventsInvalidUserid() {
        $this->load_csv_data();

        $student = new student(array('userid' => 1,
                                     'classid' => 100));

        $student->save();
    }

    /**
     * Test validation of invalid classid
     *
     * @expectedException dml_missing_record_exception
     */
    public function testStudentValidationPreventsInvalidClassid() {
        $this->load_csv_data();

        $student = new student(array('userid' => 103,
                                     'classid' => 1));

        $student->save();
    }

    /**
     * Test validation of duplicates
     *
     * Note: no exception thrown from student.class.php for dup.
     */
    public function testStudentValidationPreventsDuplicates() {
        global $DB;
        $this->load_csv_data();

        $student = new student(array('userid' => 103,
                                     'classid' => 100));

        $student->save();
        $stus = $DB->get_records(student::TABLE, array('userid' => 103, 'classid' => 100));
        $this->assertEquals(count($stus), 1);
    }

    /**
     * Test the insertion of a valid association record
     */
    public function testStudentValidationAllowsValidRecord() {
        $this->load_csv_data();

        $student = new student(array('userid' => 103,
                                     'classid' => 101));

        $student->save();

        $this->assertTrue(true);
    }
}
