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
 * Edit or create a {@link PROJECT_ENTRY}.
 * @package projects
 * @subpackage forms
 * @version 3.3.0
 * @since 1.4.1
 */
class PROJECT_ENTRY_FORM extends ENTRY_FORM
{
  /**
   * @param PROJECT $folder Project in which to add or edit the PROJECT_ENTRY.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'extra_description';
    $field->title = 'Description';
    $field->description = 'This information is not shown in job lists or change logs, '
                          . ' but is always available when a job is viewed alone. If it\'s'
                          . ' a bug, store the stack trace or log output here. If it\'s a'
                          . ' new feature, store examples and a more in-depth description here.';
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'kind';
    $field->title = 'Kind';
    $field->required = true;
    $field->sticky = true;
    $kinds = $this->app->display_options->entry_kinds ();
    if (sizeof ($kinds))
    {
      foreach ($kinds as $kind)
      {
        $field->add_value ($kind->value);
      }
    }
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'component_id';
    $field->title = 'Component';
    $field->sticky = true;
    $this->add_field ($field);

    $branch_query = $this->_folder->branch_query ();
    $this->branches = $branch_query->objects ();
    $this->default_branch_id = $this->_folder->trunk_id;

    foreach ($this->branches as $branch)
    {
      $this->_add_fields_for_branch ($branch);
    }

    $field = new INTEGER_FIELD ();
    $field->id = "main_branch_id";
    $field->title = "Main Branch";
    $field->min_value = 1;
    $this->add_field ($field);

    $field = $this->field_at ('description');
    $field->title = 'Summary';
    $field->description = 'A short description that is shown in change logs and job lists.'
                          . ' Longer text that should not appear in summaries should go in'
                          . ' the \'Extra Information\' field below.';

  }

  /**
   * Load initial properties from this PROJECT_ENTRY.
   * @param PROJECT_ENTRY $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('kind', $obj->kind);
    $this->set_value ('component_id', $obj->component_id);
    $this->set_value ('extra_description', $obj->extra_description);
    $this->set_value ('main_branch_id', $obj->main_branch_id);

    // set up branch statuses

    $branch_query = $obj->branch_info_query ();
    $branch_infos = $branch_query->indexed_objects ();
    foreach ($branch_infos as $branch_info)
    {
      $this->_load_from_branch_info ($branch_info);
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->load_from_client ('kind', 0);
    $this->load_from_client ('component_id', 0);

    $branch_id = read_var ('branch_id', $this->default_branch_id);
    $release_id = read_var ('release_id', 0);

    $this->_set_default_branch ($branch_id, $release_id);
  }

  /**
   * Does this form hold valid data for this project entry?
   * Applies additional check to ensure that at least one branch is selected.
   * @param PROJECT_ENTRY $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    $count = sizeof ($this->branches);
    $index = 0;
    $selected = false;

    while (! $selected && ($index < $count))
    {
      $branch = $this->branches [$index];
      $selected = ($this->value_for ("branch_{$branch->id}_enabled") > 0);
      $index += 1;
    }

    if (! $selected)
    {
      $this->record_error ('main_branch_id', 'Please select at least one branch.');
    }

    $main_branch_id = $this->value_for ('main_branch_id');
    if (! $main_branch_id)
    {
      $this->record_error ('main_branch_id', 'Please select a branch for use in non-branch-specific lists.');
    }
    else
    {
      if (! $this->value_for ("branch_{$main_branch_id}_enabled"))
      {
        $this->record_error ('main_branch_id', 'Please select an enabled branch for use in non-branch-specific lists.');
      }
    }
  }

  /**
   * Store the form's values for this job.
   * @param PROJECT_ENTRY $obj
   * @access private
   */
  public function commit ($obj)
  {
    parent::commit ($obj);
    $obj->store_branch_infos ();
  }

