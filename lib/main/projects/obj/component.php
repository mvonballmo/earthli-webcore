<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
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
 * A part of a {@link PROJECT}.
 * {@link JOB}s and {@link CHANGE}s can be assigned to components in a project.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 */
class COMPONENT extends OBJECT_IN_FOLDER
{
  /**
   * Location of user's icon.
   * Use {@link icon_as_html()} or {@link expanded_icon_url()} to access this property.
   * @var integer
   */
  public $icon_url = '';

  /**
   * Icon, renderered as HTML.
   * The requested size can also be given, which is either used to retrieve the image or used in the HTML.
   * @var string $size
   * @return string
   */
  public function icon_as_html ($size = '32px')
  {
    return $this->app->image_as_html ($this->expanded_icon_url ($size), ' ');
  }

  /**
   * Fully resolved path to the icon for this object.
   * @param string $size
   * @return string
   */
  public function expanded_icon_url ($size = '32px')
  {
    if ($this->icon_url)
    {
      return $this->app->sized_icon ($this->icon_url, $size);
    }
    
    return '';
  }

  /**
   * List of all entries (jobs or changes) for this release.
   * @return PROJECT_ENTRY_QUERY
   */
  public function entry_query ()
  {
    $fldr = $this->parent_folder ();
    $Result = $fldr->entry_query ();
    $Result->restrict ("entry.component_id = $this->id");
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
    $folder = $this->parent_folder ();
    $class_name = $this->app->final_class_name ('PROJECT_COMMENT_QUERY', 'projects/db/project_comment_query.php');
    $Result = new $class_name ($folder);
    $Result->restrict ("entry.component_id = $this->id");
    return $Result;
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
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->icon_url = $db->f ('icon_url');
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
    $storage->add ($tname, 'icon_url', Field_type_string, $this->icon_url);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->component_home;
  }
  
  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->components;
  }

  /**
   * @param COMPONENT_PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $entry_query = $this->entry_query ();
    $entries = $entry_query->objects ();

    foreach ($entries as $entry)
    {
      $history_item = $entry->new_history_item ();
      $history_item->publication_state = $options->sub_history_item_publication_state;

      $entry->component_id = $options->replacement_component_id;

      $entry->store_if_different ($history_item);
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
        include_once ('projects/gui/component_renderer.php');
        return new COMPONENT_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('projects/cmd/component_commands.php');
        return new COMPONENT_COMMANDS ($this);
      case Handler_history_item:
        include_once ('projects/obj/project_history_items.php');
        return new COMPONENT_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

/**
 * Used when purging components.
 * @package projects
 * @subpackage obj
 * @version 3.3.0
 * @since 1.7.0
 * @access private
 */
class COMPONENT_PURGE_OPTIONS extends PURGE_OPTIONS
{
  /**
   * Replace purged component with this one.
   * @var integer
   */
  public $replacement_component_id;
}

?>
