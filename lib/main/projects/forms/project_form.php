<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
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
require_once ('webcore/forms/folder_form.php');

/**
 * Edit or create a {@link PROJECT}.
 * @package projects
 * @subpackage forms
 * @version 3.4.0
 * @since 1.4.1
 */
class PROJECT_FORM extends FOLDER_FORM
{
  /**
   * @param PROJECT $folder Project to edit or project in which to add.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'defines_options';
    $field->caption = 'Defines options';
    $this->add_field ($field);

    include_once ('projects/obj/project_options.php');  // include the constants used below

    $field = new ENUMERATED_FIELD ();
    $field->id = 'assignee_group_type';
    $field->caption = 'Assignees';
    $field->add_value (Project_user_all);
    $field->add_value (Project_user_registered_only);
    $field->add_value (Project_user_group);
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'assignee_group_id';
    $field->caption = 'Assignee group id';
    $field->min_value = 1;
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'reporter_group_type';
    $field->caption = 'Reporters';
    $field->add_value (Project_user_all);
    $field->add_value (Project_user_registered_only);
    $field->add_value (Project_user_group);
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'reporter_group_id';
    $field->caption = 'Reporter group id';
    $field->min_value = 1;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'seconds_until_deadline';
    $field->caption = 'Releases';
    $field->description = 'Show a warning for releases with a deadline within this many days.';
    $field->min_value = 0;
    $this->add_field ($field);

    // only shown for existing objects

    $field = new INTEGER_FIELD ();
    $field->id = 'trunk_id';
    $field->caption = 'Trunk';
    $field->description = 'Default branch for new jobs and changes.';
    $field->min_value = 1;
    $this->add_field ($field);

    // only shown for new objects

    $field = new TITLE_FIELD ();
    $field->id = 'branch_title';
    $field->caption = 'Branch title';
    $field->description = 'Fill in the name of the default branch for this project.';
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this project.
   * @param PROJECT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $options = $obj->options ();
    $this->set_value ('defines_options', $obj->defines_options ());
    $this->_apply_options_to_UI ($options);

    $trunk = $obj->trunk ();
    if ($trunk)
    {
      $this->set_value ('trunk_id', $trunk->id);
    }

    $this->_set_up_options ();
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('branch_title', 'Dev');

    if ($this->_folder)
    {
      $options = $this->_folder->options ();
      $this->set_value ('defines_options', 0);
      $this->_apply_options_to_UI ($options);
    }
    
    $this->_set_up_options ();
  }

  /**
   * Set up the initial control state: if not defined then all controls 
   * are disabled group selector is enabled only if the current choice is 
   * to use a group.
   * @access private
   */
  protected function _set_up_options ()
  {
    $defines_options = $this->value_for ('defines_options');
    $this->set_enabled ('seconds_until_deadline', $defines_options);

    $this->set_enabled ('assignee_group_type', $defines_options);
    $this->set_enabled ('assignee_group_id', $defines_options && $this->value_for ('assignee_group_type') == Project_user_group);

    $this->set_enabled ('reporter_group_type', $defines_options);
    $this->set_enabled ('reporter_group_id', $defines_options && $this->value_for ('reporter_group_type') == Project_user_group);
    
    $this->set_enabled ('trunk_id', ! $this->value_for ('is_organizational'));
  }

  /**
   * Commit the changed to the database.
   * @param PROJECT $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj_exists = $obj->exists ();

    parent::commit ($obj);

    if (! $obj_exists)
    {
      $trunk = $obj->new_object ('branch');
      $trunk->title = $this->value_for ('branch_title');

      $history_item = $trunk->new_history_item ();
      $trunk->store_if_different ($history_item);

      $obj->trunk_id = $trunk->id;
      $obj->store ();
    }

    $options = $obj->options ();
    $options->apply_changes ();
  }
    
  /**
   * Apply values to the UI.
   * @param PROJECT_OPTIONS $options
   * @access private
   */
  protected function _apply_options_to_UI ($options)
  {
    $this->set_value ('assignee_group_type', $options->assignee_group_type);
    $this->set_value ('assignee_group_id', $options->assignee_group_id);
    $this->set_value ('reporter_group_type', $options->reporter_group_type);
    $this->set_value ('reporter_group_id', $options->reporter_group_id);
    $this->set_value ('seconds_until_deadline', $options->seconds_until_deadline);
  }

