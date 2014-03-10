<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 1.4.1
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
require_once ('webcore/obj/multi_type_entry.php');

/**
 * Basic entry for a {@link PROJECT}.
 * @abstract 
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 1.4.1
 */
abstract class PROJECT_ENTRY extends MULTI_TYPE_ENTRY
{
  /**
   * Which kind of entry is this?
   * @var integer
   * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::entry_kinds()
   */
  public $kind;

  /**
   * To which {@link COMPONENT} does this entry belong?
   * @var integer
   */
  public $component_id;

  /**
   * What is the main branch for this entry?
   * This branch is only used to provide a default set of branch-specific properties for an entry.
   * @var integer
   * @see PROJECT_ENTRY::main_branch_info()
   */
  public $main_branch_id;

  /**
   * Contains longer information like core-dumps, examples, etc.
   * This area can contain a longer discussion of the new feature, examples of
   * where the code fails, stack traces, etc. All information that you'd rather
   * not appear in the change log or job list, by default.
   * @var string
   */
  public $extra_description;

  /**
   * @param PROJECT_APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_main_branch_info = $this->_make_branch_info ();
  }

  /**
   * All properties of this entry's kind.
   * These are the properties defined in the user data file.
   * @see PROJECT_APPLICATION_DISPLAY_OPTIONS::entry_kinds()
   */
  public function kind_properties ()
  {
    $props = $this->app->display_options->entry_kinds ();
    if (isset ($props [$this->kind]))
    {
      return $props [$this->kind];
    }
    else
    {
      $prop = new PROPERTY_VALUE ($this->app);
      $prop->title = "[Unknown kind ($this->kind)]";
      return $prop;
    }
  }

  /**
   * HTML code for the icon to use for this kind.
   * @param string $size
   * @return string
   */
  public function kind_icon ($size = '20px')
  {
    $props = $this->kind_properties ();
    return $props->icon_as_html ($size);
  }

  /**
   * The kind as a string.
   * @return string
   * @see PROJECT_ENTRY::kind
   */
  public function kind_as_text ()
  {
    $props = $this->kind_properties ();
    return $props->title;
  }

  /**
   * This entry's component.
   * @return COMPONENT
   */
  public function component ()
  {
    if ($this->component_id)
    {
      if (! isset ($this->_component))
      {
        $this->_component = $this->app->component_at_id ($this->component_id);
      }

      return $this->_component;
    }

    return null;
  }

  /**
   * {@link $extra_description} formatted as HTML.
   * @return string
   */
  public function extra_description_as_html ()
  {
    return $this->_text_as_html ($this->extra_description);
  }

  /**
   * {@link $extra_description} formatted as plain text.
   * @return string
   */
  public function extra_description_as_plain_text ()
  {
    return $this->_text_as_plain_text ($this->extra_description);
  }

  /**
   * Returns an object which maps this entry to the given branch.
   * @param BRANCH $branch
   * @return PROJECT_ENTRY_BRANCH_INFO
   */
  public function new_branch_info ($branch)
  {
    $this->assert (isset ($branch), 'Branch cannot be empty.', 'new_branch_info', 'PROJECT_ENTRY');

    if ($this->exists ())
    {
      $stored_branch_infos = $this->stored_branch_infos ();
    }

    if (isset ($stored_branch_infos [$branch->id]))
    {
      $Result = $stored_branch_infos [$branch->id];
    }
    else
    {
      $Result = $this->_make_branch_info ();
      if (isset ($this->id))
      {
        $Result->entry_id = $this->id;
      }
      $Result->set_branch ($branch);
    }

    return $Result;
  }

  /**
   * Add a branch to this object for storage.
   * In order to update a project entry, add all required branches to it, then attempt to store it.
   * The internal storage routine takes care of comparing the new branch information against the old and
   * auditing any changes.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   */
  public function add_branch_info ($branch_info)
  {
    $this->_current_branch_infos [$branch_info->branch_id] = $branch_info;
  }

  /**
   * Return the list of unstored branches.
   * Used with {@link PROJECT_ENTRY::stored_branch_infos()} to determine whether an object's
   * branches have changed.
   * @return PROJECT_ENTRY_BRANCH_INFO[]
   */
  public function current_branch_infos ()
  {
    return $this->_current_branch_infos;
  }

