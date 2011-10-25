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
 * @copyright  (C) 2008-2011 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

defined('MOODLE_INTERNAL') || die();

$string['add'] = 'Add';
$string['add_class'] = 'Add Class Instance';
$string['add_coreq'] = 'Add co-requisites';
$string['add_coreq_to_curriculum'] = 'Add co-requisites to program';
$string['add_course'] = 'Add Course Description';
$string['add_curriculum'] = 'Add Program';
$string['add_grade'] = 'Add Grade';
$string['add_pmclass'] = 'Add Class Instance';
$string['add_prereq'] = 'Add prerequisites';
$string['add_prereq_to_curriculum'] = 'Add prerequisites to program';
$string['add_to_waitinglist'] = 'Add {$a->name}({$a->username}) to wait list?';
$string['add_track'] = 'Add Track';
$string['add_user'] = 'Add user';
$string['add_userset'] = 'Add User Set';
$string['add_usersetclassification'] = 'Add User Set Classification';
$string['added_corequisite'] = 'Added <b>{$a}</b> corequisite';
$string['added_corequisites'] = 'Added <b>{$a}</b> corequisites';
$string['added_prerequisite'] = 'Added <b>{$a}</b> prerequisite';
$string['added_prerequisites'] = 'Added <b>{$a}</b> prerequisites';
$string['added_to_waitlist_message'] = 'you have been added to the waitlist for class instance {$a->idnumber}';
$string['adding_completion_element'] = 'Adding Learning Objective';
$string['address'] = 'Address';
$string['address2'] = 'Address 2';
$string['admin_dashboard'] = 'Administrator Dashboard';
$string['all_items_assigned'] = 'All available items assigned.';
$string['all_users_already_enrolled'] = 'All users in track already enrolled.';
$string['and_all_children'] = '(and all children)';
$string['assign'] = 'Assign';
$string['assign_selected'] = 'Assign Selected';
$string['assigned'] = 'Assigned';
$string['assigntime'] = 'Assigned Time';
$string['association_clustercurriculum'] = 'Associate User Set';
$string['association_clustertrack'] = 'Associate User Set';
$string['association_curriculumcourse'] = 'Associate Program';
$string['association_info_group'] = 'Association Information';
$string['association_instructor'] = 'Associate Instructor';
$string['association_student'] = 'Associate Student'; // TBD
$string['association_trackassignment'] = 'Associate Track';
$string['association_usertrack'] = 'Associate User';
$string['autocreate'] = 'Auto Create Moodle course from template';
$string['auto_collapse_setting'] = 'Number of programs to display before automatically collapsing';
$string['auto_create_help'] = 'Moodle courses that are linked to ELIS class instances are marked as having been auto-created or created manually since ELIS 1.8.7. For courses created prior to 1.8.7, the auto-created status is unknown. This setting indicates whether these courses should be treated as having been auto-created or not.

Currently, this only affects the functionality for copying programs to a user set.';
$string['auto_create_setting'] = 'Moodle courses with unknown status treated as auto-created';
$string['auto_create_settings'] = 'Auto-create Settings';
$string['auto_idnumber_help'] = 'Automatically set a Moodle user\'s ID number to be the same as his/her username if he/she does not have one already set.

If a Moodle user does not have an ID number, then no corresponding user will be created in the Program Management system.

However, changing a user\'s ID number may result in duplicate users within the Program Management system, so this option should be turned off if users will be created in Moodle before they are assigned a permanent ID number.

In general, this option should be set to off unless a user\'s ID number will always be the same as his/her username. However, the default is on for backwards compatibility.';
$string['auto_idnumber_setting'] = 'Automatically assign an ID number to Moodle users without one';
$string['available_course_corequisites'] = 'Available Course Description Corequisites';
$string['available_course_prerequisites'] = 'Available Course Description Prerequisites';
$string['availablecourses'] = 'Program Overview';

$string['badusername'] = 'Specified username already exists, or must be specified or generated.';
$string['badidnumber'] = 'Specified ID number already exists.';
// TBD: associationpage.class.php::get_title_default()
$string['breadcrumb_trackassignmentpage'] = 'Assign Class Instances';
$string['breadcrumb_usertrackpage'] = 'Assign Tracks';
$string['breadcrumb_trackuserpage'] = 'Assign Users';
$string['breadcrumb_userclusterpage'] = 'Assign User Sets';
$string['breadcrumb_clusteruserpage'] = 'Assign Users';
$string['breadcrumb_clustertrackpage'] = 'Assign Tracks';
$string['breadcrumb_trackclusterpage'] = 'Assign User Sets';
$string['breadcrumb_clustercurriculumpage'] = 'Assign Programs';
$string['breadcrumb_curriculumclusterpage'] = 'Assign User Sets';
$string['breadcrumb_coursecurriculumpage'] = 'Assign Programs';
$string['breadcrumb_curriculumcoursepage'] = 'Assign Course Descriptions';
$string['breadcrumb_studentpage'] = 'Assign Students';
$string['breadcrumb_instructorpage'] = 'Assign Instructors';
$string['breadcrumb_studentcurriculumpage'] = 'Assign Programs';
$string['breadcrumb_studentcurriculumpage_unassign'] = 'Unassign Programs';
$string['breadcrumb_curtaginstancepage'] = 'Assign Tags';
$string['breadcrumb_clstaginstancepage'] = 'Assign Tags';
$string['breadcrumb_crstaginstancepage'] = 'Assign Tags';
$string['breadcrumb_curriculumstudentpage'] = 'Assign Students';
$string['breadcrumb_curriculumstudentpage_unassign'] = 'Unassign Students';
$string['breadcrumb_waitlistpage'] = 'Waiting List';
$string['browse'] = 'Browse';
$string['bulkedit_select_all'] = 'Select All';

$string['cert_border_help'] = 'The certificate border image is what gets displayed as the background for certificates in the program.
You can add more border images by uploading them to your moodledata directory under the directory: /elis/program/pix/certificate/borders/';
$string['cert_border_setting'] = 'Certificate border image';
$string['cert_seal_help'] = 'The certificate seal image is what gets displayed as the logo on certificates in the program.
You can add more seal images by uploading them to your moodledata directory under the directory: /elis/program/pix/certificate/seals/';
$string['cert_seal_setting'] = 'Certificate seal image';
$string['certificate_border_image'] = 'Certificate border image';
$string['certificate_certify'] = 'This is to certify that';
$string['certificate_date'] = 'on {$a}';
$string['certificate_expires'] = 'This certificate will expire on:';
$string['certificate_has_completed'] = 'has completed';
$string['certificate_seal_image'] = 'Certificate seal image';
$string['certificate_title'] = 'Certificate of Achievement';
$string['certificatelist'] = 'Certificate List';
$string['certificates'] = 'Certificates';
$string['certificates_earned'] = 'You have earned the following certificates:';
$string['certificates_none_earned'] = 'You have not earned any certificates yet.';

