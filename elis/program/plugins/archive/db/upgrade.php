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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/elis/program/lib/setup.php');
require_once(elis::lib('data/customfield.class.php'));
require_once(elis::plugin_file('pmplugins_archive', 'lib.php'));

function xmldb_pmplugins_archive_upgrade($oldversion = 0) {
    $result = true;

    if ($result && $oldversion < 2011100700) {
        // rename field
        $field = field::find(new field_filter('shortname', '_elis_curriculum_archive'));

        if ($field->valid()) {
            $field = $field->current();
            $field->shortname = ARCHIVE_FIELD;
            $field->name = get_string('archive_field_name', 'pmplugins_archive');
            $field->save();
        }
    }

    return $result;
}