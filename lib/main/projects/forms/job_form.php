<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
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
require_once ('projects/forms/project_entry_form.php');

/**
 * Edit or create a {@link JOB}.
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.4.1
 */
class JOB_FORM extends PROJECT_ENTRY_FORM
{
  /**
   * @var string
   */
  public $name = 'job_form';

  /**
   * @param PROJECT $folder Project in which to add or edit the job.
   */
  public function JOB_FORM ($folder)
  {
    PROJECT_ENTRY_FORM::PROJECT_ENTRY_FORM ($folder);

    $field = new DATE_FIELD ();
    $field->id = 'time_needed';
    $field->title = 'Needed by';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'assignee_id';
    $field->title = 'Assigned to';
    $field->min_value = 0;
    $field->sticky = true;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'reporter_id';
    $field->title = 'Reported By';
    $field->min_value = 1;
    $field->sticky = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'subscribe_creator';
    $field->title = 'Subscribe creator';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'subscribe_reporter';
    $field->title = 'Subscribe reporter (if different)';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'subscribe_assignee';
    $field->title = 'Subscribe assignee (if different)';
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this job.
   * @param JOB $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('kind', $obj->kind);
    $this->set_value ('assignee_id', $obj->assignee_id);
    $this->set_value ('extra_description', $obj->extra_description);
    $this->set_value ('time_needed', $obj->time_needed);

    if ($obj->reporter_id)
    {
      $this->set_value ('reporter_id', $obj->reporter_id);
    }
    else
    {
      $this->set_value ('reporter_id', $obj->creator_id);
    }

    // set up subscription fields, defaulting to true

    $this->set_value ('subscribe_reporter', true);
    $this->set_value ('subscribe_assignee', true);
    $this->set_value ('subscribe_creator', true);

    $creator = $obj->creator ();
    if ($creator)
    {
      $subscriber = $creator->subscriber ();
      $this->set_value ('subscribe_creator', $subscriber->subscribed ($obj, Subscribe_entry));
    }

    $reporter = $obj->reporter ();
    if ($reporter)
    {
      $subscriber = $reporter->subscriber ();
      $this->set_value ('subscribe_reporter', $subscriber->subscribed ($obj, Subscribe_entry));
    }

    $assignee = $obj->assignee ();
    if ($assignee)
    {
      $subscriber = $assignee->subscriber ();
      $this->set_value ('subscribe_assignee', $subscriber->subscribed ($obj, Subscribe_entry));
    }
  }

  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->load_from_client ('assignee_id', 0);
    $this->load_from_client ('reporter_id', 0);
    $d = $this->app->make_date_time ();
    $d->clear ();
    $this->load_from_client ('time_needed', $d);
    $this->set_value ('subscribe_reporter', true);
    $this->set_value ('subscribe_assignee', true);
    $this->set_value ('subscribe_creator', true);
  }

  /**
   * Store the form's values for this job.
   * @param JOB $obj
   * @access private
   */
  public function commit ($obj)
  {
    parent::commit ($obj);

    // after the object has been stored with (possible) new assignee and reporter
    // fix up the subscriptions, maintaining at most one per unique user

    $creator = $obj->creator ();
    $reporter = $obj->reporter ();
    $assignee = $obj->assignee ();

    $creator_equal_reporter = $creator->equals ($reporter);

    if ($assignee)
    {
      $creator_equal_assignee = $creator->equals ($assignee);
      $assignee_equal_reporter = $assignee->equals ($reporter);
    }
    else
    {
      $creator_equal_assignee = false;
      $assignee_equal_reporter = false;
    }

    $subscribe_creator_explicit = $this->value_for ('subscribe_creator');
    $subscribe_reporter_explicit = $this->value_for ('subscribe_reporter');
    $subscribe_assignee_explicit = $this->value_for ('subscribe_assignee');

    $subscribe_creator = ($subscribe_creator_explicit ||
                          ($creator_equal_reporter && $subscribe_reporter_explicit) ||
                          ($creator_equal_assignee && $subscribe_assignee_explicit));

    $subscribe_reporter = ($subscribe_reporter_explicit ||
                          ($creator_equal_reporter && $subscribe_creator_explicit) ||
                          ($assignee_equal_reporter && $subscribe_assignee_explicit));

    $subscribe_assignee = ($subscribe_assignee_explicit ||
                        ($creator_equal_assignee && $subscribe_creator_explicit) ||
                        ($assignee_equal_reporter && $subscribe_reporter_explicit));

    if ($creator)
    {
      $subscriber = $creator->subscriber ();
      if ($subscriber->email)
      {
        $subscriber->set_subscribed ($obj, Subscribe_entry, $subscribe_creator);
      }
    }

    if ($reporter && ! $creator_equal_reporter)
    {
      $subscriber = $reporter->subscriber ();
      if ($subscriber->email)
      {
        $subscriber->set_subscribed ($obj, Subscribe_entry, $subscribe_reporter);
      }
    }

    if ($assignee && ! $creator_equal_assignee && ! $assignee_equal_reporter)
    {
      $subscriber = $assignee->subscriber ();
      if ($subscriber->email)
      {
        $subscriber->set_subscribed ($obj, Subscribe_entry, $subscribe_assignee);
      }
    }
  }

  /**
   * Store the form's values for this job.
   * @param JOB $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->set_assignee_id ($this->value_for ('assignee_id'));
    $obj->reporter_id = $this->value_for ('reporter_id');
    $obj->set_time_needed ($this->value_for ('time_needed'));

    parent::_store_to_object ($obj);
  }

  /**
   * Called after fields are validated.
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    foreach ($this->branches as $branch)
    {
      if ($this->value_for ("branch_{$branch->id}_enabled"))
      {
        $branch_status = $this->value_for ("branch_{$branch->id}_status");
        $branch_release_id = $this->value_for ("branch_{$branch->id}_release_id");

        if ($branch_release_id)
        {
          $release_query = $branch->release_query ();
          $release = $release_query->object_at_id ($branch_release_id);

          if (! $release->planned ())
          {
            $statuses = $this->app->display_options->job_statuses ();
            $status = $statuses [$branch_status];

            if ($status->kind == Job_status_kind_open)
            {
              $this->record_error ("branch_{$branch->id}_release_id", 'Cannot assign open jobs to a shipped release.');
            }
          }
        }
      }
    }
  }

  /**
   * Set the initial enabled branch and release.
   * @param integer $branch_id
   * @param integer $release_id
   * @access private
   */
  protected function _set_default_branch ($branch_id, $release_id = 0)
  {
    parent::_set_default_branch ($branch_id, $release_id);
    $this->set_enabled ("branch_{$branch_id}_status", true);
    $this->set_enabled ("branch_{$branch_id}_priority", true);
    $this->load_from_client ("branch_{$branch_id}_priority", 1);
    $this->load_from_client ("branch_{$branch_id}_status", 0);
  }

  /**
   * Create the per-branch fields for this form.
   * @param BRANCH $branch
   * @access private
   */
  protected function _add_fields_for_branch ($branch)
  {
    parent::_add_fields_for_branch ($branch);

    $field = new ENUMERATED_FIELD ();
    $field->id = "branch_{$branch->id}_status";
    $field->title = "Status";
    $field->enabled = isset ($_REQUEST [$field->id]);
    $field->sticky = true;
    $statuses = $this->app->display_options->job_statuses ();
    if (sizeof ($statuses))
    {
      foreach ($statuses as $status)
      {
        $field->add_value ($status->value);
      }
    }
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = "branch_{$branch->id}_priority";
    $field->title = "Priority";
    $field->enabled = isset ($_REQUEST [$field->id]);
    $field->sticky = true;
    $priorities = $this->app->display_options->job_priorities ();
    if (sizeof ($priorities))
    {
      foreach ($priorities as $priority)
      {
        $field->add_value ($priority->value);
      }
    }
    $this->add_field ($field);
  }

  /**
   * Load the branch information into the form.
   * This is called once for each branch enabled on the attached object.
   * @param JOB_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _load_from_branch_info ($branch_info)
  {
    parent::_load_from_branch_info ($branch_info);

    $id = $branch_info->branch_id;
    $this->set_enabled ("branch_{$id}_status", true);
    $this->set_value ("branch_{$id}_status", $branch_info->status);
    $this->set_enabled ("branch_{$id}_priority", true);
    $this->set_value ("branch_{$id}_priority", $branch_info->priority);
  }

  /**
   * Store form values for this branch.
   * This is called once for each branch that is enabled when the form is committed.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _store_to_branch_info ($branch_info)
  {
    parent::_store_to_branch_info ($branch_info);
    $branch_info->set_status ($this->value_for ("branch_{$branch_info->branch_id}_status"));
    $branch_info->priority = $this->value_for ("branch_{$branch_info->branch_id}_priority");
  }

  /**
   * Draws the JavaScript for enabling/disabling branches.
   * This should just output some JavaScript code as the body of the 'on_click_branch (ctrl, id)' function.
   * @access private
   */
  protected function _draw_branch_scripts ()
  {
    parent::_draw_branch_scripts ();
?>
    var status_ctrl = ctrl.form ['branch_' + id + '_status'];
    var priority_ctrl = ctrl.form ['branch_' + id + '_priority'];
    if (status_ctrl)
    {
      status_ctrl.disabled = ! ctrl.checked;
    }
    if (priority_ctrl)
    {
      priority_ctrl.disabled = ! ctrl.checked;
    }
<?php
  }

  /**
   * Draw the selectors for branch properties.
   * If the branch or release is locked, the values should be displayed as read-only.
   * @param BRANCH $branch
   * @param FORM_RENDERER $renderer
   * @param boolean $visible Is this branch enabled for this project entry?
   * @param RELEASE $release Release in this branch (may be empty).
   * @access private
   */
  protected function _draw_branch_info_controls ($branch, $renderer, $visible, $release)
  {
    /* Get the list of statuses for this branch. */
    $selected_status = $this->value_for ("branch_{$branch->id}_status");
    if ($this->cloning () || ! $this->object_exists ())
    {
      $statuses = $this->app->display_options->job_statuses ();
    }
    else
    {
      $statuses = $this->app->display_options->job_statuses_for ($selected_status);
    }

    if ($this->_branch_is_locked ($branch, $release))
    {
      if ($visible)
      {
        $renderer->draw_hidden ("branch_{$branch->id}_status");
        $renderer->draw_hidden ("branch_{$branch->id}_priority");

        $status = $statuses [$selected_status];
        $renderer->draw_text_row ('Status', $status->icon_as_html ('16px') . ' ' . $status->title);
        $renderer->draw_error_row ("branch_{$branch->id}_status");

        $priorities = $this->app->display_options->job_priorities ();
        $priority = $priorities [$this->value_for ("branch_{$branch->id}_priority")];
        $renderer->draw_text_row ('Priority', $priority->icon_as_html ('16px') . ' ' . $priority->title);
        $renderer->draw_error_row ("branch_{$branch->id}_priority");
      }
    }
    else
    {
      // Draw statuses

      $props = $renderer->make_list_properties ();

      foreach ($statuses as $status)
      {
        $props->add_item ($status->title, $status->value);
      }
      $renderer->draw_drop_down_row ("branch_{$branch->id}_status", $props);

      // Draw priorities

      $props = $renderer->make_list_properties ();
      $priorities = $this->app->display_options->job_priorities ();
      foreach ($priorities as $priority)
      {
        $props->add_item ($priority->title, $priority->value);
      }
      $renderer->draw_drop_down_row ("branch_{$branch->id}_priority", $props);
    }

    // Draw inherited properties

    parent::_draw_branch_info_controls ($branch, $renderer, $visible, $release);
  }

  /**
   * Add all users from the query result to the result.
   * @param USER_QUERY $user_query
   * @param FORM_RENDERER $renderer
   * @return FORM_LIST_PROPERTIES
   * @access private
   */
  protected function _prepare_list_properties_for ($renderer, $user_query)
  {
    $users = $user_query->objects ();
    $Result = $renderer->make_list_properties ();
    $Result->width = '15em';
    $Result->add_item ('(None)', 0);
    foreach ($users as $user)
    {
      $Result->add_item ($user->real_name (true), $user->id);
    }
    return $Result;
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_separator ();
    $this->_draw_kind_controls ($renderer);
    $this->_draw_component_controls ($renderer);
    $renderer->draw_separator ();

    $project_options = $this->_folder->options ();

    /* Draw the assignee box */

    $props = $this->_prepare_list_properties_for ($renderer, $project_options->assignee_query ());
    $renderer->draw_drop_down_row ('assignee_id', $props);

    /* Draw the reporter box */

    /* Rebuild the list only if necessary. */
    if (! $project_options->reporters_equals_assigners ())
    {
      $props = $this->_prepare_list_properties_for ($renderer, $project_options->reporter_query ());
    }

    /* The default reporter is (Me), not (None). */
    $props->replace_item (0, '(Me)', $this->login->id);

    $renderer->draw_drop_down_row ('reporter_id', $props);

    /* Draw other options */

    $renderer->draw_date_row ('time_needed');
    $renderer->draw_check_box_row ('is_visible');

    /* Start the branch section */

    $renderer->draw_separator ();
    $renderer->start_row ('Branches');
?>
  <p class="notes">
    Assign this job to one or more of the following branches.
  </p>
<?php
      $this->_draw_branch_controls ($renderer);
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description');

    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('extra_description');

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->draw_separator ();
    $renderer->draw_text_row ('Subscriptions', 'Check the following to subscribe the following users to this job.', 'notes');

    $renderer->start_row (' ');

    if ($this->object_exists ())
    {
      $creator = $this->_object->creator ();
    }
    else
    {
      $creator = $this->app->login;
    }

    $field = $this->field_at ('subscribe_creator');
    $field->title = $field->title . ' (' . $creator->title_as_link () . ')';

    echo "<p>\n";
    echo $renderer->check_box_as_html ('subscribe_creator');
    echo "<br>\n";
    echo $renderer->check_box_as_html ('subscribe_assignee');
    echo "<br>\n";
    echo $renderer->check_box_as_html ('subscribe_reporter');
    echo "</p>\n";

    $renderer->finish_row ();

    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }
}
?>