$string['choose_class_course'] = 'Choose class for course {$a}';
$string['chooseclass'] = 'Choose class';
$string['class'] = 'Class Instance';
$string['classcompletionreport'] = 'Class Instance Completion Report';
$string['classreportlinks'] = 'Reports';
$string['classrosterreport'] = 'Class Instance Roster Report';
$string['class_assigntrackhead'] = 'Assigned Tracks';
$string['class_attached_course'] = 'This class instance is already attached to the Moodle course';
$string['class_course'] = 'Course Description';
$string['class_enddate'] = 'End Date';
$string['class_endtime'] = 'End Time';
$string['class_idnumber'] = 'ID Number';
$string['class_limit'] = 'Class Limit';
$string['class_maxstudents'] = 'Max # of Students';
$string['class_moodle_course'] = 'Moodle Course';
$string['class_role_help'] = 'This is the default role to assign to a Program Management user in any class instances they create.
This type of role assignment will not take place for a particular class instance if that user is already permitted to edit that class instance.
To disable this functionality, select "N/A" from the list.';
$string['class_role_setting'] = 'Default Class Instance Role';
$string['class_size'] = 'Class Size';
$string['class_startdate'] = 'Start Date';
$string['class_starttime'] = 'Start Time';
$string['class_unassigntrackhead'] = 'Unassigned Tracks';
$string['clear'] = 'Clear';
$string['clustcpycurr'] = 'Program {$a->name} copied to {$a->newname}';
$string['cluster'] = 'User Set';
$string['clusters'] = 'User Sets';
$string['cluster_assigned'] = 'User subset assigned to user set';
$string['cluster_role_help'] = 'This is the default role to assign to a Program Management user in any user sets they create.
This type of role assignment will not take place for a particular user set if that user is already permitted to edit that user set.
To disable this functionality, select "N/A" from the list.';
$string['cluster_role_setting'] = 'Default User Set Role';
$string['cluster_subcluster_prompt'] = 'Assign existing user set as a user subset';
$string['cluster_tree_na'] = 'N/A';
$string['completed_label'] = 'Completed';
$string['completionform:completion_grade'] = 'Completion grade';
$string['completionform:completion_grade_help'] = '<p>Minimum grade the learner must received to identify the element as &ldquo;completed&rdquo;.</p>';
$string['completionform:course_idnumber'] = 'ID Number';
$string['completionform:course_idnumber_help'] = '<p>When an element is an activity within Moodle, this number should correspond to the id number of that activity in Moodle.</p>';
$string['completionform:course_name'] = 'Name';
$string['completionform:course_name_help'] = '<p>Element name.  Should correspond with the name of the element in Moodle, when Moodle is being used.</p>';
$string['completionform:course_syllabus'] = 'Description';
$string['completionform:course_syllabus_help'] = '<p>Description information about the element.</p>';
$string['completionform:required'] = 'Required';
$string['completionform:required_help'] = '<p>Is the learning objective required to complete the course description? Some objectives may be optional and therefore not required for course description completion.</p>';
$string['completion_description'] = 'Description';
$string['completion_elements'] = 'Learning Objectives';
$string['completion_idnumber'] = 'ID Number';
$string['completion_grade'] = 'Completion grade';
$string['completion_name'] = 'Name';
$string['completion_status'] = 'Completion Status';
$string['completion_time'] = 'Completion Time';
$string['confirm_bulk_inactive'] = 'Are you sure you want to mark the following users as inactive: {$a}?';
$string['confirm_bulk_delete'] = 'Are you sure you want to delete the following users: {$a}?';
$string['confirm_delete_association'] = 'Are you sure you want to delete this entry?';
$string['confirm_delete_category'] = 'Are you sure you want to delete the category named "{$a}"?  This will delete all fields in that category.';
$string['confirm_delete_class'] = 'Are you sure you want to delete the class instance {$a->idnumber}?';
$string['confirm_delete_completion'] = 'Are you sure you want to delete the learning objective "name: {$a}"?';
$string['confirm_delete_course'] = 'Are you sure you want to delete the course description named {$a->name} (ID number: {$a->idnumber})?';
$string['confirm_delete_curriculum'] = 'Are you sure you want to delete the program named {$a->name} (ID number: {$a->idnumber})?';
$string['confirm_delete_field'] = 'Are you sure you want to delete the {$a->datatype} field named "{$a->name}"?';
$string['confirm_delete_instructor'] = 'Are you sure you want to delete the instructor "name: {$a->name}"?';
$string['confirm_delete_pmclass'] = 'Are you sure you want to delete the class instance {$a->idnumber}?';
$string['confirm_delete_track'] = 'Are you sure you want to delete the track named {$a->idnumber}?';
$string['confirm_delete_user'] = 'Are you sure you want to delete the user named {$a->firstname} {$a->lastname} (ID number: {$a->idnumber})?';
$string['confirm_delete_userset'] = 'Are you sure you want to delete the User Set named {$a->name}?';
$string['confirm_delete_usersetclassification'] = 'Are you sure you want to delete the User Set classification named {$a->name}?';
$string['confirm_delete_with_usersubsets'] = 'Are you sure you want to delete the user set named {$a->name}?  This user set has {$a->subclusters} subset(s).';
$string['confirm_delete_with_usersubsets_and_descendants'] = 'Are you sure you want to delete the user set named {$a->name}?  This user set has {$a->subclusters} subset(s) and {$a->descendants} other descendant user sets.';
$string['confirm_waitlist'] = 'Are you sure to {$a->action} {$a->num} entries in the waiting list?';
$string['context_level_user'] = 'Program Management User';
$string['corequisites'] = 'Corequisite';
$string['cost'] = 'Cost';
$string['country'] = 'Country';
$string['course'] = 'Course Description';
$string['coursecatalog'] = 'Learning Plan';
$string['courseform:completion_grade'] = 'Completion grade';
$string['courseform:completion_grade_help'] = '<p>Minimum grade to complete/pass the course description.</p>';
$string['courseform:cost'] = 'Cost';
$string['courseform:cost_help'] = '<p>Registration fee (if any).</p>';
$string['courseform:course_idnumber'] = 'ID Number';
$string['courseform:course_idnumber_help'] = '<p>Course Description ID number; may be the same as the code, may be something compoletely different. This number can contain numbers, letters, spaces and special characters and will show up on reports.</p>';
$string['courseform:course_name'] = 'Name';
$string['courseform:course_name_help'] = '<p>Name of course description. A course description may have many &ldquo;class instances&rdquo; (or sometimes called sections). This is the name of the parent course description.</p>';
$string['courseform:course_syllabus'] = 'Description';
$string['courseform:course_syllabus_help'] = '<p>Course description.</p>';
$string['courseform:course_code'] = 'Code';
$string['courseform:course_code_help'] = '<p>Roughly corresponds to the &ldquo;short name&rdquo; in Moodle.</p>';
$string['courseform:course_version'] = 'Version';
$string['courseform:course_version_help'] = '<p>Version of course description being used. If you update your course descriptions periodically, the update date could be entered here.</p>';
$string['courseform:coursetemplate'] = 'Course Template in Moodle';
$string['courseform:coursetemplate_help'] = '<p>Moodle course template/master course, if applicable. This field is only necessary if master Moodle courses are being used to create new instances of course descriptions. This allows the Moodle course template to automatically be copied as a new course description when a new instance of a track is created.</p>';
$string['courseform:credits'] = 'Credits';
$string['courseform:credits_help'] = '<p>Number of credits the course description is worth. If credits are not being used, this field can be left blank.</p>';
$string['courseform:curriculum'] = 'Program';
$string['courseform:curriculum_help'] = '<p>Select the program the course description will be assigned to.  A course description can be assigned to more than one program by contol click.</p>';
$string['courseform:duration'] = 'Duration';
$string['courseform:duration_help'] = '<p>Number of units the course descriptions runs.</p>';
$string['courseform:environment'] = 'Environment';
$string['courseform:environment_help'] = '<p>Where/how is the course description delivered. Select from the choices available.  If no enviroments have been entered into the system, go to Program Administration block &gt; Information Elements &gt; Environments to enter environment options, such as online, face-to-face, blended, etc.</p>';
$string['courseform:length_description'] = 'Length Description';
$string['courseform:length_description_help'] = '<p>Defines the units for duration, such as days, weeks, months, semesters, etc.</p>';
$string['course_assigncurriculum'] = 'Assign Program';
$string['course_catalog_time_na'] = 'n/a';
$string['course_classes'] = 'Class Instances';
$string['course_curricula'] = 'Programs';
$string['course_code'] = 'Code';
$string['course_curricula'] = 'Programs';
$string['course_idnumber'] = 'ID Number';
$string['course_name'] = 'Name';
$string['course_role_help'] = 'This is the default role to assign to a Program Management user in any course descriptions they create.
This type of role assignment will not take place for a particular course description if that user is already permitted to edit that course description.
To disable this functionality, select "N/A" from the list.';
$string['course_role_setting'] = 'Default Course Description Role';
$string['course_syllabus'] = 'Description';
$string['course_version'] = 'Version';
$string['courses'] = 'Course Descriptions';
$string['coursetemplate'] = 'Course Template in Moodle';
$string['credits'] = 'Credits';
$string['credits_rec'] = 'Credits Rec\'vd.';
$string['crlm_admin_blk_settings'] = 'Program Management Block Settings';
$string['crlm_expire_setting'] = 'Enable program expiration';
$string['cron_notrun'] = '<b>Never!</b>';
$string['currentcourses'] = 'Current Classes';
$string['curricula'] = 'Programs';
$string['curriculaform:curriculum_description'] = 'Long description';
$string['curriculaform:curriculum_description_help'] = '<p>Description information about the program. A complete and thorough
description will help administrators, teachers and students know if this program is correct for them.</p>';
$string['curriculaform:curriculum_idnumber'] = 'ID Number';
$string['curriculaform:curriculum_idnumber_help'] = '<p>Program ID number. This number will display in reports.</p>';
$string['curriculaform:curriculum_name'] = 'Name';
$string['curriculaform:curriculum_name_help'] = '<p>Name of program.</p>';
$string['curriculaform:expiration'] = 'Expiration';
$string['curriculaform:expiration_help'] = '<p>The date on which credit for the program expires.<br/>
For example, 4y = every four years.<br/>
If the user\'s credit for the given program does not expire, this field should be left blank.<br/><br/>
The expiration date is printed on the Program report, the Individual User report, and on program certificates.<br/>
This expiration is informational only - credit is not removed from the system, but the student\'s transcript and certificate show the expiration date.</p>';
$string['curriculaform:priority'] = 'Display priority';
$string['curriculaform:priority_help'] = '<p>Determines the order in which programs are displayed from an enrolled user\'s perspective.
The lower the priority number, the higher the program will display on the user\'s listing.</p>';
$string['curriculaform:required_credits'] = 'Required Credits';
$string['curriculaform:required_credits_help'] = '<p>Number of credits the learner must receive before the program is
complete.  Each course description can be assigned a credit value. If credits are not being used this field can be left blank.</p>';
$string['curriculaform:time_to_complete'] = 'Time to complete';
$string['curriculaform:time_to_complete_help'] = '<p>The time a learner has to complete the program once they have been
assigned to the program. For example, 18m = 18 months. If there is not a time limit for the program, this field can be left blank.</p>';
$string['curriculum'] = 'Program';
$string['curriculumcourse'] = 'Course Description';
$string['curriculumcourse_assigncourse'] = 'Assign course description';
$string['curriculumcourse_position'] = 'Position';
$string['curriculumcourseform:course'] = 'Course Description';
$string['curriculumcourseform:course_help'] = '<p>The name of the Program Administration course description being associated with a program.</p>';
$string['curriculumcourseform:curriculum'] = 'Program';
$string['curriculumcourseform:curriculum_help'] = '<p>The name of the program being associated with a course description.</p>';
$string['curriculumcourseform:frequency'] = 'Frequency';
$string['curriculumcourseform:frequency_help'] = '<p>The frequency the course description must be repeated, if necessary.  For example,
4y = every four years. If the course description does not need to be repeated periodically, this field should be left blank.</p>
<p>This field is for information only, and does not affect the behaviour of the system.</p>';
$string['curriculumcourseform:position'] = 'Position';
$string['curriculumcourseform:position_help'] = '<p>Determines the order in which course descriptions are listed within this program. Course descriptions with lower position numbers are displayed first.</p>';
$string['curriculumcourseform:required'] = 'Required';
$string['curriculumcourseform:required_help'] = '<p>If enabled, completion of the associated course description is required in order for students to complete the selected program.</p>';
$string['curriculumcourseform:time_period'] = 'Timepreriod';
$string['curriculumcourseform:time_period_help'] = '<p>The units used in specifying the course description frequency.</p>';
$string['curriculum_expire_enrol_start'] = 'enrolled into a program';
$string['curriculum_expire_enrol_complete'] = 'completed a program';

