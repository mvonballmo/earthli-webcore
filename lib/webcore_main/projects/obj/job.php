<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('projects/obj/project_entry.php');
require_once ('webcore/sys/property.php');

/**
 * Represents a job that should be made in a {@link PROJECT}.
 * This can be a bug fix, a new feature, a general change or something else. Can have a list
 * of {@link CHANGE}s.
 * @package projects
 * @subpackage obj
 * @version 3.0.0
 * @since 1.4.1
 */
class JOB extends PROJECT_ENTRY
{
  /**
   * When should this job be finished?
    * This is the date that the user creating the job would like it completed.
    * @var DATE_TIME
    */
  var $time_needed;

  /**
   * Which user should take care of this job?
    * @var integer
    * @see assignee()
    */
  var $assignee_id = 0;
  /**
   * Which user reported this job (if not the creator)?
    * @var integer
    * @see reporter()
    */
  var $reporter_id;

  /**
   * @param PROJECT_APPLICATION &$app Main application.
   */
  function JOB (&$app)
  {
    PROJECT_ENTRY::PROJECT_ENTRY ($app);

    $this->time_needed = $app->make_date_time ();
    $this->time_assignee_changed = $app->make_date_time ();
  }

  /**
   * Set the due date for this job.
   * @param DATE_TIME $t
   */
  function set_time_needed ($t)
  {
    if ($t)
      $this->time_needed = $t;
    else
      $this->time_needed->clear ();
  }

  /**
   * HTML code for the icon to use for this assignee.
    * Pass in a folder relative to the project owners folder.
    * @param string $size
    * @return string
    */
  function assignee_icon ($size = '16px')
  {
    $assignee =& $this->assignee ();
    if ($assignee)
    {
      $Result = $assignee->icon_as_html ($size, 'Assigned');
      if (! isset ($Result))
        $Result = $this->app->resolve_icon_as_html ('{app_icons}owners/assigned', 'Assigned', $size);
    }
    else
      $Result = $this->app->resolve_icon_as_html ('{app_icons}owners/unassigned', 'Unassigned', $size);

    return $Result;
  }

  /**
   * List of changes for this job.
   * @return PROJECT_ENTRY_QUERY
   */
  function change_query ()
  {
    $fldr =& $this->parent_folder ();
    $Result = $fldr->entry_query ();
    $Result->set_type ('change');
    $Result->restrict ("chng.job_id = $this->id");
    return $Result;
  }

  /**
   * List of branch infos for the job.
   * @return JOB_BRANCH_INFO_QUERY
   */
  function branch_info_query ()
  {
    $class_name = $this->app->final_class_name ('JOB_BRANCH_INFO_QUERY', 'projects/db/entry_branch_query.php');
    return new $class_name ($this);
  }

  /**
   * Display the job summary information.
   * When showing a short preview, show the age, job closer, priority and
   * status.
   */
  function preview ()
  {
?>
    <p class="detail">
<?php
    $branch_info =& $this->_main_branch_info;
    $interval =& $branch_info->age ();
     $closer =& $branch_info->closer ();
     if ($closer)
     {
?>
    <span class="field"><?php echo $branch_info->status_as_text (); ?></span>
    (<?php echo $branch_info->time_closed->format (); ?>
    by <?php echo $closer->title_as_link (); ?>
    after <?php echo $interval->format (); ?>)
<?php
    }
    else
    {
?>
    <span class="field"><?php echo $branch_info->status_as_text (); ?></span> (<?php echo $interval->format (); ?>)<br>
    <?php echo $branch_info->priority_as_text (); ?>
<?php
      $assignee =& $this->assignee ();
      if ($assignee)
      {
?>
    <br>Assigned to <span class="field"><?php echo $assignee->title_as_link (); ?></span>
<?php
      }
    }
?>
    </p>
<?php
  }

  /**
   * Who is responsible for this job?
   * This user is responsible for managing/fixing/closing this job.
   * @return PROJECT_USER
   */
  function &assignee ()
  {
    return $this->app->user_at_id ($this->assignee_id, FALSE, TRUE);
  }

  /**
   * How long has this job been assigned to {@link assigned()}?
   * @return TIME_INTERVAL
   */
  function assignee_age ()
  {
    $now = new DATE_TIME ();
    if ($this->time_assignee_changed->is_valid ())
      return $now->diff ($this->time_assignee_changed);
  }

