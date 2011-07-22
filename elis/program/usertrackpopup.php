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
 * @subpackage curriculummanagement
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2010 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once (dirname(__FILE__) . '/lib/setup.php');
require_once elispm::lib('lib.php');
require_once elispm::lib('data/user.class.php');
require_once elispm::lib('data/usertrack.class.php');
require_once elispm::lib('data/track.class.php');
require_once elispm::file('usertrackpage.class.php');

global $DB, $OUTPUT;

$site = get_site();

$trackid = required_param('track', PARAM_INT);
$track = new track($trackid);
$userid = optional_param('userid', 0,  PARAM_INT);
// get searching/sorting parameters
$sort = optional_param('sort','name',PARAM_ALPHA);
$alpha = optional_param('alpha','',PARAM_ALPHA);
$namesearch = optional_param('namesearch','',PARAM_MULTILANG);
$dir = optional_param('dir','ASC',PARAM_ALPHA);
if ($dir != 'ASC' && $dir != 'DESC') {
    $dir = 'ASC';
}
$page = optional_param('page',0,PARAM_INT);
$perpage = optional_param('perpage',30,PARAM_INT);

$url = new moodle_url('/elis/program/usertrackpopup.php',array('track'             => $trackid,
                                                               'userid'            => $userid,
                                                               'sort'              => $sort,
                                                               'alpha'      => $alpha,
                                                               'namesearch' => $namesearch,
                                                               'dir'               => $dir,
                                                               'page'              => $page,
                                                               'perpage'           => $perpage));
$PAGE->set_url($url);
require_login();
$context = get_context_instance(context_level_base::get_custom_context_level('track', 'elis_program'), $trackid);
$PAGE->set_context($context);
//todo: integrate this better with user-track page?
//this checks permissions at the track level
if(!trackpage::can_enrol_into_track($trackid)) {
    //standard failure message
    require_capability('block/curr_admin:track:enrol', $context);
}

// add user to track
if ($userid) {
    //todo: integrate this better with user-track page?
    //this checks permissions at the user-track association level
    if(!usertrack::can_manage_assoc($userid, $trackid)) {
        //standard failure message
        require_capability('block/curr_admin:track:enrol', $context);
    }

    usertrack::enrol($userid, $trackid);
    // reload the main page with the new assignments
    $target = new trackuserpage(array('id' => $trackid))
?>
<script type="text/javascript">
//<![CDATA[
window.opener.location = "<?php echo htmlspecialchars_decode($target->url); ?>";
//]]>
</script>
<?php
}

// find all users not enrolled in the track
$FULLNAME = $DB->sql_concat('usr.firstname', "' '", 'usr.lastname');
$NAMELIKE = $DB->sql_like($FULLNAME, ':namesearch', FALSE);
$ALPHA_LIKE = $DB->sql_like($FULLNAME, ':lastname', FALSE);

$select = 'SELECT usr.*, ' . $FULLNAME . ' AS name ';
$sql = 'FROM {' . user::TABLE . '} usr '
    . 'LEFT OUTER JOIN {' . usertrack::TABLE . '} ut ON ut.userid = usr.id AND ut.trackid = :trackid '
    . 'WHERE ut.userid IS NULL ';
$params = array('trackid'=> $trackid);
if (!empty($namesearch)) {
        $namesearch = trim($namesearch);
        $sql .= ' AND '. $NAMELIKE;
        $params['namesearch'] = "%{$namesearch}%";
    }

if ($alpha) {
    $sql .= ' AND '. $ALPHA_LIKE;
    $params['lastname'] = "{$alpha}%";
}

if(!trackpage::_has_capability('block/curr_admin:track:enrol', $trackid)) {
    //perform SQL filtering for the more "conditional" capability
    //get the context for the "indirect" capability
    $context = pm_context_set::for_user_with_capability('cluster', 'block/curr_admin:track:enrol_cluster_user', $USER->id);

    //get the clusters and check the context against them
    $clusters = clustertrack::get_clusters($trackid);
    $allowed_clusters = $context->get_allowed_instances($clusters, 'cluster', 'clusterid');

    if(empty($allowed_clusters)) {
        $sql .= 'AND 0=1';
    } else {
        $cluster_filter = implode(',', $allowed_clusters);
        $sql .= "AND usr.id IN (
                   SELECT userid FROM " . $DB->prefix_table(CLSTUSERTABLE) . "
                   WHERE clusterid IN (:clusterfilter))";
        $params['clusterfilter'] = $cluster_filter;
    }
}

