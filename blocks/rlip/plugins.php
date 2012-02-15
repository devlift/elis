<?php
/**
 * ELIS(TM): Enterprise Learning Intelligence Suite
 * Copyright (C) 2008-2012 Remote-Learner.net Inc (http://www.remote-learner.net)
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
 * @subpackage core
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2012 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/adminlib.php');

//permissions checking
require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/site:config', $context);

//header
admin_externalpage_setup('rlipsettingplugins');
$PAGE->requires->css('/blocks/rlip/styles.css');
echo $OUTPUT->header();

//export plugin header
echo $OUTPUT->box_start('generalbox pluginspageheading');
print_string('exportplugins', 'block_rlip');
echo $OUTPUT->box_end();

//initialize table
$table = new html_table();
$table->head = array(get_string('name'), get_string('settings'));
$table->align = array('left', 'left');
$table->size = array('80%', '20%');
$table->data = array();
$table->width = '30%';

//obtain plugins and iterate through them
$plugins = get_plugin_list('rlipexport');
foreach ($plugins as $name => $path) {
    //get the display name from the plugin-specific language string
    $displayname = get_string('pluginname', "rlipexport_{$name}");

    //configuration link
    $url = $CFG->wwwroot."/admin/settings.php?section=rlipsettingrlipexport_{$name}";
    $attributes = array('href' => $url);
    $tag = html_writer::tag('a', get_string('edit'), $attributes);

    //combine into row data
    $table->data[] = array($displayname, $tag);
}

//output the table
echo html_writer::table($table);

//footer
echo $OUTPUT->footer();