  /**
   * Store form field values to the options.
   * @param PROJECT_OPTIONS $options
   * @access private
   */
  protected function _store_to_options ($options)
  {
    $options->assignee_group_type = $this->value_for ('assignee_group_type');
    $options->assignee_group_id = $this->value_for ('assignee_group_id');
    $options->reporter_group_type = $this->value_for ('reporter_group_type');
    $options->reporter_group_id = $this->value_for ('reporter_group_id');
    $options->seconds_until_deadline = $this->value_for ('seconds_until_deadline');
  }

  /**
   * Store the form's values for this project.
   * @param PROJECT $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);

    if ($obj->exists ())
    {
      $obj->trunk_id = $this->value_for ('trunk_id');
    }

    $options = $obj->options ();
    $options->set_inherited (! $this->value_for ('defines_options'), false);
    $this->_store_to_options ($options);

    if ($this->previewing ())
    {
      if (! $obj->exists ())
      {
        $trunk = $obj->new_object ('branch');
        $trunk->title = $this->value_for ('branch_title');
        $obj->_trunk = $trunk;
      }
    }
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function on_inherit_changed (ctrl)
  {
    var ctrls_disabled = is_selected (ctrl, 0);
    enable_items( ctrl.form.assignee_group_type, ! ctrls_disabled);
    enable_items( ctrl.form.reporter_group_type, ! ctrls_disabled);
    enable_items( ctrl.form.seconds_until_deadline, ! ctrls_disabled);
    on_group_type_changed (ctrl.form.assignee_group_type, ctrl.form.assignee_group_id);
    on_group_type_changed (ctrl.form.reporter_group_type, ctrl.form.reporter_group_id);
  }

  function on_group_type_changed (ctrl, group_ctrl)
  {
    var ctrls_disabled = ! is_selected (ctrl, <?php echo Project_user_group; ?>);
    group_ctrl.disabled = ctrls_disabled;
  }
  
  function on_organizational_changed (ctrl)
  {
    ctrl.form.trunk_id.disabled = ctrl.checked;
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->start_column ();
    $renderer->start_row ();
?>
    <div class="left-sidebar">
      <?php
        $folder_query = $this->login->folder_query ();
        $folders = $folder_query->tree ();

        include_once ('projects/gui/project_tree_node_info.php');
        $tree_node_info = new PROJECT_TREE_NODE_INFO ($this->app);
        $tree_node_info->page_link = $this->env->url (Url_part_file_name);
        $tree_node_info->set_visible_node ($this->_folder);
        $tree_node_info->set_selected_node ($this->_folder);
        $tree_node_info->set_defined_nodes_visible ($folders);

        /* Make a copy (not a reference). */
        $tree = $this->app->make_tree_renderer ();
        $tree->node_info = $tree_node_info;
        $tree->display ($folders);
      ?>
    </div>
<?php
    $renderer->finish_row ();
    $renderer->start_column ();

    $renderer->draw_text_line_row ('title');
    $renderer->draw_icon_browser_row ('icon_url', 'id', '28em');

    $renderer->draw_check_box_row ('is_visible');
    $item = $renderer->make_check_properties ();
    $item->on_click_script = 'on_organizational_changed (this)';
    $item->smart_wrapping = true;
    $renderer->draw_check_box_row ('is_organizational', $item);
    $renderer->draw_separator ();


    if (! $this->object_exists () || ! $this->_object->is_root ())
    {
      if (! $this->object_exists ())
      {
        $renderer->draw_text_line_row ('branch_title');
      }
      elseif (! $this->_object->is_root ())
      {
        $branch_query = $this->_object->branch_query ();
        $branches = $branch_query->objects ();
  
        $props = $renderer->make_list_properties ();
        foreach ($branches as $branch)
        {
          $props->add_item ($branch->title_as_plain_text (), $branch->id);
        }
        $renderer->draw_drop_down_row ('trunk_id', $props);
      }
      $renderer->draw_separator ();
    }
    
