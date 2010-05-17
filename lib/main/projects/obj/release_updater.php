<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');

/**
 * Manages entries in a {@link RELEASE}.
 * Use internally by a release when it is shipped or purged. The class makes sure to update associated
 * entry histories properly when applying the change.
 * @package projects
 * @subpackage obj
 * @access private
 * @version 3.3.0
 * @since 1.7.0
 */
abstract class RELEASE_UPDATER extends WEBCORE_OBJECT
{
  /**
   * The release to be committed.
   *  @var RELEASE
   */
  public $release;

  /**
   * Release belongs to this branch.
   *  @var BRANCH
   */
  public $branch;

  public function __construct ($release)
  {
    parent::__construct ($release->app);
    $this->release = $release;
    $this->branch = $release->branch ();
  }

  /**
   * Perform the update to the {@link RELEASE}.
   * The function may generate sub-history items for affected entries. Use the parameter to determine
   * whether these sub-history items are published or not.
   * @param string $sub_history_item_publication_state Can be {@link History_item_silent} or {@link History_item_needs_send}.
   * 
   * @abstract
   */
  public abstract function apply ($sub_history_item_publication_state = History_item_silent);

  /**
   * Return the most likely candidate.
   * If there is a newer release, that is returned and all jobs/changes in this
   * release are assigned to that one. If not, jobs/changes are assigned to no
   * release in this branch.
   * 
   * @return RELEASE
   */
  public function replacement_release ()
  {
    if (! isset ($this->_replacement_release))
    {
      $release_query = $this->branch->release_query ();
      $release_query->set_order ('rel.time_next_deadline DESC');
      $releases = $release_query->objects ();

      /* Look for this release in the list of releases for this branch. If it's found,
         get the next release in the list, if it exists. If there is no newer release,
         relegate all jobs and changes back to 'unassigned' release. */

      $index = 0;
      $current_index = 0;
      while (! $current_index && ($index < sizeof ($releases)))
      {
        if ($releases [$index]->id == $this->release->id)
        {
          $current_index = $index;
        }
        $index += 1;
      }

      while (! isset ($this->_replacement_release) && ($current_index > 0))
      {
        $current_index--;
        if ($releases [$current_index]->planned ())
        {
          $this->_replacement_release = $releases [$current_index];
        }
      }

      if (! isset ($this->_replacement_release))
      {
        $this->_replacement_release = null;
      }
    }

    return $this->_replacement_release;
  }

  /**
   * Apply the change to all entries in the query.
   * If the entry is in this updater's branch, the method 'applier_func' is called with that
   * branch info object. The method automatically updates the entry's history accordingly.
   * @param QUERY $entry_query
   * @param string $sub_history_item_publication_state Can be {@link History_item_silent} or {@link History_item_needs_send}.
   * @param string $applier_func
   */
  protected function _apply_to_entries ($entry_query, $sub_history_item_publication_state, $applier_func)
  {
    $entries = $entry_query->objects ();

    $this_branch_info = null; // Compiler warning

    foreach ($entries as $entry)
    {
      $history_item = $entry->new_history_item ();
      $history_item->compare_branches = true;
      $history_item->publication_state = $sub_history_item_publication_state;
      $this_branch_info = null;

      $branch_infos = $entry->stored_branch_infos ();

      if (sizeof ($branch_infos) > 0)
      {
        foreach ($branch_infos as $branch_info)
        {
          if ($branch_info->branch_id == $this->branch->id)
          {
            $this->$applier_func ($branch_info);

            if ($branch_info->branch_id == $entry->main_branch_id)
            {
              $entry->set_main_branch_info ($branch_info);
            }

            $this_branch_info = $branch_info;
          }

          $entry->add_branch_info ($branch_info);
        }
      }

      $entry->store_if_different ($history_item);
      $this_branch_info->store ();
    }
  }
  
  /**
   * Apply the required change to the {@link BRANCH}.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   */
  protected function _set_replacement_release ($branch_info)
  {
    $branch_info->release_id = $this->_replacement_release_id ();
  }

  /**
   * Id of the {@link replacement_release()}.
   * @return integer
   */
  protected function _replacement_release_id ()
  {
    $rel = $this->replacement_release ();
    if (isset ($rel))
    {
      return $rel->id;
    }

    return 0;
  }
}

/**
 * Manages purging of a {@link RELEASE}.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */
class RELEASE_PURGER extends RELEASE_UPDATER
{
  /**
   * Re-assign any entries associated with this {@link RELEASE}.
   * @param string $sub_history_item_publication_state Can be {@link History_item_silent} or {@link History_item_needs_send}.
   */
  public function apply ($sub_history_item_publication_state = History_item_silent)
  {
    $entry_query = $this->release->entry_query ();
    $this->_apply_to_entries ($entry_query, $sub_history_item_publication_state, '_set_replacement_release');
  }
}

/**
 * Manages entries in a {@link RELEASE}.
 * Use internally by a release when it is shipped or purged. The class makes sure to update associated
 * entry histories properly when applying the change.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */
class RELEASE_SHIPPER extends RELEASE_UPDATER
{
  /**
   * Mapping for closed jobs in this release.
   * @var JOB_STATUS_MAP
   */
  public $status_map;

