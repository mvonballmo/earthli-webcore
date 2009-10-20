<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.2.0
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
require_once ('webcore/gui/panel.php');

/**
 * Display a list of {@link RELEASE}s in a {@link PANEL}.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class RELEASE_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'release';

  /**
   * @var string
   */
  public $title = 'Releases';

  /**
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * @return RELEASE_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    include_once ('projects/gui/release_grid.php');
    return new RELEASE_GRID ($this->app);
  }
}

/**
 * Display a list of {@link BRANCH}es in a {@link PANEL}.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class BRANCH_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'branch';

  /**
   * @var string
   */
  public $title = 'Branches';

  /**
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * @return BRANCH_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    include_once ('projects/gui/branch_grid.php');
    return new BRANCH_GRID ($this->app);
  }
}

/**
 * Display a list of {@link COMPONENT}s in a {@link PANEL}.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.7.0
 */
class COMPONENT_PANEL extends QUERY_PANEL
{
  /**
   * @var string
   */
  public $id = 'component';

  /**
   * @var string
   */
  public $title = 'Components';

  /**
   * @var boolean
   */
  public $uses_time_selector = false;

  /**
   * @return COMPONENT_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    include_once ('projects/gui/component_grid.php');
    return new COMPONENT_GRID ($this->app);
  }
}

/**
 * Adds restrictions for finding only open jobs.
 * @access private
 */
function restrict_to_open ($query)
{
  $query->restrict ("closer_id = 0");
}

/**
 * Adds restrictions for finding only closed jobs.
 * @access private
 */
function restrict_to_closed ($query)
{
  $query->restrict ("closer_id <> 0");
  $query->set_order ('time_closed DESC');
  $query->store_order_as_recent ();
}

/**
 * Adds restrictions for finding only scheduled jobs.
 * @access private
 */
function restrict_to_scheduled ($query)
{
  $query->add_table ('project_releases rel_sched', 'rel_sched.id = entry.release_id');
  $query->restrict ('not ISNULL(rel_sched.time_next_deadline)');
  $query->restrict ('rel_sched.time_next_deadline <> \'\'');
  $query->restrict ('rel_sched.time_next_deadline <> \'00-00-0000 00:00:00\'');
  $query->add_order ('rel_sched.time_next_deadline ASC', true);
  $query->store_order_as_recent ();
}

/**
 * Adds restrictions for finding only unscheduled jobs.
 * 
 * @param QUERY $query The query to adjust; cannot be null.
 * @access private
 */
function restrict_to_unscheduled ($query)
{
  $query->restrict('entry.release_id = 0');  
}

/**
 * Options used by the project {@link PANEL_MANAGER}s.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.9.0
 */
class PROJECT_PANEL_OPTIONS extends PANEL_OPTIONS
{
  /**
   * Show branch info with objects in the grid?
   * Does not apply to all grids.
   * @var boolean
   */
  public $show_branch = true;

  /**
   * Show release info with objects in the grid?
   * Does not apply to all grids.
   * @var boolean
   */
  public $show_release = true;