  /**
   * Who reported this job?
   * This is the user that causes the job to be created. It can be different
   * than the creator of the job. (e.g. if the job is reported by a user in the
   * field, but entered by a developer later.)
   * @return PROJECT_USER
   */
  function &reporter ()
  {
    if ($this->reporter_id)
      return $this->app->user_at_id ($this->reporter_id);
    else
      return $this->creator ();
  }

  /**
   * @return TITLE_FORMATTER
   */
  function title_formatter ()
  {
    $Result = parent::title_formatter ();

    $branch_info =& $this->main_branch_info ();

    if ($branch_info->is_closed ())
    {
      $closer =& $branch_info->closer ();
      $age = $branch_info->age ();
      $Result->title = $branch_info->status_as_text () . ' by ' . $closer->title_as_plain_text () . ' after ' . $age->format ();
    }
    else
      $Result->title = $branch_info->status_as_text () . ' - ' . $branch_info->priority_as_text ();

    return $Result;
  }

  /**
   * Changed the assignee for the job.
   * @param integer $assignee_id
   */
  function set_assignee_id ($assignee_id)
  {
    if ($assignee_id != $this->assignee_id)
    {
      $this->time_assignee_changed->set_now ();
      $this->assignee_id = $assignee_id;
    }
  }

  /**
   * @param DATABASE &$db
   */
  function load (&$db)
  {
    parent::load ($db);
    $this->time_needed->set_from_iso ($db->f ('time_needed'));
    $this->time_assignee_changed->set_from_iso ($db->f ('time_assignee_changed'));
    $this->assignee_id = $db->f ('assignee_id');
    $this->reporter_id = $db->f ('reporter_id');
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
   */
  function store_to (&$storage)
  {
    parent::store_to ($storage);
    $tname = $this->_secondary_table_name ();
    $storage->add ($tname, 'time_needed', Field_type_date_time, $this->time_needed);
    $storage->add ($tname, 'time_assignee_changed', Field_type_date_time, $this->time_assignee_changed);
    $storage->add ($tname, 'assignee_id', Field_type_integer, $this->assignee_id);
    $storage->add ($tname, 'reporter_id', Field_type_integer, $this->reporter_id);

    $storage->add ($tname, 'status', Field_type_integer, $this->_main_branch_info->status);
    $storage->add ($tname, 'priority', Field_type_integer, $this->_main_branch_info->priority);
    $storage->add ($tname, 'closer_id', Field_type_integer, $this->_main_branch_info->closer_id);
    if ($this->_main_branch_info->closer_id)
      $storage->add ($tname, 'time_closed', Field_type_date_time, $this->_main_branch_info->time_closed);
    $storage->add ($tname, 'time_status_changed', Field_type_date_time, $this->_main_branch_info->time_status_changed);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  function page_name ()
  {
    return $this->app->page_names->job_home;
  }

  /**
   * Copy properties from the given object.
   * @param JOB $other
   * @access private
   */
  function _copy_from ($other)
  {
    unset ($this->time_needed);
    $this->time_needed = $other->time_needed;
  }

  /**
   * Name of this object's secondary database table.
   * @return string
   * @access private
   */
  function _secondary_table_name ()
  {
    return $this->app->table_names->jobs;
  }

  /**
   * @return JOB_BRANCH_INFO
   * @access private
   */
  function _make_branch_info ()
  {
    return new JOB_BRANCH_INFO ($this);
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_navigator:
        include_once ('projects/gui/job_navigator.php');
        return new JOB_NAVIGATOR ($this);
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('projects/gui/job_renderer.php');
        return new JOB_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/job_commands.php');
        return new JOB_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new JOB_HISTORY_ITEM ($this->app);
      case Handler_associated_data:
        include_once ('projects/gui/job_renderer.php');
        return new JOB_ASSOCIATED_DATA_RENDERER ($this->app, $options);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Name of this type of project entry.
   * @var string
   * @access private
   */
  var $type = 'job';

  /**
   * @var JOB_BRANCH_INFO
   * @access private
   */
  var $_main_branch_info;
}

/**
 * A possible value for a {@link JOB_BRANCH_INFO::$status}.
 * @package projects
 * @subpackage obj
 * @version 3.0.0
 * @since 1.7.0
 */
class JOB_STATUS_VALUE extends PROPERTY_VALUE
{
  /**
   * Indicates whether the job is open or closed.
   * Can be either {@link Job_status_kind_open} or {@link Job_status_kind_closed}.
   * @var integer
   */
  var $kind;
  /**
   * Controls movement between statuses.
   * A job at a particular status can only be moved to another status with level equal
   * to or greater than that of the current status. Use this to prevent certain statuses
   * from being selected again, in effect enforcing the semantics implied by the status labels.
   * (e.g. Open = 1, Closed = 2, Re-opened = 2. With those values, you can only go to re-opened
   * from closed; Open is no longer reachable.)
   * @var integer
   */
  var $level;
}

/**
 * Connects a {@link JOB} to a particular {@link BRANCH}.
 * Manages a job's relatationship within a branch. Use this to update settings,
 * and to add or remove a job from a branch.
 * @package projects
 * @subpackage obj
 * @version 3.0.0
 * @since 1.4.1
 */
class JOB_BRANCH_INFO extends PROJECT_ENTRY_BRANCH_INFO
{
  /**
   * How important is this job?
   * @var integer
   * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::job_priorities()
   */
  var $priority;
  /**
   * In what phase is this job?
   * @var integer
   * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::job_statuses()
   */
  var $status = 0;
  /**
   * When was this job closed?
   * @var DATE_TIME
   */
  var $time_closed;
  /**
   * Which user closed this job?
   * @var integer
   * @see closer()
   */
  var $closer_id = 0;
  /**
   * When did the status last change?
   * @var boolean
   */
  var $time_status_changed;

  /**
   * @param JOB &$entry Branch info is attached to this job.
   */
  function JOB_BRANCH_INFO (&$entry)
  {
    PROJECT_ENTRY_BRANCH_INFO::PROJECT_ENTRY_BRANCH_INFO ($entry);

    $this->time_closed = $this->app->make_date_time ();
    $this->time_status_changed = $this->app->make_date_time ();
  }

  /**
   * Is this job closed?
   * @return boolean
   */
  function is_closed ()
  {
    $statuses = $this->app->display_options->job_statuses ();
    return $this->closer_id && $statuses [$this->status]->kind == Job_status_kind_closed;
  }

  /**
   * How long has this job been open?
   * @return TIME_INTERVAL
   */
  function age ()
  {
    if ($this->closer_id)
      return $this->time_closed->diff ($this->_entry->time_created);
    else
    {
      $now = new DATE_TIME ();
      return $now->diff ($this->_entry->time_created);
    }
  }

  /**
   * How long has this job been in this status?
   * @return TIME_INTERVAL
   */
  function status_age ()
  {
    $now = new DATE_TIME ();
    if ($this->time_status_changed->is_valid ())
      return $now->diff ($this->time_status_changed);
    else
      return $now->diff ($this->_entry->time_created);
  }

  /**
   * Returns True if the job is overdue.
   * The job is overdue if it is not closed and if either any of the due dates in an assigned release
   * have past or its own "needed by" date has past.
   * @return boolean
   */
  function is_overdue ()
  {
    $Result = ! $this->is_closed ();

    if ($Result)
    {
      $rel =& $this->release ();
      if (isset ($rel))
      {
        $status = $rel->status ();
        $Result = $status->is_overdue ();
      }
      else
        $Result = FALSE;

      if (! $Result)
      {
        $entry =& $this->entry ();
        if ($entry->time_needed->is_valid ())
        {
          $now = new DATE_TIME ();
          $Result = $entry->time_needed->less_than ($now, Date_time_date_part);
        }
      }
    }

    return $Result;
  }

  /**
   * Return "needed by" status as HTML.
   * @return string
   */
  function needed_by_as_html ()
  {
    return $this->_needed_by_as_text (FALSE);
  }

  /**
   * Return "needed by" status as plain text.
   * @return string
   */
  function needed_by_as_plain_text ()
  {
    return $this->_needed_by_as_text (TRUE);
  }

  /**
   * Who closed this job?
   * Can be empty.
   * @return PROJECT_USER
   */
  function &closer ()
  {
    return $this->app->user_at_id ($this->closer_id, FALSE, TRUE);
  }

  /**
   * Set the new status for the job.
   * Does not call 'store'.
   * @param integer
   */
  function set_status ($status)
  {
    if ($status != $this->status)
    {
      $this->time_status_changed->set_now ();

      $statuses = $this->app->display_options->job_statuses ();
      $new_status = $statuses [$status];

      // If the job is now closed in this branch, set the closer and the time.
      // If it is once again open, then clear the closer and release.

      if ($new_status->kind == Job_status_kind_closed)
      {
        $this->time_closed->set_now ();
        $this->closer_id = $this->login->id;
        if (! $this->_entry->assignee_id)
          $this->_entry->assignee_id = $this->login->id;
      }
      else
      {
        $this->closer_id = 0;

        // Clear the release only if the release has been shipped (no longer in planning stage).
        // Otherwise, the association can stand.

        $rel =& $this->release ();
        if (isset ($rel) && $rel->locked ())
          $this->release_id = 0;
      }

      $this->status = $status;
    }
  }

  /**
   * All properties of this entry's priority.
   * The available priorities are defined by {@link
   * PROJECT_APPLICATION_DISPLAY_OPTIONS::job_priorities()}.
   */
  function priority_properties ()
  {
    $props = $this->app->display_options->job_priorities ();
    if (isset ($props [$this->priority]))
      return $props [$this->priority];
    else
    {
      $prop = new PROPERTY_VALUE ($this->app);
      $prop->title = "[Unknown priority ($this->priority)]";
      return $prop;
    }
  }

  /**
   * {@link $priority} as an HTML image.
   * @param string $size
   * @return string
   */
  function priority_icon ($size = '16px')
  {
    $props = $this->priority_properties ();
    return $props->icon_as_html ($size);
  }

  /**
   * {@link $priority} as a string.
   * @return string
   */
  function priority_as_text ()
  {
    $props = $this->priority_properties ();

    return $props->title;
  }

  /**
   * All properties of this entry's status.
   * The available statuses are defined by {@link PROJECT_APPLICATION::
   * job_statuses()}.
   */
  function status_properties ()
  {
    $props = $this->app->display_options->job_statuses ();
    if (isset ($props [$this->status]))
      return $props [$this->status];
    else
    {
      $prop = new PROPERTY_VALUE ($this->app);
      $prop->title = "[Unknown status ($this->status)]";
      return $prop;
    }
  }

  /**
   * {@link $status} as an HTML image.
    * @param string $size
    * @return string
    */
  function status_icon ($size = '16px')
  {
    $props = $this->status_properties ();
    return $props->icon_as_html ($size);
  }

  /**
   * {@link $status} as a string.
   * @return string
   */
  function status_as_text ()
  {
    $props = $this->status_properties ();

    return $props->title;
  }

  /**
   * @param DATABASE &$db
   */
  function load (&$db)
  {
    parent::load ($db);
    $this->status = $db->f ('branch_status');
    $this->time_status_changed->set_from_iso ($db->f ('branch_time_status_changed'));
    $this->priority = $db->f ('branch_priority');
    $this->time_closed->set_from_iso ($db->f ('branch_time_closed'));
    $this->closer_id = $db->f ('branch_closer_id');
  }

  /**
   * The name of the 'extra-info' table for this type.
   * @return string
   * @access private
   */
  function _secondary_table_name ()
  {
    return $this->app->table_names->jobs_to_branches;
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
   * @access private
   */
  function store_to (&$storage)
  {
    parent::store_to ($storage);
    $tname = $this->_secondary_table_name ();
    $storage->add ($tname, 'branch_status', Field_type_integer, $this->status);
    $storage->add ($tname, 'branch_priority', Field_type_integer, $this->priority);
    $storage->add ($tname, 'branch_closer_id', Field_type_integer, $this->closer_id);
    if ($this->closer_id)
      $storage->add ($tname, 'branch_time_closed', Field_type_date_time, $this->time_closed);
    $storage->add ($tname, 'branch_time_status_changed', Field_type_date_time, $this->time_status_changed);
  }

  /**
   * Return the needed by status as HTML or plain text.
   * Returns empty if there is no needed by date.
   * @param boolean $text_only Do not use HTML tags when formatting.
   * @return string
   * @access private
   */
  function _needed_by_as_text ($text_only)
  {
    $Result = '';
    $entry =& $this->entry ();
    if ($entry->time_needed->is_valid ())
    {
      $rel = $this->release ();
      if ($rel)
      {
        $occurred = new DATE_TIME ();
        $occurred->clear ();
        include_once ('projects/obj/release_status.php');
        $status = new RELEASE_DATE_STATUS ($rel, $occurred, $entry->time_needed);

        if ($text_only)
        {
          if ($status->text)
            $Result = $status->text . ' ';
        }
        else
        {
          if ($status->icon)
            $Result = $status->icon . ' ';
        }
        $diff_as_text = $status->diff_as_text ($text_only);
        $Result .= 'Needed by ' . $status->date_as_text ($text_only);
        if ($diff_as_text)
        {
          $Result .= ' (' . $diff_as_text;
          if ($status->diff_label)
            $Result .= ' ' . $status->diff_label . ')';
          else
            $Result .= ')';
        }
      }
    }
    return $Result;
  }

  /**
   * Foreign key reference to the entry/branch info.
   * @var integer
   * @access private
   */
  var $entry_to_branch_id;
}

?>