$string['curriculum_idnumber'] = 'ID Number';
$string['curriculum_description'] = 'Long description';
$string['curriculum_name'] = 'Name';
$string['curriculum_reqcredits'] = ' Required Credits';
$string['curriculum_role_help'] = 'This is the default role to assign to a Program Management user in any programs they create.
This type of role assignment will not take place for a particular program if that user is already permitted to edit that program.
To disable this functionality, select "N/A" from the list.';
$string['curriculum_role_setting'] = 'Default Program Role';
$string['curriculum_shortdescription'] = 'Short description';
$string['curriculum_userid_mismatch'] = 'Your current user ID does not match the user ID for this curriculum completion.';
$string['customfields'] = 'Custom fields';

$string['dashboard'] = 'Dashboard';
$string['date'] = 'Date';
$string['datecompleted'] = 'Completion Date';
$string['date_completed'] = 'Date Completed';
$string['date_graded'] = 'Date Graded';
$string['defaultcls'] = 'Default Class Instance Settings';
$string['defaultcrs'] = 'Default Course Description Settings';
$string['default_role_settings'] = 'Default Role Assignments Settings';
$string['delete'] = 'Delete';
$string['delete_cancelled'] = 'Delete cancelled';
$string['deleted_corequisite'] = 'Deleted <b>{$a}</b> corequisite';
$string['deleted_corequisites'] = 'Deleted <b>{$a}</b> corequisites';
$string['deleted_prerequisite'] = 'Deleted <b>{$a}</b> prerequisite';
$string['deleted_prerequisites'] = 'Deleted <b>{$a}</b> prerequistes';
$string['delete_class'] = 'Delete Class Instance';
$string['delete_course'] = 'Delete Course Description';
$string['delete_curriculum'] = 'Delete Program';
$string['delete_label'] = 'Delete';
$string['delete_pmclass'] = 'Delete Class Instance';
$string['delete_track'] = 'Delete Track';
$string['delete_user'] = 'Delete user';
$string['delete_userset'] = 'Delete User Set';
$string['delete_usersetclassification'] = 'Delete User Set Classification';
$string['deleting_completion_element'] = 'Deleting Learning Objective';
$string['deletesubs'] = 'Delete user subsets';
$string['description'] = 'Description';
$string['detail'] = 'Detail';
$string['disable_cert_setting'] = 'Disable Certificates';
$string['duration'] = 'Duration';

$string['edit'] = 'Edit';
$string['editing_completion_element'] = 'Editing Learning Objective';
$string['edit_cancelled'] = 'Edit cancelled';
$string['edit_course_corequisites'] = 'Edit Course Description Corequisites';
$string['edit_course_prerequisites'] = 'Edit Course Description Prerequisites';
$string['edit_student_attendance'] = 'Edit Student Attendance';
$string['elis_config'] = 'ELIS Configuration';
$string['elis_doc_class_link'] = '<strong>Documentation for ELIS</strong> &mdash; we have over 200
pages of documentation for ELIS in our <a href="http://rlcommunity.remote-learner.net">ELIS Support Course</a>.
If you have problems accessing this course, please contact your sales representative.';
$string['elis_settings'] = 'ELIS Settings';
$string['elispmversion'] = '<strong>ELIS Program Manager Version:</strong> {$a}';
$string['email'] = 'Email address';
$string['email2'] = 'Email address 2';
$string['enrol'] = 'Enrol';
$string['enrol_all_users_now'] = 'Enrol all users from this track now';
$string['enrol_confirmation'] = 'you will be placed on a waiting list for this course. Are you sure  you would like to enrol in ({$a->coursename}){$a->classid}?';
$string['enrol_select_all'] = 'Select All';
$string['enrol_selected'] = 'Enrol Selected';
$string['enrole_sync_settings'] = 'Enrolment Role Sync Settings';
$string['enroled'] = 'Enrolled';
$string['enrolment'] = 'Enrolment';
$string['enrolment_time'] = 'Enrolment Time';
$string['enrolments'] = 'Enrolments';
$string['enrolstudents'] = 'Enrol Student'; // TBD (s) ?
$string['environment'] = 'Environment';
$string['error_bulk_delete'] = 'Error deleting users.';
$string['error_bulk_inactive'] = 'Error marking users as inactive.';
$string['error_curriculum_incomplete'] = 'Error: curriculum not completed.';
$string['error_date_range'] = 'Start date must be before the end date.';
$string['error_duration'] = 'Start time must be before the end time.';
$string['error_n_overenrol'] = 'The over enrol capability is required for this';
$string['error_not_timeformat'] = 'time not in proper format';
$string['error_not_durrationformat'] = 'durration not in proper format';
$string['error_not_using_elis_enrolment'] = 'The associated Moodle course is not using the ELIS enrolment plugin';
$string['error_waitlist_remove'] = 'Error removing users from waitlist.';
$string['error_waitlist_overenrol'] = 'Error over enrolling.';
$string['errorroleassign'] = 'Failed to assign role to Moodle course.';
$string['errorsynchronizeuser'] = 'Could not create an associated Moodle user.';
$string['existing_course_corequisites'] = 'Existing Course Description Corequisites';
$string['existing_course_prerequisites'] = 'Existing Course Description Prerequisites';
$string['exit'] = 'Exit';
$string['expiration'] = 'Expiration';
$string['expire_basis_setting'] = 'Calculate program expiration based on the time a student';