  /**
   * Show component info with objects in the grid?
   * Does not apply to all grids.
   * @var boolean
   */
  public $show_component = true;
}

/**
 * Manage a list of {@link PANEL}s for all {@link PROJECT}s.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_INDEX_PANEL_MANAGER extends INDEX_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }
  
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $this->add_panels_after ('project');
    
    $branch_query = $this->login->all_branch_query ();
    $class_name = $this->app->final_class_name ('BRANCH_PANEL', '');
    $panel = new $class_name ($this, $branch_query);
    $this->add_panel ($panel);

    $release_query = $this->login->all_release_query ();
    $class_name = $this->app->final_class_name ('RELEASE_PANEL', '');
    $panel = new $class_name ($this, $release_query);
    $this->add_panel ($panel);

    $this->add_panels_after ('job');

    $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
    $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
    $job_query = $this->login->all_entry_query ();
    $job_query->set_type ('job');

    $in_progress_job_query = clone($job_query);
    $in_progress_job_query->restrict ("status = 3");
    $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
    $panel->id = 'in_progress_jobs';
    $panel->title = 'Jobs in progress';
    $this->add_panel ($panel);

    $scheduled_job_query = clone($job_query);
    restrict_to_open ($scheduled_job_query);
    restrict_to_scheduled ($scheduled_job_query);
    $panel = new $job_panel_class_name ($this, $scheduled_job_query, $job_type_info);
    $panel->id = 'scheduled_jobs';
    $panel->title = 'Scheduled jobs';
    $panel->show_folder = true;
    $this->add_panel ($panel);

    $unscheduled_job_query = clone($job_query);
    restrict_to_open ($unscheduled_job_query);
    restrict_to_unscheduled ($unscheduled_job_query);
    $panel = new $job_panel_class_name ($this, $unscheduled_job_query, $job_type_info);
    $panel->id = 'unscheduled_jobs';
    $panel->title = 'Unscheduled jobs';
    $panel->show_folder = true;
    $this->add_panel ($panel);

    $open_job_query = clone($job_query);
    $open_job_query->restrict ("closer_id = 0");
    restrict_to_open ($open_job_query);
    $panel = new $job_panel_class_name ($this, $open_job_query, $job_type_info);
    $panel->id = 'open_jobs';
    $panel->title = 'Open jobs';
    $this->add_panel ($panel);

    $closed_job_query = clone($job_query);
    restrict_to_closed ($closed_job_query);
    $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
    $panel->id = 'closed_jobs';
    $panel->title = 'Closed jobs';
    $this->add_panel ($panel);

    $unassigned_job_query = clone($job_query);
    $unassigned_job_query->restrict ("assignee_id = 0");
    $panel = new $job_panel_class_name ($this, $unassigned_job_query, $job_type_info);
    $panel->id = 'unassigned_jobs';
    $panel->title = 'Unassigned jobs';
    $this->add_panel ($panel);
    
    $this->move_panel_to ('in_progress_jobs', 0, Panel_selection);
    $this->move_panel_to ('scheduled_jobs', 1, Panel_selection);
    $this->move_panel_to ('open_jobs', 2, Panel_selection);
    $this->move_panel_to ('unassigned_jobs', 3, Panel_selection);
    $this->move_panel_to ('job', 4, Panel_selection);
    $this->move_panel_to ('change', 5, Panel_selection);
  }
}

/**
 * Manage a list of {@link PANEL}s associated with {@link PROJECT}s.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_FOLDER_PANEL_MANAGER extends FOLDER_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }
  
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $this->add_panels_after ('project');

    if (! $this->_folder->is_organizational ())
    {
      $branch_panel_class_name = $this->app->final_class_name ('BRANCH_PANEL', '');
      $branch_query = $this->_folder->branch_query ();
      $panel = new $branch_panel_class_name ($this, $branch_query);
      $this->add_panel ($panel);
  
      $comp_panel_class_name = $this->app->final_class_name ('COMPONENT_PANEL', '');
      $comp_query = $this->_folder->component_query ();
      $panel = new $comp_panel_class_name ($this, $comp_query);
      $this->add_panel ($panel);
  
      $release_panel_class_name = $this->app->final_class_name ('RELEASE_PANEL', '');
      $release_query = $this->_folder->release_query ();
      $panel = new $release_panel_class_name ($this, $release_query);
      $this->add_panel ($panel);
  
      $pending_release_query = $this->_folder->release_query ();
      $pending_release_query->set_up_pending ();
      $panel = new $release_panel_class_name ($this, $pending_release_query);
      $panel->id = 'pending_releases';
      $panel->title = 'Pending releases';
      $this->add_panel ($panel);
  
      $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
      $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
      $job_query = $this->_folder->entry_query ();
      $job_query->set_type ('job');
  
      $this->add_panels_after ('job');
  
      $in_progress_job_query = clone($job_query);
      $in_progress_job_query->restrict ("status = 3");
      $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
      $panel->id = 'in_progress_jobs';
      $panel->title = 'Jobs in progress';
      $this->add_panel ($panel);
  
      $scheduled_job_query = clone($job_query);
      restrict_to_open ($scheduled_job_query);
      restrict_to_scheduled ($scheduled_job_query);
      $panel = new $job_panel_class_name ($this, $scheduled_job_query, $job_type_info);
      $panel->id = 'scheduled_jobs';
      $panel->title = 'Scheduled jobs';
      $this->add_panel ($panel);
  
      $unscheduled_job_query = clone($job_query);
      restrict_to_open ($unscheduled_job_query);
      restrict_to_unscheduled ($unscheduled_job_query);
      $panel = new $job_panel_class_name ($this, $unscheduled_job_query, $job_type_info);
      $panel->id = 'unscheduled_jobs';
      $panel->title = 'Unscheduled jobs';
      $this->add_panel ($panel);
        
      $open_job_query = clone($job_query);
      restrict_to_open ($open_job_query);
      $panel = new $job_panel_class_name ($this, $open_job_query, $job_type_info);
      $panel->id = 'open_jobs';
      $panel->title = 'Open jobs';
      $this->add_panel ($panel);
  
      $closed_job_query = clone($job_query);
      restrict_to_closed ($closed_job_query);
      $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
      $panel->id = 'closed_jobs';
      $panel->title = 'Closed jobs';
      $this->add_panel ($panel);
  
      $unassigned_job_query = clone($job_query);
      $unassigned_job_query->restrict ("assignee_id = 0");
      $panel = new $job_panel_class_name ($this, $unassigned_job_query, $job_type_info);
      $panel->id = 'unassigned_jobs';
      $panel->title = 'Unassigned jobs';
      $this->add_panel ($panel);
  
      $this->move_panel_to ('pending_releases', 0, Panel_selection);
      $this->move_panel_to ('in_progress_jobs', 1, Panel_selection);
      $this->move_panel_to ('scheduled_jobs', 2, Panel_selection);
      $this->move_panel_to ('open_jobs', 3, Panel_selection);
      $this->move_panel_to ('unassigned_jobs', 4, Panel_selection);
      $this->move_panel_to ('job', 5, Panel_selection);
      $this->move_panel_to ('change', 6, Panel_selection);
    }
  }
}

/**
 * Manage a list of {@link PANEL}s associated with {@link PROJECT_USER}s.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_USER_PANEL_MANAGER extends USER_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }
  
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $this->add_panels_after ('job');

    $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
    $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
    $job_query = $this->login->user_entry_query ($this->_user->id);
    $job_query->set_type ('job');
    $all_jobs_query = $this->login->all_entry_query ();
    $all_jobs_query->set_type ('job');
    $user_id = $this->_user->id;

    $in_progress_job_query = clone($job_query);
    $in_progress_job_query->restrict ('assignee_id = ' . $user_id);
    $in_progress_job_query->restrict ('status = 3');
    $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
    $panel->id = 'in_progress_jobs';
    $panel->title = 'Jobs in progress';
    $this->add_panel ($panel);

    $scheduled_job_query = clone($job_query);
    restrict_to_open ($scheduled_job_query);
    restrict_to_scheduled ($scheduled_job_query);
    $scheduled_job_query->restrict ('assignee_id = ' . $user_id);
    $panel = new $job_panel_class_name ($this, $scheduled_job_query, $job_type_info);
    $panel->id = 'scheduled_jobs';
    $panel->title = 'Scheduled jobs';
    $this->add_panel ($panel);

    $unscheduled_job_query = clone($job_query);
    restrict_to_open ($unscheduled_job_query);
    restrict_to_unscheduled ($unscheduled_job_query);
    $panel = new $job_panel_class_name ($this, $unscheduled_job_query, $job_type_info);
    $panel->id = 'unscheduled_jobs';
    $panel->title = 'Unscheduled jobs';
    $this->add_panel ($panel);

    $assigned_job_query = clone($job_query);
    $assigned_job_query->restrict ('assignee_id = ' . $user_id);
    restrict_to_open ($assigned_job_query);
    $panel = new $job_panel_class_name ($this, $assigned_job_query, $job_type_info);
    $panel->id = 'assigned_jobs';
    $panel->title = 'Assigned jobs';
    $this->add_panel ($panel);

    $closed_job_query = clone($job_query);
    $closed_job_query->restrict ('closer_id = ' . $user_id);
    $closed_job_query->set_order ('job.time_closed DESC');
    $closed_job_query->store_order_as_recent ();
    $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
    $panel->id = 'closed_jobs';
    $panel->title = 'Closed jobs';
    $this->add_panel ($panel);

    $reported_job_query = clone($job_query);
    $reported_job_query->restrict ('reporter_id = ' . $user_id);
    $reported_job_query->restrict ('entry.creator_id <> ' . $user_id);
    $panel = new $job_panel_class_name ($this, $reported_job_query, $job_type_info);
    $panel->id = 'reported_jobs';
    $panel->title = 'Reported jobs';
    $this->add_panel ($panel);

    $this->move_panel_to ('in_progress_jobs', 1, Panel_selection);
    $this->move_panel_to ('scheduled_jobs', 2, Panel_selection);
    $this->move_panel_to ('assigned_jobs', 3, Panel_selection);
    $this->move_panel_to ('closed_jobs', 4, Panel_selection);
    $this->move_panel_to ('job', 5, Panel_selection);
    $this->move_panel_to ('change', 6, Panel_selection);
  }
}

/**
 * Manage a list of {@link PANEL}s associated with {@link BRANCH}es.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_BRANCH_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = false;
    $options->show_branch = false;
  }
  
  /**
   * @param PROJECT $folder Project for which to show panels.
   */
  public function __construct ($branch)
  {
    $this->_branch = $branch;
    parent::__construct ($branch->app);
  }
  
  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $comp_panel_class_name = $this->app->final_class_name ('COMPONENT_PANEL', '');
    $folder = $this->_branch->parent_folder ();
    $comp_query = $folder->component_query ();
    $panel = new $comp_panel_class_name ($this, $comp_query);
    $this->add_panel ($panel);

