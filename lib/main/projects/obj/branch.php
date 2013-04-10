<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Represents a code-line within a {@link PROJECT}.
 * Each project contains one or more branches. {@link CHANGE}s and {@link JOB}s can be attached to
 * one or more branches within the project.
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 1.4.1
 */
class BRANCH extends OBJECT_IN_FOLDER
{
  /**
   * The id of the release on which this project is based.
   * @var integer
   * @see BRANCH::parent_release()
   */
  public $parent_release_id;

  /**
   * Latest available release for this branch.
   * Release must be available (cannot be 'planned'). May return empty.
   * @return RELEASE
   */
  public function latest_release ()
  {
    if (! isset ($this->_latest_release))
    {
      $q = $this->release_query ();
      $q->restrict_to_one_of (array ('rel.state = ' . Locked, 'rel.state = ' . Shipped));
      $this->_latest_release = $q->first_object ();
    }

    return $this->_latest_release;
  }

  /**
   * Next planned release in this branch.
   * May return empty.
   * @return RELEASE
   */
  public function next_release ()
  {
    if (! isset ($this->_next_release))
    {
      $q = $this->pending_release_query ();
      $this->_next_release = $q->first_object ();
    }

    return $this->_next_release;
  }

  /**
   * Release on which this branch is based.
   * May return empty.
   * @return RELEASE
   */
  public function parent_release ()
  {
    if (! isset ($this->_parent_release) && isset ($this->parent_release_id))
    {
      $f = $this->parent_folder ();
      $q = $f->release_query ();
      $this->_parent_release = $q->object_at_id ($this->parent_release_id);
    }

    return $this->_parent_release;
  }

  /**
   * List of all releases in this branch.
   * @return BRANCH_RELEASE_QUERY
   */
  public function release_query ()
  {
    $class_name = $this->app->final_class_name ('BRANCH_RELEASE_QUERY', 'projects/db/branch_release_query.php');
    return new $class_name ($this);
  }
  
  /**
   * Show only non-locked releases and sort them by plan date.
   * Releases without a date sort after those with a date and
   * shipped releases sort last.
   * @param integer $filter Use {@link Release_not_locked} or {@link
   * Release_is_pending}.
   * @return BRANCH_RELEASE_QUERY
   */
  public function pending_release_query ($filter = Release_is_pending)
  {
    $Result = $this->release_query ();
    $Result->set_up_pending ($filter);
    return $Result;
  }
  
  /**
   * List of all entries ({@link JOB}s or {@link CHANGE}s) for this branch.
   * @return BRANCH_ENTRY_QUERY
   */
  public function entry_query ()
  {
    $class_name = $this->app->final_class_name ('BRANCH_ENTRY_QUERY', 'projects/db/branch_entry_query.php');
    return new $class_name ($this);
  }

  /**
   * List of all changes for this branch.
   * @return BRANCH_ENTRY_QUERY
   */
  public function change_query ()
  {
    $Result = $this->entry_query ();
    $Result->set_type ('change');
    return $Result;
  }

  /**
   * List of all jobs for this branch.
   * @return BRANCH_ENTRY_QUERY
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
    return new $class_name ($this);
  }

  /**
   * Creates a new release object for this branch.
   * This only returns a PHP object; no information is added to the database.
   * @return RELEASE
   */
  public function new_release ()
  {
    $class_name = $this->app->final_class_name ('RELEASE', 'projects/obj/release.php');
    $Result = new $class_name ($this->app);
    $Result->branch_id = $this->id;
    $Result->_branch = $this;
    $Result->set_parent_folder ($this->parent_folder ());
    return $Result;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->parent_release_id = $db->f ('parent_release_id');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $fldr_id = $this->parent_folder_id ();
    $storage->add ($tname, 'folder_id', Field_type_integer, $fldr_id, Storage_action_create);
    $storage->add ($tname, 'parent_release_id', Field_type_integer, $this->parent_release_id);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->branch_home;
  }
  
  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->branches;
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
    $folder = $this->parent_folder ();
    $folder_url = $folder->_object_url ($use_links, $separator, $formatter);

