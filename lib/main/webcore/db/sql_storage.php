<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Do not store this field.
 */
define ('Storage_action_none', 0);
/**
 * Store this field when creating the object.
 */
define ('Storage_action_create', 1);
/**
 * Store this field when updating the object.
 */
define ('Storage_action_update', 2);
/**
 * Always store this field.
 */
define ('Storage_action_all', 3);

define ('Field_type_integer', 1);
define ('Field_type_date_time', 2);
define ('Field_type_string', 3);
define ('Field_type_boolean', 4);

/**
 * Messages are logged to this channel.
 */
define ('Msg_channel_sql', '__sql');

/**
 * Describes a field in a physical database.
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.2.1
 * @access private
 */
class SQL_FIELD
{
  /**
   * @var string
   */
  public $table_id;

  /**
   * @var string
   */
  public $id;

  /**
   * @var integer
   */
  public $type;

  /**
   * @var object
   */
  public $value;

  /**
   * @var integer
   */
  public $action;

  /**
   * Create a field mapping to a physical database field.
   * Check the constants in 'sql_storage.php' for possible values for 'type' and
   * 'action'.
   * @param string $id Name of the field in the database.
   * @param integer $type Is it an integer, date or string?
   * @param object $value The raw value
   * @param integer $action Which actions is this field used for?
   */
  public function __construct ($id, $type, $value, $action)
  {
    $this->id = $id;
    $this->type = $type;
    $this->value = $value;
    $this->action = $action;
  }

  /**
   * Is this field used for this 'action'?
   * @param integer $action
   * @return boolean
   */
  public function needed_for_action ($action)
  {
    return ($this->action & $action);
  }

  /**
   * Escape the value for use as SQL.
   * @return string
   */
  public function value_for_sql ()
  {
    switch ($this->type)
    {
    case Field_type_integer:
      return $this->value;
    case Field_type_date_time:
    {
      $iso_time = $this->value->as_iso ();
      if ($iso_time == Date_time_unassigned)
      {
        $iso_time = '0000-00-00 00:00:00';
      }
      return "'" . $iso_time . "'";
    }
    case Field_type_string:
      return "'" . addslashes ($this->value) . "'";
    case Field_type_boolean:
      return ($this->value) ? '1' : '0';
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($this->type);
    }
  }
}

/**
 * Describes a table in a physical database.
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.2.1
 * @access private
 */
class SQL_TABLE extends WEBCORE_OBJECT
{
  /**
   * The name of the table in the database.
   *
   * @var string
   */
  public $name;
  
  /**
   * The list of fields in this table.
   *
   * @var array[SQL_FIELD]
   * 
   * @see add()
   */
  public $fields;

  /**
   * The list of restrictions in this table.
   *
   * @var array[string]
   * 
   * @see restrict()
   */
  public $restrictions;

  /**
   * @param CONTEXT $context
   * @param string $name Name of the database table.
   */
  public function __construct ($context, $name)
  {
    parent::__construct ($context);
    $this->name = $name;
  }

  /**
   * Add a field to the table schema.
   * 
   * @see SQL_FIELD
   * @param string $field_id
   * @param integer $field_type
   * @param object $value
   * @param integer $action
   */
  public function add ($field_id, $field_type, $value, $action)
  {
    $class_name = $this->context->final_class_name ('SQL_FIELD');
    $this->fields [$field_id] = new $class_name ($field_id, $field_type, $value, $action);
  }
  
  /**
   * Update the value of an existing field.
   *
   * @param string $field_id
   * @param object $value
   */
  public function update ($field_id, $value)
  {
    $this->fields [$field_id]->value = $value;
  }

  /**
   * Restrict on this field (and it's value) when updating.
   * @param string $field_id
   */
  public function restrict ($field_id)
  {
    $this->restrictions [] = $field_id;
  }

