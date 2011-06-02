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
 * @subpackage core
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2008-2010 Remote Learner.net Inc http://www.remote-learner.net
 *
 */

require_once(elis::lib('data/data_object.class.php'));

/**
 * Custom fields.
 */
class field extends elis_data_object {
    const TABLE = 'elis_field';

    protected $_dbfield_shortname;
    protected $_dbfield_name;
    protected $_dbfield_datatype;
    protected $_dbfield_description;
    protected $_dbfield_categoryid;
    protected $_dbfield_sortorder;
    protected $_dbfield_forceunique;
    protected $_dbfield_multivalued;
    protected $_dbfield_params;

    const CHECKBOX = 'checkbox';
    const MENU = 'menu';
    const TEXT = 'text';
    const TEXTAREA = 'textarea';

    public function __construct($src=false, $field_map=null, array $associations=array(),
                                $from_db=false, array $extradatafields=array(),
                                moodle_database $database=null) {
        parent::__construct($src, $field_map, $associations, $from_db, $extradatafields, $database);

        if (empty($this->params)) {
            $this->params = serialize(array());
        }
    }

    /**
     * Magic getter to get parameter values
     */
    public function __get($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            return $params[$paramname];
        }
        if ($name == 'owners') {
            $this->owners = field_owner::get_for_field($this);
            return $this->owners;
        }

