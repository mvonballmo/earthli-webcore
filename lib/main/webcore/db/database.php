<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.6.0
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
include_once ('webcore/obj/webcore_object.php');

/**
 * Messages from the database are logged to this channel.
 */
define ('Msg_channel_database', 'Database');

/**
 * Vendor-independent access to a database.
 *
 * @package webcore
 * @subpackage db
 * @version 3.6.0
 * @since 2.2.1
 */
class DATABASE extends WEBCORE_OBJECT
{
  /**
   * @var string
   */
  public $Host = "localhost";

  /**
   * @var string
   */
  public $Database = '';

  /**
   * @var string
   */
  public $User = '';

  /**
   * @var string
   */
  public $Password = '';

  /**
   * @param ENVIRONMENT $env Global environment.
   */
  public function __construct ($env)
  {
    $this->env = $env;
  }

  /**
   * Search for the table in the current database.
   * 
   * @param string $name
   * @return boolean
   */
  public function table_exists ($name)
  {
    $this->logged_query ("SHOW TABLES FROM $this->Database LIKE '$name'");
    return $this->next_record ();
  }
  
  /**
   * Determine whether the table with the given name has a primary key.
   *
   * @param string $name
   * @return boolean
   */
  public function table_has_primary_index ($name)
  {
    $this->logged_query ("SHOW INDEX FROM $name FROM $this->Database");
    
    return ($this->next_record () && $this->f ('Key_name') == 'Primary');
  }

  /**
   * Return a field from the current row.
   * @param string|integer $name Index or name of the column to retrieve.
   * @return string
   */
  public function f ($name)
  {
    if (isset ($this->_row[$name]))
    {
      return $this->_row[$name];
    }

    return null;
  }

  /**
   * Execute the SQL query.
   * @param string $qs
   */
  public function query ($qs)
  {
    if ($this->env->warn_if_duplicate_query_executed)
    {
      if (isset ($this->_query_texts [$qs]))
      {
        $count = $this->_query_texts [$qs];
        log_message ("The query [$qs] has already been executed [$count] times.", Msg_type_debug_warning, Msg_channel_database);
        $this->_query_texts [$qs] += 1;
      }
      else
      {
        $this->_query_texts [$qs] = 1;
      }
    }

    if (isset ($this->env->profiler))
    {
      $this->env->profiler->start ('db');
      $this->_execute_query($qs);
      $this->env->profiler->stop ('db');
    }
    else
    {
      $this->_execute_query($qs);
    }

    $this->env->num_queries_executed += 1;
  }

  /**
   * Execute the SQL query and log the command.
   * Use this when querying outside of the QUERY objects. QUERY objects have
   * automated logging built-in.
   * @param string $qs
   */
  public function logged_query ($qs)
  {
    if (isset ($this->env->profiler))
    {
      $this->env->profiler->restart ('query');
    }
    $this->query ($qs);
    if (isset ($this->env->profiler))
    {
      $elapsed = $this->env->profiler->elapsed ('query');
    }
    else
    {
      $elapsed = '[not profiled]';
    }
    log_message ("<b>Ran generic query in [$elapsed] seconds:</b><p>$qs</p>", Msg_type_debug_info, Msg_channel_database, true);
  }

  /**
   * Called in PHP5 when cloning an object. Calls {@link copy_from()} to create copies of all 
   * references not cloned by the default shallow copy.
   */
  function __clone ()
  {
    $this->_connection = null;
    $this->_result_set = null;
    $this->_row = null;
  }

  /**
   * @return bool
   */
  public function next_record()
  {
    if (!$this->_result_set)
    {
      $this->halt ("next_record called with no query pending.");

      return false;
    }

    $this->_row = $this->_result_set->fetch_array();

    return isset($this->_row);
  }

  private function _execute_query($qs)
  {
    if (!isset($this->_connection))
    {
      $this->_connection = new mysqli($this->Host, $this->User, $this->Password, $this->Database);

      if ($this->_connection->connect_errno)
      {
        $msg = "Failed to connect to MySQL: (" . $this->_connection->connect_errno . ") " . $this->_connection->connect_error;
        raise ($msg, '_execute_query', 'DATABASE', $this);

        $this->_connection = null;
      }

      mysqli_set_charset ($this->_connection, 'utf8mb4');
    }

    if (isset($this->_connection))
    {
      $query_result = $this->_connection->query($qs);
      if (is_a($query_result, 'mysqli_result'))
      {
        $this->_result_set = $query_result;
        $this->_result_set->data_seek(0);
      }
      else if ($query_result === false)
      {
        $this->raise('Error executing query: ' . $this->_connection->error, '_execute_query', 'DATABASE');
      }
    }
  }

  /**
   * @var mysqli
   */
  private $_connection;

  /**
   * @var mysqli_result
   */
  private $_result_set;

  /**
   * @var array
   */
  private $_row;

  /**
   * Used for duplicate query checking.
   * @var int[]
   * @access private
   */
  protected $_query_texts;

  /**
   * @var string
   * @access private
   */
  public $classname = "DATABASE";

  /**
   * Shortcut to global environment.
   * @var ENVIRONMENT
   * @access private
   */
  public $env = null;

  /**
   * Shortcut to global profiler.
   * @var PROFILER
   * @access private
   */
  public $profiler = null;
}