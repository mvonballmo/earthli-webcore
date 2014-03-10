<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/db/database.php');

/**
 * Interface to an SQL command that generates various WebCore objects.
 * Allows the construction of complex queries through a simple API that is
 * applied uniformly regardless of the objects being retrieved.
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 2.2.1
 * @abstract
 */
abstract class QUERY extends WEBCORE_OBJECT
{
  /**
   * SQL alias for the "main" table.
   * This is the name assigned to the primary table for a query. Provides
   * generic access for applying custom restrictions or retrieving values.
   * @see $id
   * @var string
   */
  public $alias = 'obj';

  /**
   * Name of the SQL field for the ID in the "main" table.
   * @see $alias
   * @var string
   */
  public $id = 'id';

  /**
   * @param CONTEXT $context Attach the query to this object.
   */
  public function __construct ($context)
  {
    $this->raise_if_not_is_a ($context, 'CONTEXT', 'QUERY', 'QUERY');
    $context->ensure_database_exists ();   // queries always need a database
    parent::__construct ($context);

    $this->_restrictions = array ();
    $this->_system_restrictions = array ();
    $this->_calculated_restrictions = array ();

    $this->apply_defaults ();
  }

  /**
   * Returns if the filter is included in the query.
   * Provides for descendant classes.
   * @param $filter
   * @return boolean
   */
  public function includes ($filter)
  {
    return true;
  }

  /**
   * Return the SQL table to use for the given privilege set.
   * Used by {@link QUERY_SECURITY} to build the security restriction when
   * applied to a query. Only needs to be implemented if the query applies
   * security to its output.
   * @param string $set_name Can be {@link Privilege_set_folder}, {@link
   * Privilege_set_entry} or {@link Privilege_set_comment}.
   * @throws METHOD_NOT_IMPLEMENTED_EXCEPTION
   * @return string
   * @abstract
   */
  public function table_for_set ($set_name)
  {
    throw new METHOD_NOT_IMPLEMENTED_EXCEPTION();
  }

  /**
   * Apply default restrictions and tables.
   * Called from the constructor to initialize the query. Subsequent calls
   * clear all tables and restrictions and re-apply the defaults.
   */
  public function apply_defaults ()
  {
    $this->set_select ('*');
    $this->set_table ('');
    $this->order_by_recent ();
  }

  /**
   * Return only the specified set of objects.
   * @param integer $first 0-based index of first record to retrieve.
   * @param integer $count Number of records to retrieve.
   */
  public function set_limits ($first, $count)
  {
    $first = $this->validate_as_integer ($first);
    $count = $this->validate_as_integer ($count);
    $this->_invalidate ();
    $this->_first_record = $first;
    $this->_num_records = $count;
  }

  /**
   * Return only objects between these days.
   * Pass in ISO-formatted dates. Works on the set date fields for this query.
   * @see set_day_field()
   * @param string $first_day
   * @param string $last_day
   */
  public function set_days ($first_day, $last_day)
  {
    $this->_invalidate ();
    $this->_first_day = $first_day;
    $this->_last_day = $last_day;
  }

  /**
   * Specify which database fields to use with 'set_days'.
   * @see set_days()
   * @param string $field
   */
  public function set_day_field ($field)
  {
    $this->_day_field = $field;
    $this->_invalidate ();
  }

  /**
   * Specify which fields to pull from the database.
   * @param string $select
   */
  public function set_select ($select)
  {
    $this->_select = '';
    $this->add_select ($select);
  }

  /**
   * Add additional fields to the query string.
   * @param string $select
   */
  public function add_select ($select)
  {
    if (! $this->_select)
    {
      $this->_select = $select;
    }
    else
    {
      $this->_select .= ", $select";
    }

    $this->_invalidate ();
  }

  /**
   * Set the initial table(s) for the query.
   * The initial table can be several joined tables, though it may be easier to build more complex
   * joins with {@link add_table()}.
   * @param string $table
   */
  public function set_table ($table)
  {
    $this->_tables = $table;
    $this->_invalidate ();
  }

  /**
   * Add another table to the query.
   * @param string $table
   * @param string $join_condition Only used if there is already a table.
   * @param string $join_type Only used if there is already a table.
   */
  public function add_table ($table, $join_condition, $join_type = 'INNER')
  {
    if (! $this->_tables)
    {
      $this->_tables = $table;
    }
    else
    {
      $this->_tables .= " $join_type JOIN $table ON $join_condition";
    }

    $this->_invalidate ();
  }

  /**
   * Add an ordering on the result set.
   * @param string $order SQL-formatted sort directive.
   * @param boolean $insert_before Use this as the primary sort order.
   */
  public function add_order ($order, $insert_before = false)
  {
    $this->_invalidate ();
    if (isset ($this->_order) && $this->_order)
    {
      if ($insert_before)
      {
        $this->_order = $order . ', ' . $this->_order;
      }
      else
      {
        $this->_order .= ', ' . $order;
      }
    }
    else
    {
      $this->_order = $order;
    }
  }

  /**
   * Set (replace) the ordering on the result set.
   * @param string $order
   */
  public function set_order ($order)
  {
    $this->_order = '';
    $this->add_order ($order);
  }