        return parent::__get($name);
    }

    /**
     * Magic setter to set parameter values
     */
    public function __set($name, $value) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            $params[$paramname] = $value;
            $this->params = serialize($params);
        } else {
            return parent::__set($name, $value);
        }
    }

    /**
     * Magic isset to test parameter values
     */
    public function __isset($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            return isset($params[$paramname]);
        } else if ($name == 'owners') {
            return true;
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * Magic unset function for parameter values
     */
    public function __unset($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            unset($params[$paramname]);
            $this->params = serialize($params);
        } else {
            return parent::__unset($name);
        }
    }

    protected function _load_data_from_record($rec, $overwrite=false, $field_map=null, $from_db=false, array $extradatafields=array()) {
        parent::_load_data_from_record($rec, $overwrite, $field_map, $from_db, $extradatafields);

        $data = (array)$rec;
        foreach ($data as $key => $value) {
            if (strncmp($key, 'param_', 6) === 0) {
                $this->$key = $value;
            }
        }
    }

    /*
    function delete() {
        $result = parent::delete();
        // FIXME: delete all field data associated with this field
        return $result;
    }
    */

    /**
     * Gets the custom field types, along with their categories, for a given
     * context level.
     *
     * @param mixed $contextlevel the context level.  Either a numeric value,
     * or the name of the context level from the ELIS Program Manager
     */
    public static function get_for_context_level($contextlevel) {
        global $DB;
        if (!$contextlevel) {
            return array();
        }
        if (!is_numeric($contextlevel)) {
            $contextlevel = context_level_base::get_custom_context_level($contextlevel, 'elis_program');
        }
        if ($contextlevel == context_level_base::get_custom_context_level('user', 'elis_program')) {
            // need to include extra fields for PM users
            $sql = 'SELECT field.*, category.name AS categoryname, mfield.id AS mfieldid, owner.exclude AS syncwithmoodle
                      FROM {'.self::TABLE.'} field
                 LEFT JOIN {user_info_field} mfield ON field.shortname = mfield.shortname
                 LEFT JOIN {'.field_category::TABLE.'} category ON field.categoryid = category.id
                 LEFT JOIN {'.field_owner::TABLE.'} owner ON field.id = owner.fieldid AND owner.plugin = \'moodle_profile\'
                      JOIN {'.field_contextlevel::TABLE."} ctx ON ctx.fieldid = field.id AND ctx.contextlevel = {$contextlevel}
                  ORDER BY category.sortorder, field.sortorder";
        } else {
            $sql = 'SELECT field.*, category.name AS categoryname
                      FROM {'.self::TABLE.'} field
                 LEFT JOIN {'.field_category::TABLE.'} category ON field.categoryid = category.id
                      JOIN {'.field_contextlevel::TABLE."} ctx ON ctx.fieldid = field.id AND ctx.contextlevel = {$contextlevel}
                  ORDER BY category.sortorder, field.sortorder";
        }
        return new data_collection($DB->get_recordset_sql($sql), 'field', null, array(), true,
                                   array('categoryname', 'mfieldid', 'syncwithmoodle'));
    }

    /**
     * Get the custom field for a specified context level with a specified
     * short name.
     *
     * @param mixed $contextlevel the context level.  Either a numeric value,
     * or the name of the context level from the ELIS Program Manager
     * @param string $name the shortname of the field
     */
    public static function get_for_context_level_with_name($contextlevel, $name) {
        global $DB;
        if (!$contextlevel) {
            return false;
        }
        if (!is_numeric($contextlevel)) {
            $contextlevel = context_level_base::get_custom_context_level($contextlevel, 'elis_program');
        }
        $select = 'id IN (SELECT fctx.fieldid
                            FROM {'.field_context::TABLE."} fctx
                           WHERE fctx.contextlevel = {$contextlevel})
               AND shortname=?";
        return new field($DB->get_record_select(self::TABLE, $select, array($name)), null, array(), true);
    }

    /**
     * Get the storage data type for the field.
     */
    public function data_type() {
        switch ($this->datatype) {
            case 'int':
            case 'bool':
                return 'int';
                break;
            case 'num':
                return 'num';
                break;
            case 'char':
                return 'char';
                break;
            default:
                return 'text';
        }
    }

    /**
     * Get the database table used to store data for the field.
     */
    public function data_table() {
        return field_data::TABLE.'_'.$this->data_type();
    }

    /**
     * Makes sure that a custom field (identified by $field->shortname) exists
     * for the given context level.  If not, it will create a field, putting it
     * in the given category (identified by $category->name), creating it if
     * necessary.
     *
     * @param field $field a field object, specifying the field configuration
     * if a new field is created
     * @param mixed $contextlevel the context level
     * @param field_category $category a field_category object, specifying the
     * category configuration if a new category is created
     * @return object a field object
     */
    public static function ensure_field_exists_for_context_level(field $field, $contextlevel, field_category $category) {
        if (!is_numeric($contextlevel)) {
            $contextlevel = context_level_base::get_custom_context_level($contextlevel, 'elis_program');
        }

        // see if we need to create a new field
        $fields = self::get_for_context_level($contextlevel);
        if (!empty($fields)) {
            foreach ($fields as $f) {
                if ($f->shortname === $field->shortname) {
                    return $f;
                }
            }
        }

        // No existing field found.  See if we need to create a category for it
        $categories = field_category::get_for_context_level($contextlevel);
        $found = false;
        if (!empty($categories)) {
            foreach ($categories as $c) {
                if ($c->name === $category->name) {
                    $category = $found = $c;
                    break;
                }
            }
        }
        if (!$found) {
            // create the category
            $category->save();
            $categorycontext = new field_category_contextlevel();
            $categorycontext->categoryid = $category->id;
            $categorycontext->contextlevel = $contextlevel;
            $categorycontext->save();
        }

        // create the field
        $field->categoryid = $category->id;
        $field->save();
        $fieldcontext = new field_contextlevel();
        $fieldcontext->fieldid = $field->id;
        $fieldcontext->contextlevel = $contextlevel;
        $fieldcontext->save();

        return $field;
    }

}

/**
 * Field owners.
 */
class field_owner extends elis_data_object {
    const TABLE = 'elis_field_owner';

    protected $_dbfield_fieldid;
    protected $_dbfield_plugin;
    protected $_dbfield_exclude;
    protected $_dbfield_params;

    public function __construct($src=false, $field_map=null, array $associations=array(),
                                $from_db=false, array $extradatafields=array(),
                                moodle_database $database=null) {
        parent::__construct($src, $field_map, $associations, $from_db, $extradatafields, $database);

        if (empty($this->params)) {
            $this->params = serialize(array());
        }
    }

    /**
     * Magic getter to get parameter values
     */
    public function __get($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            return $params[$paramname];
        }

        return parent::__get($name);
    }

    /**
     * Magic setter to set parameter values
     */
    public function __set($name, $value) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            $params[$paramname] = $value;
            $this->params = serialize($params);
        } else {
            return parent::__set($name, $value);
        }
    }

    /**
     * Magic isset to test parameter values
     */
    public function __isset($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            return isset($params[$paramname]);
        } else {
            return parent::__isset($name, $value);
        }
    }

    /**
     * Magic unset function for parameter values
     */
    public function __unset($name) {
        if (strncmp($name, 'param_', 6) == 0) {
            $paramname = substr($name, 6);
            $params = unserialize($this->params);
            unset($params[$paramname]);
            $this->params = serialize($params);
        } else {
            return parent::__unset($name);
        }
    }

    /**
     * Get the owners for a given field
     * @param field the field to get the owners for
     */
    public static function get_for_field(field $field) {
        $owners = self::find(new field_filter('fieldid', $field->id));
        return $owners->to_array('plugin');
    }

    /**
     * Creates the owner record corresponding to the supplied field if it does not already exist
     *
     * @param   field   $field   The field to create the owner for
     * @param   string  $plugin  The plugin used for the owner field
     * @param   array   $params  Any additional parameters to pass to the owner record
     */
    public static function ensure_field_owner_exists(field $field, $plugin, array $params = array()) {
        $owners = $field->owners;
        if (!empty($owners[$plugin])) {
            return;
        }

        $owner = new field_owner();
        $owner->fieldid = $field->id;
        $owner->plugin = $plugin;
        $owner->params = serialize($params);
        $owner->save();
    }
}

