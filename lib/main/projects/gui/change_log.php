<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/gui/object_list_title.php');

/**
 * Displays {@link RELEASE}s, {@link CHANGE}s and {@link JOB}s in a logical, ordered change list.
 * @package projects
 * @subpackage gui
 * @version 3.4.0
 * @since 1.4.1
 */
class CHANGE_LOG extends WEBCORE_OBJECT
{
  public $show_date = false;
  public $show_description = false;
  public $show_user = true;
  
  /**
   * Display the jobs, changes and releases given.
   * if there are jobs and changes not associated with a release, show first those jobs,
   * then those changes. Then, for each release in the list, show the release details, then
   * the list of associated jobs (from the list) and the list of assocated changes (from the
   * list).
   * @param array[JOB] $jobs
   * @param array[CHANGE] $chngs
   * @param array[RELEASE] $rels
   */
  public function display ($jobs, $changes, $releases)
  {
    $this->app->display_options->overridden_max_title_size = 150;
    $this->app->date_time_toolkit->formatter->set_default_formatter (Date_time_format_short_date);

    $change_idx = 0;
    $job_idx = 0;

    // show all unassociated jobs and changes

    ob_start ();
      $this->_display_entries ($jobs, 0, $job_idx, '_draw_job', 'Jobs');
      $this->_display_entries ($changes, 0, $change_idx, '_draw_change', 'Changes');
      $content = ob_get_contents ();
    ob_end_clean ();

    if (($job_idx > 0) || ($change_idx > 0))
    {
      echo $content;
?>
  <span id="releases"></span>
<?php
    }

    if (sizeof ($releases))
    {
      foreach ($releases as $rel)
      {
        $orig_job_idx = $job_idx;
        $orig_chng_idx = $change_idx;
        ob_start ();
          $this->_display_entries ($jobs, $rel->id, $job_idx, '_draw_job', 'Jobs');
          $this->_display_entries ($changes, $rel->id, $change_idx, '_draw_change', 'Changes');
          $content = ob_get_contents ();
        ob_end_clean ();
        $this->_draw_release ($rel, $job_idx - $orig_job_idx, $change_idx - $orig_chng_idx);
        echo $content;
      }
    }
  }

  /**
   * Display all changes and jobs which belong to 'rel'.
   * @param array[JOB] $jobs
   * @param array[CHANGE] $chngs
   * @param RELEASE $rel
   */
  public function display_release ($jobs, $chngs, $rel)
  {
    $this->app->display_options->overridden_max_title_size = 150;
    $this->app->date_time_toolkit->formatter->set_default_formatter (Date_time_format_short_date);

    $job_idx = 0;
    $change_idx = 0;
    
    $this->_display_entries ($jobs, $rel->id, $job_idx, '_draw_job', 'Jobs');
    $this->_display_entries ($chngs, $rel->id, $change_idx, '_draw_change', 'Changes');
  }

  /**
   * Display all entries that have release equal to 'release_id'.
   * 'entry_idx' is the current position in the given entry list. Processing should start with this index
   * and should increment it for each entry processed.
   * @param PROJECT_ENTRY[] $entries
   * @param integer $release_id
   * @param integer $entry_idx
   * @param string $draw_entry Use this method to render the 'entries' in the list.
   * @param string $label Identifies the type of entry in the heading.
   * @access private
   */
  protected function _display_entries ($entries, $release_id, $entry_idx, $draw_entry, $label)
  {
    unset ($this->curr_day);
    unset ($this->_component_id);
    
    $entry_count = sizeof ($entries);

    $title = new OBJECT_LIST_TITLE ($this->context);
    
    if ($entry_idx < $entry_count)
    {
      $branch_info = $entries [$entry_idx]->main_branch_info ();

      if ($branch_info->release_id == $release_id)
      {
        ob_start ();
          $old_idx = $entry_idx;
          while (($entry_idx < $entry_count) && ($branch_info->release_id == $release_id))
          {
            $entry = $entries [$entry_idx];
            if (is_a ($entry, 'JOB'))
            {
              $this->_draw_job ($entry);
            }
            else
            {
              $this->_draw_change ($entry);
            }
            $title->add_object ($entries [$entry_idx]);
            $entry_idx += 1;
            if ($entry_idx < $entry_count)
            {
              $branch_info = $entries [$entry_idx]->main_branch_info ();
            }
          }
          if ($old_idx != $entry_idx)
          {
            echo "</dl>\n";
          }
          $content = ob_get_contents ();
        ob_end_clean ();
        echo '<h2>' . $title->as_text () . "</h2>\n";
        echo $content;
      }
    }
  }
  
  /**
   * Draw the component for the given entry if different from the last entry's component.
   *
   * @param PROJECT_ENTRY $entry
   */
  protected function _draw_component_break ($entry)
  {
    if (! isset ($this->_component_id) || ($this->_component_id != $entry->component_id))
    {      
      if (isset ($this->_component_id))
      {
        echo "</dl>\n";
        echo '<div class="horizontal-separator"></div>';
      }
      
      echo "<h3>" . $this->comp_name_for ($entry) . "</h3>\n<dl>\n";

      $this->_component_id = $entry->component_id;
    }    
  }
  
