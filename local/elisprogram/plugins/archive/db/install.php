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
 * @package    elisprogram_archive
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2008-2013 Remote-Learner.net Inc (http://www.remote-learner.net)
 *
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/../../../../../config.php');
global $CFG;
require_once($CFG->dirroot.'/local/elisprogram/lib/setup.php');
require_once elispm::file('plugins/archive/lib.php');

/**
 * Install function for this plugin
 *
 * @return  boolean  true  Returns true to satisfy install procedure
 */
function xmldb_elisprogram_archive_install() {
    global $CFG;

    require_once elispm::lib('setup.php');
    require_once elis::lib('data/customfield.class.php');

    // Migrate component.
    $oldcmp = 'pmplugins_archive';
    $newcmp = 'elisprogram_archive';
    $upgradestepfuncname = 'elisprogram_archive_pre26upgradesteps';
    $migrator = new \local_elisprogram\install\migration\migrator($oldcmp, $newcmp, $upgradestepfuncname);
    if ($migrator->old_component_installed() === true) {
        $migrator->migrate();
    }

    // Archive field
    $field = new field();
    $field->shortname = ARCHIVE_FIELD;
    $field->name = get_string('archive_field_name', 'elisprogram_archive');
    $field->datatype = 'bool';

    $category = new field_category();
    $category->name = get_string('archive_category_name', 'elisprogram_archive');

    $field = field::ensure_field_exists_for_context_level($field, CONTEXT_ELIS_PROGRAM, $category);

    // make sure 'manual' is an owner
    if (!isset($field->owners['manual'])) {
        $owner = new field_owner();
        $owner->fieldid = $field->id;
        $owner->plugin = 'manual';
        $owner->param_required = 0;
        $owner->param_view_capability = '';
        $owner->param_edit_capability = '';
        $owner->param_control = 'checkbox';
        $owner->param_options_source = '';
        $owner->param_help_file = 'elisprogram_archive/archive_program';
        $owner->save();
    }

    $owner_options = array('required'        => 0,
                           'edit_capability' => '',
                           'view_capability' => '',
                           'control'         => 'checkbox',
                           'columns'         => 30,
                           'rows'            => 10,
                           'maxlength'       => 2048,
                           'help_file'       => 'elisprogram_archive/archive_program');
    field_owner::ensure_field_owner_exists($field, 'manual', $owner_options);

    return true;
}

/**
 * Run all upgrade steps from before elis 2.6.
 *
 * @param int $oldversion The currently installed version of the old component.
 * @return bool Success/Failure.
 */
function elisprogram_archive_pre26upgradesteps($oldversion) {
    $result = true;

    if ($result && $oldversion < 2011100700) {
        // rename field
        $field = field::find(new field_filter('shortname', '_elis_curriculum_archive'));

        if ($field->valid()) {
            $field = $field->current();
            $field->shortname = ARCHIVE_FIELD;
            $field->name = get_string('archive_field_name', 'elisprogram_archive');
            $field->save();
        }

        upgrade_plugin_savepoint($result, 2011100700, 'pmplugins', 'archive');
    }

    if ($result && $oldversion < 2011101200) {
        $field = field::find(new field_filter('shortname', ARCHIVE_FIELD));

        if ($field->valid()) {
            $field = $field->current();
            if ($owner = new field_owner((!isset($field->owners) || !isset($field->owners['manual'])) ? false : $field->owners['manual'])) {
                $owner->fieldid = $field->id;
                $owner->plugin = 'manual';
                //$owner->exclude = 0; // TBD
                $owner->param_help_file = 'elisprogram_archive/archive_program';
                $owner->save();
            }

        }

        upgrade_plugin_savepoint($result, 2011101200, 'pmplugins', 'archive');
    }

    return $result;
}