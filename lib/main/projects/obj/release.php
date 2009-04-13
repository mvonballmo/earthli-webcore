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
require_once ('webcore/obj/object_in_folder.php');

/**
 * Marks a milestone in a {@link BRANCH}.
 * This can be a release, an internal milestone or a patch. All {@link JOB}s closed and
 * {@link CHANGE}s made since the last release are included in this release when it is created.
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 */
class RELEASE extends OBJECT_IN_FOLDER
{
  /**
   * This release belongs to this branch.
   * @var integer
   * @see RELEASE::branch()
   */
  public $branch_id;

  /**
   * Scheduled release date.
   * @var DATE_TIME
   */
  public $time_scheduled;

  /**
   * Scheduled internal testing date.
   * @var DATE_TIME
   */
  public $time_testing_scheduled;

  /**
   * Actual internal testing date.
   * @var DATE_TIME
   */
  public $time_tested;

  /**
   * Actual release date.
   * @var DATE_TIME
   */
  public $time_shipped;

  /**
   * Next due date (testing or release).
   * @var DATE_TIME
   */
  public $time_next_deadline;

  /**
   * @param PROJECT_APPLICATION $app Main application.
   */
  public function RELEASE ($app)
  {
    OBJECT_IN_FOLDER::OBJECT_IN_FOLDER ($app);

    $this->time_scheduled = $app->make_date_time ();
    $this->time_testing_scheduled = $app->make_date_time ();
    $this->time_next_deadline = $app->make_date_time ();

    $this->time_tested = $app->make_date_time ();
    $this->time_tested->clear ();

    $this->time_shipped = $app->make_date_time ();
    $this->time_shipped->clear ();
  }

  public function planned ()
  {
    return ! $this->time_shipped->is_valid ();
  }

  public function shipped ()
  {
    return $this->state == Shipped;
  }

  /**
   * The state of this object as a string.
   * Useful for formatting titles and object descriptions.
   * @return string
   */
  public function state_as_string ()
  {
    switch ($this->state)
    {
    case Planned:
      return 'Planned';
    case Testing:
      return 'Testing';
    case Shipped:
      return 'Shipped';
    case Locked:
      return 'Locked';
    default:
      return parent::state_as_string ();
    }
  }

  /**
   * Set the due date.
   * @param DATE_TIME $t
   */
  public function set_time_scheduled ($t)
  {
    if ($t)
    {
      $this->time_scheduled = $t;
    }
    else
    {
      $this->time_scheduled->clear ();
    }

    $this->_update_next_deadline ();
  }

  /**
   * Set the release-to-testing date.
   * @param DATE_TIME $t
   */
  public function set_time_testing_scheduled ($t)
  {
    if ($t)
    {
      $this->time_testing_scheduled = $t;
    }
    else
    {
      $this->time_testing_scheduled->clear ();
    }

    $this->_update_next_deadline ();
  }

  /**
   * Return the time to issue "warning" for the given date.
   * The {@link RELEASE_DATE_STATUS} uses this time to determine whether to format
   * a warning into the status or not. Based on values taken from {@link PROJECT_OPTIONS}.
   * @param DATE_TIME $date
   * @return DATE_TIME
   */
  public function warning_time ($date)
  {
    $fldr = $this->parent_folder ();
    $options = $fldr->options ();
    switch ($options->seconds_until_deadline)
    {
    case 0:
      return new DATE_TIME ();
    default:
      return new DATE_TIME ($date->as_php () - $options->seconds_until_deadline);
    }
  }

  protected function _update_next_deadline ()
  {
    $rel_valid = $this->time_scheduled->is_valid ();
    $test_valid  = $this->time_testing_scheduled->is_valid ();

    if ($rel_valid)
    {
      if ($test_valid)
      {
        if ($this->time_scheduled->less_than ($this->time_testing_scheduled))
        {
          $this->time_next_deadline = $this->time_scheduled;
        }
        else
        {
          $this->time_next_deadline = $this->time_testing_scheduled;
        }
      }
      else
      {
        $this->time_next_deadline = $this->time_scheduled;
      }
    }
    else
    {
      if ($test_valid)
      {
        $this->time_next_deadline = $this->time_testing_scheduled;
      }
      else
      {
        $this->time_next_deadline->clear ();
      }
    }
  }