    $release_panel_class_name = $this->app->final_class_name ('RELEASE_PANEL', '');
    $release_query = $this->_branch->release_query ();
    $panel = new $release_panel_class_name ($this, $release_query);
    $this->add_panel ($panel);

    $pending_release_query = $this->_branch->pending_release_query ();
    $panel = new $release_panel_class_name ($this, $pending_release_query);
    $panel->id = 'pending_releases';
    $panel->title = 'Pending releases';
    $this->add_panel ($panel);

    $this->_add_entry_panels_for ($this->_branch->entry_query ());

    $this->add_panels_after ('job');

    $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
    $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
    $job_query = $this->_branch->job_query ();

    $in_progress_job_query = clone($job_query);
    $in_progress_job_query->restrict ("jtob.branch_status = 3");
    $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
    $panel->id = 'in_progress_jobs';
    $panel->title = 'Jobs in progress';
    $this->add_panel ($panel);

    $scheduled_job_query = clone($job_query);
    $scheduled_job_query->restrict ("jtob.branch_closer_id = 0");
    restrict_to_scheduled ($scheduled_job_query);
    $panel = new $job_panel_class_name ($this, $scheduled_job_query, $job_type_info);
    $panel->id = 'scheduled_jobs';
    $panel->title = 'Scheduled jobs';
    $this->add_panel ($panel);

