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
 * Edit or create a {@link CHANGE}.
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.4.1
 */
class CHANGE_FORM extends PROJECT_ENTRY_FORM
{
  /**
   * @var string
   */
  public $name = 'change_form';
  
  /**
   * @param PROJECT $folder Project in which to add or edit the change.
   */
  public function CHANGE_FORM ($folder)
  {
    PROJECT_ENTRY_FORM::PROJECT_ENTRY_FORM ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'job_id';
    $field->title = 'Job';
    $field->min_value = 0;
    $field->sticky = true;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'files';
    $field->title = 'Files';
    $field->min_length = 0;
    $field->max_length = 65535;
    $this->add_field ($field);

    $this->_fields ['title']->required = false;
  }

  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $job_id = read_var ('job_id');
    if (empty ($job_id))
    {
      $this->load_from_client ('job_id', 0);
      $job_id = $this->value_for ('job_id');
    }
    
    $job = $this->job_at ($job_id); 
    if (isset ($job))
    {
      $this->set_value ('job_id', $job_id);
      $this->add_preview ($job, 'Attached to job: ' . $job->title_as_html (), ! $this->previewing ());
    }
    else
    {
      $this->set_value ('job_id', 0);
    }
  }

  /**
   * Load initial properties from this change.
   * @param CHANGE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('files', $obj->files);

    $job = $this->job_at ($obj->job_id);
    if (isset ($job))
    {
      $this->set_value ('job_id', $job->id);
      $this->add_preview ($job, 'Attached to job: ' . $job->title_as_html (), ! $this->previewing ());
    }

    // When updating a change, do not publish by default

    $this->set_value ('publication_state', History_item_silent);
  }

  /**
   * Store the form's values for this change.
   * @param CHANGE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->job_id = $this->value_for ('job_id');
    $obj->files = $this->value_for ('files');

    parent::_store_to_object ($obj);
  }

  /**
   * Does this form hold valid data for this change?
   * @param CHANGE $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->value_for ('title') && ! $this->value_for ('description'))
    {
      $this->record_error ('title', 'Please provide a title or description.');
    }
  }

  /**
   * Retrieve the valid jobs for this change.
   * Only jobs that were either created before or closed after this change was created are available.
   * @return PROJECT_ENTRY_QUERY
   * @access private
   */
  public function job_query ()
  {
    if (! isset ($this->_job_query))
    {
      $q = $this->_folder->entry_query ();

      $q->set_type ('job');
      if ($this->object_exists ())
      {
        $t = $this->_object->time_created;
      }
      else
      {
        $t = new DATE_TIME ();
      }
      $q->restrict ("closer_id <> 0 OR job.time_closed < '" . $t->as_iso () . "'");
      $q->restrict ("entry.time_created < '" . $t->as_iso () . "'");

      $this->_job_query = $q;
    }

    return $this->_job_query;
  }

  /**
   * Retrieve the job for 'id'
   * If the job cannot be assigned to this change or it's not visible, returns empty.
   * @return JOB
   * @access private
   */
  public function job_at ($id)
  {
    $job_query = $this->job_query ();
    return $job_query->object_at_id ($id);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_check_box_row ('is_visible');
    $renderer->draw_separator ();
    $this->_draw_kind_controls ($renderer);
    $this->_draw_component_controls ($renderer);

    // Start the branch section

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
    $renderer->draw_text_box_row ('files');

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('extra_description');
    $renderer->draw_separator ();

    $branch_id = $this->value_for ('main_branch_id');    
    $branch_query = $this->_folder->branch_query ();
    $branch = $branch_query->object_at_id ($branch_id);

    $release_id = $this->value_for ("branch_{$branch_id}_release_id");
    if ($release_id)
    {
      $release_query = $branch->release_query ();
      $release = $release_query->object_at_id ($release_id);
      $entry_query = $release->entry_query ();
    }
    else
    {
      $entry_query = $branch->entry_query ();
    }

    $entry_query->set_type ('job');
    if ($this->object_exists ())
    {
      $t = $this->_object->time_created;
    }
    else
    {
      $t = new DATE_TIME ();
    }

    $job_id = $this->value_for ('job_id');
    if (empty ($job_id))
    {
      $job_id = 0;
    }

    $entry_query->restrict ("(entry.id = $job_id) OR (closer_id <> 0) <> 0 OR (job.time_closed < '" . $t->as_iso () . "')");
    $this->jobs = $entry_query->objects ();

    $num_jobs = sizeof ($this->jobs);
    if ($num_jobs)
    {
      $props = $renderer->make_list_properties ();
      $props->height = min ($num_jobs + 1, 10);

      $props->add_item ('[None]', 0);

      foreach ($this->jobs as $iter_job)
      {
        $t = $iter_job->title_formatter ();
        $t->max_visible_output_chars = 55;
        $props->add_item ($iter_job->title_as_plain_text ($t), $iter_job->id);
      }

      $job = $this->job_at ($this->value_for ('job_id'));
      $job_text = 'A change can be attached to the job to which it contributed. Only the jobs for the selected branch and release are shown.';
      if ($job)
      {
        $renderer->draw_text_row (' ', $job_text . ' The current job is previewed above.', 'notes');
        $renderer->draw_list_box_row ('job_id', $props);
      }
      else
      {
        $renderer->draw_text_row (' ', $job_text, 'notes');
        $renderer->draw_list_box_row ('job_id', $props);
      }
    }

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->draw_separator ();
    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }

  /**
   * @var QUERY
   * @access private
   */
  protected $_job_query;
}

?>