$string['failed'] = 'Failed';
$string['fax'] = 'Fax';
$string['female'] = 'Female';
$string['field_category_deleted'] = 'Deleted category {$a}';
$string['field_category_saved'] = 'Saved category {$a}';
$string['field_create_category'] = 'Create a new category';
$string['field_create_new'] = 'Create a new field';
$string['field_confirm_force_resync'] = 'Are you sure you want to force re-synchronization of custom user profile data with Moodle at this time?  This normally does not need to be done, but can be a useful function if the data is out of sync for any reason.  This may take a long time, depending on the number of users in the site.';
$string['field_datatype'] = 'Data type';
$string['field_datatype_bool'] = 'Boolean (yes/no)';
$string['field_datatype_char'] = 'Short text';
$string['field_datatype_int'] = 'Integer';
$string['field_datatype_num'] = 'Decimal number';
$string['field_datatype_text'] = 'Long text';
$string['field_deleted'] = 'Field successfully deleted';
$string['field_force_resync'] = 'Force re-sync of custom profile fields';
$string['field_from_moodle'] = 'Create field from Moodle field';
$string['field_multivalued'] = 'Multivalued';
$string['field_no_categories_defined'] = 'No categories defined';
$string['field_no_fields_defined'] = 'No fields defined';
$string['field_resyncing'] = 'Please wait.  Resynchronizing data with Moodle.';
$string['field_saved'] = 'Field saved';
$string['field_syncwithmoodle'] = 'Sync with Moodle';
$string['force_unenrol_in_moodle_setting'] = 'Force unenrolment in Moodle course';
$string['force_unenrol_in_moodle_help'] = 'If this setting is set, then ELIS will forcibly unenrol users from the associated Moodle course when they are unenrolled from the ELIS class instance, regardless of which enrolment plugin they used to enrol.

If this setting is unset, ELIS will only unenrol users who were originally enrolled via ELIS.';
$string['form_error'] = 'Selection page form error - expecting array!';
$string['frequency'] = 'Frequency';

$string['grade_element'] = 'Grade Element';
$string['grade_update_warning'] = '<div><strong>Note:</strong> updating grades for individual learning objectives on this screen will not automatically update the class instance grade or completion status.</div>';

$string['health_checking'] = "Checking...\n<ul>\n";
$string['health_check_link'] = 'The <a href="{$a->wwwroot}/elis/program/index.php?s=health">ELIS health page</a> may help diagnose potential problems with the site.';
$string['health_cluster_orphans'] = 'Orphaned user sets found!';
$string['health_cluster_orphansdesc'] = 'There are {$a->count} user subsets which have had their parent user sets deleted.<br/><ul>';
$string['health_cluster_orphansdescnone'] = 'There were no orphaned user sets found.';
$string['health_cluster_orphanssoln'] = 'From the command line change to the directory {$a}/elis/program/scripts<br/>
                Run the script fix_cluster_orphans.php to convert all user sets with missing parent user sets to top-level.';
$string['health_completion'] = 'Completion export';
$string['health_completiondesc'] = 'The Completion Export block, which conflicts with Integration Point, is present.';
$string['health_completionsoln'] = 'The completion export block should be automatically removed when the site is properly upgraded via CVS or git.
If it is still present, go to the <a href="{$a->wwwroot}/admin/blocks.php">Manage blocks</a> page and delete the completion export block,
and then remove the <tt>{$a->dirroot}/blocks/completion_export</tt> directory.';
$string['health_cron_block'] = '<b>Block \'{$a->name}\' last run:</b> {$a->lastcron}<br/>';
$string['health_cron_elis'] = '<b>ELIS scheduled tasks last run:</b> {$a}';
$string['health_cron_plugin'] = '<b>Plugin \'{$a->name}\' last run:</b> {$a->lastcron}<br/>';
$string['health_cron_title'] = 'Last cron run times';
$string['health_curriculum'] = 'Stale ELIS Course Description - Moodle Program record';
$string['health_curriculumdesc'] = 'There are {$a->count} records in the {$a->table} table referencing nonexistent ELIS course descriptions';
$string['health_curriculumsoln'] = 'These records need to be removed from the database.<br/>Suggested SQL:';
$string['health_duplicate'] = 'Duplicate enrolment records';
$string['health_duplicatedesc'] = 'There were {$a} duplicate enrolments records in the ELIS enrolments table.';
$string['health_duplicatesoln'] = 'The duplicate enrolments need to be removed directly from the database.  <b>DO NOT</b> try to remove them via the UI.<br/><br/>
Recommended to escalate to development for solution.';
$string['health_stale'] = 'Stale PM Class Instance - Moodle course description record';
$string['health_staledesc'] = 'There were {$a} records in the crlm_class_moodle table referencing nonexistent ELIS class instances.';
$string['health_stalesoln'] = 'These records need to be removed from the database.<br/>Suggested SQL:';
$string['health_trackcheck'] = 'Unassociated class instances found in tracks';
$string['health_trackcheckdesc'] = 'Found {$a} class instances that are attached to tracks when associated course descriptions are not attached to the program.';
$string['health_trackcheckdescnone'] = 'There were no issues found.';
$string['health_trackchecksoln'] = 'Need to remove all class instances in tracks that do not have an associated course descriptions in its associated program by running the script linked below.<br/><br/>' .
               '<a href="{$a}/elis/program/scripts/fix_track_classes.php">Fix this now</a>';
$string['health_user_sync'] = 'User Records Mismatch - Synchronize Users';
$string['health_user_syncdesc'] = 'There are {$a} extra user records for Moodle which don\'t exist for ELIS.';
$string['health_user_syncsoln'] = 'Users need to be synchronized by running the script which is linked below.<br/><br/>
                This process can take a long time, we recommend you run it during non-peak hours, and leave this window open until you see a success message.
                If the script times out (stops loading before indicating success), please open a support ticket to have this run for you.<br/><br/>
                <a href="{$a->wwwroot}/elis/program/scripts/migrate_moodle_users.php">Fix this now</a>';
$string['hidecourses'] = 'Hide Courses';

$string['icon_collapse_help'] = 'This setting determines the number of icons of each type to display in the Program Administration block.
This setting applies at the top level and also for nest entities.
Please set this value to a number greater than zero.';
$string['icon_collapse_setting'] = 'Number of entity icons to display before collapsing';
$string['id'] = 'ID';
$string['idnumber'] = 'ID Number';
$string['idnumber_already_used'] = 'ID Number is already in use';
$string['id_same_as_user'] = 'Same as username';
$string['if_class_full'] = 'note: if the class you wish to join is full you may still be placed on the waiting list';
$string['inactive'] = 'Inactive';
$string['incomplete_course_message'] = 'You have not completed the class instance {$a} before the end date';
$string['informationalelements'] = 'Information Elements';
$string['instructedcourses'] = 'Instructed Class Instances';
$string['instructor'] = 'Instructor';
$string['instructor_add'] = 'Add Instructor';
$string['instructor_assignment'] = 'Assignment Time';
$string['instructor_completion'] = 'Completion Time';
$string['instructor_deleted'] = 'Instructor: {$a->name} deleted.';
$string['instructor_idnumber'] = 'ID Number';
$string['instructor_name'] = 'Name';
$string['instructor_notdeleted'] = 'Instructor: {$a->name} not deleted.';
$string['instructor_role_help'] = 'The default role assigned to instructors when they are synchronized into Moodle.
This synchronization typically takes place when user is assigned as an instructor of a class instance or when a class instance becomes associated with a Moodle course.
If this setting not associated with a valid Moodle role, instructors will not be assigned roles when this synchonization takes place.';
$string['instructor_role_setting'] = 'Default Instructor Role';
$string['instructors'] = 'Instructors';
$string['interface_settings'] = 'Interface Settings';
$string['invalid_category_id'] = 'Invalid category ID';
$string['invalid_context_level'] = 'Invalid context level';
$string['invalid_curriculum_completion'] = 'Invalid curriculum completion.';
$string['invalid_field_id'] = 'Invalid field ID';
$string['invalid_objectid'] = 'Invalid object id: {$a->id}';
$string['invalidconfirm'] = 'Invalid confirmation code!';
$string['items_found'] = '{$a->num} items found.';