  public function comp_name_for ($entry)
  {
    if (! $entry->component_id)
    {
      return '(no component)';
    }

    if (! isset ($this->_components))
    {
      $folder = $entry->parent_folder ();
      $component_query = $folder->component_query ();
      $this->_components = $component_query->indexed_objects ();
    }
    
    $component = $this->_components [$entry->component_id];
    
    return $component->icon_as_html ('20px') . ' ' . $component->title_as_link ();
  }

  /**
   * Draw the given project entry in the list.
   *
   * @param PROJECT_ENTRY $entry
   * @param USER $user
   * @param DATE_TIME $time
   * @access private
   */
  protected function _draw_entry ($entry, $user, $time)
  {
    $this->_draw_component_break ($entry);

    if ($this->show_date)
    {
      $f = $time->formatter ();
      $f->type = Date_time_format_short_date;
      $details [] = $time->format ($f);
    }
    
    if ($this->show_user && isset ($user))
    {
      $uf = $user->title_formatter ();
      $uf->CSS_class = '';
      $details [] = $user->title_as_link ($uf);
    }
    
    $detail = '';
    if (! empty ($details))
    {
      $detail = '[' . implode (' - ', $details) . '] ';
    }
    
    $detail .= $entry->title_as_link ();
    
    $props = $entry->kind_properties ();

    echo "<div class=\"sixteen\" style=\"background-image: url(" . $props->expanded_icon_url() . ")\">";

    echo $detail;

    if ($this->show_description)
    {
      $munger = $entry->html_formatter ();
      $munger->force_paragraphs = false;
      $desc = $entry->description_as_html ($munger);
      if ($desc)
      {
        echo "<div class=\"description\">$desc</div>";
      }
    }

    echo '</div>';
  }

  /**
   * Draw a job in the list.
   * @param JOB $job
   * @access private
   */
  protected function _draw_job ($job)
  {
    /** @var $branch_info JOB_BRANCH_INFO */
    $branch_info = $job->main_branch_info ();
    $this->_draw_entry ($job, $branch_info->closer (), $branch_info->time_closed);
  }
  
  /**
   * Draw a change in the list.
   * @param CHANGE $chng
   * @access private
   */
  protected function _draw_change ($chng)
  {
    $this->_draw_entry ($chng, $chng->creator (), $chng->time_created);
  }

  /**
   * Draw a release in the list.
   * If the job and changes counts are 0, then assume that the entries for
   * that release weren't in the list and retrieve the counts.
   * @param RELEASE $release
   * @param integer $num_jobs Number of jobs in this release. Can be empty.
   * @param integer $num_changes Number of changes in this release. Can be empty.
   * @access private
   */
  protected function _draw_release ($release, $num_jobs, $num_changes)
  {
    if (! $num_changes)
    {
      $chng_query = $release->change_query ();
      $num_changes = $chng_query->size ();
    }

    if (! $num_jobs)
    {
      $job_query = $release->job_query ();
      $num_jobs = $job_query->size ();
    }

    $t = $release->title_formatter ();
    $t->set_name ('view_release_change_log.php');
?>
  <h2>
    <?php echo $release->title_as_link ($t) ?>
  </h2>
    <table class="basic columns left-labels">
      <tr>
        <th>Jobs</th>
        <td><?php echo $num_jobs; ?></td>
      </tr>
      <tr>
        <th>Changes</th>
        <td><?php echo $num_changes; ?></td>
      </tr>
      <tr>
        <th></th>
        <td>
          <?php
          $status = $release->status ();
          echo $status->as_html ();
          ?>
        </td>
      </tr>
    </table>
<?php
    if ($this->show_description)
    {
      $munger = $release->html_formatter ();
      $desc = $release->description_as_html ($munger);
      if ($desc)
      {
      ?>
      <div class="text-flow">
        <?php echo $desc; ?>
      </div>
      <?php
      }
    }

    if ($this->show_user || $this->show_date)
    {
      $t = $release->time_created->formatter ();
      $t->type = Date_time_format_date_and_time;
      $creator = $release->creator ();
?>
    <p class="info-box-bottom">
      Created
    <?php
      if ($this->show_user)
      {
    ?>
      by <?php echo $creator->title_as_link (); ?>
    <?php
      }

      if ($this->show_date)
      {
        ?>
        on
        <?php echo $release->time_created->format ($t);
      }
    ?>
    </p>
<?php
    }
  }
  
  /**
   * List of components available for the folder being rendered.
   *
   * @var array[COMPONENT]
   */
  protected $_components;
  
  /**
   * The component id of the last rendered entry; used to detected when the component changes.
   *
   * @var integer
   */
  protected $_component_id;
}
?>