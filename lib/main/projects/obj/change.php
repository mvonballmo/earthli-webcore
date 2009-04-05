<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

/**
 * Represents a change made to a {@link PROJECT}.
 * This can be a bug fix, a new feature, a general change or something else. Can be attached
 * to {@link JOB}s.
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 */
class CHANGE extends PROJECT_ENTRY
{
  /**
   * The number of the change within the project.
   * Always one more than the last change made.
   * @var integer
   */
  public $number;

  /**
   * Id of the job to which this change is attached.
   * Can be empty.
   * @var integer
   * @see PROJECT_ENTRY::job ()
   */
  public $job_id;

  /**
   * List of files associated with this change.
   * Can be empty. If non-empty, should be a newline-separated list. This is
   * useful for integration with source-control systems that show the affected
   * files.
   * @var string
   */
  public $files;

  /**
   * Job associated with this change.
   * May be empty.
   * @return JOB
   */
  public function job ()
  {
    if (! isset ($this->_job))
    {
      $fldr = $this->parent_folder ();
      $job_query = $fldr->entry_query ();
      $this->_job = $job_query->object_at_id ($this->job_id);
    }

    return $this->_job;
  }

  /**
   * Number of files listed in 'files'.
   * @return integer
   */
  public function num_files ()
  {
    return substr_count (trim ($this->files), "\n") + 1;
  }

  /**
   * 'files' formatted as HTML by the MUNGER.
   * @return string
   * @see MUNGER
   */
  public function files_as_html ()
  {
    $munger = $this->html_formatter ();
    $munger->force_paragraphs = true;
    return $this->_text_as_html ("<code>$this->files</code>", $munger);
  }

  public function branch_info_query ()
  {
    include_once ('projects/db/entry_branch_query.php');
    return new CHANGE_BRANCH_INFO_QUERY ($this);
  }

  public function raw_title ()
  {
    if (isset ($this->number))
    {
      $number = '#' . $this->number;
    }
    else
    {
      $number = '#???';
    }
    $Result = parent::raw_title ();
    if ($Result)
    {
      $Result = "$number - $Result";
    }
    else
    {
      $Result = $number;
    }
    return $Result;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->number = $db->f ('number');
    $this->job_id = $db->f ('job_id');
    $this->files = $db->f ('files');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->_secondary_table_name ();
    $storage->add ($tname, 'number', Field_type_integer, $this->number, Storage_action_create);
    $storage->add ($tname, 'job_id', Field_type_integer, $this->job_id);
    $storage->add ($tname, 'files', Field_type_string, $this->files);
    $storage->add ($tname, 'applier_id', Field_type_integer, $this->_main_branch_info->applier_id);
    $storage->add ($tname, 'time_applied', Field_type_date_time, $this->_main_branch_info->time_applied);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->change_home;
  }
  
  /**
   * Returns a new object which maps this change to the given branch.
   * @param BRANCH $branch
   * @return CHANGE_BRANCH_INFO
   */
  public function new_branch_info ($branch)
  {
    $Result = parent::new_branch_info ($branch);
    $Result->time_applied->set_now ();
    $Result->applier_id = $this->app->login->id;
    return $Result;
  }

  /**
   * Name of this object's secondary database table.
   * @return string
   * @access private
   */
  protected function _secondary_table_name ()
  {
    return $this->app->table_names->changes;
  }

  /**
   * @access private
   */
  protected function _create ()
  {
    $this->db->logged_query ("SELECT MAX(number) FROM {$this->app->table_names->changes} chng" .
                             " INNER JOIN {$this->app->table_names->entries} entry on chng.entry_id = entry.id" .
                             " WHERE folder_id = " . $this->parent_folder_id ());
    if ($this->db->next_record ())
    {
      $this->number = $this->db->f (0) + 1;
    }

    parent::_create ();
  }

  /**
   * @return CHANGE_BRANCH_INFO
   * @access private
   */
  protected function _make_branch_info ()
  {
    return new CHANGE_BRANCH_INFO ($this);
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_navigator:
        include_once ('projects/gui/change_navigator.php');
        return new CHANGE_NAVIGATOR ($this);
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('projects/gui/change_renderer.php');
        return new CHANGE_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/change_commands.php');
        return new CHANGE_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new CHANGE_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * Apply class-specific restrictions to this query.
   * @param SUBSCRIPTION_QUERY $query
   * @param HISTORY_ITEM $history_item Action that generated this request. May be empty.
   * @access private
   */
  protected function _prepare_subscription_query ($query, $history_item)
  {
    $query->restrict ('watch_entries > 0');
    $query->restrict_kinds (array (Subscribe_folder => $this->parent_folder_id ()
                                   , Subscribe_entry => $this->job_id
                                   , Subscribe_entry => $this->id
                                   , Subscribe_user => $this->creator_id));
  }

  /**
   * Name of this type of project entry.
   * @var string
   * @access private
   */
  public $type = 'change';

  /**
   * @var JOB
   * @access private
   */
  protected $_job;
}

/**
 * Connects a {@link CHANGE} to a particular {@link BRANCH}.
 * Manages a change's relatationship within a branch. Use this to update settings,
 * and to add or remove a change from a branch.
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 */
class CHANGE_BRANCH_INFO extends PROJECT_ENTRY_BRANCH_INFO
{
  /**
   * When was this change applied?
   * @var DATE_TIME
   */
  public $time_applied;

  /**
   * Which user applied the change?
   * @var integer
   * @see CHANGE::applier()
   */
  public $applier_id;

  /**
   * @param CHANGE $entry Branch info is attached to this job.
   */
  public function CHANGE_BRANCH_INFO ($entry)
  {
    PROJECT_ENTRY_BRANCH_INFO::PROJECT_ENTRY_BRANCH_INFO ($entry);

    $this->time_applied = $this->app->make_date_time ();
  }

  /**
   * Who applied this change?
   * @return PROJECT_USER
   */
  public function applier ()
  {
    return $this->app->user_at_id ($this->applier_id, false, true);
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->time_applied->set_from_iso ($db->f ('branch_time_applied'));
    $this->applier_id = $db->f ('branch_applier_id');
  }

  /**
   * The name of the 'extra-info' table for this type.
   * @return string
   * @access private
   */
  protected function _secondary_table_name ()
  {
    return $this->app->table_names->changes_to_branches;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   * @access private
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->_secondary_table_name ();
    $storage->add ($tname, 'branch_applier_id', Field_type_integer, $this->applier_id);
    $storage->add ($tname, 'branch_time_applied', Field_type_date_time, $this->time_applied);
  }

  /**
   * Foreign key reference to the entry/branch info.
   * @var integer
   * @access private
   */
  public $entry_to_branch_id;
}

?>