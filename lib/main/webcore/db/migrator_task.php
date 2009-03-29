<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/sys/task.php');
require_once ('webcore/db/database.php');

/**
 * Channel used to log all migration-related messages.
 */
define ('Msg_channel_migrate', 'Migrate');

/**
 * Facilitates migrating databases for WebCore {@link APPLICATION}s.
  * @package webcore
  * @subpackage db
  * @version 3.0.0
  * @since 2.6.0
  * @access private
  */
class MIGRATOR_TASK extends TASK
{
  /**
   * Framework to upgrade.
   * Used to determine which framework to upgrade in the database.
   * @var FRAMEWORK_INFO
   * @access private
   */
  var $info;
  /**
   * Log all messages in this channel.
   * @var string
   */
  var $log_channel = Msg_channel_migrate;
  /**
   * Version from which this migrator works.
   * The version in the database must match this version or the database will
   * not be migrated. Use {@link $ignore_from_version} to force an upgrade.
   * @var string
   * @see $ignore_from_version
   * @see $version_to
   */
  var $version_from = '';
  /**
   * Version to which application will be migrated.
   * Version number is updated when the migration finishes.
   * @var string
   * @see $version_from
   */
  var $version_to = '';
  /**
   * Migrate regardless of whether the database has the correct version.
   * This may cause errors in the migration process, as the migration does
   * not examine the database in any detail to determine whether it can
   * proceed with an action or not. Use this to make a migration for databases
   * without any version information at all.
   * @var string
   * @see $version_to
   * @see $version_from
   */
  var $ignore_from_version = FALSE;
  /**
   * Icon to show in the title bar when executing.
   * @var string
   */
  var $icon = '{icons}buttons/upgrade';

  /**
   * @param FRAMEWORK_INFO
   */
  function MIGRATOR_TASK (&$info)
  {
    TASK::TASK ($info->context);
    $this->info =& $info;
    $this->ignore_from_version = ! $this->info->exists ();
  }

  /**
   * Return a formatted title for this task.
   * @return string
   */
  function title_as_text ()
  {
    return 'Migrate ' . $this->info->title . ' from ' . $this->version_from . ' to ' . $this->version_to;
  }

  /**
   * Return a form to display options and execute this task.
   * @return FORM
   */
  function form ()
  {
    $class_name = $this->context->final_class_name ('EXECUTE_MIGRATOR_TASK_FORM', 'webcore/forms/execute_migrator_task_form.php');
    return new $class_name ($this->context);
  }

  /**
   * Returns True if the migrator applies to the current database.
   * @return boolean
   * @access private
   */
  function _can_be_executed ()
  {
    $Result = FALSE;
    if ($this->ignore_from_version)
    {
      $Result = TRUE;
    }
    else
    {
      if ($this->info->database_version)
      {
        $Result = $this->info->database_version == $this->version_from;
        if (! $Result)
        {
          $this->_log ('Cannot migrate. database is at version [' . $this->info->database_version . ']; expected version [' . $this->version_from . '].', Msg_type_warning);
        }
      }
      else
      {
        $Result = TRUE;
      }
    }

    return $Result;
  }

  /**
   * Perform cleanup for a process that has run.
   * @access private
   */
  function _post_execute ()
  {
    if (! $this->env->num_exceptions_raised)
    {
      $this->info->database_version = $this->version_to;
      if (! $this->testing)
      {
        $this->info->store ();
      }
      $this->_log ('Migration completed with (0) errors.', Msg_type_info);
    }
    else
    {
      $this->_log ('Version not updated because of [' . $this->env->num_exceptions_raised . '] errors.', Msg_type_warning);
    }
  }
}

?>