// get the total number of matching users
$count = $DB->count_records_sql('SELECT COUNT(usr.id) '.$sql, $params);
if ($sort) {
    $sql .= 'ORDER BY '.$sort.' '.$dir.' ';
}
/*if ($perpage) {
    if ($CURMAN->db->_dbconnection->databaseType == 'postgres7') {
        $sql .= 'LIMIT ' . $perpage . ' OFFSET ' . $page * $perpage . ' ';
    } else {
        $sql .= 'LIMIT ' . $page * $perpage . ', ' . $perpage . ' ';
    }

}*/

$users = $DB->get_records_sql($select.$sql, $params, $page * $perpage, $perpage);

//print_header($site->shortname . ': Assign users to track "' . $track->name . '"');
$PAGE->set_title($site->shortname . ': Assign users to track "' . $track->name . '"');
$PAGE->set_heading($site->shortname . ': Assign users to track "' . $track->name . '"');
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();
echo $OUTPUT->box_start();
// create a link based on the given parameters and the current page
// parameters
function make_link($psort=null, $pdir=null, $palpha=null, $ppage=null, $pnamesearch=null) {
    global $trackid, $sort, $alpha, $link, $namesearch, $dir, $page;
    $link = 'usertrackpopup.php?track='.$trackid;
    if ($psort!==null) {
        $link .= '&amp;sort='.$psort;
    } else if ($sort != 'name') {
        $link .= '&amp;sort='.$sort;
    }
    if ($palpha!==null) {
        $link .= '&amp;alpha='.$palpha;
    } else if ($alpha) {
        $link .= '&amp;alpha='.$alpha;
    }
    if ($pnamesearch===null && $namesearch) {
        $link .= '&amp;namesearch='.rawurlencode($namesearch);
    }
    if ($pdir!==null) {
        $link .= '&amp;dir='.$pdir;
    } else if ($dir != 'ASC') {
        $link .= '&amp;dir='.$dir;
    }
    if ($ppage!==null) {
        $link .= '&amp;page='.$ppage;
    } else if ($page) {
        $link .= '&amp;page='.$page;
    }
    return $link;
}

/// Bar of first initials
pmalphabox($url);
// note: use moodle_url so that it will replace the current page parameter,
// if present

echo $OUTPUT->paging_bar($count, $page, $perpage, $url);

?>
<center><form action="usertrackpopup.php" method="get">
<input type="hidden" name="track" value="<?php echo $trackid; ?>" />
<?php
if ($sort != 'name') {
    echo '<input type="hidden" name="sort" value="'.$sort.'" />';
}
if ($alpha) {
    echo '<input type="hidden" name="alpha" value="'.$alpha.'" />';
}
if ($dir != 'ASC') {
    echo '<input type="hidden" name="dir" value="'.$dir.'" />';
}
?>
<input type="text" name="namesearch" value="<?php echo s($namesearch,true); ?>" size="20" />
<input type="submit" value="Search" /></form></center>
<?php

// show list of available users
if (empty($users)) {
    echo '<div>' . get_string('track_no_matching_users', 'elis_program') . '</div>';
} else {
    echo '<div>' . get_string('track_click_user_enrol_track', 'elis_program') . '</div>';
    //$table = null;
    $headings = array();
    $columns = array(
        'idnumber'    => get_string('track_idnumber', 'elis_program'),
        'name'        => get_string('track_name', 'elis_program'),
        'email'       => get_string('email', 'elis_program'),
        );

    foreach ($columns as $column => $cdesc) {
        if ($sort != $column) {
            $columnicon = "";
            $columndir = "ASC";
        } else {
            $columndir  = $dir == "ASC" ? 'DESC':'ASC';
            $columnicon = $dir == "ASC" ? 'down':'up';
            $columnicon = ' <img src="'.$OUTPUT->pix_url($columnicon).'" alt="" />';

        }
        //$$column        = '<a href="'.make_link($column,$columndir).'">'.$cdesc."</a>$columnicon";
        $headings[$column] = '<a href="'.make_link($column,$columndir).'">'.$cdesc."</a>$columnicon";

    }
    $table = new html_table();
    //$table->head[]  = $$column;
    //$table->align[] = 'left';
    $table->align = array('left', 'left', 'left');
    $table->head = $headings;

    $table->width = "95%";
    foreach ($users as $user) {
        $newarr = array();
        foreach ($columns as $column => $cdesc) {
            $newarr[] = '<a href="'.make_link().'&amp;userid='.$user->id.'">'.$user->$column.'</a>';
        }
        $table->data[] = $newarr;
    }

    //print_table($table);
    echo html_writer::table($table);
}

echo $OUTPUT->box_end();
echo $OUTPUT->close_window_button();
echo $OUTPUT->footer();
?>