  /**
   * Store the form's values for this job.
   * @param PROJECT_ENTRY $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $main_branch_id = $this->value_for ('main_branch_id');

    $obj->kind = $this->value_for ('kind');
    $obj->component_id = $this->value_for ('component_id');
    $obj->extra_description = $this->value_for ('extra_description');

    foreach ($this->branches as $branch)
    {
      if ($this->value_for ("branch_{$branch->id}_enabled"))
      {
        $branch_info = $obj->new_branch_info ($branch);
        $this->_store_to_branch_info ($branch_info);
        $obj->add_branch_info ($branch_info);

        if ($main_branch_id == $branch->id)
        {
          $obj->set_main_branch_info ($branch_info);
        }
      }
    }

    parent::_store_to_object ($obj);
  }

  /**
   * Set the initial enabled branch and release.
   * @param integer $branch_id
   * @param integer $release_id
   * @access private
   */
  protected function _set_default_branch ($branch_id, $release_id = 0)
  {
    $this->set_value ('main_branch_id', $branch_id);
    $this->set_value ("branch_{$branch_id}_enabled", true);
    $this->set_enabled ("branch_{$branch_id}_release_id", true);
    $this->set_value ("branch_{$branch_id}_release_id", $release_id);
  }

  /**
   * Create the per-branch fields for this form.
   * @param BRANCH $branch
   * @access private
   */
  protected function _add_fields_for_branch ($branch)
  {
    $field = new BOOLEAN_FIELD ();
    $field->id = "branch_{$branch->id}_enabled";
    $field->title = $branch->title_as_link ();
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = "branch_{$branch->id}_release_id";
    $field->title = "Release";
    $field->enabled = isset ($_REQUEST [$field->id]);
    $field->min_value = 0;
    $this->add_field ($field);
  }

  /**
   * Load the branch information into the form.
   * This is called once for each branch that is enabled on a loaded object.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _load_from_branch_info ($branch_info)
  {
    $id = $branch_info->branch_id;
    $show_branch = true;

    if ($this->cloning ())
    {
      $branch = $branch_info->branch ();
      $show_branch = ! $branch->locked ();

      if (! $show_branch && ($this->value_for ('main_branch_id') == $id))
      {
        $this->set_value ('main_branch_id', 0);
      }

      if ($show_branch && ! $this->value_for ('main_branch_id'))
      {
        $this->set_value ('main_branch_id', $id);
      }
    }

    $this->set_value ("branch_{$id}_enabled", $show_branch);
    $this->set_enabled ("branch_{$id}_release_id", true);
    $this->set_value ("branch_{$id}_release_id", $branch_info->release_id);
  }

  /**
   * Store form values for this branch.
   * This is called once for each branch that is enabled when the form is committed.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _store_to_branch_info ($branch_info)
  {
    $branch_info->release_id = $this->value_for ("branch_{$branch_info->branch_id}_release_id");
  }

  /**
   * Configure the history item's properties.
   * Makes sure that the branch information is compared when the main object is
   * stored.
   * @param AUDITABLE $obj The object to be stored.
   * @param HISTORY_ITEM $history_item
   * @access private
   */
  protected function _adjust_history_item ($obj, $history_item)
  {
    parent::_adjust_history_item ($obj, $history_item);
    $history_item->compare_branches = true;
  }

  /**
   * Draws the JavaScript for enabling/disabling branches.
   * This should just output some JavaScript code as the body of the 'on_click_branch (ctrl, id)' function.
   * @access private
   */
  protected function _draw_branch_scripts ()
  {
?>
    var settings = document.getElementById ('branch_' + id + '_settings');
    var panel = document.getElementById ('branch_' + id + '_panel');
    var title = document.getElementById ('branch_' + id + '_title');
    var main_branch = ctrl.form ['main_branch_id'];

    var release_ctrl = ctrl.form ['branch_' + id + '_release_id'];
    release_ctrl.disabled = ! ctrl.checked;
    enable_item (main_branch, id, ctrl.checked);

    if (ctrl.checked)
    {
      settings.style.display = 'block';
      panel.className = 'chart';
      title.className = 'chart-title';

      /* Make sure at least one radio button is selected. If none is selected, the control
         corresponding to 'id' will be selected. */

      ensure_has_selected (main_branch, id);
    }
    else
    {
      settings.style.display = 'none';
      panel.className = '';
      title.className = '';

      /* Make sure a different radio button is selected (the algorithm simply finds the first
         radio button which is not the one specified and selects that. */

      ensure_not_selected (main_branch, id);
      ensure_has_selected (main_branch);
    }

<?php
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function on_click_branch (ctrl, id)
  {
    <?php $this->_draw_branch_scripts (); ?>
  }
<?php
  }