/**
 * Field categories.
 */
class field_category extends elis_data_object {
    const TABLE = 'elis_field_categories';

    protected $_dbfield_name;
    protected $_dbfield_sortorder;

    public static function get_all() {
        return self::find(null, array('sortorder' => 'ASC'));
    }

    /**
     * Gets the custom field categories for a given context level.
     */
    public static function get_for_context_level($contextlevel) {
        if (!$contextlevel) {
            return array();
        }
        if (!is_numeric($contextlevel)) {
            $contextlevel = context_level_base::get_custom_context_level($contextlevel, 'elis_program');
        }
        return self::find(new join_filter('contextlevel',
                                          field_category_contextlevel::TABLE, 'categoryid',
                                          new field_filter('contextlevel', $contextlevel)),
                          array('sortorder' => 'ASC'));
    }

    /*
    function delete() {
        $result = parent::delete();
        // FIXME: delete all fields associated with this category
        return $result;
    }
    */
}

/**
 * Base class for field data.
 */
abstract class field_data extends elis_data_object {
    const TABLE = 'elis_field_data';

    protected $_dbfield_contextid;
    protected $_dbfield_fieldid;
    //protected $_dbfield_plugin;
    protected $_dbfield_data;

    /**
     * Gets the custom field data, along with their categories, for a given
     * context.  If a field value is not set, the default value will be given,
     * and the data id will be null.
     *
     * @param mixed $contextlevel the context level.  Either a numeric value,
     * or the name of the context level from the ELIS Program Manager
     *
     * @return array An array with items of the form fieldshortname => value,
     * where value is an array if the field is multivalued, or a single value
     * if not.
     */
    public static function get_for_context($context) {
        // find out which fields we have, and what tables to look for the values
        // in
        $fields = field::get_for_context_level($context->contextlevel);
        $fieldarray = array();
        $data_tables = array();
        foreach ($fields as $field) {
            $fieldarray[$field->id] = $field;
            $data_types[$field->data_type()] = true;
        }
        $fields = $fieldarray;

        // load the values from the database, and sort them into the fields
        $values = array();
        $default_values = array();
        foreach ($data_types as $datatype => $unused) {
            $fielddatatype = "field_data_{$record->data_type()}";
            $records = $fielddatatype::find(new OR_filter(new field_filter('contextid', $context->id),
                                                          new field_filter('contextid', null)));
            foreach ($records as $record) {
                if (!isset($fields[$record->fieldid]) || $fields[$record->fieldid]->data_type() != $datatype) {
                    // nonexistent field, or this data isn't supposed to come from this table
                    continue;
                }
                if ($record->contextid) {
                    if (!isset($values[$record->fieldid])) {
                        $values[$record->fieldid] = array();
                    }
                    $values[$record->fieldid][] = $record->data;
                } else {
                    if (!isset($default_values[$record->fieldid])) {
                        $default_values[$record->fieldid] = array();
                    }
                    $default_values[$record->fieldid][] = $record->data;
                }
            }
        }

        // create the final result
        $result = array();
        foreach ($fields as $field) {
            // If multivalued, copy the whole array; otherwise just copy the
            // first value.  If a value for the context is set, then use that
            // value; otherwise use the default value.
            if ($field->multivalued) {
                if (!empty($values[$field->id])) {
                    $result[$field->shortname] = $values[$field->id];
                } else if (!empty($default_values[$field->id])) {
                    $result[$field->shortname] = $default_values[$field->id];
                }
            } else {
                if (!empty($values[$field->id])) {
                    $result[$field->shortname] = $values[$field->id][0];
                } else if (!empty($default_values[$field->id])) {
                    $result[$field->shortname] = $default_values[$field->id][0];
                }
            }
        }
        return $result;
    }

