<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage config
 * @version 3.6.0
 * @since 1.9.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/config/application_config.php');

/**
 * @package projects
 * @subpackage config
 * @version 3.6.0
 * @since 1.9.0
 */
class PROJECT_APPLICATION_DISPLAY_OPTIONS extends APPLICATION_DISPLAY_OPTIONS
{
  /**
   * List of {@link PROJECT_ENTRY::$kind}s.
   * @see add_entry_kind()
   * @see PROPERTY_VALUE
   * @return PROPERTY_VALUE[]
   */
  public function entry_kinds ()
  {
    if (! isset ($this->_entry_kinds))
    {
      $this->_init_entry_kinds ();
    }
    return $this->_entry_kinds;
  }

  /**
   * List of {@link JOB_BRANCH_INFO::$priority}s.
   * @see add_job_priority()
   * @see PROPERTY_VALUE
   * @return PROPERTY_VALUE[]
   */
  public function job_priorities ()
  {
    if (! isset ($this->_job_priorities))
    {
      $this->_init_job_priorities ();
    }
    return $this->_job_priorities;
  }

  /**
   * List of {@link JOB_BRANCH_INFO::$status}es.
   * @see add_job_status()
   * @see JOB_STATUS_VALUE
   * @return JOB_STATUS_VALUE[]
   */
  public function job_statuses ()
  {
    if (! isset ($this->_job_statuses))
    {
      $this->_init_job_statuses ();
    }
    return $this->_job_statuses;
  }

  /**
   * List of {@link JOB_BRANCH_INFO::$status}es to map when a {@link RELEASE} is shipped.
   * @see add_job_status_map()
   * @see JOB_STATUS_MAP
   * @return JOB_STATUS_MAP[]
   */
  public function job_status_map ()
  {
    if (! isset ($this->_job_status_map))
    {
      $this->_init_job_status_map ();
    }
    return $this->_job_status_map;
  }

  /**
   * List of statuses to which a job with this status can be transferred.
   * Return only show the statuses on the same or higher level than this one.
   * That means that it's possible to make certain statuses not available once
   * their level has been passed. For example, 'open' is at a lower level than
   * all others, since you can never return to just 'open' again. You can
   * instead go to 're-opened'.
   * @see JOB_STATUS_MAP
   * @see job_statuses()
   * @param $status
   * @return JOB_STATUS_MAP[]
   */
  public function job_statuses_for ($status)
  {
    $Result = array ();

    $statuses = $this->job_statuses ();

    if (sizeof ($statuses))
    {
      if (isset ($statuses [$status]))
      {
        $current_status = $statuses [$status];

        foreach ($statuses as $status)
        {
          if ($status->level >= $current_status->level)
          {
            $Result [] = $status;
          }
        }
      }
      else
      {
        $Result = $statuses;
      }
    }

    return $Result;
  }

  /**
   * Add a possible {@link PROJECT_ENTRY::$kind} value.
   * Retrieve the current list with {@link entry_kinds()}.
   * @see PROPERTY_VALUE
   * @param integer $value
   * @param string $title
   * @param string $icon
   */
  public function add_entry_kind ($value, $title, $icon)
  {
    include_once ('webcore/sys/property.php');
    $kind = new PROPERTY_VALUE ($this->context);
    $kind->value = $value;
    $kind->title = $title;
    $kind->icon = '{' . Folder_name_app_icons . '}/kinds/' . $icon;
    $this->_entry_kinds [$kind->value] = $kind;
  }

  /**
   * Add a possible {@link JOB_BRANCH_INFO::$priority} value.
   * Retrieve the current list with {@link job_priorities()}.
   * @see PROPERTY_VALUE
   * @param integer $value
   * @param string $title
   * @param string $icon
   */
  public function add_job_priority ($value, $title, $icon)
  {
    include_once ('webcore/sys/property.php');
    $priority = new PROPERTY_VALUE ($this->context);
    $priority->title = $title;
    $priority->value = $value;
    $priority->icon = '{' . Folder_name_app_icons . '}priorities/' . $icon;
    $this->_job_priorities [$priority->value] = $priority;
  }

  /**
   * Add a possible {@link JOB_BRANCH_INFO::$status} value.
   * Retrieve the current list with {@link job_statuses()}.
   * @see JOB_STATUS_VALUE
   * @param integer $value
   * @param string $title
   * @param string $icon
   * @param integer $kind
   * @param integer $level
   */
  public function add_job_status ($value, $title, $icon, $kind, $level)
  {
    include_once ('projects/obj/job.php');
    $status = new JOB_STATUS_VALUE ($this->context);
    $status->value = $value;
    $status->title = $title;
    $status->icon = '{' . Folder_name_app_icons . '}statuses/' . $icon;
    $status->kind = $kind;
    $status->level = $level;
    $this->_job_statuses [$status->value] = $status;
  }

