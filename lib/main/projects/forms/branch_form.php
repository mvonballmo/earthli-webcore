<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.3.0
 * @since 1.4.1
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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Edit or create a {@link RELEASE}.
 * @package projects
 * @subpackage forms
 * @version 3.3.0
 * @since 1.4.1
 */
class BRANCH_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param PROJECT $folder Project in which to add or edit the job.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'parent_release_id';
    $field->title = 'Parent release';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'state';
    $field->title = 'Status';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('state', Visible);

    $branch_id = read_var ('branch_id', 0);

    if ($branch_id > 0)
    {
      $branch = $this->app->branch_at_id ($branch_id);
      if (isset ($branch))
      {
        $release = $branch->latest_release ();
        if (isset ($release))
        {
          $this->set_value ('parent_release_id', $release->id);
        }
      }
    }
  }

  /**
   * Load initial properties from this branch.
   * @param BRANCH $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('parent_release_id', $obj->parent_release_id);
    $this->set_value ('state', $obj->state);
  }

  /**
   * Store the form's values for this change.
   * @param CHANGE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);
    $obj->parent_release_id = $this->value_for ('parent_release_id');

    switch ($this->value_for ('state'))
    {
    case Hidden:
      $obj->hide (Defer_database_update);
      break;
    case Locked:
      $obj->lock (Defer_database_update);
      break;
    case Visible:
      $obj->show (Defer_database_update);
      break;
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');

    $props = $renderer->make_list_properties ();
    $props->show_descriptions = true;

    if ($this->visible ('is_visible'))
    {
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/invisible', ' ', '16px') . ' Hidden', Hidden, 'Only administrators can see this branch\'s contents.');
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/view', ' ', '16px') . ' Visible', Visible, 'Jobs and changes can be added and removed.');
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/locked', ' ', '16px') . ' Locked', Locked, 'Cannot add or remove jobs and changes (undoable).');
    }
    else
    {
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/view', ' ', '16px') . ' Unlocked', Visible, 'Jobs and changes can be added and removed.');
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/locked', ' ', '16px') . ' Locked', Locked, 'Cannot add or remove jobs and changes (undoable).');
    }

    $renderer->draw_separator ();
    $renderer->draw_radio_group_row ('state', $props);
    $renderer->draw_separator ();

    $props = $renderer->make_list_properties ();
    $props->add_item ('[None]', 0);
    $release_query = $this->_folder->release_query ();
    $release_query->restrict ('rel.state = ' . Locked);
    $releases = $release_query->objects ();
    foreach ($releases as $release)
    {
      $branch = $release->branch ();
      $props->add_item ($branch->title_as_plain_text () . $this->app->display_options->object_separator . $release->title_as_plain_text (), $release->id);
    }

    $renderer->draw_drop_down_row ('parent_release_id', $props);
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
}
?>