  /**
   * Return the list of unstored branches.
   * Used with {@link PROJECT_ENTRY::current_branch_infos()} to determine whether an object's
   * branches have changed.
   * @return PROJECT_ENTRY_BRANCH_INFO[]
   */
  public function stored_branch_infos ()
  {
    if (! isset ($this->_stored_branch_infos))
    {
      $branch_query = $this->branch_info_query ();
      $this->_stored_branch_infos = $branch_query->indexed_objects_by_branch_id (true);
    }

    return $this->_stored_branch_infos;
  }

  /**
   * Returns those properties which exist only within the context of a branch.
   * In {@link JOB} objects, this function returns {@link JOB_BRANCH_INFO}.
   * @return PROJECT_ENTRY_BRANCH_INFO
   */
  public function main_branch_info ()
  {
    $this->assert (isset ($this->_main_branch_info), 'Branch information must be set.', 'main_branch_info', 'PROJECT_ENTRY');

    return $this->_main_branch_info;
  }

  /**
   * Returns the main branch for this entry.
   * @return BRANCH
   */
  public function main_branch ()
  {
    return $this->app->branch_at_id ($this->main_branch_id);
  }

  /**
   * A query that retrieve the branches for this entry.
   * This will always retrieve branch information from the database. User {@link PROJECT_ENTRY::branch_infos()} to
   * retrieve a local list (e.g. when checking branches that have been modified on an object, but not yet stored).
   * @return PROJECT_ENTRY_BRANCH_INFO_QUERY
   */
  public function branch_info_query ()
  {
    include_once ('projects/db/entry_branch_query.php');
    return new PROJECT_ENTRY_BRANCH_INFO_QUERY ($this);
  }

  /**
   * Get the branch infos for this entry.
   * If branch information has been updated, but not stored, then this will
   */

  /**
   * Set the main branch for this project entry.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   */
  public function set_main_branch_info ($branch_info)
  {
    $this->_main_branch_info = $branch_info;
    $this->main_branch_id = $branch_info->branch_id;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->kind = $db->f ('kind');
    $this->component_id = $db->f ('component_id');
    $this->extra_description = $db->f ('extra_description');
    $this->main_branch_id = $db->f ('main_branch_id');
    $this->_main_branch_info->load ($db);
    $this->_main_branch_info->entry_id = $this->id;
    $this->_main_branch_info->branch_id = $this->main_branch_id;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname =$this->table_name ();
    $storage->add ($tname, 'extra_description', Field_type_string, $this->extra_description);
    $storage->add ($tname, 'kind', Field_type_integer, $this->kind);
    $storage->add ($tname, 'component_id', Field_type_integer, $this->component_id);
    $storage->add ($tname, 'main_branch_id', Field_type_integer, $this->main_branch_id);
    $storage->add ($tname, 'release_id', Field_type_integer, $this->_main_branch_info->release_id);
  }

  public function store_branch_infos ()
  {
    $orig_branch_infos = $this->stored_branch_infos ();
    $new_branch_infos = $this->current_branch_infos ();

    if (isset ($new_branch_infos))
    {
      foreach ($new_branch_infos as $branch_id => $new_branch_info)
      {
        $new_branch_info->store ();
      }
    }

    if (isset ($orig_branch_infos))
    {
      foreach ($orig_branch_infos as $branch_id => $orig_branch_info)
      {
        if (! isset ($new_branch_infos [$branch_id]))
        {
          $orig_branch_info->purge ();
        }
      }
    }
  }

  /**
   * Copy properties from the given object.
   * @param PROJECT_ENTRY $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from ($other);
    $this->_main_branch_info = clone($other->_main_branch_info);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $branch_infos = $this->stored_branch_infos ();
    foreach ($branch_infos as $branch_info)
    {
      $branch_info->purge ($options);
    }
    parent::_purge ($options);
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
      case Handler_location:
        include_once ('projects/gui/project_entry_renderer.php');
        return new PROJECT_ENTRY_LOCATION_RENDERER ($this->context);
      case Handler_navigator:
        include_once ('projects/gui/project_entry_navigator.php');
        return new PROJECT_ENTRY_NAVIGATOR ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }

  /**
   * @return PROJECT_ENTRY_BRANCH_INFO
   * @access private
   */
  protected abstract function _make_branch_info ();

  /**
   * @var PROJECT_ENTRY_BRANCH_INFO
   * @access private
   */
  protected $_main_branch_info;

  /**
   * Used during storage to hold new branches.
   * @var PROJECT_ENTRY_BRANCH_INFO[]
   * @access private
   */
  protected $_current_branch_infos;

  /**
   * Used during storage to hold stored branches.
   * @var PROJECT_ENTRY_BRANCH_INFO[]
   * @access private
   */
  protected $_stored_branch_infos;
}