    if (! isset ($separator))
    {
      $separator = $this->app->display_options->obj_url_separator;
    }

    return $folder_url . $separator . $Result;
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $folder = $this->parent_folder ();
    $branch_query = $folder->branch_query ();
    $branches = $branch_query->objects ();
    $num_branches = sizeof ($branches);

    $this->assert ($num_branches > 1, 'The last branch in a project cannot be purged.', '_purge', 'BRANCH');

    $entry_query = $this->entry_query ();
    $new_trunk = null;

    if ($folder->trunk_id == $this->id)
    {
      $idx = 0;
      while (! isset ($new_trunk) && ($idx < $num_branches))
      {
        if ($branches [$idx]->id != $this->id)
        {
          $new_trunk = $branches [$idx];
        }

        $idx += 1;
      }

      if (isset ($new_trunk))
      {
        $history_item = $folder->new_history_item ();
        $history_item->publication_state = $options->sub_history_item_publication_state;
        $folder->trunk_id = $new_trunk->id;
        $folder->store_if_different ($history_item);

        $trunk = $new_trunk;
      }
    }
    else
    {
      $trunk = $folder->trunk ();
    }

    /* Update every entry, making sure to create a history item for each one, indicating
       the change (branch removed, trunk added, main branch changed). */

    $entries = $entry_query->objects ();
    $main_branch_info = new stdClass();

    foreach ($entries as $entry)
    {
      $history_item = $entry->new_history_item ();
      $history_item->compare_branches = true;
      $history_item->publication_state = $options->sub_history_item_publication_state;
      $branch_infos = $entry->stored_branch_infos ();
      $main_branch_info = null;

      /* The check should actually be for equals to 1, but if an entry has no branch information
         because of corruption, we can clean that up here and re-assign the entry to the trunk. */

      if (sizeof ($branch_infos) > 0)
      {
        foreach ($branch_infos as $branch_info)
        {
          if ($branch_info->branch_id != $this->id)
          {
            if (! isset ($main_branch_info))
            {
              $main_branch_info = $branch_info;
            }
            $entry->add_branch_info ($branch_info);
          }
        }
      }

      if (! isset ($main_branch_info))
      {
        $branch_info = $entry->new_branch_info ($trunk);
        $entry->add_branch_info ($branch_info);
        $entry->set_main_branch_info ($branch_info);
      }
      else
      {
        if ($entry->main_branch_id == $this->id)
        {
          $entry->set_main_branch_info ($main_branch_info);
        }
      }

      $entry->store_if_different ($history_item);

      $orig_branch_infos = $entry->stored_branch_infos ();
      $new_branch_infos = $entry->current_branch_infos ();

      if (! isset ($main_branch_info))
      {
        foreach ($new_branch_infos as $branch_id => $new_branch_info)
        {
          $new_branch_info->store ();
        }
      }

      foreach ($orig_branch_infos as $branch_id => $orig_branch_info)
      {
        if (! isset ($new_branch_infos [$branch_id]))
        {
          $orig_branch_info->purge ();
        }
      }
    }

    parent::_purge ($options);
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
        include_once ('projects/gui/branch_renderer.php');
        return new BRANCH_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/branch_commands.php');
        return new BRANCH_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new BRANCH_HISTORY_ITEM ($this->app);
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
                                   , Subscribe_user => $this->creator_id));
  }

  /**
   * Locally-cached reference to the last shipped release.
   * @see latest_release()
   * @var RELEASE
   * @access private
   */
  protected $_latest_release;

  /**
   * Locally-cached reference to the next planned release.
   * @see next_release()
   * @var RELEASE
   * @access private
   */
  protected $_next_release;
}

?>