$string['lastname'] = 'Last Name';
$string['learningplan'] = 'Learning Plan';
$string['learningplanname'] = 'Learning Plan: {$a}';
$string['learningplanintro'] = 'Learning Plan: You have completed {$a->percent} ({$a->coursescomplete} of {$a->coursestotal}) of your current learning plan.';
$string['learningplanwelcome'] = 'Welcome {$a}';
$string['learning_plan_setting'] = 'Turn off learning plan';
$string['legacy_settings'] = 'Legacy Settings';
$string['legacy_show_inactive_users'] = 'Show inactive users';
$string['legacy_show_inactive_users_help'] = 'If this setting is enabled, inactive users will be displayed in user listings that cannot be filter based on the user\'s inactive flag.  This setting replicates the behaviour of previous versions of ELIS, and should not be used unless you specifically need the system to behave this way.';
$string['length_description'] = 'Length Description';
$string['lp_class_instructions'] = 'This listing indicates all programs you have been assigned to, as well as all class instances within those programs that you are currently enrolled in (automatically or manually).<br/><br/>
                                    This listing also includes class instances you are currently enrolled in that are not included in any programs, if applicable.';
$string['lp_curriculum_instructions'] = 'This listing indicates all curricula you have been assigned to, as well as all courses within those curricula, whether or not you are enrolled in them through classes.<br/><br/>
                                         This listing also includes the classes that you are enrolled in for the applicable courses.';
$string['lp_waitlist_instructions'] = 'This listing indicates all programs you have been assigned to, as well as all class instances tied to course descriptions in those programs that you are on the waiting list for.';

$string['makecurcourse'] = 'Make a program for this course description';
$string['male'] = 'Male';
$string['management'] = 'Management';
$string['manage_class'] = 'Manage Class Instances';
$string['manage_course'] = 'Manage Course Descriptions';
$string['manage_curriculum'] = 'Manage Programs';
$string['manage_custom_fields'] = 'Manage Custom Fields';
$string['manage_pmclass'] = 'Manage Class Instances';
$string['manage_student'] = 'Manage Students';
$string['manage_track'] = 'Manage Tracks';
$string['manage_user'] = 'Manage users';
$string['manage_userset'] = 'Manage User Sets';
$string['manage_usersetclassification'] = 'Manage User Set classifications';
$string['manageclasses'] = 'Manage Class Instances';
$string['manageclusters'] = 'Manage User Sets';
$string['managecourses'] = 'Manage Course Descriptions';
$string['managecurricula'] = 'Manage Programs';
$string['managetracks'] = 'Manage Tracks';
$string['manageusers'] = 'Manage Users';
$string['mark_inactive'] = 'Mark as inactive';
$string['message_nodestinationuser'] = 'No destination user specified for message system.';
$string['messageprovider:notify_pm'] = 'Program Management notifications';
$string['misc_category'] = 'Miscellaneous';
$string['moodle_field_sync_warning'] = '* <strong>Warning:</strong> this field is set to synchronize with Moodle user profile fields, but there is no Moodle profile field with the same short name.';
$string['moodlecourse'] = 'Moodle course';
$string['moodlecourseurl'] = 'Mooodle Course URL';
$string['moodleenrol'] = 'You have been removed from the waiting list for class instance {$a->idnumber}.
Please visit {$a->wwwroot}/course/enrol.php?id={$a->id} to complete your enrolment.';
$string['moodleenrol_subj'] = 'Ready to enrol in {$a->idnumber}.';