  /**
   * Set (replace) the ordering to sort by the day field.
   * @param string $direction Can be 'ASC' or 'DESC'.
   */
  public function order_by_day ($direction)
  {
    $this->set_order ($this->_day_field . ' ' . $direction);
  }

  /**
   * Reset the ordering to show recent items first.
   * Override the query's default recent ordering by applying an ordering with
   * {@link set_order()} or {@link add_order()}, then calling {@link
   * store_order_as_recent()}.
   */
  public function order_by_recent ()
  {
    if (! empty ($this->_recent_order))
    {
      $this->set_order ($this->_recent_order);
    }
    else
    {
      $this->_order_by_recent ();
    }
  }

  /**
   * Remembers the current order as the recent one to use.
   */
  public function store_order_as_recent ()
  {
    $this->_recent_order = $this->_order;
  }

  /**
   * Remove all cached results.
   * Forces a query on the database (use this as a refresh function).
   */
  public function clear_results ()
  {
    $this->_invalidate ();
  }

  /**
   * Remove all restrictions imposed with 'restrict'.
   * @see restrict()
   */
  public function clear_restrictions ()
  {
    if (sizeof ($this->_restrictions) > 0)
    {
      $this->_invalidate ();
    }
    $this->_restrictions = array ();
    $this->_system_restrictions = array ();
    $this->_calculated_restrictions = array ();
  }

  /**
   * Return only objects which match 'clause'.
   * @param string $clause
   */
  public function restrict ($clause)
  {
    $this->_restrictions [] = $clause;
    $this->_invalidate ();
  }

  /**
   * Restrict a text or date field.
   * Wraps the value in quote marks appropriate to the context.
   */
  public function restrict_text ($name, $value)
  {
    $this->restrict ("$name = '$value'");
  }

  /**
   * Apply generic restrictions.
   * Used by {@link restrict_to_ids()}.
   * @param string|integer[] $field Can be an array or a comma-separated string or any other value.
   * @param object $value
   * @param string $operator Can be any of the {@link Operator_constants}.
   */
  public function restrict_by_op ($field, $value, $operator = Operator_equal)
  {
    if (is_array ($value))
    {
      // If the first element is numeric, assume they all are, otherwise, format as text.

      reset ($value);
      $first = current ($value);
      if (is_numeric ($first))
      {
        $value = join (',', $value);
      }
      else
      {
        $value = "'" . join ("','", $value) . "'";
      }
    }
    elseif (! is_numeric ($value))
    {
      $value = "'$value'";
    }

    switch ($operator)
    {
    case Operator_not_in:
      if ($value)
      {
        $this->restrict ('NOT (' . $field . ' IN (' . $value . '))');
      }
      break;
    case Operator_in:
      if ($value)
      {
        $this->restrict ($field . ' IN (' . $value . ')');
      }
      break;
    default:
      if ($value)
      {
        $this->restrict ($field . ' ' . $operator . ' ' . $value);
      }
    }
  }

  /**
   * Restrict the ids to a given set.
   * Restricts on the  {@link $alias} and {@link $id}.
   * @param string|integer[] $ids Can be an array or a comma-separated
   * string.
   * @param string $operator Can be {@link Operator_in} or {@link
   * Operator_not_in}.
   */
  public function restrict_to_ids ($ids, $operator = Operator_in)
  {
    $this->restrict_by_op ($this->alias . '.' . $this->id, $ids, $operator);
  }

  /**
   * Restrict to one of the given choices.
   * This constructs an OR statement with the list of choices.
   * @param string[] $choices
   */
  public function restrict_to_one_of ($choices)
  {
    $this->restrict ('(' . join (') OR (', $choices) . ')');
  }

  /**
   * Return only objects which match 'words' on 'fields' in a full-text search.
   * Objects are sorted by relevance by default. 'words' is matched against each
   * item of the array 'fields' and OR'd together.
   *
   * For example, with $words = 'search this' and $fields = array (title,
   * description), this will generate:
   *
   * (MATCH (title) AGAINST ('search this') OR MATCH (description) AGAINST
   * ('search this')
   *
   * @param string $words Space-separated list of words to search.
   * @param string $fields Fields in which to search (must be full-text
   * indexed).
   */
  public function add_search ($words, $fields)
  {
    $words = addslashes ($words);
    if (is_array ($fields))
    {
      foreach ($fields as $field)
      {
        $clauses [] = "MATCH ($field) AGAINST ('$words')";
      }
      $clause = '(' . join (' OR ', $clauses) . ')';
    }
    else
    {
      $clause = "MATCH ($fields) AGAINST ('$words')";
    }

    $this->restrict ($clause);
  }

  /**
   * Restrict on a date field.
   * 'from' or 'to' may be empty.
   * @param string $field
   * @param DATE_TIME $from
   * @param DATE_TIME $to
   */
  public function restrict_date ($field, $from, $to)
  {
    if ($from && $from->is_valid ())
    {
      $this->restrict ("$field >= '" . $from->as_ISO () . "'");
    }

    if ($to && $to->is_valid ())
    {
      $this->restrict ("$field <= '" . $to->as_ISO () . "'");
    }
  }