  /**
   * List of all entries (jobs or changes) for this release.
   * @return BRANCH_ENTRY_QUERY
   */
  public function entry_query ()
  {
    $class_name = $this->app->final_class_name ('BRANCH_ENTRY_QUERY', 'projects/db/branch_entry_query.php');
    $Result = new $class_name ($this->branch ());
    $Result->restrict ("etob.branch_release_id = $this->id");
    return $Result;
  }

  /**
   * List of all changes for this release.
   * @return PROJECT_ENTRY_QUERY
   */
  public function change_query ()
  {
    $Result = $this->entry_query ();
    $Result->set_type ('change');
    return $Result;
  }

  /**
   * List of all jobs for this release.
   * @return PROJECT_ENTRY_QUERY
   */
  public function job_query ()
  {
    $Result = $this->entry_query ();
    $Result->set_type ('job');
    return $Result;
  }

  /**
   * List of all {@link COMMENT}s for this branch.
   * @return BRANCH_COMMENT_QUERY
   */
  public function comment_query ()
  {
    $class_name = $this->app->final_class_name ('BRANCH_COMMENT_QUERY', 'projects/db/branch_comment_query.php');
    $Result = new $class_name ($this->branch ());
    $Result->restrict ("etob.branch_release_id = $this->id");
    return $Result;
  }

  /**
   * The branch on which this release occurred (or is planned).
   * @return BRANCH
   */
  public function branch ()
  {
    $this->assert (isset ($this->_branch), '_branch is not cached.', 'branch', 'RELEASE');
    return $this->_branch;
  }

  /**
   * Current overall status as a set of properties.
   * @return RELEASE_STATUS
   */
  public function status ()
  {
    if (! isset ($this->_status))
    {
      $this->_status = $this->_make_status ();
    }
    return $this->_status;
  }

  /**
   * @return TITLE_FORMATTER
   */
  public function title_formatter ()
  {
    $Result = parent::title_formatter ();
    $status = $this->status ();
    $Result->title = $status->as_plain_text();

    return $Result;
  }

  /**
   * Ship the release.
   * This marks this release as shipped. Jobs and changes can still be added or removed
   * from this release. Use {@link lock()} to prevent this release from appearing in job and change
   * forms and to prevent new jobs and changes from being added.
   * @param boolean $update_now Actualize the database?
   */
  public function ship ($update_now = true)
  {
    if (! $this->shipped ())
    {
      $this->set_state (Shipped, $update_now);
      $this->time_shipped->set_now ();
      $this->time_next_deadline->clear ();
    }
  }

  /**
   * Release to testing.
   * The release is not yet shipped, but should be feature-complete.
   * @param boolean $update_now Actualize the database?
   */
  public function test ($update_now = true)
  {
    if ($this->planned ())
    {
      $this->set_state (Testing, $update_now);
      $this->time_tested->set_now ();
      $this->time_next_deadline = $this->time_scheduled;
    }
  }

  /**
   * Returns to development.
   * The release has been set back to planning stages. Clears testing and shipping times.
   * @param boolean $update_now Actualize the database?
   */
  public function plan ($update_now = true)
  {
    if ($this->planned ())
    {
      $this->set_state (Planned, $update_now);
      $this->time_tested->clear ();
      $this->time_shipped->clear ();
      $this->_update_next_deadline ();
    }
  }