$string['na'] = 'NA';
$string['name'] = 'Name';
$string['name_lower_case'] = 'name';
$string['n_completed'] = 'Not Completed';
$string['n_users_enrolled'] = '{$a} user(s) enrolled.';
$string['no_associate_caps_class'] = 'You cannot associate any classes because do not have the elis/program:associate capability on any classes.';
$string['no_associate_caps_track'] = 'You cannot associate any tracks because do not have the elis/program:associate capability on any tracks.';
$string['no_classes_available'] = 'No classes currently available.  Please check again later.';
$string['no_completion_elements'] = 'There are no learning objectives defined.';
$string['no_courses'] = 'No course descriptions found';
$string['nocoursesinthiscurriculum'] = 'No Course Descriptions in this Program.';
$string['no_default_role'] = 'N/A';
$string['no_instructor_matching'] = 'No instructors matching {$a->match}';
$string['no_item_matching'] = 'No items matching {$a->match}';
$string['no_items_matching'] = 'No items matching ';
$string['no_items_selected'] = 'No items selected';
$string['no_moodlecourse'] = 'No Moodle courses on this site';
$string['no_student_matching'] = 'No students matching {$a->match}';
$string['no_user_matching'] = 'No users matching {$a->match}';
$string['no_users_matching'] = 'No users matching {$a->match}';
$string['noarchivedplan'] = 'You do not currently have any archived learning plans';
$string['noclassavail'] = 'No classes available yet';
$string['noclassyet'] = 'No Class Instance Yet';
$string['nolearningplan'] = 'You do not currently have a learning plan assigned';
$string['nomoodleuser'] = 'No moodle user found for specified user id.';
$string['nomoodleuser_filt'] = 'Does not have an associated Moodle user';
$string['noncurriculacourses'] = 'Non-program Course Descriptions';
$string['none'] = 'None';
$string['noroleselected'] = 'N/A';
$string['norolesexist'] = 'There are currently no roles that users can be assigned on this entity.';
$string['norolespermitted'] = 'There are currently no roles you have sufficient permissions to assign users on this entity. For further details, contact a site administrator.';
$string['notassignedtocurricula'] = 'You are not assigned to any program yet.';
$string['notelisuser'] = 'Error: must be a valid ELIS user!';
$string['notemplate'] = 'Could not auto-create Moodle course: no template defined in course description.  Created class instance without an associated Moodle course.';
$string['notice_class_deleted'] = 'Deleted the class instance {$a->idnumber}';
$string['notice_clusterassignment_deleted'] = 'Deleted the user-set/user association {$a->id}';
$string['notice_clustercurriculum_deleted'] = 'Deleted the user-set/track association {$a->id}';
$string['notice_clustertrack_deleted'] = 'Deleted the user-set/track association {$a->id}';
$string['notice_course_deleted'] = 'Deleted the course description named {$a->name} (ID number: {$a->idnumber})';
$string['notice_curriculum_deleted'] = 'Deleted the program named {$a->name} (ID number: {$a->idnumber})';
$string['notice_curriculumcourse_deleted'] = 'Deleted the program/course description association {$a->id}';
$string['notice_curriculumstudent_deleted'] = 'Deleted the program/student association {$a->id}';
$string['notice_pmclass_deleted'] = 'Deleted the class instance {$a->idnumber}';
$string['notice_user_deleted'] = 'Deleted the user named {$a->firstname} {$a->lastname} (ID number: {$a->idnumber})';
$string['notice_userset_deleted'] = 'Deleted the User Set named {$a->name}';
$string['notice_usersetclassification_deleted'] = 'Deleted the User Set classification named {$a->name}';
$string['notice_usertrack_deleted'] = 'Unenroled the user from track: {$a->trackid}';
$string['notice_track_deleted'] = 'Deleted the track {$a->idnumber}';
$string['notice_trackassignment_deleted'] = 'Deleted the track assignment: {$a->id}';
$string['notifications'] = 'Notifications';
$string['notificationssettings'] = 'Notifications Settings';
$string['notifications_notifyuser'] = 'User';
$string['notifications_notifyrole'] = 'User with {$a} capability at system context';
$string['notifications_notifysupervisor'] = 'User with {$a} capability at target user\'s context';
$string['notifyclasscompletedmessage'] = 'Message template for class instance completion';
$string['notifyclasscompletedmessagedef'] = "%%userenrolname%% has completed the class instance %%classname%%.";
$string['notifyclassenrolmessage'] = 'Message template for class instance enrolment';
$string['notifyclassenrolmessagedef'] = "%%userenrolname%% has been enrolled in the class instance %%classname%%.";
$string['notifyclassnotstarteddays'] = 'Number of days after enrolment to send message';
$string['notifyclassnotstartedmessage'] = 'Message template for class instance not started';
$string['notifyclassnotstartedmessagedef'] = "%%userenrolname%% has not started the class instance %%classname%%.";
$string['notifyclassnotcompleteddays'] = 'Number of days before class instance ends to send message';
$string['notifyclassnotcompletedmessage'] = 'Message template for class instance not completed';
$string['notifyclassnotcompletedmessagedef'] = "%%userenrolname%% has not completed the class instance %%classname%%.";
$string['notifycourserecurrencedays'] = 'Number of days before course description expires to send message';
$string['notifycourserecurrencemessage'] = 'Message template for course description expiration';
$string['notifycourserecurrencemessagedef'] = "%%userenrolname%% is due to re-take the course description %%coursename%%.";
$string['notifycurriculumcompletedmessage'] = 'Message template for program completion';
$string['notifycurriculumcompletedmessagedef'] = "%%userenrolname%% has completed the program %%curriculumname%%.";
$string['notifycurriculumnotcompleteddays'] = 'Number of days before program ends to send message';
$string['notifycurriculumnotcompletedmessage'] = 'Message template for program not completed';
$string['notifycurriculumnotcompletedmessagedef'] = "%%userenrolname%% has not completed the program %%curriculumname%%.";
$string['notifycurriculumrecurrencedays'] = 'Number of days before program expires to send message';
$string['notifycurriculumrecurrencemessage'] = 'Message template for program expiration';
$string['notifycurriculumrecurrencemessagedef'] = "%%userenrolname%% is due to re-take the program %%curriculumname%%.";
$string['notifytrackenrolmessage'] = 'Message template for track enrolment';
$string['notifytrackenrolmessagedef'] = "%%userenrolname%% has been enrolled in the track %%trackname%%.";
$string['notify_classcomplete'] = "Receive class instance completion notifications";
$string['notify_classenrol'] = "Receive class instance enrolment notifications";
$string['notify_classnotstart'] = "Receive class instance not started notifications";
$string['notify_classnotcomplete'] = "Receive class instance not completed notifications";
$string['notify_coursedue'] = "Receive course description due to begin notifications";
$string['notify_courserecurrence'] = "Receive course description expiration notifications";
$string['notify_curriculumcomplete'] = "Receive program completed notifications";
$string['notify_curriculumdue'] = "Receive program due to begin notifications";
$string['notify_curriculumnotcomplete'] = "Receive program not completed notifications";
$string['notify_curriculumrecurrence'] = "Receive program expiration notifications";
$string['notify_trackenrol'] = "Receive track enrolment notifications";
$string['nouser'] = 'No user found for specified user id.';
$string['nowenroled'] = 'You have been removed from the waiting list and placed in class instance {$a->idnum}.';
$string['num_class_found'] = '{$a->num} class instance(s) found';
$string['num_course_found'] = '{$a->num} course description(s) found';
$string['num_courses'] = 'Num Course Descriptions';
$string['num_curricula_assigned'] = '{$a->num} programs assigned';
$string['num_curricula_unassigned'] = '{$a->num} programs unassigned';
$string['num_curriculum_found'] = '{$a->num} programs found';
$string['num_curriculumstudent_found'] = '{$a->num} item(s) found';
$string['num_max_students'] = 'Max # of Students';
$string['num_not_shown'] = '{$a->num} not shown';
$string['num_pmclass_found'] = '{$a->num} class instance(s) found';
$string['num_student_found'] = '{$a->num} student(s) found';
$string['num_students_failed'] = 'number of students failed';
$string['num_students_not_complete'] = 'number of students not complete';
$string['num_students_passed'] = 'number of students passed';
$string['num_track_found'] = '{$a->num} track(s) found';
$string['num_user_found'] = '{$a->num} user(s) found';
$string['num_users_assigned'] = '{$a->num} users assigned';
$string['num_users_unassigned'] = '{$a->num} users unassigned';
$string['num_usersetclassification_found'] = '{$a->num} User Set classification(s) found';
$string['num_userset_found'] = '{$a->num} User Set(s) found';
$string['num_waitlist'] = 'number of students in the waiting list';
$string['numselected'] = '{$a->num} currently selected';

$string['o_active'] = 'Only active';
$string['o_inactive'] = 'Only inactive';
$string['onenroledlist'] = 'You are currently enrolled';
$string['onfailed'] = 'You failed this course';
$string['onpassed'] = 'You passed this course';
$string['onwaitlist'] = 'You are on the waiting list';
$string['othercourses'] = 'Other Class Instances';
$string['over_enrol'] = 'Over Enrol';