  /**
   * Returns a reference to the opened database query if the command succeeds.
   * This is useful to use the query to build the SQL, but then retrieve the
   * data in a customized way. The database is NOT aligned to the first record.
   * That is, it can still represent an empty result.
   * @return DATABASE
   * @access private
   */
  public function raw_output ()
  {
    if (! $this->_returns_no_data ())
    {
      $this->db->logged_query ($this->_objects_SQL);
      
      return $this->db;
    }
    
    return null;
  }

  /**
   * How many objects would be returned by this query.
   * This result ignores the limit set in {@link set_limits()}
   * @return integer
   */
  public function size ()
  {
    $this->_check_system_call ();

    if (! isset ($this->_num_objects))
    {
      $this->_num_objects = 0;
      if (! $this->_returns_no_data ())
      {
        if (isset ($this->env->profiler))
        {
          $this->env->profiler->restart ('query');
        }

        log_message ("<b>Reading count:</b><div style=\"margin: 1em 0 0 1.5em\">$this->_count_SQL</div>", Msg_type_debug_info, Msg_channel_database, true);

        $this->db->query ($this->_count_SQL);
        if ($this->db->next_record ())
        {
          $this->_num_objects = $this->db->f (0);
        }

        if (isset ($this->env->profiler))
        {
          $elapsed = $this->env->profiler->elapsed ('query');
          $msg = "<b>Count = [$this->_num_objects] ([$elapsed] seconds)</b><br>";
        }
        else
        {
          $msg = "<b>Count = [$this->_num_objects]</b><br>";
        }

        log_message ($msg, Msg_type_debug_info, Msg_channel_database, true);
      }
    }

    return $this->_num_objects;
  }

  /**
   * Return the requested objects.
   * Use {@link restrict()} to constrain the result set.
   * @return stdClass[]
   */
  public function objects ()
  {
    $this->_check_system_call ();

    if (! isset ($this->_objects))
    {
      $this->_objects = array ();

      if (! $this->_returns_no_data ())
      {
        log_message ("<b>Reading objects:</b><div style=\"margin: 1em 0em 0em 1.5em\">$this->_objects_SQL</div>", Msg_type_debug_info, Msg_channel_database, true);

        if (isset ($this->env->profiler))
        {
          $this->env->profiler->restart ('query');
        }

        $this->db->query ($this->_objects_SQL);

        if (isset ($this->env->profiler))
        {
          $db_time = $this->env->profiler->elapsed ('query');
          $this->env->profiler->restart ('query');
        }

        while ($this->db->next_record ())
        {
          if ($this->_is_valid_object ($this->db))
          {
            /** @var WEBCORE_OBJECT $obj */
            $obj = $this->_make_object ();
            $obj->load ($this->db);
            $this->_prepare_object ($obj);
            $this->_objects [] = $obj;

            if ($this->env->log_class_names)
            {
              log_message ("Loaded [" . $obj->instance_description () . ']', Msg_type_debug_info, Msg_channel_system);
            }
          }
        }
      }

      $this->_num_objects = sizeof ($this->_objects);

      if (! $this->_returns_no_data ())
      {
        if (isset ($this->env->profiler))
        {
          $obj_time = $this->env->profiler->elapsed ('query');
          $msg = "<b>Loaded [$this->_num_objects] objects ([db:$db_time, build:$obj_time] seconds)</b><br>";
        }
        else
        {
          $msg = "<b>Loaded [$this->_num_objects] objects</b><br>";
        }

        log_message ($msg, Msg_type_debug_info, Msg_channel_database, true);
      }
    }

    return $this->_objects;
  }

