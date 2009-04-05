<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ("third_party/php_lib73/db_mysql.inc");

/**
 * Messages from the database are logged to this channel.
 */
define ('Msg_channel_database', 'Database');

/**
 * Vendor-independent access to a database.
 *
 * @package webcore
 * @subpackage db
 * @version 3.1.0
 * @since 2.2.1
 */
class DATABASE extends DB_Sql
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
  public function DATABASE ($env)
  {
    $this->env = $env;
    $this->Halt_On_Error = "no";
    $this->Auto_Free = 1;
  }

  /**
   * Internal error-handling.
   * @param string $msg
   * @access private
   */
  public function halt($msg)
  {
    DB_Sql::halt ($msg);

    include_once ('webcore/obj/webcore_object.php');
    raise ("$msg (MySQL Error = $this->Errno, $this->Error)", 'halt', 'DATABASE', $this);
  }

  /**
   * Analogous to the 'use' SQL command.
   * Should be used instead of setting the 'Database' directly because it
   * handles closing an existing connection.
   * @param string $name
   */
  public function set_name ($name)
  {
    if ($this->Link_ID)
    {
      mysql_close ($this->Link_ID);
    }
    $this->Link_ID = 0;
    $this->Database = $name;
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
   */
  public function f ($name)
  {
    if (isset ($this->Record[$name]))
    {
      return $this->Record[$name];
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
        $this->_query_texts [$qs]++;
      }
      else
      {
        $this->_query_texts [$qs] = 1;
      }
    }

    if (isset ($this->env->profiler))
    {
      $this->env->profiler->start ('db');
      DB_Sql::query ($qs);
      $this->env->profiler->stop ('db');
    }
    else
    {
      DB_Sql::query ($qs);
    }

    $this->env->num_queries_executed++;
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
    log_message ("<b>Ran generic query in [$elapsed] seconds:</b><p>$qs</p>", Msg_type_debug_info, Msg_channel_database, true);
  }

  /**
   * Make a copy of this connection description.
   * This avoids copying the database connection state if another connection
   * needs to opened. Defined here to simulate Zend 2.0 features.
   * @return DATABASE
   */
  public function make_clone ()
  {
    $Result = new DATABASE ($this->env);
    $Result->Host = $this->Host;
    $Result->User = $this->User;
    $Result->Password = $this->Password;
    $Result->Database = $this->Database;
    $Result->_query_texts = $this->_query_texts;
    $Result->Link_ID = 0;
    $Result->Query_ID = 0;

    return $Result;
  }

  /**
   * Used for duplicate query checking.
   * @var array[string]
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

?>