<?php
/**
 * A repository factory that is used to create an instance of a repository
 * plug-in.
 *
 * Note: shamelessly "borrowed" from /enrol/enrol.class.php
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
 * @package    repository
 * @subpackage elis_files
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2011 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

/*
 * This file was based on /files/index.php from Moodle, with the following
 * copyright and license:
 */
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 2004  Martin Dougiamas  http://moodle.com               //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

class repository_factory {

    function factory() {
        global $SESSION, $USER;

        if (file_exists(dirname(__FILE__).'/lib/ELIS_files.php')) {
            require_once(dirname(__FILE__).'/lib/ELIS_files.php');

            $class = "ELIS_files";

            if (!(isset($SESSION->repo))) {
                $SESSION->repo = new $class;
            } else {
                $SESSION->repo = unserialize(serialize($SESSION->repo));
            }

            return $SESSION->repo;
        } else {
            trigger_error(dirname(__FILE__).'/lib/ELIS_files.php does not exist');
            error(dirname(__FILE__).'/lib/ELIS_files.php does not exist');
        }
    }
}