  /**
   * Return only the object with this id.
   * @see $alias
   * @see $id
   * @param integer $id
   * @return UNIQUE_OBJECT
   */
  public function object_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);

    if ($id > 0)
    {
      $Result = null;

      if (isset ($this->_objects))
      {
        // objects have already been calculated

        $this->_system_call = true;
        $indexed_objs = $this->indexed_objects ();
        $this->_system_call = false;
        
        if (isset($indexed_objs [$id]))
        {
          return $indexed_objs [$id];
        }
      }

      $this->_start_system_call ($this->alias . '.' . $this->id . '=' . $id);
      $objs = $this->objects ();
      
      if (!empty($objs))
      {
        $Result = $objs [0];
      }
      
      $this->_end_system_call ();

      return $Result;
    }

    return null;
  }

  /**
   * Return the first object to match the query.
   * @return object
   */
  public function first_object ()
  {
    $old_first = $this->_first_record;
    $old_count = $this->_num_records;
    $this->set_limits (0, 1);
    $objs = $this->objects ();
    $this->first = $old_first;
    $this->count = $old_count;
    if (! empty ($objs))
    {
      return $objs [0];
    }

    return null;
  }

  /**
   * Return only the objects with these ids.
   * @see $alias
   * @see $id
   * @param string $ids Comma-separated list of integers.
   * @param boolean $invert_logic If true, returns all objects NOT matching the
   * query.
   * @return object[]
   */
  public function objects_at_ids ($ids, $invert_logic = false)
  {
    return $this->_objects_at_ids ($ids, $invert_logic, 'objects');
  }

  /**
   * Return only the objects with these ids.
   * @param string $ids Comma-separated list of integers.
   * @param boolean $invert_logic If true, returns all objects NOT matching the
   * query.
   * @return object[]
   */
  public function indexed_objects_at_ids ($ids, $invert_logic = false)
  {
    return $this->_objects_at_ids ($ids, $invert_logic, 'indexed_objects');
  }

  /**
   * Return only the objects with 'field' equal to 'value'.
   * @param string $field
   * @param string $value
   * @return object[]
   */
  public function objects_with_field ($field, $value)
  {
    $this->assert (! empty ($field) && ! empty ($value), 'both field and value must be non-empty', 'objects_with_field', 'QUERY');
    $value = addslashes ($value);
    $this->_start_system_call ("$field = '$value'");
    $Result = $this->objects ();
    $this->_end_system_call ();
    return $Result;
  }

  /**
   * Return only the objects with all 'fields' equal to all 'values'.
   * @param string[] $fields
   * @param string[] $values
   * @param string $operator Can be any of the {@link Operator_constants}.
   * @return object[]
   */
  public function objects_with_fields ($fields, $values, $operator = Operator_equal)
  {
    $arrays_match = is_array ($fields) && is_array ($values) && (sizeof ($fields) == sizeof ($values));
    $this->assert ($arrays_match, 'both fields and values must be the same size', 'objects_with_fields', 'QUERY');

    $operations = array ();
    $index = 0;
    while ($index < sizeof ($fields))
    {
      $operations [] = $fields[$index] . ' ' . $operator . ' \'' . addslashes ($values[$index]) . '\'';
      $index += 1;
    }
    $this->_start_system_call ('(' . implode (') AND (', $operations) . ')');
    $Result = $this->objects ();
    $this->_end_system_call ();
    return $Result;
  }

  /**
   * Return the first object with 'field' equal to 'value'.
   * Ordering can be used to guarantee the correct object is returned.
   * @param string $field
   * @param string $value
   * @return object[]
   */
  public function object_with_field ($field, $value)
  {
    $objs = $this->objects_with_field ($field, $value);
    if (! empty($objs))
    {
      return $objs [0];
    }
    
    return null;
  }

  /**
   * Return only the objects with all 'fields' equal to all 'values'.
   * Ordering can be used to guarantee the correct object is returned.
   * @param string[] $fields
   * @param string[] $values
   * @param string $operator Can be any of the {@link Operator_constants}.
   * @return object
   */
  public function object_with_fields ($fields, $values, $operator = Operator_equal)
  {
    $objs = $this->objects_with_fields ($fields, $values, $operator);
    
    if (isset($objs [0]))
    {
      return $objs [0];
    }
    
    return null;
  }

  /**
   * Result set with each object stored as [id => object].
   * Each object is stored in the result set, mapped to its id.
   * @return object[]
   */
  public function indexed_objects ()
  {
    if (! isset ($this->_indexed_objects))
    {
      $this->_indexed_objects = array ();

      $objs = $this->objects ();
      foreach ($objs as &$obj)
      {
        if ($this->_is_indexable_object ($obj))
        {
          $this->_indexed_objects [$this->_id_for_object ($obj)] = $obj;
        }
      }
    }

    return $this->_indexed_objects;
  }

  /**
   * Ids of all objects in the result set.
   * @return string
   */
  public function indexed_ids ()
  {
    $objs = $this->indexed_objects ();

    if (sizeof ($objs) > 0)
    {
      $Result = array_keys ($objs);
    }
    else
    {
      $Result = array ();
    }

    return $Result;
  }

  /**
   * Ids of all objects in the result set as a comma-separated string.
   * @return string
   */
  public function indexed_ids_as_string ()
  {
    return implode (",", $this->indexed_ids ());
  }

  /**
   * Cache this set of objects as the result of the query.
   * The objects for this query have already been calculated elsewhere and are
   * assumed to represent the correct query results.
   * @param object[] $objects
   * @access private
   */
  public function cache ($objects)
  {
    $this->_objects = $objects;
    $this->_num_objects = sizeof ($objects);
    $this->_prepared = true;
  }

  /**
   *  Retrieve a hash of the object query SQL.
   * @return string
   */
  public function hash ()
  {
    $this->_prepare ();
    return md5 ($this->_objects_SQL);
  }

  /**
   * Force preparation of the configured SQL.
   * Used for debugging generated SQL.
   */
  public function prepare ()
  {
    if (! $this->_preparing_query)
    {
      $this->_prepare ();
    }
  }

  /**
   * Return only the objects with these ids.
   * @param string $ids Comma-separated list of integers.
   * @param boolean $invert_logic If true, returns all objects NOT matching the query.
   * @param string $method_name Name of the method to call to retrieve objects.
   * @return object[]
   */
  protected function _objects_at_ids ($ids, $invert_logic, $method_name)
  {
    if (! is_array ($ids))
    {
      $ids = trim_array (explode (',', $ids));
    }

    if (sizeof ($ids) > 0)
    {
      if (! $invert_logic && isset ($this->_objects))
      {
        // objects have already been calculated

        $this->_system_call = true;
        $indexed_objs = $this->indexed_objects ();
        $this->_system_call = false;

        /* loop through all the ids, getting them out of cache. As soon as one
         * is not in the cache, erase the whole result and go to the database in
         * the next step. Otherwise, all the objects were found without hitting
         * the cache.
         */

        $Result = null;
        foreach ($ids as $id)
        {
          if (isset ($indexed_objs [$id]))
          {
            $Result [] = $indexed_objs [$id];
          }
          else
          {
            $Result = null;
            break;
          }
        }
      }

      if ((! isset ($Result) || ! sizeof ($Result)) && sizeof ($ids))
      {
        $ids = join (', ', $ids);

        if ($ids)
        {
          if ($invert_logic)
          {
            $this->_start_system_call ('NOT (' . $this->alias . '.' . $this->id . ' IN (' . $ids . '))');
          }
          else
          {
            $this->_start_system_call ($this->alias . '.' . $this->id . ' IN (' . $ids . ')');
          }
          $Result = $this->$method_name ();
          $this->_end_system_call ();
        }
      }

      if (isset ($Result))
      {
        return $Result;
      }
    }
    
    return null;
  }

  /**
   * Indicate that the query returns no data.
   * Should be called by descendent queries from {@link _prepare_restrictions()} when they realize
   * that their conditions can logically return no data.
   * @access private
   */
  protected function _set_returns_no_data ()
  {
   $this->_returns_no_data_flag = true;
  }

  /**
   * Could this query return data?
   * Returns true if it can be determined that the query will not return data. This
   * is the 'optimizer' and avoids going to the database with queries that include
   * security or filtering restrictions that cannot return data.
   * @return boolean
   * @access private
   */
  protected function _returns_no_data ()
  {
    $this->prepare ();
    return $this->_returns_no_data_flag;
  }

  /**
   * Update the internal SQL.
   * Called automatically from {@link _returns_no_data()}.
   * @access private
   */
  protected function _prepare ()
  {
    if (! $this->_prepared)
    {
      $this->_preparing_query = true;
      $this->_returns_no_data_flag = false;
      $current_select = $this->_select;

      $this->_prepare_restrictions ();
      if ($this->_returns_no_data_flag)
      {
        log_message ("Optimized [" . get_class ($this) . "] - returned no data.", Msg_type_debug_info, Msg_channel_database);
      }
      else
      {
        $this->_prepare_SQL ();
      }

      $this->_select = $current_select;
      $this->_preparing_query = false;
      $this->_prepared = true;
    }
  }

  /**
   * Creates the SQL for getting {@link size()} and {@link objects()}.
   * @access private
   */
  protected function _prepare_SQL ()
  {
    $this->assert (isset ($this->_select) && isset ($this->_tables), '\'_select\' and \'_tables\' must be non-empty.', '_prepare_SQL', 'QUERY');

    $this->_objects_SQL = $this->_objects_command_as_SQL ();
    $this->_count_SQL = $this->_count_command_as_SQL ();

    $restrictions = $this->_object_restrictions ();
    if ($restrictions)
    {
      $this->_objects_SQL .= ' WHERE (' . implode (') AND (', $restrictions) . ')';
    }

    $restrictions = $this->_count_restrictions ();
    if ($restrictions)
    {
      $this->_count_SQL .= ' WHERE (' . implode (') AND (', $restrictions) . ')';
    }
    
    if ($this->_order)
    {
      $this->_objects_SQL .= ' ORDER BY ' . $this->_order;
    }

    if ($this->_num_records)
    {
      $this->_objects_SQL .= " LIMIT $this->_first_record, $this->_num_records";
    }
  }
  
  /**
   * Return the list of restrictions for retrieving objects.
   * @see _prepare_restrictions()
   * @return string[]
   * @access private
   */
  protected function _object_restrictions ()
  {
    return $this->_restrictions ();
  }

  /**
   * Return the list of restrictions for calculating size.
   * @see _prepare_restrictions()
   * @return string[]
   * @access private
   */
  protected function _count_restrictions ()
  {
    return $this->_restrictions ();
  }

  /**
   * Return the list of restrictions for this query.
   * Includes {@link $_system_restrictions}, {@link $_calculated_restrictions}
   * and restrictions for {@link $_first_day} and {@link $_last_day}.
   * @see _prepare_restrictions()
   * @return string[]
   * @access private
   */
  protected function _restrictions ()
  {
    $Result = $this->_restrictions;
    $Result = array_merge ($Result, $this->_system_restrictions);
    $Result = array_merge ($Result, $this->_calculated_restrictions);

    if ($this->_first_day)
    {
      $Result [] = $this->_day_field  . " >= '" . $this->_first_day . "'";
    }

    if ($this->_last_day)
    {
      $Result [] = $this->_day_field  . " <= '" . $this->_last_day . "'";
    }

    return $Result;
  }

  /**
   * A query parameter has changed.
   * Make sure that the query is updated when a request for objects or size is made.
   * @access private
   */
  protected function _invalidate ()
  {
    if (! $this->_preparing_query)
    {
      $this->_prepared = false;
      $this->_objects = null;
      $this->_num_objects = null;
      $this->_indexed_objects = null;
      $this->_object_tree = null;
      $this->_system_call = false;
      $this->_system_restrictions = array ();
      $this->_calculated_restrictions = array ();
    }
  }

  /**
   * Return the text of the object-retrieval command.
   * @return string
   * @access private
   */
  protected function _objects_command_as_SQL ()
  {
    return "SELECT $this->_select FROM $this->_tables";
  }

  /**
   * Return the text of the size-retrieval command.
   * @return string
   * @access private
   */
  protected function _count_command_as_SQL ()
  {
    return 'SELECT COUNT(' . $this->alias . '.' . $this->id . ') FROM ' . $this->_tables;
  }

  /**
   * Make the specific object returned by this query.
   * @access private
   * @abstract
   */
  protected abstract function _make_object ();

  /**
   * Perform any setup needed on each returned object.
   * @param object $obj
   * @access private
   */
  protected function _prepare_object ($obj) {}

  /**
   * Prepare security- and filter-based restrictions.
   * Apply changes to {@link $_calculated_restrictions}.
   * @access private
   */
  protected function _prepare_restrictions () {}

  /**
   * Should this object be added to the current result set?
   * @param DATABASE $db
   * @return bool
   * @access private
   */
  protected function _is_valid_object ($db) { return true; }

  /**
   * Should this object be indexed?
   * @param $obj
   * @return bool
   * @access private
   */
  protected function _is_indexable_object ($obj) { return true; }

  /**
   * @param object
   * @return integer
   * @access private
   */
  protected function _id_for_object ($obj) 
  { 
    return $obj->id; 
  }

  /**
   * Used internally to signal that parameters are updated for a system-initiated retrieval.
   * For example, the 'objects_at_ids' function will use an internal restriction
   * on those ids that should not be noticed by users of this class.
   * @param string $clause
   * @access private
   */
  protected function _start_system_call ($clause)
  {
    $this->_invalidate ();
    $this->_system_call = true;
    $this->_system_restrictions = array ($clause);
  }

  /**
   * System-initiated query is complete.
   * @see _start_system_call()
   * @access private
   */
  protected function _end_system_call ()
  {
    $this->_system_call = false;
  }

  /**
   * Called internally to synchronize before going to the database.
   * If a system call is not pending, but there are still system clauses in the
   * query clause stream, then invalidate and regenerate the query command.
   * @access private
   */
  protected function _check_system_call ()
  {
    if (! $this->_system_call && sizeof ($this->_system_restrictions))
    {
      $this->_invalidate ();
    }
  }

  /**
   * Overridable called from {@link order_by_recent()}.
   * @access private */
  protected function _order_by_recent ()
  {
    $this->order_by_day ('DESC');
  }

  /**
   * Index in query result of first record to return.
   * @var integer
   * @see set_limits()
   * @access private
   */
  protected $_first_record = 0;

  /**
   * Number of records to return.
   * @var integer
   * @see set_limits()
   * @access private
   */
  protected $_num_records = 0;

  /**
   * Return only records after this date.
   * @see set_days()
   * @var string
   * @access private
   */
  protected $_first_day = '';

  /**
   * Return only records before this date.
   * @see set_days()
   * @var string
   * @access private
   */
  protected $_last_day = '';

  /**
   * Apply date filter to this SQL field. Use {@link set_day_field()}
   * to change this value. Use {@link set_days()} to change the restricted
   * values.
   * @var string
   * @access private
   */
  protected $_day_field = 'time_created';

  /**
   * SQL table/join statement.
   * Usually set in the constructor, but also can be set in response to
   * {@link _update()}.
   * @see _update()
   * @var string
   * @access private
   */
  protected $_tables;

  /**
   * SQL fields to select from '_tables'.
   * Usually set in the constructor, but also can be set in response to
   * {@link _update()}. Can also be set externally.
   * @see set_select_fields()
   * @var string
   * @access private
   */
  protected $_select;

  /**
   * SQL fields and ordering specifications.
   * Usually set in the constructor. Can also be set externally.
   * @see set_order()
   * @var string
   * @access private
   */
  protected $_order;

  /**
   * Ordering to use when {@link order_by_recent()} is called.
   * Call {@link set_order_as_recent()} to store the current ordering to be used
   * as the most recent ordering.
   * @var string
   * @access private
   */
  protected $_recent_order;

  /**
   * Does this query need updating from the database?
   * Whenever the results for the current set of query properties have been
   * calculated, this flag is set. If a property changes, this flag is cleared.
   * Maintained intenally using {@link _invalidate()}.
   * @see _invalidate()
   * @var boolean
   * @access private
   */
  protected $_prepared = false;

  /**
   * Is the query preparing itself?
   * This flag is set when the query is asked to prepare all restrictions and determine
   * whether data is returned or not. This allows the preparation process to ask whether it
   * has already been determined that the query returns no data, in order to abort.
   * @var boolean
   * @access private
   */
  protected $_preparing_query = false;

  /**
   * Does the query contain system information?
   * The query will add restrictions and arguments if certain functions are called. This
   * flag indicates that this is the case. This is only used internally.
   * @var boolean
   * @access private
   */
  protected $_system_call = false;

  /**
   * List of restrictions imposed by a system call.
   * Restrictions added by this object internally are added to this list. They are cleared when
   * a different request is made and should never be visible to the user. For example, if the user
   * calls {@link objects_with_field()}, the query imposes an internal restriction, but one that the
   * user didn't actually add. This list holds temporary restrictions, as opposed to those stored in
   * {@link _calculated_restrictions}.
   * @var string[]
   * @access private
   */
  protected $_system_restrictions;

  /**
   * List of restrictions imposed by security and filtering.
   * Descendent query classes override {@link _prepare_restrictions} to impose security and filtering
   * restrictions. Those are added to this list.
   * @var string[]
   * @access private
   */
  protected $_calculated_restrictions;

  /**
   * Can this query possibly return data?
   * Internal flag indicating that the restrictions imposed on this query obviate the need for
   * actually going to the database -- the query is known to return no data.
   * @var boolean
   * @access private
   */
  protected $_returns_no_data_flag;

  /**
   * Text of SQL query to retrieve objects.
   * Valid only after an 'objects' (or any of 'object_at_id', 'objects_at_ids',
   * etc.) request has been successfully issued.
   * @var string
   * @access private
   */
  protected $_objects_SQL;

  /**
   * Text of SQL query to retrieve the number of objects.
   * Valid only after a 'size' request has been successfully issued.
   * @var string
   * @access private
   */
  protected $_count_SQL;

  /**
   * Current result set.
   * Valid only after an 'objects' (or any of 'object_at_id', 'objects_at_ids',
   * etc.) request has been successfully issued.
   * @var object[]
   * @access private
   */
  protected $_objects;

  /**
   * Current number of objects.
   * Valid only after a 'size' request has been successfully issued.
   * @var string
   * @access private
   */
  protected $_num_objects;

  /**
   * Current result set.
   * Valid only after a call to 'indexed_objects' has been issued.
   * @var object[]
   * @access private
   */
  protected $_indexed_objects;

  /**
   * Current result set.
   * Valid only after a call to 'tree' or 'root_tree' has been issued.
   * @var object[]
   * @access private
   */
  protected $_object_tree;
}

