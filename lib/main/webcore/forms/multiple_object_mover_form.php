<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/forms/multiple_object_action_form.php');

/**
 * Moves one or more {@link ENTRY}s or {@link FOLDER}s to another folder.
 * User must have create folder/content rights in the target folder and delete content/folder rights
 * in the source folder.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */
class MULTIPLE_OBJECT_MOVER_FORM extends MULTIPLE_OBJECT_ACTION_FORM
{
  /**
   * If <code>True</code>, makes copies of the objects instead.
   * @var boolean
   */
  public $copy = false;
  
  /**
   * @param FOLDER $folder Objects are from this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'selected_folder_id';
    $field->caption = 'Target';
    $field->required = true;
    $field->min_value = 1;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'maintain_permissions';
    $field->caption = 'Maintain permissions';
    $field->description = 'If checked, permissions are created where needed to ensure that folders retain their current permissions.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'copy_as_draft';
    $field->caption = 'Copy as Draft';
    $field->visible = false;
    $field->description = 'If checked, copied entries are stored as drafts instead of published.';
    $this->add_field ($field);
  }

  /**
   * Read in values from the {@link $method} array.
   * @access private
   */
  protected function _load_from_request ()
  {
    parent::_load_from_request ();
    $this->set_visible ('maintain_permissions', $this->object_list->has_folders ());
    $this->set_visible ('copy_as_draft', $this->copy);
  }

  public function run ()
  {
    if ($this->_target)
    {
      parent::run ();
    }
  }

  /**
   * Options for moving an object to the target folder.
   * @param OBJECT_IN_FOLDER $obj
   * @return FOLDER_OPERATION_OPTIONS
   * @access private
   */
  protected function _move_options_for ($obj)
  {
    $Result = $obj->make_move_options ();
    $Result->maintain_permissions = $this->value_for ('maintain_permissions');
    $Result->copy_as_draft = $this->value_for ('copy_as_draft');
    return $Result;
  }
  
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);
    $folder_query = $this->app->login->folder_query ();
    $this->_target = $folder_query->object_at_id ($this->value_for ('selected_folder_id'));
    if (empty ($this->_target))
    {
      $this->record_error ('selected_folder_id', 'Please choose a valid folder.');
    }
    elseif (! $this->copy && $this->_folder->equals ($this->_target))
    {
      $this->record_error ('selected_folder_id', 'You cannot move to the same folder.');
    }
    elseif (($this->object_list->has_folders () && ! $this->app->login->is_allowed (Privilege_set_folder, Privilege_create, $this->_target)) ||
            ($this->object_list->has_entries () && ! $this->app->login->is_allowed (Privilege_set_entry, Privilege_create, $this->_target)))
      $this->record_error ('selected_folder_id', 'You are not allowed to add to that folder.');
    elseif (($this->object_list->has_folders () && ! $this->app->login->is_allowed (Privilege_set_folder, Privilege_delete, $this->_folder)) ||
            ($this->object_list->has_entries () && ! $this->app->login->is_allowed (Privilege_set_entry, Privilege_delete, $this->_folder)))
      $this->record_error ('selected_folder_id', 'You are not allowed to remove from this folder.');
  }

  /**
   * Execute action for a single folder.
   * @param FOLDER $fldr
   * @access private
   */
  protected function _folder_run ($fldr)
  {
    $opts = $this->_move_options_for ($fldr);
    if ($this->copy)
    {
      $fldr->copy_to ($this->_target, $opts);
    }
    else
    {
      $fldr->move_to ($this->_target, $opts);
    }
  }

  /**
   * Execute action for a single entry
   * @param ENTRY $entry
   * @access private
   */
  protected function _entry_run ($entry)
  {
    $opts = $this->_move_options_for ($entry);
    if ($this->copy)
    {
      $entry->copy_to ($this->_target, $opts);
    }
    else
    {
      $entry->move_to ($this->_target, $opts);
    }
  }

  /**
   * Draw the controls for the form.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    parent::_draw_controls ($renderer);
  }

  /**
   * Draw a confirmation message for this action.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_message ($renderer)
  {
    if ($this->copy)
    {
      $renderer->draw_text_row ('', 'Are you sure you want to copy ' . $this->object_list->description () . '?');
    }
    else
    {
      $renderer->draw_text_row ('', 'Are you sure you want to move ' . $this->object_list->description () . '?');
    }
    
    if ($this->object_list->has_folders () && $this->visible ('maintain_permissions'))
    {
      $renderer->start_row ();
      echo $renderer->check_box_as_html ('maintain_permissions');
      $renderer->finish_row ();
    }

    if ($this->object_list->has_entries () && $this->visible ('copy_as_draft'))
    {
      $renderer->start_row ();
      echo $renderer->check_box_as_html ('copy_as_draft');
      $renderer->finish_row ();
    }
    
    /* Make a copy (not a reference). */
    $tree = $this->app->make_tree_renderer ();

    include_once ('webcore/gui/folder_tree_node_info.php');
    $tree_node_info = new EXPLORER_FOLDER_TREE_NODE_INFO ($this->context);
    $tree_node_info->set_visible_node ($this->_folder);
    $tree_node_info->set_selected_node ($this->_folder);
    $tree_node_info->nodes_are_links = false;

    include_once ('webcore/gui/selector_tree_decorator.php');
    $decorator = new SELECTOR_TREE_DECORATOR ($tree);
    $decorator->control_name = 'selected_folder_id';

    $tree->node_info = $tree_node_info;
    $tree->decorator = $decorator;

    $folder_query = $this->app->login->folder_query ();
    $folders = $folder_query->tree ();
    
    $renderer->start_row ('Target');
    $tree->display ($folders);
    $renderer->finish_row ();

    $renderer->draw_error_row ('selected_folder_id');

    $buttons [] = $renderer->button_as_HTML ('No', "view_explorer.php?id={$this->_folder->id}");
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);
  }
}

/**
 * Moves one or more {@link DRAFTABLE_ENTRY}s or {@link FOLDER}s to another folder.
 * User must have create folder/content rights in the target folder and delete content/folder rights
 * in the source folder.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.7.0
 */
class MULTIPLE_DRAFTABLE_OBJECT_MOVER_FORM extends MULTIPLE_OBJECT_MOVER_FORM
{
  /**
   * @param FOLDER $folder Objects are from this folder.
   * @param FOLDER $target Move objects to this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = $this->field_at ('copy_as_draft');
    $field->visible = true;
  }
}

?>