    /**
     * Gets the custom field data for a specified context and field.  If a
     * field value is not set, the default value will be given.
     *
     * @param object $context the context to get the field data from
     * @param mixed $field the field shortname, or a field object
     */
    public static function get_for_context_and_field($context, $field) {
        if (is_string($field)) {
            $find = field::find(array(new field_filter('shortname', $field),
                                      new join_filter('id',
                                                      field_contextlevel::TABLE, 'fieldid',
                                                      new field_filter('contextlevel', $context->contextlevel))));
            foreach ($find as $rec) {
                $field = $rec;
            }
            if (is_string($field)) {
                // no field found
                return null;
            }
        }
        $fielddatatype = "field_data_{$field->data_type()}";
        if ($context) {
            $filter = array();
            $filter[] = new field_filter('contextid', $context->id);
            $filter[] = new field_filter('fieldid', $field->id);
            $count = $fielddatatype::count($fielddatatype, $filter);
            if ($count) {
                return $fielddatatype::find($filter);
            }
        }
        return $fielddatatype::find(array(new field_filter('contextid', null),
                                          new field_filter('fieldid', $field->id)));
    }

    /**
     * Sets the custom field data for a specified context and field.
     *
     * @param object $context the context to set the data for
     * @param field $field the field object to set the data for
     * @param mixed $data a single value or an array depending on whether
     *        $field is multivalued or not
     * @param string $plugin
     * @return boolean whether or not the data was modified
     */
    public static function set_for_context_and_field($context, field $field, $data) {
        global $DB;
        if ($context) {
            $contextid = $context->id;
        } else {
            $contextid = null;
        }
        $data_table = $field->data_table();
        // FIXME: check exclude, unique, etc
        if ($field->multivalued) {
            // find what data already exists
            $records = self::get_for_context_and_field($context, $field);
            $records = $records ? $records : array();
            $todelete = array();
            $existing = array();
            foreach ($records as $rec) {
                if (in_array($rec->data, $data)) {
                    $existing[] = $rec->data;
                } else {
                    $todelete[] = $rec;
                }
            }
            // delete obsolete data
            foreach ($todelete as $rec) {
                $rec->delete();
            }
            // add new data
            $toadd = array_diff($data, $existing);
            foreach ($toadd as $value) {
                $rec = new field_data(false, $field->data_type());
                $rec->contextid = $contextid;
                $rec->fieldid = $field->id;
                $rec->data = $value;
                $rec->save();
            }
            return !empty($toadd) || !empty($todelete);
        } else {
            if (($rec = $DB->get_record($data_table, array('contextid' => $contextid, 'fieldid' => $field->id)))) {
                $fielddata = new field_data($rec, $field->data_type());
                if ($data === null) {
                    $fielddata->delete();
                    return true;
                }
                if ($fielddata->data == $data) {
                    return false;
                }
                $fielddata->contextid = $contextid; // needed, or else NULL becomes 0
                $fielddata->data = $data;
                $fielddata->update();
                return true;
            } else if ($data !== null) {
                $rec = new field_data(false, $field->data_type());
                $rec->contextid = $contextid;
                $rec->fieldid = $field->id;
                $rec->data = $data;
                $rec->add();
                return true;
            }
        }
    }

    /**
     * Convenience function for use by data_object objects
     *
     * @param mixed $contextlevel the context level.  Either a numeric value,
     * or the name of the context level from the ELIS Program Manager
     * @param object $record the data_object to fetch the field values from
     */
    public function set_for_context_from_datarecord($contextlevel, $record) {
        if (!isnumeric($contextlevel)) {
            $contextlevel = context_level_base::get_custom_context_level($level, 'elis_program');
            if (!$contextlevel) {
                // context levels not set up -- we must be in initial installation,
                // so no fields set up
                return true;
            }
        }
        $context = get_context_instance($contextlevel, $record->id);
        $fields = field::get_for_context_level($contextlevel);
        $fields = $fields ? $fields : array();
        foreach ($fields as $field) {
            $fieldname = "field_{$field->shortname}";
            if (isset($record->$fieldname)) {
                self::set_for_context_and_field($context, $field, $record->$fieldname);
            }
        }

        return true;
    }
}

/**
 * Integer field data.
 */
class field_data_int extends field_data {
    const TABLE = 'elis_field_data_int';
}

/**
 * Floating point field data.
 */
class field_data_num extends field_data {
    const TABLE = 'elis_field_data_num';
}

/**
 * Character field data.
 */
class field_data_char extends field_data {
    const TABLE = 'elis_field_data_char';
}

/**
 * Text field data.
 */
class field_data_text extends field_data {
    const TABLE = 'elis_field_data_text';
}

/**
 * Which contexts a field applies to.
 */
class field_contextlevel extends elis_data_object {
    const TABLE = 'elis_field_contextlevels';

    protected $_dbfield_fieldid;
    protected $_dbfield_contextlevel;
}

/**
 * Which contexts a field category applies to.
 */
class field_category_contextlevel extends elis_data_object {
    const TABLE = 'elis_fied_category_contexts';

    protected $_dbfield_categoryid;
    protected $_dbfield_contextlevel;
}