/**
 * A query that supports returning objects in a hierarchy.
 * 
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 3.1.0
 * @abstract
 */
abstract class HIERARCHICAL_QUERY extends QUERY 
{
  /**
   * Objects in the result set returned as a hierarchical tree.
   * @param integer $sub_folder_id Return only the hierarchy stemming from this
   * folder.
   * @param integer $root_id A hint that can be used to limit the number of
   * objects to check when building the tree.
   * @return object[]
   */
  public function tree ($sub_folder_id = 0, $root_id = 0)
  {
    if (! isset ($this->_object_tree))
    {
      $this->_object_tree = array ();

      if ($root_id)
      {
        $this->_start_system_call ("$this->alias.root_id = $root_id");
        $objs = $this->objects ();
        $parents = $this->indexed_objects ();
          // Retrieve the indexed list here so that it is retrieved from the
          // correct list of objects, e.g. that which matches the 'root_id' search
          // Otherwise, the _end_system_call invalidates the query and a later
          // call for the indexed objects requeries and gets a list without the
          // 'root_id' optimization
        $this->_end_system_call ();
      }
      else
      {
        $objs = $this->objects ();
          // get each object indexed by its 'id'
        $parents = $this->indexed_objects ();
      }

      // Search all objects, adding those without parents to the
      // root (_object_tree), otherwise, adding them into the
      // hierarchy at the appropriate point.
      
      foreach ($objs as &$obj)
      {
        $parent_id = $this->_parent_id_for_object ($obj);
        if ($parent_id)
        {
          if (isset($parents [$parent_id]))
          {
            $this->_obj_connect_to_parent ($parents [$parent_id], $obj);
          }
          else
          {
            $this->_object_tree [] = $obj;
          }
        }
        else
        {
          $this->_object_tree [] = $obj;
        }

        $this->_obj_set_sub_objects_cached ($obj);
      }
    }

    if ($sub_folder_id)
    {
      if (! isset ($parents))
      {
        $parents = $this->indexed_objects ();
      }

      if (isset ($parents [$sub_folder_id]))
      {
        return $this->_obj_sub_objects ($parents [$sub_folder_id]);
      }
    }

    return $this->_object_tree;
  }