$string['passed'] = 'Passed';
$string['phone2'] = 'Phone 2';
$string['pluginname'] = 'ELIS Program';
$string['pm_date_format'] = 'M j, Y';
$string['pmclassform:class_idnumber'] = 'ID Number';
$string['pmclassform:class_idnumber_help'] = '<p>Class Instance ID number.</p>';
$string['pmclassform:class_startdate'] = 'Start Date';
$string['pmclassform:class_startdate_help'] = '<p>Enter the course description start and end date, if applicable.</p>';
$string['pmclassform:class_starttime'] = 'Start Time';
$string['pmclassform:class_starttime_help'] = '<p>Enter the course description start and end time, if applicable.  This is appropriate for synchronous online sessions, as well as face-to-face classes.</p>';
$string['pmclassform:class_maxstudents'] = 'Max # of Students';
$string['pmclassform:class_maxstudents_help'] = '';
$string['pmclassform:class_unassigntrackhead'] = 'Unassigned Tracks';
$string['pmclassform:class_unassigntrackhead_help'] = '<p>If tracks have been created in the system, tracks will be displayed here. If this class instance should be included in a track, select the appropriate track.</p>';
$string['pmclassform:course'] = 'Course Description';
$string['pmclassform:course_help'] = '<p>Select the course description this class instance is an instance of. The drop down menu will show all course descriptions created in the system.</p>';
$string['pmclassform:environment'] = 'Environment';
$string['pmclassform:environment_help'] = '<p>Select the appropriate environment from the drop down menu. If no
environments have been entered into the system, they can be entered by going to
Program Administration &gt; Information Elements &gt; Environments.</p>';
$string['pmclassform:moodlecourse'] = 'Moodle course';
$string['pmclassform:moodlecourse_help'] = '<p>The Moodle course that this class instance is attached to and is an instance of.</p>';
$string['pmclassform:waitlistenrol'] = 'Auto enrol from waitlist';
$string['pmclassform:waitlistenrol_help'] = '<p>on to automatically enrol students from the waitlist into the course description when an erolled student completes (passes or fails) the course description.</p>';
$string['pmclass_delete_warning'] = 'Warning!  Deleting this class instance will also delete all stored enrolment information for the class instance.';
$string['pmclass_delete_warning_continue'] = 'I understand all enrolments for the class instance will be deleted, continue ...';
$string['position'] = 'Position';
$string['postalcode'] = 'Postal code';
$string['prerequisites'] = 'Prerequisite';
$string['priority'] = 'Display priority';
$string['progman'] = 'Program Manager';
$string['program'] = 'ELIS Program'; // accesslib.php::get_contextlevel_name()
$string['program:assign_class_instructor'] = 'Manage class instance instructor assignments';
$string['program:assign_userset_user_class_instructor'] = 'Manage User Set\'s users\' class instance instructor assignments';
$string['program:associate'] = 'Associate program management items';
$string['program:class_create'] = 'Create class instance';
$string['program:class_delete'] = 'Delete class instance';
$string['program:class_edit'] = 'Edit class instance';
$string['program:class_enrol'] = 'Manage class instance enrolments';
$string['program:class_enrol_userset_user'] = 'Manage User Set\'s users\' class instance enrolments';
$string['program:class_view'] = 'View class instance';
$string['program:config'] = 'Configure program management settings';
$string['program:course_create'] = 'Create course description';
$string['program:course_delete'] = 'Delete course description';
$string['program:course_edit'] = 'Edit course description';
$string['program:course_view'] = 'View course description';
$string['program:manage'] = 'Manage Program Management system (Deprecated)';
$string['program:managefiles'] = 'Manage Program files';
$string['program:notify_classcomplete'] = "Receive class completion notifications";
$string['program:notify_classenrol'] = "Receive class enrolment notifications";
$string['program:notify_classnotcomplete'] = "Receive class not completed notifications";
$string['program:notify_classnotstart'] = "Receive class not started notifications";
$string['program:notify_coursedue'] = "Receive course due to begin notifications";
$string['program:notify_courserecurrence'] = "Receive course expiration notifications";
$string['program:notify_programcomplete'] = "Receive program completed notifications";
$string['program:notify_programdue'] = "Receive program due to begin notifications";
$string['program:notify_programnotcomplete'] = "Receive program not completed notifications";
$string['program:notify_programrecurrence'] = "Receive program expiration notifications";
$string['program:notify_trackenrol'] = "Receive track enrolment notifications";
$string['program:overrideclasslimit'] = 'Can over enrol a class';
$string['program:program_create'] = 'Create program';
$string['program:program_delete'] = 'Delete program';
$string['program:program_edit'] = 'Edit program';
$string['program:program_enrol'] = 'Manage program enrolments';
$string['program:program_enrol_userset_user'] = 'Manage User Set\'s users\' program enrolments';
$string['program:program_view'] = 'View program';
$string['program:track_create'] = 'Create track';
$string['program:track_delete'] = 'Delete track';
$string['program:track_edit'] = 'Edit track';
$string['program:track_enrol'] = 'Manage track enrolments';
$string['program:track_enrol_userset_user'] = 'Manage User Set\'s users\' track enrolments';
$string['program:track_view'] = 'View track';
$string['program:user_create'] = 'Create user';
$string['program:user_delete'] = 'Delete user';
$string['program:user_edit'] = 'Edit user';
$string['program:user_view'] = 'View user';
$string['program:userset_create'] = 'Create User Set';
$string['program:userset_delete'] = 'Delete User Set';
$string['program:userset_edit'] = 'Edit User Set';
$string['program:userset_enrol'] = 'Manage User Set membership';
$string['program:userset_enrol_userset_user'] = 'Manage User Set\'s users\' user subset membership';
$string['program:userset_role_assign_userset_users'] = 'Only assign roles in a User Set to User Set members';
$string['program:userset_view'] = 'View User Set';
$string['program:viewcoursecatalog'] = 'Can view learning plan';
$string['program:viewgroupreports'] = 'Can view reports for own group';
$string['program:viewownreports'] = 'Can view own reports';
$string['program:viewreports'] = 'Can view reports for all users';
$string['program:viewusers'] = 'Can view user profiles';
$string['program_copy_mdlcrs_copyalways'] = 'Always copy';
$string['program_copy_mdlcrs_copyautocreated'] = 'Copy auto-created course descriptions';
$string['program_copy_mdlcrs_autocreatenew'] = 'Auto-create from template';
$string['program_copy_mdlcrs_link'] = 'Link to existing course description';
$string['program_display'] = 'Display';
$string['program_info_group'] = 'Program Information';
$string['program_name'] = 'Name';
$string['program_numcourses'] = 'Num Course Descriptions';
$string['program_reqcredits'] = ' Required Credits';
$string['programmanagement'] = 'Program Management';
$string['promotesubs'] = 'Promote user subsets to top-level user sets';

$string['record_not_created'] = 'Record not created.';
$string['record_not_created_reason'] = 'Record not created. Reason: {$a->message}';
$string['record_not_updated'] = 'Record not updated. Reason: {$a->message}';
$string['redirect_dashbrd_setting'] = 'Redirect users accessing My Moodle to the dashboard';
$string['registered_date'] = 'Registered date';
$string['remove_coreq'] = 'Remove co-requisites';
$string['remove_prereq'] = 'Remove prerequisites';
$string['reports'] = 'Reports';
$string['required'] = 'Required';
$string['required_credits'] = 'Required Credits';
$string['required_field'] = 'Error: {$a} is a required field';

$string['save_enrolment_changes'] = 'Save Changes';
$string['saved'] = 'saved';
$string['score'] = 'Score';
$string['search'] = 'Search';
$string['selectaclass'] = 'Select a class';
$string['selectacourse'] = 'Select a course';
$string['selectedonly'] = 'Show selected items only';
$string['selecttemplate'] = 'No template course selected.  In order to browse ID numbers, you must select a course template in the "Edit" tab.';
$string['showallitems'] = 'Show all items';
$string['show_all_users'] = 'Show All Users';
$string['showcourses'] = 'Show Courses';
$string['showinactive'] = 'Show inactive';
$string['site_not_defined'] = 'Site is not defined';
$string['student_credits'] = 'Credits';
$string['student_deleteconfirm'] = 'Are you sure you want to unenrol the student name: {$a->name} ?<br />'.
                                   'NOTE: This will delete all records for this student in this class instance and will unenrol them from any connected Moodle course!';
$string['student_email'] = 'Email'; // TBD
$string['student_grade'] = 'Grade';
$string['student_id'] = 'ID'; // TBD
$string['student_idnumber'] = 'ID Number';
$string['student_locked'] = 'Locked';
$string['student_name'] = 'Student Name';
$string['student_name_1'] = 'Name';
$string['student_status'] = 'Status';
$string['studentnotunenrolled'] = 'Student: {$a->name} not unenrolled.';
$string['students'] = 'Students';
$string['studentunenrolled'] = 'Student: {$a->name} unenrolled.';
$string['subplugintype_pmplugins_plural'] = 'General plugins';
$string['subplugintype_usersetenrol_plural'] = 'User Set enrolment methods';
$string['success_bulk_delete'] = 'Successfully deleted users.';
$string['success_bulk_inactive'] = 'Successfully marked users as inactive.';
$string['success_waitlist_remove'] = 'Successfully removed from waitlist.';
$string['success_waitlist_overenrol'] = 'Successfully over enrolled.';
$string['sync_instructor_role_help'] = 'If you select a role here, then any user with this role in an ELIS class instance will be assigned as an instructor in the class instance.';
$string['sync_instructor_role_setting'] = 'Instructor Role';
$string['sync_student_role_help'] = 'If you select a role here, then any user with this role in an ELIS class instance will be enrolled as a student in the class instance.';
$string['sync_student_role_setting'] = 'Student Role';