/**
 * Connects a {@link PROJECT_ENTRY} to a particular {@link BRANCH}.
 * Manages a project entry's basic relatationship within a branch. Use this to update settings,
 * and to add or remove a project entry from a branch.
 * @package projects
 * @subpackage obj
 * @version 3.4.0
 * @since 1.4.1
 * @abstract
 */
abstract class PROJECT_ENTRY_BRANCH_INFO extends UNIQUE_OBJECT
{
  /**
   * Which branch is this info attached to?
   * @var integer
   * @see PROJECT_ENTRY_BRANCH_INFO::branch()
   */
  public $branch_id;

  /**
   * Which entry is this info attached to?
   * @var integer
   * @see PROJECT_ENTRY_BRANCH_INFO::entry()
   */
  public $entry_id;

  /**
   * Which release is this info attached to?
   * @var integer
   * @see PROJECT_ENTRY_BRANCH_INFO::release()
   */
  public $release_id = 0;

  /**
   * @param PROJECT_ENTRY $entry Branch info is attached to this project entry.
   */
  public function __construct ($entry)
  {
    parent::__construct ($entry->app);
    $this->_entry = $entry;
  }

  /**
   * Entry for this info.
   * @return PROJECT_ENTRY
   */
  public function entry ()
  {
    $this->assert (isset ($this->_entry), '_entry is not cached.', 'entry', 'PROJECT_ENTRY_BRANCH_INFO');
    return $this->_entry;
  }

  /**
   * Is this the main branch for the attached entry?
   * @return boolean
   */
  public function is_main ()
  {
    return $this->_entry->main_branch_id == $this->branch_id;
  }

  /**
   * Branch for this info.
   * @return BRANCH
   */
  public function branch ()
  {
    if (! isset ($this->_branch))
    {
      $this->_branch = $this->app->branch_at_id ($this->branch_id);
    }

    return $this->_branch;
  }

  /**
   * Release for this info.
   * Can be empty.
   * @return RELEASE
   */
  public function release ()
  {
    if (! isset ($this->_release) && ($this->release_id > 0))
    {
      $this->_release = $this->app->release_at_id ($this->release_id);
    }

    return $this->_release;
  }

  /**
   * Change the branch for this info.
   * @param BRANCH $branch
   */
  public function set_branch ($branch)
  {
    // Deliberately break the reference here (avoids a copy bug)

    $this->_branch = clone($branch);
    $this->branch_id = $branch->id;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->entry_id = $db->f ('entry_id');
    $this->branch_id = $db->f ('branch_id');
    $this->release_id = $db->f ('branch_release_id');
    $this->entry_to_branch_id = $db->f ('entry_to_branch_id');
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    $branch = $this->branch ();
    return $branch->raw_title ();
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
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return 'id=' . $this->branch_id;
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   * @access private
   */
  public function store_to ($storage)
  {
    if (! $this->entry_id)
    {
      $entry = $this->entry ();
      $this->assert ($entry->exists (), 'Entry does not exist (cannot store branch information).', 'store_to', 'PROJECT_ENTRY_BRANCH_INFO');
      $this->entry_id = $entry->id;
    }

    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'entry_id', Field_type_integer, $this->entry_id);
    $storage->add ($tname, 'branch_id', Field_type_integer, $this->branch_id);
    $storage->add ($tname, 'branch_release_id', Field_type_integer, $this->release_id);

    $tname = $this->secondary_table_name ();
    $storage->restrict ($tname, 'entry_to_branch_id');
    $storage->add ($tname, 'entry_to_branch_id', Field_type_integer, $this->entry_to_branch_id, Storage_action_create);
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->entries_to_branches;
  }

  /**
   * @return SQL_PROJECT_ENTRY_BRANCH_INFO_STORAGE
   * @access private
   */
  protected function _make_storage ()
  {
    include_once ('projects/db/sql_project_entry_branch_info_storage.php');
    return new SQL_PROJECT_ENTRY_BRANCH_INFO_STORAGE ($this->app);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    parent::_purge ($options);
    $tname = $this->secondary_table_name ();
    $this->db->logged_query ("DELETE FROM {$tname} WHERE entry_to_branch_id = {$this->entry_to_branch_id}");
  }

  /**
   * @var PROJECT_ENTRY
   * @access private
   */
  protected $_entry;

  /**
   * @var RELEASE
   * @access private
   */
  protected $_release;

  /**
   * @var BRANCH
   * @access private
   */
  protected $_branch;

  /**
   * @var COMPONENT
   * @access private
   */
  protected $_component;
}