  /**
   * Returns objects without a parent as well as objects rooted at 'parent_id'.
   * Also, if there is only one object at the root, it is discarded and the sub-
   * objects are returned instead.
   * @param integer $parent_id
   * @return object[]
   */
  public function root_tree ($parent_id = 0)
  {
    // first, get the whole tree, regardless of the root

    $nodes = $this->tree ();

    if ((sizeof ($nodes) == 1) && ($nodes [0]->id == $parent_id))
    {
      return $this->_obj_sub_objects ($nodes [0]);
    }

    return $nodes;
  }

  /**
   * @param object
   * @return integer
   * @access private
   */
  protected function _parent_id_for_object ($obj) 
  { 
    return $obj->parent_id; 
  }

  /**
   * @param object $obj
   * @access private
   * @abstract
   */
  protected abstract function _obj_set_sub_objects_cached ($obj);

  /**
   * @param object $parent
   * @param object $obj
   * @access private
   * @abstract
   */
  protected abstract function _obj_connect_to_parent ($parent, $obj);

  /**
   * @param object $obj
   * @return object[]
   * @access private
   * @abstract
   */
  protected abstract function _obj_sub_objects ($obj);
}

/**
 * Uses a {@link QUERY} to retrieve and cache requested objects.
 * Use this class whenever you want to optimize access to single objects
 * retrieved with a query. The {@link APPLICATION} does this with user objects
 * since users are requested from various portions of a page, it's difficult to
 * retrieve a list in one query.
 *
 * Using this cache is a reasonable alternative which reduces queries to only the
 * number needed for the individual objects, but allows a more flexible programming
 * model. This cache is very useful if repeated hits are expected on a relatively
 * small set of objects.
 * @access private
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 2.2.1
 */