  public function __construct ($release)
  {
    parent::__construct ($release);
    $this->status_map = $this->app->display_options->job_status_map ();
  }

  /**
   * Release has been shipped.
   * @param string $sub_history_item_publication_state Can be {@link History_item_silent} or {@link History_item_needs_send}.
   * Update all affected entries.
   */
  public function apply ($sub_history_item_publication_state = History_item_silent)
  {
    $this->_apply_to_entries ($this->change_query (), $sub_history_item_publication_state, '_set_release');
    $this->_apply_to_entries ($this->closed_job_query (), $sub_history_item_publication_state, '_set_release');
    $this->_apply_to_entries ($this->open_job_query (), $sub_history_item_publication_state, '_set_replacement_release');
    $this->_apply_to_entries ($this->remapped_job_query (), $sub_history_item_publication_state, '_map_status');
  }

  /**
   * Changes that will be assigned to the release if committed.
   * These are {@link CHANGE}s made in this release's {@link BRANCH} that are not assigned to other releases.
   * @return QUERY
   */
  public function change_query ()
  {
    $Result = $this->branch->change_query ();
    $Result->restrict ('etob.branch_release_id <> ' . $this->release->id);
    $Result->add_table ($this->app->table_names->releases . ' rel', 'etob.branch_release_id = rel.id', 'LEFT');
    $Result->restrict_to_one_of (array ('etob.branch_release_id = 0', 'rel.time_shipped is null', 'rel.time_shipped = \'0000-00-00 00:00:00\'' ));
    return $Result;
  }

  /**
   * Jobs that will be removed from the release if committed.
   * These are {@link JOB}s assigned to this release that are not closed. If the release is closed, they will
   * be assigned to the next pending planned release or left unnassigned.
   * @return QUERY
   */
  public function open_job_query ()
  {
    $Result = $this->release->job_query ();
    $Result->restrict ("jtob.branch_closer_id = 0");
    return $Result;
  }

  /**
   * Jobs that will be assigned to the release if committed.
   * These are {@link JOB}s made in this release's {@link BRANCH} that are not assigned to other releases.
   * @return QUERY
   */
  public function closed_job_query ()
  {
    $Result = $this->branch->job_query ();
    $Result->restrict ('etob.branch_release_id <> ' . $this->release->id);
    $Result->add_table ($this->app->table_names->releases . ' rel', 'etob.branch_release_id = rel.id', 'LEFT');
    $Result->restrict_to_one_of (array ('etob.branch_release_id = 0', 'rel.time_shipped is null', 'rel.time_shipped = \'0000-00-00 00:00:00\'' ));
    $Result->restrict ("jtob.branch_closer_id > 0");
    return $Result;
  }

  /**
   * Which job statuses are remapped in the release?
   * When a release is locked, it remaps some statuses (e.g. changes 'Fixed' to 'Closed') which allows an
   * application to mark jobs that were tentatively closed as finally closed.
   * @return QUERY
   */
  public function remapped_job_query ()
  {
    $Result = $this->branch->job_query ();
    $Result->restrict ("jtob.branch_closer_id > 0");
    $Result->restrict ("branch_status IN ({$this->status_map->from})");
    return $Result;
  }

  /**
   * Set the release to the one being updated.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _set_release ($branch_info)
  {
    $branch_info->release_id = $this->release->id;
  }

  /**
   * Clear the release for this branch.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _clear_release ($branch_info)
  {
    $branch_info->release_id = 0;
  }

  /**
   * Changes the closed status for this branch.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _map_status ($branch_info)
  {
    $branch_info->status = $this->status_map->to;
  }
}

/**
 * Previews a multi-step change to a {@link RELEASE}.
 * Used when purging or shipping a release.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.9.0
 */
class UPDATE_RELEASE_PREVIEW_SETTINGS extends FORM_PREVIEW_SETTINGS
{
  /**
   * Draw a list with a title.
   * @param string $title
   * @param string $text
   * @param QUERY $query
   * @access private
   */
  protected function _draw_section ($title, $text, $query)
  {
    $objs = $query->objects ();
    if (sizeof ($objs))
    {
      $this->_objects_displayed = true;
  ?>
  <h3><?php echo sizeof ($objs); ?> <?php echo $title; ?></h3>
  <p class="notes">
    <?php echo $text; ?>
  </p>
  <?php
      $this->_draw_entries ($objs);
    }
  }

  /**
   * @param array[PROJECT_ENTRY] $entries
   * @see PROJECT_ENTRY
   * @access private
   */
  protected function _draw_entries ($entries, $show_status = false)
  {
    $this->app->display_options->overridden_max_title_size = 100;
    echo '<div style="margin-left: 2em">';
    foreach ($entries as $entry)
    {
      $this->_draw_entry ($entry, $show_status);
    }
    echo '</div>';
  }

  /**
   * @param PROJECT_ENTRY $entry
   * @access private
   */
  protected function _draw_entry ($entry, $show_status = false)
  {
    $icon = $entry->kind_icon ('16px');
    if ($show_status)
    {
      $branch_info = $entry->main_branch_info ();
      $icon = $branch_info->status_icon () . ' ' . $icon;
    }
    echo '<div>' . $icon . ' ' . $entry->title_as_link () . '</div>';
  }

  /**
   * @var boolean
   * @access private
   */
  protected $_objects_displayed = false;
}

?>