    $unscheduled_job_query = clone($job_query);
    $unscheduled_job_query->restrict ("jtob.branch_closer_id = 0");
    restrict_to_unscheduled ($unscheduled_job_query);
    $panel = new $job_panel_class_name ($this, $unscheduled_job_query, $job_type_info);
    $panel->id = 'unscheduled_jobs';
    $panel->title = 'Unscheduled jobs';
    $this->add_panel ($panel);
    
    $open_job_query = clone($job_query);
    $open_job_query->restrict ("jtob.branch_closer_id = 0");
    $panel = new $job_panel_class_name ($this, $open_job_query, $job_type_info);
    $panel->id = 'open_jobs';
    $panel->title = 'Open jobs';
    $this->add_panel ($panel);

    $unassigned_job_query = clone($job_query);
    $unassigned_job_query->restrict ("assignee_id = 0");
    $panel = new $job_panel_class_name ($this, $unassigned_job_query, $job_type_info);
    $panel->id = 'unassigned_jobs';
    $panel->title = 'Unassigned jobs';
    $this->add_panel ($panel);

    $closed_job_query = clone($job_query);
    $closed_job_query->restrict ("jtob.branch_closer_id <> 0");
    $closed_job_query->set_order ('jtob.branch_time_closed DESC');
    $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
    $panel->id = 'closed_jobs';
    $panel->title = 'Closed jobs';
    $this->add_panel ($panel);