  /**
   * Set the mapping from a list of {@link JOB_BRANCH_INFO::$status}es to another.
   * Used to indicate which statuses should be converted to another status
   * when a release is shipped. This allows various states to be marked as
   * 'closed' so that a shipped release has no open issues. Retrieve the current
   * list with {@link job_status_map()}.
   * @see JOB_STATUS_MAP
   * @param integer[] $from_statuses
   * @param integer $to_status
   */
  public function set_job_status_mapping ($from_statuses, $to_status)
  {
    $this->_job_status_map = new JOB_STATUS_MAP ($this->context, $from_statuses, $to_status);
  }

  /**
   * Initialize the initial list of entry kinds.
   * Called from {@link entry_kinds()}.
   * @access private
   */
  protected function _init_entry_kinds ()
  {
    $this->add_entry_kind (0, 'Bug fix', 'bug_fix');
    $this->add_entry_kind (1, 'New feature', 'new_feature');
    $this->add_entry_kind (2, 'General work', 'general_work');
    $this->add_entry_kind (3, 'Bug with workaround', 'workaround');
    $this->add_entry_kind (4, 'Improvement', 'improvement');
  }

  /**
   * Initialize the initial list of job priorities.
   * Called from {@link job_priorities()}.
   * @access private
   */
  protected function _init_job_priorities ()
  {
    $this->add_job_priority (0, 'Trivial', 'trivial');
    $this->add_job_priority (1, 'Minor (needed eventually)', 'minor');
    $this->add_job_priority (2, 'Major (needed soon)', 'major');
    $this->add_job_priority (3, 'Critical (cannot ship)', 'critical');
    $this->add_job_priority (4, 'Showstopper (cannot work)', 'showstopper');
  }

  /**
   * Initialize the initial list of job statuses.
   * Called from {@link job_statuses()}.
   * @access private
   */
  protected function _init_job_statuses ()
  {
    $this->add_job_status (0, 'Open (New)', 'new', Job_status_kind_open, 0);
    $this->add_job_status (1, 'Reopened', 'reopened', Job_status_kind_open, 1);
    $this->add_job_status (2, 'Waiting for input', 'waiting', Job_status_kind_open, 1);
    $this->add_job_status (3, 'Working', 'working', Job_status_kind_open, 1);
    $this->add_job_status (4, 'Stopped working', 'stopped', Job_status_kind_open, 1);
    $this->add_job_status (5, 'Testing', 'working', Job_status_kind_open, 1);
    $this->add_job_status (6, 'Patched', 'released', Job_status_kind_closed, 1);
    $this->add_job_status (7, 'Fixed', 'released', Job_status_kind_closed, 1);
    $this->add_job_status (8, 'Released', 'released', Job_status_kind_closed, 1);
    $this->add_job_status (9, 'Closed', 'closed', Job_status_kind_closed, 1);
    $this->add_job_status (10, 'Abandoned', 'abandoned', Job_status_kind_closed, 1);
    $this->add_job_status (11, 'Cannot reproduce', 'non-reproducible', Job_status_kind_closed, 1);
    $this->add_job_status (12, 'Not a bug', 'not-a-bug', Job_status_kind_closed, 1);
  }

  /**
   * Initialize the initial job status map.
   * Called from {@link job_status_map()}.
   * @access private
   */
  protected function _init_job_status_map ()
  {
    /* The mapping below marks 'Patched', 'Fixed' and 'Released' entries as 'Closed' in a shipped release. */
    $this->set_job_status_mapping (array (6, 7, 8), 9);
  }

  /**
   * List of entry kinds.
   * @see PROPERTY_VALUE
   * @var PROPERTY_VALUE[]
   */
  protected $_entry_kinds;

  /**
   * List of job priorities.
   * @see PROPERTY_VALUE
   * @var PROPERTY_VALUE[]
   */
  protected $_job_priorities;

  /**
   * List of job statuses.
   * @see JOB_STATUS_VALUE
   * @var JOB_STATUS_VALUE[]
   */
  protected $_job_statuses;

  /**
   * List of job status mappings.
   * @var JOB_STATUS_MAP
   */
  protected $_job_status_map;
}

/**
 * Maps statuses when a {@link RELEASE} is shipped.
 * @package projects
 * @subpackage config
 * @version 3.6.0
 * @since 1.5.0
 * @access private
 */
class JOB_STATUS_MAP extends WEBCORE_OBJECT
{
  /**
   * List of statuses to map from.
   * Stored as a comma-separated list.
   * @var string
   */
  public $from;

  /**
   * Map statuses in 'map_from' to this status.
   * @var integer
   */
  public $to;

  /**
   * @param APPLICATION $context
   * @param integer[] $from
   * @param integer $to
   */
  public function __construct ($context, $from, $to)
  {
    parent::__construct ($context);

    $this->from = join (',', $from);
    $this->to = $to;
  }

  /**
   * The name of the {@link $to} status.
   * @return JOB_STATUS_VALUE
   */
  public function to_status ()
  {
    /** @var PROJECT_APPLICATION_DISPLAY_OPTIONS $display_options */
    $display_options = $this->app->display_options;
    $statuses = $display_options->job_statuses ();
    return $statuses [$this->to];
  }
}