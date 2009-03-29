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
require_once ('webcore/obj/folder.php');

/**
 * Holds {@link CHANGE}s, {@link JOB}s and {@link RELEASE}s to define a project.
 * @package projects
 * @subpackage obj
 * @version 3.0.0
 * @since 1.4.1
 */
class PROJECT extends FOLDER
{
  /**
   * Id of the main {@link BRANCH} for this project.
   * @var integer
   */
  var $trunk_id;
  /**
   * Use options from the project with this id.
   * @var integer
   */
  var $options_id;

  /**
   * The main code line for this project.
    * @return BRANCH
    */
  function &trunk ()
  {
    if (! isset ($this->_trunk))
    {
      $q = $this->branch_query ();
      $this->_trunk =& $q->object_at_id ($this->trunk_id);
    }
    return $this->_trunk;
  }

  /**
   * List of all {@link RELEASE}s in this project.
   * This returns all releases, regardless of branch.
   * @return PROJECT_RELEASE_QUERY
   */
  function release_query ()
  {
    $class_name = $this->app->final_class_name ('PROJECT_RELEASE_QUERY', 'projects/db/project_release_query.php');
    return new $class_name ($this);
  }

  /**
   * List of all {@link BRANCH}es in this project.
   * @return PROJECT_BRANCH_QUERY
   */
  function branch_query ()
  {
    $class_name = $this->app->final_class_name ('PROJECT_BRANCH_QUERY', 'projects/db/project_branch_query.php');
    return new $class_name ($this);
  }

  /**
   * List of all {@link COMPONENT}s in this project.
   * @return PROJECT_BRANCH_QUERY
   */
  function component_query ()
  {
    $class_name = $this->app->final_class_name ('PROJECT_COMPONENT_QUERY', 'projects/db/project_component_query.php');
    return new $class_name ($this);
  }

  /**
   * Create a sub folder of this one.
   * Does not store to the database.
   * @return FOLDER
   */
  function new_folder ()
  {
    $Result = parent::new_folder ();
    $Result->options_id = $this->options_id;
    return $Result;
  }

  /**
   * Does this project define its own options?
    * Options can also be inherited from an ancestor project.
    * @return bool
    * @see PROJECT_OPTIONS
    */
  function defines_options ()
  {
    return $this->options_id == $this->id;
  }

  /**
   * The options for this project.
   * This will be either the inherited options or options specified in this
   * project.
   * @return PROJECT_OPTIONS
   */
  function &options ()
  {
    if (! isset ($this->_options))
    {
      $this->_options = $this->_make_options ();
      if ($this->options_id)
      {
        $this->db->logged_query ("SELECT * from {$this->app->table_names->folder_options} WHERE folder_id = $this->options_id");
        if ($this->db->next_record ())
        {
          $this->_options->load ($this->db);
        }
      }
    }
    return $this->_options;
  }

  /**
   * @param DATABASE &$db
   */
  function load (&$db)
  {
    $this->options_id = $db->f ('options_id');
    $this->trunk_id = $db->f ('trunk_id');
    parent::load ($db);
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
   */
  function store_to (&$storage)
  {
    parent::store_to ($storage);
    $tname = $this->_table_name ();
    $storage->add ($tname, 'options_id', Field_type_integer, $this->options_id);
    $storage->add ($tname, 'trunk_id', Field_type_integer, $this->trunk_id);
  }

  /**
   * Copy properties from the given object. 
   * @param PROJECT_ENTRY $other
   * @access private
   */
  function _copy_from ($other)
  {
    unset($this->_options);
    if ($other->exists ())
    {
      $this->_options = $other->options ();
    }
    else
    {
      $this->_options = null;
    }
  }
  
  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  function _purge ($options)
  {
    $tables = $this->app->table_names;

    $branch_query = $this->branch_query ();
    $branches =& $branch_query->objects ();
    foreach ($branches as $branch)
      $branch_ids [] = $branch->id;

    if (sizeof ($branch_ids))
    {
      $branch_ids = implode (',', $branch_ids);

      // remove associated branches and releases

      $this->db->logged_query ("DELETE FROM {$tables->branches} WHERE id IN ($branch_ids)");
      $this->db->logged_query ("DELETE FROM {$tables->releases} WHERE branch_id IN ($branch_ids)");

      // Remove entry to branch mappings

      $this->db->logged_query ("DELETE FROM {$tables->entries_to_branches} WHERE branch_id IN ($branch_ids)");
    }

    // Remove associated options, if there are any.

    if ($this->defines_options ())
    {
      $options = $this->options ();
      $options->delete ();
    }

    /* Remove change-specific map information */
    $this->_purge_foreign_key ($tables->entries_to_branches, 'id', $tables->changes_to_branches, 'entry_to_branch_id');
    /* Remove job-specific map information */
    $this->_purge_foreign_key ($tables->entries_to_branches, 'id', $tables->jobs_to_branches, 'entry_to_branch_id');
    /* remove associated changes */
    $this->_purge_foreign_key ($tables->entries, 'id', $tables->changes, 'entry_id');
    /* remove associated jobs */
    $this->_purge_foreign_key ($tables->entries, 'id', $tables->jobs, 'entry_id');
    /* remove special history items */
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$this->app->table_names->history_items} WHERE access_id = $this->id AND (object_type IN ('branch', 'release'))");

    parent::_purge ($options);
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
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('projects/gui/project_renderer.php');
        return new PROJECT_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/project_commands.php');
        return new PROJECT_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new PROJECT_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * @return PROJECT_OPTIONS
    * @access private
    */
  function _make_options ()
  {
    $class_name = $this->app->final_class_name ('PROJECT_OPTIONS', 'projects/obj/project_options.php');
    return new $class_name ($this);
  }

  /**
   * Return a new change object.
   * All changes are created without releases to begin with, so that they remain
   * unassociated until a release is added to the project. When the release is
   * created, the project associates all unassociated changes with that release.
   * @see FOLDER::new_entry()
   * @return CHANGE
   * @access private
   */
  function _make_change ()
  {
    $class_name = $this->app->final_class_name ('CHANGE', 'projects/obj/change.php');
    return new $class_name ($this->app);
  }

  /**
   * @return JOB
   * @see FOLDER::new_entry()
   * @access private
   */
  function _make_job ()
  {
    $class_name = $this->app->final_class_name ('JOB', 'projects/obj/job.php');
    return new $class_name ($this->app);
  }

  /**
   * @return BRANCH
   * @see FOLDER::new_entry()
   * @access private
   */
  function _make_branch ()
  {
    $class_name = $this->app->final_class_name ('BRANCH', 'projects/obj/branch.php');
    return new $class_name ($this->app);
  }

  /**
   * @return COMPONENT
   * @access private
   */
  function _make_component ()
  {
    $class_name = $this->app->final_class_name ('COMPONENT', 'projects/obj/component.php');
    return new $class_name ($this->app);
  }

  /**
   * @var BRANCH
   * @access private
   */
  var $_trunk;
  /**
   * @var PROJECT_OPTIONS
   * @access private
   */
  var $_options;

  /**
   * @var QUERY_BASED_CACHE
   * @access private
   */
  var $_branch_cache;
  /**
   * @var QUERY_BASED_CACHE
   * @access private
   */
  var $_release_cache;
}

?>