    $renderer->start_row ('Options');
    $renderer->start_block (true);

      $renderer->start_row ('');

      // if this folder has a parent, then show the inheritance options

      if (! $this->object_exists ())
      {
        $parent = $this->_folder;
      }
      else
      {
        $parent = $this->_folder->parent_folder ();
      }

      if ($parent)
      {
        $options_folder = $folder_query->object_at_id ($parent->options_id);
        $props = $renderer->make_list_properties ();
        $props->on_click_script = 'on_inherit_changed (this)';

        if ($this->login->is_allowed (Privilege_set_folder, Privilege_modify, $options_folder))
        {
          $t = $options_folder->title_formatter ();
          $t->set_name ($this->env->url (Url_part_file_name));
          $title = 'Inherit options from ' . $options_folder->title_as_link ($t);
        }
        else
        {
          $title = 'Inherit options from ' . $options_folder->title_as_html ();
        }

        $props->add_item ($title, 0);
        $props->add_item ('Define options below.', 1);

        echo $renderer->radio_group_as_HTML ('defines_options', $props);
      }
      else
      {
        $renderer->draw_hidden ('defines_options');
      }

      $renderer->finish_row ();

      $renderer->draw_separator ();

      $props = $renderer->make_list_properties ();
      $props->width = '15em';
      $props->add_item ('[No warning]', 0);
      $props->add_item ('1 day', 86400);
      $props->add_item ('2 days', 2 * 86400);
      $props->add_item ('3 days', 3 * 86400);
      $props->add_item ('5 days', 5 * 86400);
      $props->add_item ('1 week', 7 * 86400);
      $props->add_item ('2 weeks', 14 * 86400);
      $props->add_item ('1 month', 30 * 86400);
      $renderer->draw_drop_down_row ('seconds_until_deadline', $props);

      $renderer->draw_separator ();

      /* Prepare the option and group lists for assignees and reporters. */
      
      $props = $this->_make_user_list_properties_for ($renderer, 'assignee_group_id');
      $renderer->draw_radio_group_row ('assignee_group_type', $props);
      $renderer->draw_separator ();
      $props = $this->_make_user_list_properties_for ($renderer, 'reporter_group_id');
      $renderer->draw_radio_group_row ('reporter_group_type', $props);
      
      if (isset ($this->_user_list_error_message))
      {
        $renderer->draw_caution_row (' ', $this->_user_list_error_message);
      }

    $renderer->finish_block ();
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('summary');
    $renderer->draw_text_box_row ('description');

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish_column ();
    $renderer->finish ();
  }
  
  protected function _make_user_list_properties_for ($renderer, $ctrl_id)
  {
    $Result = $renderer->make_list_properties ();
    $Result->on_click_script = 'on_group_type_changed (this, this.form.' . $ctrl_id . ')';
    $Result->add_item ('Allow all users', Project_user_all);
    $Result->add_item ('Allow only registered users', Project_user_registered_only);

    if ($this->login->is_allowed (Privilege_set_group, Privilege_view))
    {
      $group_query = $this->app->group_query ();
      $groups = $group_query->objects ();
      if (sizeof ($groups))
      {
        $group_props = $renderer->make_list_properties ();
        foreach ($groups as $group)
        {
          $group_props->add_item ($group->title_as_plain_text (), $group->id);
        }
        
        $Result->add_item ('Allow only ' . $renderer->drop_down_as_HTML ($ctrl_id, $group_props), Project_user_group);
      }
      else
      {
        $this->_user_list_error_message = 'Cannot limit by group (no groups available)';
      }
    }
    else
    {
      $this->_user_list_error_message = 'Cannot limit by group (cannot see groups)';
    }
      
    return $Result;
  }
  
  /**
   * Set when {@link _make_user_list_properties_for()} is called.
   * @var boolean
   * @access private
   */
  protected $_user_list_error_message;
}
?>