$string['tab_archived_learning_plans'] = 'Archived Learning Plans';
$string['tab_current_learning_plans'] = 'Current Learning Plans';
$string['tag_custom_data'] = 'Custom Data for Tag {$a}';
$string['tag_name'] = 'Name';
$string['tags'] = 'Tags';
$string['timecreated'] = 'Creation time';
$string['timeofday'] = 'Time of Day';
$string['time_12h_setting'] = 'Display time selection in a 12 hour format';
$string['time_period'] = 'Timeperiod';
$string['time_settings'] = 'Time Settings';
$string['tips_time_format'] = "The format of this is ' *h, *d, *w, *m, *y ' (representing hours, days, weeks, months and years - where * can be any number) Each format must be separated by a comma";
$string['time_to_complete'] = 'Time to complete';
$string['top_clusters_help'] = 'This setting controls whether existing user sets are listed at the top level of the Program Administration block.
When changing the value of this setting, please navigate to another page to determine whether this functionality is working as expected.';
$string['top_clusters_setting'] = 'Display User Sets as the Top Level';
$string['top_curricula_help'] = 'This setting controls whether existing programs are listed at the top level of the Program Administration block.
When changing the value of this setting, please navigate to another page to determine whether this functionality is working as expected.';
$string['top_curricula_setting'] = 'Display Programs at the Top Level';
$string['track'] = 'Track';
$string['trackform:curriculum_curid'] = 'Program';
$string['trackform:curriculum_curid_help'] = '<p>The program this track is an instance or replica of.</p>';;
$string['trackform:curriculum_curidstatic'] = 'Program';
$string['trackform:curriculum_curidstatic_help'] = '<p>The program this track is an instance or replica of.</p>';
$string['trackform:track_autocreate'] = 'Create all class instances';
$string['trackform:track_autocreate_help'] = '<p>Enter the course description start and end date, if applicable.</p>';
$string['trackassignmentform:track_autoenrol'] = 'Auto-enrol';
$string['trackassignmentform:track_autoenrol_help'] = '<p>Auto enrol into this track.</p>';
$string['trackassignmentform:track_autoenrol_long'] = 'Auto-enrol users into this class instance when they are added to this track';
$string['trackform:curriculum_curid'] = 'Program';
$string['trackform:curriculum_curid_help'] = '<p>The program this track is an instance or replica of.</p>';
$string['trackform:curriculum_curidstatic'] = 'Program';
$string['trackform:curriculum_curidstatic_help'] = '<p>The program this track is an instance or replica of.</p>';
$string['trackform:track_autocreate'] = 'Create all class instances';
$string['trackform:track_autocreate_help'] = '<p>Enter the course description start and end date, if applicable.</p>';
$string['trackform:track_description'] = 'Description';
$string['trackform:track_description_help'] = '<p>Description of the track.</p>';
$string['trackform:track_idnumber'] = 'ID Number';
$string['trackform:track_idnumber_help'] = '<p>Enter an id number for the track. This number will appear in the class instance id number for each class instance which is a part of the track.</p>';
$string['trackform:track_name'] = 'Name';
$string['trackform:track_name_help'] = '<p>Name of the track.</p>';
$string['trackform:track_startdate'] = 'Start Date';
$string['trackform:track_startdate_help'] = '<p>Start and end date for the track, if applicable.</p>';
$string['tracks'] = 'Tracks';
$string['trackuserset_auto_enrol'] = 'Auto-enrol';
$string['track_assign_users'] = 'Assign users';
$string['track_autocreate'] = 'Create all class instances';
$string['track_autocreate_button'] = 'Auto-create class instances';
$string['track_auto_enrol'] = 'Auto-enrol';
$string['track_classes'] = 'Class Instances';
$string['track_click_user_enrol_track'] = 'Click on a user to enrol him/her in the track.';
$string['track_curriculumid'] = 'Program';
$string['track_description'] = 'Description';
$string['track_edit'] = 'Edit Track';
$string['track_enddate'] = 'End Date';
$string['track_idnumber'] = 'ID Number';
$string['track_info_group'] = 'Track Information';
$string['track_maxstudents'] = 'Max Students';
$string['track_name'] = 'Name';
$string['track_no_matching_users'] = 'No matching users.';
$string['track_num_classes'] = 'Number of class instances';
$string['track_parcur'] = 'Parent program';
$string['track_role_help'] = 'This is the default role to assign to a Program Management user in any tracks they create.
This type of role assignment will not take place for a particular track if that user is already permitted to edit that track.
To disable this functionality, select "N/A" from the list.';
$string['track_role_setting'] = 'Default Track Role';
$string['track_settings'] = 'Track Settings';
$string['track_startdate'] = 'Start Date';
$string['track_success_autocreate'] = 'Auto-created class instances for this track';

$string['unassign'] = 'unassign';
$string['unassigned'] = 'Unassigned';
$string['unassignroles'] = 'Unassign roles';
$string['unenrol'] = 'Unenrol';
$string['unsatisfiedprereqs'] = 'One or more prerequisites are not completed yet.';
$string['update_assignment'] = 'Update Assignment';
$string['update_enrolment'] = 'Update Enrolment';
$string['update_grade'] = 'Update Grade';
$string['user'] = 'User';
$string['user_comments'] = 'Comments';
$string['user_comments_help'] = 'Any free-form comments you would like to record about yourself, and comments about any other appropriate subject matter.';
$string['user_inactive'] = 'Inactive';
$string['user_inactive_help'] = 'Flags the user as inactive when checked.  Does not restrict the user\'s actions within this site, but can be used by administrators on an informational basis.';
$string['user_language'] = 'Language';
$string['user_language_help'] = 'The user\'s primary language. This field is primarily used in synchronizing information between Program Management and Moodle users.';
$string['user_notes'] = 'Notes';
$string['user_notes_help'] = 'Any additional notes you would like to record.';
$string['user_settings'] = 'User Settings';
$string['user_transfercredits'] = 'Transfer credits';
$string['user_transfercredits_help'] = 'An informational field that indicates the number of credits you possess that were transferred from an external institution.';
$string['user_waitlisted'] = 'user added to waitlist';
$string['user_waitlisted_msg'] = 'user with idnumber {$a->user} has been added to the waitlist for class instances {$a->pmclass}';
$string['users'] = 'Users';
$string['userbirthdate'] = 'Birth date';
$string['userdef_tracks_setting'] = 'Turn off user defined tracks';
$string['usergender'] = 'Gender';
$string['usermi'] = 'Middle initials';
$string['useridnumber'] = 'ID number';
$string['useridnumber_help'] = 'An id number is a unique value used to identify you within your organization.
It also serves as way to tie Program Management users to Moodle users.';
$string['users_assigned_to_role'] = '{$a} users assigned to role';
$string['users_removed_from_role'] = '{$a} users removed from role';
$string['usersetprogramform:autoenrol'] = 'Auto-enrol';
$string['usersetprogramform:autoenrol_help'] = '<p>If this box is checked then new users will be enrolled in this program when they are added to this User Set.</p>';
$string['usersetprogramform_auto_enrol'] = 'Auto-enrol users into this program when they are added to this User Set';
$string['usersetprogram_auto_enrol'] = 'Auto-enrol';
$string['usersetprogram_unassociated'] = 'Unassociated';
$string['userset_addcurr_instruction'] = 'Use the add drop down to LINK this User Set to a program.';
$string['userset_classification'] = 'User Set Classification';
$string['userset_cpyclustname'] = 'User Set';
$string['userset_cpycurname'] = 'Program';
$string['userset_cpycurr'] = 'Copy Program';
$string['userset_cpycurr_instruction'] = 'Use the copy button to make a copy of a program and link it to this user set.';
$string['userset_cpyadd'] = 'Add';
$string['userset_cpytrkcpy'] = 'Copy Tracks';
$string['userset_cpycrscpy'] = 'Copy Course Descriptions';
$string['userset_cpyclscpy'] = 'Copy Class Instances';
$string['userset_cpymdlclscpy'] = 'Copy Moodle Courses';
$string['userset_idnumber'] = 'ID Number';
$string['userset_info_group'] = 'User Set Information';
$string['userset_name'] = 'Name';
$string['userset_name_help'] = 'Name of the User Set';
$string['userset_description'] = 'Description';
$string['userset_description_help'] = 'Description of the User Set.';
$string['userset_parent'] = 'Parent User Set';
$string['userset_parent_help'] = 'Parent User Set of this User Set.  "Top Level" indicates no parent User Set.';
$string['userset_saveexit'] = 'Save and Exit';
$string['userset_top_level'] = 'Top level';
$string['userset_user_assigned'] = 'Assigned {$a} user(s) to the User Set.';
$string['userset_userassociation'] = 'User association';
$string['usersettrack_autoenrol'] = 'Auto-enrol';
$string['usersettrack_auto_enrol'] = 'Auto-enrol users into this track when they are added to this User Set';
$string['usersettrackform:autoenrol'] = 'Auto-enrol';
$string['usersettrackform:autoenrol_help'] = '<p>If this box is checked then new users will be enrolled in this track when they are added to this User Set.</p>';
$string['usersfound'] = '{$a} users found';
$string['usersubsets'] = 'User Subsets';

$string['waiting'] = 'Waiting';
$string['waitinglistform_title'] = 'Class Instance is full';
$string['waitlist'] = 'waitlist';
$string['waitlist_size'] = 'Waitlist Size';
$string['waitlistcourses'] = 'Waitlist';
$string['waitlistenrol'] = 'Auto enrol from waitlist';