class QUERY_BASED_CACHE extends RAISABLE
{
  /**
   * @var QUERY
   * @access private
   */
  public $query;
  
  /**
   * @param QUERY $query Retrieve objects using this query.
   */
  public function __construct ($query)
  {
    /* Make sure the query is its own reference, so changes to the
       query elsewhere don't affect which objects can be returned. */
    $this->query = $query;
  }

  /**
   * Return the requested object; might be cached.
   * @param integer $id
   * @return UNIQUE_OBJECT
   */
  public function object_at_id ($id)
  {
    $this->assert (! empty ($id), 'ID cannot be empty (in ' . strtoupper (get_class ($this->query)) . ')', 'object_at_id', 'QUERY_BASED_CACHE');

    if (isset ($this->_cache [$id]))
    {
      return $this->_cache [$id];
    }

    $Result = $this->query->object_at_id ($id);
    
    $this->add_object($Result);

    return $Result;
  }

  /**
   * Add a pre-created object to the cache.
   * This allows the cache to be expanded with objects created by other queries.
   * @param UNIQUE_OBJECT $obj
   */
  public function add_object ($obj)
  {
    if (isset($obj))
    {
      $this->_cache [$obj->id] = $obj;
    }
  }

  /**
   * @var UNIQUE_OBJECT[]
   * @access private
   */
  protected $_cache;
}