  /**
   * Lock the release.
   * Jobs and changes can no longer be added to this release, nor can they be taken away.
   * @param boolean $update_now Actualize the database?
   */
  public function lock ($update_now = true)
  {
    $this->ship ($update_now);
    parent::lock ($update_now);
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->branch_id = $db->f ('branch_id');
    $this->summary = $db->f ('summary');
    $this->time_next_deadline->set_from_iso ($db->f ('time_next_deadline'));
    $this->time_scheduled->set_from_iso ($db->f ('time_scheduled'));
    $this->time_shipped->set_from_iso ($db->f ('time_shipped'));
    $this->time_testing_scheduled->set_from_iso ($db->f ('time_testing_scheduled'));
    $this->time_tested->set_from_iso ($db->f ('time_tested'));
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'branch_id', Field_type_integer, $this->branch_id, Storage_action_create);
    $storage->add ($tname, 'summary', Field_type_string, $this->summary);
    $storage->add ($tname, 'time_next_deadline', Field_type_date_time, $this->time_next_deadline);
    $storage->add ($tname, 'time_scheduled', Field_type_date_time, $this->time_scheduled);
    $storage->add ($tname, 'time_shipped', Field_type_date_time, $this->time_shipped);
    $storage->add ($tname, 'time_testing_scheduled', Field_type_date_time, $this->time_testing_scheduled);
    $storage->add ($tname, 'time_tested', Field_type_date_time, $this->time_tested);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->release_home;
  }

  /**
   * Render the location within the object hierarchy.
   * @param boolean $use_links Show objects as links?
   * @param string $separator Optional separator. If not set, {@link APPLICATION_DISPLAY_OPTIONS::$obj_url_separator} is used.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   * @access private
   */
  protected function _object_url ($use_links, $separator = null, $formatter = null)
  {
    $Result = parent::_object_url ($use_links, $separator, $formatter);
    $branch = $this->branch ();
    $branch_url = $branch->_object_url ($use_links, $separator, $formatter);

    if (! isset ($separator))
    {
      $separator = $this->app->display_options->obj_url_separator;
    }

    return $branch_url . $separator . $Result;
  }

  /**
   * Copy properties from the given object.
   * @param RELEASE $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->time_scheduled = clone ($other->time_scheduled);
    $this->time_shipped = clone ($other->time_shipped);
    $this->time_tested = clone ($other->time_tested);
    $this->time_testing_scheduled = clone ($other->time_testing_scheduled);
    $this->time_next_deadline = clone ($other->time_next_deadline);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    include_once ('projects/obj/release_updater.php');
    $purger = new RELEASE_PURGER ($this);
    $purger->apply ($options->sub_history_item_publication_state);

    parent::_purge ($options);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->releases;
  }

  /**
   * @return string
   * @access private
   */
  protected function _state_icon_name ()
  {
    switch ($this->state)
    {
    case Planned:
      return '{icons}buttons/calendar';
    case Testing:
      return '{app_icons}statuses/working';
    case Shipped:
      return '{icons}buttons/ship';
    default:
      return parent::_state_icon_name ();
    }
  }

  /**
   * Format a date for status displays.
   * @param DATE_TIME $date
   * @param boolean $text_only Omit all tags if True.
   * @access private
   */
  protected function _date_as_text ($date, $text_only)
  {
    $Result = '';

    if ($date->is_valid ())
    {
      $f = $date->formatter ();
      $f->type = Date_time_format_date_only;
      $f->show_local_time = ! $text_only && $this->context->local_times_allowed ();
      $f->show_CSS = ! $text_only;

      $Result = $date->format ($f);
      if (! $text_only)
      {
        $Result = '<span class="visible" style="white-space: nowrap">' . $Result . '</span>';
      }
    }

    return $Result;
  }

  /**
   * Create a status object describing this release.
   * @return RELEASE_STATUS
   * @access private
   */
  protected function _make_status ($text_only = false)
  {
    include_once ('projects/obj/release_status.php');
    return new RELEASE_STATUS ($this, $text_only);
  }

  /**
   * Name of the {@link FOLDER_PERMISSIONS} to use for this object.
   * @access private
   */
  protected function _privilege_set ()
  {
    return Privilege_set_entry;
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
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('projects/gui/release_renderer.php');
        return new RELEASE_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/release_commands.php');
        return new RELEASE_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new RELEASE_HISTORY_ITEM ($this->app);
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
    $branch = $this->branch ();

    $query->restrict ('watch_entries > 0');
    $query->restrict_kinds (array (Subscribe_folder => $branch->parent_folder_id ()
                                   , Subscribe_user => $this->creator_id));
  }
}

?>