  /**
   * Make sure the given values are valid for their types.
   * Raises an exception if the data is not valid.
   * @param integer $action
   */
  public function validate ($action)
  {
    foreach ($this->fields as $field)
    {
      if ($field->needed_for_action ($action) && ($field->type == Field_type_integer))
      {
        $field->value = $this->validate_as_integer_silent ($field->value);
        if ($field->value === false)
        {
          $this->raise ("[$field->value] is not an integer. (setting field [$field->id])", 'validate', 'SQL_TABLE');
        }
      }
    }
  }

  /**
   * Check for existence of field.
   * @return boolean
   */
  public function exists ()
  {
    $restrictions = $this->restrictions_as_sql ();
    $this->assert ($restrictions, 'Cannot check object without a restriction.', 'exists', 'SQL_TABLE');
    
    $this->db->logged_query ("SELECT COUNT(*) FROM $this->name WHERE $restrictions");
    if ($this->db->next_record ())
    {
      return $this->db->f (0); 
    }
    
    return false;
  }

  /**
   * Execute the requested action on this table.
   * @param integer $action
   */
  public function commit ($action)
  {
    switch ($action)
    {
    case Storage_action_create:
      $this->_create ();
      break;
    case Storage_action_update:
      $this->_update ();
      break;
    }
  }

  /**
   * Get the fields and values for this action.
   * @param integer $action
   * @return array[string,string]
   */
  public function fields_as_sql ($action)
  {
    $Result = '';
    foreach ($this->fields as $id => $field)
    {
      if ($field->needed_for_action ($action))
      {
        $Result [$id] = $field->value_for_sql ();
      }
    }
    return $Result;
  }

  /**
   * Get the restrictions for this table.
   * Used by {@link _update()} and {@link exists()}.
   * @param integer $action
   * @return string
   */
  public function restrictions_as_sql ()
  {
    foreach ($this->restrictions as $id)
    {
      $field = $this->fields [$id];
      $this->assert (isset ($field), 'restriction on non-existent field [$id]', 'restrictions_as_sql', 'SQL_TABLE');
      $Result [$id] = $field->value_for_sql ();
    }
    
    if (! empty ($Result))
    {
      $pairs = array ();
      foreach ($Result as $key => $value)
      {
        $pairs [] = "($key = $value)";
      }
      return join (' AND ', $pairs);
    }
    
    return '';
  }
  
  /**
   * Create the object specified in the schema.
   * @access private
   */
  protected function _create ()
  {
    $fields = $this->fields_as_sql (Storage_action_create);

    $field_names = join (', ', array_keys ($fields));
    $field_values = join (', ', array_values ($fields));

    $this->_query ("INSERT INTO $this->name ($field_names) VALUES ($field_values)");
  }

  /**
   * Update the object specified in the schema.
   * @access private
   */
  protected function _update ()
  {
    $fields = $this->fields_as_sql (Storage_action_update);
    if ($fields)
    {
      foreach ($fields as $key => $value)
      {
        $pairs [] = "$key = $value";
      }
      $data = join( ', ', $pairs);
      
      if ($data)
      {
        $restrictions = $this->restrictions_as_sql ();
        $this->assert ($restrictions, 'Cannot update without a restriction.', '_update', 'SQL_TABLE');
        
        // Just in case the exception handler doesn't stop execution, make sure not
        // to mess up the entire database.
        
        if ($restrictions)
        {
          $this->_query ("UPDATE $this->name SET $data WHERE $restrictions");
        }
      }
    }
    else
    {
      log_message ("No fields to update for [$this->name] (SQL_TABLE)", Msg_type_debug_warning, Msg_channel_sql);      
    }
  }
  
  /**
   * Execute an SQL statement
   * @param string $qs
   * @access private
   */
  protected function _query ($qs)
  {
    $this->db->logged_query ($qs);
  }
}

/**
 * Knows how to store objects to a database.
 * Provides validation and completely automated storage for multi-table objects.
 * @see STORABLE
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.2.1
 * @access private
 */