  /**
   * Draws the entry {@link PROJECT_ENTRY::$kind} selector controls.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_kind_controls ($renderer)
  {
    $kinds = $this->app->display_options->entry_kinds ();
    if (sizeof ($kinds))
    {
      $props = $renderer->make_list_properties ();
      $props->items_per_row = 1;
      $props->line_spacing = '.25em';
      $index = 0;
      foreach ($kinds as &$kind)
      {
        $props->add_item ($kind->icon_as_html ('20px') . ' ' . $kind->title, $index);
        $index += 1;
      }
      $renderer->draw_radio_group_row ('kind', $props);
    }
  }

  protected function _draw_component_controls ($renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('[None]', 0);

    $component_query = $this->_folder->component_query ();
    $comps = $component_query->objects ();
    foreach ($comps as $comp)
    {
      $props->add_item ($comp->title_as_plain_text (), $comp->id);
    }

    $renderer->draw_drop_down_row ('component_id', $props);
  }

  /**
   * Should controls for this branch be disabled?
   * @param BRANCH $branch
   * @param RELEASE $release
   * @access private
   */
  protected function _branch_is_locked ($branch, $release)
  {
    return $branch->locked () || (isset ($release) && $release->locked () && ! $this->cloning ());
  }

  /**
   * Draw the selector's for branch properties.
   * If the branch or release is locked, the values should be displayed as read-only.
   * @param BRANCH $branch
   * @param FORM_RENDERER $renderer
   * @param boolean $visible Is this branch enabled for this project entry?
   * @param RELEASE $release Release in this branch (may be empty).
   * @access private
   */
  protected function _draw_branch_info_controls ($branch, $renderer, $visible, $release)
  {
    if ($this->_branch_is_locked ($branch, $release))
    {
      $renderer->draw_hidden ("branch_{$branch->id}_release_id");

      if (isset ($release))
      {
        $title = $release->title_as_link ();
        if ($release->locked ())
        {
          $title = $this->app->resolve_icon_as_html ('{icons}indicators/locked', 'Locked', '16px') . ' ' . $title;
        }

        $renderer->draw_text_row ('Release', $title);
      }
      else
      {
        $renderer->draw_text_row ('Release', 'Not released');
      }

      $renderer->draw_error_row ("branch_{$branch->id}_release_id");
    }
    else
    {
      $props = $renderer->make_list_properties ();
      $props->add_item ('[Next release]', 0);

      $release_query = $branch->pending_release_query (Release_not_locked);
      $releases = $release_query->objects ();
      
      $planned_release = null;

      if (sizeof ($releases))
      {
        foreach ($releases as $release)
        {
          $nd = $release->time_next_deadline;
          if ($nd->is_valid ())
          {
            $df = $nd->formatter ();
            $df->set_type_and_clear_flags (Date_time_format_short_date);
            $status = $nd->format ($df);
          }
          else
          {
            $status = $release->state_as_string ();
          }
            
          $title = $release->title_as_plain_text () . ' (' . $status . ')';
          $props->add_item ($title, $release->id);

          if ($release->planned () && ! isset ($planned_release))
          {
            $planned_release = $release;
          }
        }
      }

      if (! $this->value_for ("branch_{$branch->id}_release_id"))
      {
        /* If the branch isn't selected or the object is new, then set the
           first planned release as the default instead of '[Next release]'. */

        if ((! $this->object_exists () || ! $visible || $this->cloning ()) && isset ($planned_release))
        {
          $this->set_value ("branch_{$branch->id}_release_id", $planned_release->id);
        }
      }

      $renderer->draw_drop_down_row ("branch_{$branch->id}_release_id", $props);
    }

    $renderer->start_row (' ');
      $props = $renderer->make_list_properties ();
      $props->add_item ('Use for non-branch-specific lists.', $branch->id, '', $visible);
      echo $renderer->radio_group_as_html ('main_branch_id', $props);
    $renderer->finish_row ();
  }