    $this->add_panels_after ('change');
    
    $this->_add_comment_panel_for ($this->_branch->comment_query ());

    $this->move_panel_to ('pending_releases', 0, Panel_selection);
    $this->move_panel_to ('in_progress_jobs', 1, Panel_selection);
    $this->move_panel_to ('scheduled_jobs', 2, Panel_selection);
    $this->move_panel_to ('open_jobs', 3, Panel_selection);
    $this->move_panel_to ('unassigned_jobs', 4, Panel_selection);
    $this->move_panel_to ('job', 5, Panel_selection);
    $this->move_panel_to ('change', 6, Panel_selection);
  }
  
  /**
   * @var BRANCH
   * @access private
   */
  protected $_branch;
}

/**
 * Manage a list of {@link PANEL}s associated with {@link RELEASE}es.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_RELEASE_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = false;
    $options->show_branch = false;
    $options->show_release = false;
  }

  /**
   * @param PROJECT $folder Project for which to show panels.
   */
  public function __construct ($release)
  {
    $this->_release = $release;
    parent::__construct ($release->app);
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $comp_panel_class_name = $this->app->final_class_name ('COMPONENT_PANEL', '');
    $folder = $this->_release->parent_folder ();
    $comp_query = $folder->component_query ();
    $panel = new $comp_panel_class_name ($this, $comp_query);
    $this->add_panel ($panel);

    $this->_add_entry_panels_for ($this->_release->entry_query ());

    $this->add_panels_after ('job');

    $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
    $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
    $job_query = $this->_release->job_query ();

    $in_progress_job_query = clone($job_query);
    $in_progress_job_query->restrict ("jtob.branch_status = 3");
    $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
    $panel->id = 'in_progress_jobs';
    $panel->title = 'Jobs in progress';
    $this->add_panel ($panel);

    $open_job_query = clone($job_query);
    $open_job_query->restrict ("jtob.branch_closer_id = 0");
    $panel = new $job_panel_class_name ($this, $open_job_query, $job_type_info);
    $panel->id = 'open_jobs';
    $panel->title = 'Open jobs';
    $this->add_panel ($panel);

    $unassigned_job_query = clone($job_query);
    $unassigned_job_query->restrict ("assignee_id = 0");
    $panel = new $job_panel_class_name ($this, $unassigned_job_query, $job_type_info);
    $panel->id = 'unassigned_jobs';
    $panel->title = 'Unassigned jobs';
    $this->add_panel ($panel);

    $closed_job_query = clone($job_query);
    $closed_job_query->restrict ("jtob.branch_closer_id <> 0");
    $closed_job_query->set_order ('jtob.branch_time_closed DESC');
    $closed_job_query->store_order_as_recent ();
    $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
    $panel->id = 'closed_jobs';
    $panel->title = 'Closed jobs';
    $this->add_panel ($panel);

    $this->_add_comment_panel_for ($this->_release->comment_query ());

    $this->move_panel_to ('in_progress_jobs', 0, Panel_selection);
    $this->move_panel_to ('open_jobs', 1, Panel_selection);
    $this->move_panel_to ('unassigned_jobs', 2, Panel_selection);
    $this->move_panel_to ('closed_jobs', 3, Panel_selection);
    $this->move_panel_to ('job', 4, Panel_selection);
    $this->move_panel_to ('change', 5, Panel_selection);
  }
  
  /**
   * @var RELEASE
   * @access private
   */
  protected $_release;
}