class SQL_STORAGE extends WEBCORE_OBJECT
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    $this->raise_if_not_is_a ($context, 'CONTEXT', 'QUERY', 'QUERY');
    $context->ensure_database_exists ();   // storage always need a database
    parent::__construct ($context);
  }
  
  /**
   * Add a field to be stored.
   * @param string $table_id Store value to this table.
   * @param string $field_id Store value to this field.
   * @param integer $field_type Field is of this type (can be
   * 'Field_type_string', 'Field_type_integer', 'Field_type_date_time').
   * @param object $value Reference to the actual value to store.
   * @param integer $action Store this field for these actions (can be {@link
   * Storage_action_none}, {@link Storage_action_create}, {@link
   * Storage_action_update}, {@link Storage_action_all}).
   */
  public function add ($table_id, $field_id, $field_type, $value, $action = Storage_action_all)
  {
    $table = $this->_table_at_id ($table_id);
    $table->add ($field_id, $field_type, $value, $action);
  }

  /**
   * Add a restriction to use when updating.
   * The stored field values will be applied to all objects matching all
   * restrictions specified here. The field must be added with 'add' before
   * storing.
   * @param string $table_id Restrict updates to this table.
   * @param string $field_id Restrict updates to objects with this field set to
   * the stored value.
   */
  public function restrict ($table_id, $field_id)
  {
    $table = $this->_table_at_id ($table_id);
    $table->restrict ($field_id);
  }

  /**
   * Create the object specified in the schema.
   * @param STORABLE $obj
   */
  public function create_object ($obj)
  {
    $this->_commit ($obj, Storage_action_create);
  }

  /**
   * Update the object specified in the schema.
   * @param STORABLE $obj
   */
  public function update_object ($obj)
  {
    $this->_commit ($obj, Storage_action_update);
  }
  
  /**
   * Check existence of the object specified in the schema.
   * The object is deemed to exist if it exists in at least one of the tables in
   * its schema.
   * @param STORABLE $obj
   * @return boolean
   */
  public function object_exists ($obj)
  {
    $obj->store_to ($this);

    if (! empty ($this->_tables))
    {
      foreach ($this->_tables as $table)
      {
        if ($table->exists ())
        {
          return true;
        }
      }
    }
    
    return false;
  }

  /**
   * Called internally to commit the storage action.
   * @param STORABLE $obj
   * @param integer $action
   * @access private
   */
  protected function _commit ($obj, $action)
  {
    $obj->store_to ($this);

    if (sizeof ($this->_tables))
    {
      /* Validate all tables before committing. Validate may changes 
       * values (e.g. setting defaults), so we have to make a validated tables
       * array because foreach doesn't use references.
       */

      foreach ($this->_tables as $table)
      {
        $table->validate ($action);
        $validated_tables [] = $table;
      }

      foreach ($validated_tables as $table)
      {
        $this->_commit_table ($table, $action, $obj);
      }
    }
  }

  /**
   * Commits changes to a single table in a storage action.
   * @param SQL_TABLE $table
   * @param integer $action
   * @param STORABLE $obj
   * @access private
   */
  protected function _commit_table ($table, $action, $obj)
  {
    $table->commit ($action);
  }

  /**
   * Retrieve the requested table. Create it if it doesn't exist.
   * @param integer $table_id
   * @return SQL_TABLE
   * @access private
   */
  protected function _table_at_id ($table_id)
  {
    $this->assert (! empty ($table_id), 'table_id cannot be empty.', '_table_at_id', 'SQL_STORAGE');

    $Result = (isset($this->_tables) && isset($this->_tables[$table_id])) ? $this->_tables [$table_id] : null;
    
    if (! isset($Result))
    {
      $class_name = $this->context->final_class_name ('SQL_TABLE');
      $Result = new $class_name ($this->context, $table_id);
      $this->_tables [$table_id] = $Result;
    }
    
    return $Result;
  }
  
  /**
   * List of tables registered with this object.
   * 
   * Filled by calls to {@link add()}.
   * 
   * @var array[SQL_TABLE]
   */
  private $_tables;
}

?>