  /**
   * Draw the selector for all branches.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_branch_controls ($renderer)
  {
    if (sizeof ($this->branches))
    {
      $renderer->start_block ();
      $renderer->start_row ();

      $use_DHTML = $this->context->dhtml_allowed ();
      $check_props = $renderer->make_check_properties ();

      foreach ($this->branches as $branch)
      {
        $style = '';
        $visible = $this->value_for ("branch_{$branch->id}_enabled");

        if ($visible || ! $branch->locked ())
        {
          if ($use_DHTML)
          {
            if (! $visible)
            {
              $style = 'display: none';
            }
          }
        ?>
        <div id="branch_<?php echo $branch->id; ?>_panel" <?php if ($visible) echo "class=\"chart\""; ?> style="margin-bottom: .25em; width: 35em">
          <div id="branch_<?php echo $branch->id; ?>_title" <?php if ($visible) echo "class=\"chart-title\""; ?> style="text-align: left">
            <?php
              $locked = $branch->locked ();

              $release_id = $this->value_for ("branch_{$branch->id}_release_id");
              if ($release_id)
              {
                $release_query = $branch->release_query ();
                $release = $release_query->object_at_id ($release_id);
                $locked = $locked || $this->_branch_is_locked ($branch, $release);
              }
              else
              {
                $release = null;
              }

              if ($locked)
              {
                $renderer->draw_hidden ("branch_{$branch->id}_enabled");
                if ($branch->locked ())
                {
                  echo $this->app->resolve_icon_as_html ('{icons}indicators/locked', 'Locked', '16px') . ' ';
                }
                echo $branch->title_as_link ();
              }
              else
              {
                $check_props->on_click_script = "on_click_branch (this, '$branch->id')";
                echo $renderer->check_box_as_HTML ("branch_{$branch->id}_enabled", $check_props);
              }
            ?>
          </div>
          <div class="chart-body" id="branch_<?php echo $branch->id; ?>_settings" <?php echo "style=\"$style; margin-left: 1em\""; ?>>
          <?php
            $renderer->start_block ();
              $this->_draw_branch_info_controls ($branch, $renderer, $visible, $release);
            $renderer->finish_block ();
          ?>
          </div>
        </div>
        <?php
        }
      }

      $renderer->finish_row ();
      $renderer->draw_error_row ('main_branch_id');
      $renderer->finish_block ();
    }
  }

  /**
   * Draws the description with project-specific help-text.
   * @access private
   */
  protected function _draw_description_field ()
  {
    $this->_draw_text_box ('description', '35em', '8em'); ?>
    <div class="notes" style="width: 40em">A short description that is shown in change logs and job lists.
      Longer text that should not appear in summaries should go in the 'Extra Information' field below. Find out more
      about <a style="text-decoration: underline" href="text_formatting.php">supported tags and formatting</a>.</div>
<?php
  }

  /**
   * Draws the extra description with project-specific help-text.
   * @access private
   */
  protected function _draw_extra_description_field ()
  {
//    $this->_draw_button ('Preview', "preview_text (document.$this->name.extra_description)");
    $this->_draw_text_box ('extra_description', '35em', '15em'); ?>
    <div class="notes" style="width: 40em">This information is not shown in job lists or change logs, but is
      always available when a job is viewed alone. If it's a bug, store the stack trace
      or log output here. If it's a new feature, store examples and a more in-depth
      description here. Find out more about <a style="text-decoration: underline" href="text_formatting.php">supported tags and formatting</a>.</div>
<?php
  }

  /**
   * Id of the branch to use for new entries.
   * If this is not set in the request, the first defined branch for the project is used.
   * @var integer
   * @access private
   */
  public $default_branch_id;
}
?>