/**
 * Iterates a {@link QUERY} using a batch size.
 * Use this as a wrapper for a query to iterate all available objects without
 * loading too many at any one time.
 * @access private
 * @package webcore
 * @subpackage db
 * @version 3.5.0
 * @since 2.7.0
 */
class QUERY_ITERATOR extends RAISABLE
{
  /**
   * Number of objects to load at once.
   * @var integer
   */
  public $batch_size = 10;

  /**
   * @param QUERY $query Retrieve objects using this query.
   */
  public function __construct ($query)
  {
    /* Make sure the query is its own reference, so changes to the
       query elsewhere don't affect which objects can be returned. */
    $this->_query = $query;
  }

  /**
   * Number of items seen so far.
   * @return integer
   */
  public function num_items_iterated ()
  {
    if (isset ($this->_item_index))
    {
      return $this->_num_items_iterated + $this->_item_index;
    }

    return $this->_num_items_iterated;
  }

  /**
   * Does this iterator contain items?
   * @return boolean
   */
  public function has_items ()
  {
    return isset ($this->_item_index);
  }

  /**
   * Return the object at the current position.
   * @return object
   */
  public function item ()
  {
    return $this->_objects [$this->_item_index];
  }

  /**
   * Move the position to the first item.
   */
  public function go_to_first ()
  {
    $this->_num_items_iterated = 0;
    $this->_first_item_to_get = 0;
    $this->_load_batch ();
  }

  /**
   * Move the position to the next item.
   */
  public function go_to_next ()
  {
    if ($this->_item_index < sizeof ($this->_objects) - 1)
    {
      $this->_item_index += 1;
    }
    else
    {
      $this->_num_items_iterated += $this->_item_index + 1;
      $this->_first_item_to_get += $this->batch_size;
      $this->_load_batch ();
    }
  }

  /**
   * Load another batch of items from the database.
   * Called in response to a {@link load_next()} that exceeds the
   * bounds of the current batch.
   * @access private
   */
  protected function _load_batch ()
  {
    $this->_query->set_limits ($this->_first_item_to_get, $this->batch_size);
    $this->_objects = $this->_query->objects ();
    if (sizeof ($this->_objects))
    {
      $this->_item_index = 0;
    }
    else
    {
      $this->_item_index = null;
    }
  }

  /**
   * @var integer
   */
  protected $_item_index;

  /**
   * @var integer
   */
  protected $_num_items_iterated;

  /**
   * @var integer
   */
  protected $_first_item_to_get;

  /**
   * @var object[]
   */
  protected $_objects;

  /**
   * @var QUERY
   * @access private
   */
  protected $_query;
}