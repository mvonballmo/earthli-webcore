<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.7.0
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
require_once ('projects/gui/project_entry_renderer.php');

/**
 * Render details for a {@link JOB}.
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.7.0
 */
class JOB_RENDERER extends PROJECT_ENTRY_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param JOB $entry
   * @access private
   */
  protected function _display_as_html ($entry)
  {
    $this->_echo_subscribe_status ($entry);
?>
<div>
  <table class="basic columns left-labels">
    <tr>
      <th>Kind</th>
      <td><?php echo $entry->kind_icon () . ' ' . $entry->kind_as_text (); ?></td>
    </tr>
    <?php
      if ($entry->component_id)
      {
        $comp = $entry->component ();
    ?>
    <tr>
      <th>Component</th>
      <td><?php echo $comp->icon_as_html ('16px') . ' ' . $comp->title_as_link (); ?></td>
    </tr>
<?php
      }
      if ($entry->time_needed->is_valid ())
      {
    ?>
    <tr>
      <th>Needed By</th>
      <td>
      <?php
        $f = $entry->time_needed->formatter ();
        $f->type = Date_time_format_date_only;
        echo $entry->time_needed->format ($f);
      ?></td>
    </tr>
    <?php
      }
    ?>
    <tr>
      <td class="label">Assigned to</td>
      <td>
      <?php
        echo $entry->assignee_icon () . ' ';

        $assignee = $entry->assignee ();
        if ($assignee)
        {
          echo $assignee->title_as_link ();
          $time_owned = $entry->assignee_age ();
          if (isset ($time_owned))
          {
            echo ' (' . $time_owned->format () . ')';
          }
        }
        else
        {
          echo '(None)';
        }
      ?>
      </td>
    </tr>
    <?php
      $creator = $entry->creator ();
      $reporter = $entry->reporter ();
      if (! $reporter->equals ($creator))
      {
    ?>
    <tr>
      <th>Reported By</th>
      <td>
      <?php
        if ($reporter)
        {
          echo $reporter->title_as_link ();
        }
        else
        {
          echo '(None)';
        }
      ?>
      </td>
    </tr>
    <?php
      }

      if (! $this->_hide_users)
      {
    ?>
    <tr>
      <th>Created</th>
      <td>
        <?php echo $entry->time_created->format (); ?>
        by
        <?php
          echo $creator->title_as_link ();
        ?>
      </td>
    </tr>
    <?php
        if (! $entry->time_created->equals ($entry->time_modified))
        {
          $modifier = $entry->modifier ();
    ?>
    <tr>
      <th>Modified</th>
      <td><?php echo $entry->time_modified->format (); ?> by <?php echo $modifier->title_as_link (); ?></td>
    </tr>
    <?php
        }
      }
    ?>
  </table>
<?php
  $this->_echo_branches_as_html ($entry);
?>
</div>
<?php
    $this->_echo_html_description ($entry);
    $this->_echo_html_extra_description ($entry);
  }

  /**
   * Show information for this branch as HTML.
   * @param JOB $obj
   * @param JOB_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_html_branch_info ($obj, $branch_info)
  {
    if (! $branch_info->is_closed())
    {
      $needed_by = $branch_info->needed_by_as_html ();
      if ($needed_by)
      {
        echo $needed_by . '<br>';
      }
    }
?>
  <span class="field"><?php echo $branch_info->status_icon () . ' ' . $branch_info->status_as_text (); ?></span>
  <span class="detail">
  <?php
    $closer = $branch_info->closer ();
    $time_open = $branch_info->age ();
    $time_in_status = $branch_info->status_age ();
    if (isset ($closer))
    {
  ?>
  <?php echo $branch_info->time_closed->format (); ?>
  by <?php echo $closer->title_as_link (); ?>
  after <?php echo $time_open->format (); ?>
  <?php
    }
    else
    {
      if ($time_open->equals ($time_in_status))
      {
  ?>
  (<?php echo $time_open->format (); ?>)<br>
  <?php
      }
      else
      {
  ?>
  (<?php echo $time_in_status->format (); ?>) <span class="notes">(open for <?php echo $time_open->format (); ?>)</span><br>
  <?php
      }
  ?>
  <?php echo $branch_info->priority_icon () . ' ' . $branch_info->priority_as_text (); ?>
  <?php
    }
  ?>
  </span>
  <?php
  }

  /**
   * Show information for a branch's release.
   * @param JOB_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_html_branch_release_info ($branch_info)
  {
    if ($branch_info->is_closed ())
    {
      echo 'Next release';
    }
    else
    {
      echo 'Not scheduled for release';
    }
  }

  /**
   * Outputs the object as plain text.
   * @param JOB $entry
   * @access private
   */
  protected function _display_as_plain_text ($entry)
  {
    echo $this->line ('[Kind]: ' . $entry->kind_as_text ());

    $assignee = $entry->assignee ();
    if ($assignee)
    {
      $assignee_text = '[Assigned to]: ' . $assignee->title_as_plain_text ();
      $time_owned = $entry->assignee_age ();
      if (isset ($time_owned))
      {
        $assignee_text .= ' (' . $time_owned->format () . ')';
      }

      echo $this->line ($assignee_text);
    }
    else
    {
      echo $this->line ('[Assigned to]: Nobody');
    }

    $creator = $entry->creator ();
    $reporter = $entry->reporter ();
    if (! $reporter->equals ($creator))
    {
      echo $this->line ('[Reported By]: ' . $reporter->title_as_plain_text ());
    }

    if ($entry->time_needed->is_valid ())
    {
      echo $this->line ('[Needed by]: ' . $this->time ($entry->time_needed, Date_time_format_short_date));
    }

    $this->_echo_plain_text_user_information ($entry);
    $this->_echo_branches_as_plain_text ($entry);
    $this->_echo_plain_text_description ($entry);
    $this->_echo_plain_text_extra_description ($entry);
  }

  /**
   * Show information for this branch
   * @param JOB $entry
   * @param JOB_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_plain_text_branch_info ($entry, $branch_info)
  {
    if (! $branch_info->is_closed())
    {
      $needed_by = $branch_info->needed_by_as_plain_text ();
      if ($needed_by)
      {
        echo $this->line ($needed_by);
      }
    }

    echo '    ' . $branch_info->status_as_text ();

    $closer = $branch_info->closer ();
    $time_open = $branch_info->age ();
    $time_in_status = $branch_info->status_age ();
    if ($closer)
    {
      echo ' ' . $this->time ($branch_info->time_closed) . ' by ' . $closer->title_as_plain_text () . ' after ' . $time_open->format ();
    }
    else
    {
      if ($time_open->equals ($time_in_status))
      {
        echo $this->line (' (' . $time_open->format () . ')');
      }
      else
      {
        echo $this->line (' (' . $time_in_status->format () . ') (open for ' . $time_open->format () . ')');
      }
      echo '    ' . $branch_info->priority_as_text ();
    }
  }

  /**
   * Show information for a branch's release.
   * @param JOB_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_plain_text_branch_release_info ($branch_info)
  {
    if ($branch_info->is_closed ())
    {
      echo 'Next release';
    }
    else
    {
      echo 'Not scheduled for release';
    }
  }

  /**
   * Outputs the object for print preview.
   * @param JOB $entry
   * @access private
   */
  protected function _display_as_printable ($entry)
  {
    parent::_display_as_printable ($entry);

    if ($this->_options->show_changes)
    {
      $change_query = $entry->change_query ();
      /** @var $changes CHANGE[] */
      $changes = $change_query->objects ();
      $num_changes = sizeof ($changes);
      if ($num_changes)
      {
  ?>
    <h2><?php echo $num_changes; ?> Changes</h2>
  <?php
        $renderer = $changes[0]->handler_for (Handler_print_renderer, $this->_options);
        foreach ($changes as $change)
        {
          echo '<h3>' . $change->title_as_link () . '</h3>';
          $renderer->display ($change);
        }
      }
    }
  }
}

/**
 * Render {@link CHANGE}s for a {@link JOB}.
 * Also renders other data with {@link ENTRY_ASSOCIATED_DATA_RENDERER}.
 * @package projects
 * @subpackage gui
 * @version 3.3.0
 * @since 1.9.0
 */
class JOB_ASSOCIATED_DATA_RENDERER extends ENTRY_ASSOCIATED_DATA_RENDERER
{
  /**
   * Draws the list of {@link CHANGE}s.
   * @param JOB $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $change_query = $obj->change_query ();
    $num_changes = $change_query->size ();
    if ($num_changes)
    {
?>
<h2>
  <?php echo $num_changes; ?> Changes
</h2>
<div class="grid-content">
<?php
  $class_name = $this->app->final_class_name ('CHANGE_GRID', 'projects/gui/change_grid.php');
  /** @var $grid CHANGE_GRID */
  $grid = new $class_name ($this->app, read_var ('search_text'));
  $grid->set_ranges (20, 1);
  $grid->set_query ($change_query);
  $grid->display ();
?>
</div>
<?php
    }

    parent::display ($obj, $options);
  }
}

?>