/**
 * Manage a list of {@link PANEL}s associated with {@link PROJECT}s.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class PROJECT_COMPONENT_PANEL_MANAGER extends WEBCORE_PANEL_MANAGER
{
  /**
   * Create panel options for display.
   * @return PROJECT_PANEL_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new PROJECT_PANEL_OPTIONS ();
  }

  /**
   * Initialize the panel options.
   * @param PANEL_OPTIONS $options
   */
  protected function _init_options ($options)
  {
    $options->show_folder = false;
    $options->show_component = false;
  }

  /**
   * @param PROJECT $folder Project for which to show panels.
   */
  public function __construct ($component)
  {
    $this->_component = $component;
    parent::__construct ($component->app);
  }

  /**
   * Create the set of panels to use.
   * @access private
   */
  protected function _add_panels ()
  {
    parent::_add_panels ();

    $this->_add_entry_panels_for ($this->_component->entry_query ());

    $this->add_panels_after ('job');

    $job_panel_class_name = $this->app->final_class_name ('ENTRY_PANEL', '', 'job');
    $job_type_info = $this->app->type_info_for ('JOB', 'projects/obj/job.php');
    $job_query = $this->_component->job_query ();

    $in_progress_job_query = clone($job_query);
    $in_progress_job_query->restrict ("status = 3");
    $panel = new $job_panel_class_name ($this, $in_progress_job_query, $job_type_info);
    $panel->id = 'in_progress_jobs';
    $panel->title = 'Jobs in progress';
    $this->add_panel ($panel);

    $scheduled_job_query = clone($job_query);
    restrict_to_open ($scheduled_job_query);
    restrict_to_scheduled ($scheduled_job_query);
    $panel = new $job_panel_class_name ($this, $scheduled_job_query, $job_type_info);
    $panel->id = 'scheduled_jobs';
    $panel->title = 'Scheduled jobs';
    $this->add_panel ($panel);

    $unscheduled_job_query = clone($job_query);
    restrict_to_open ($unscheduled_job_query);
    restrict_to_unscheduled ($unscheduled_job_query);
    $panel = new $job_panel_class_name ($this, $unscheduled_job_query, $job_type_info);
    $panel->id = 'unscheduled_jobs';
    $panel->title = 'Unscheduled jobs';
    $this->add_panel ($panel);
    
    $open_job_query = clone($job_query);
    restrict_to_open ($open_job_query);
    $panel = new $job_panel_class_name ($this, $open_job_query, $job_type_info);
    $panel->id = 'open_jobs';
    $panel->title = 'Open jobs';
    $this->add_panel ($panel);

    $unassigned_job_query = clone($job_query);
    $unassigned_job_query->restrict ("assignee_id = 0");
    $panel = new $job_panel_class_name ($this, $unassigned_job_query, $job_type_info);
    $panel->id = 'unassigned_jobs';
    $panel->title = 'Unassigned jobs';
    $this->add_panel ($panel);

    $closed_job_query = clone($job_query);
    restrict_to_closed ($closed_job_query);
    $panel = new $job_panel_class_name ($this, $closed_job_query, $job_type_info);
    $panel->id = 'closed_jobs';
    $panel->title = 'Closed Jobs';
    $this->add_panel ($panel);

    $this->_add_comment_panel_for ($this->_component->comment_query ());

    $this->move_panel_to ('in_progress_jobs', 0, Panel_selection);
    $this->move_panel_to ('scheduled_jobs', 1, Panel_selection);
    $this->move_panel_to ('open_jobs', 2, Panel_selection);
    $this->move_panel_to ('unassigned_jobs', 3, Panel_selection);
    $this->move_panel_to ('closed_jobs', 4, Panel_selection);
    $this->move_panel_to ('job', 5, Panel_selection);
    $this->move_panel_to ('change', 6, Panel_selection);
  }

  /**
   * @var COMPONENT
   * @access private
   */
  protected $_component;
}

?>