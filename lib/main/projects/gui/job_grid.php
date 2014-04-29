<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.5.0
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
require_once ('projects/gui/project_entry_grid.php');

/**
 * Display a list of {@link JOB}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.4.1
 */
class JOB_GRID extends PROJECT_ENTRY_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Job';

  /**
   * Draw entry-specific information for the given release.
   * @param JOB $obj
   * @param JOB_BRANCH_INFO $branch_info
   */
  protected function _draw_release_details ($obj, $branch_info)
  {
    $rel = $branch_info->release ();
    if (isset ($rel))
    {
      $status = $rel->status ();
      echo $status->as_html ();
    }
    else
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
  }

  /**
   * Draw user-specific information for the given release.
   * @param JOB $obj
   * @param JOB_BRANCH_INFO $branch_info
   */
  protected function _draw_user_details ($obj, $branch_info)
  {
    $is_closed = $branch_info->is_closed ();

    $time_open = $branch_info->age ();

    $time_in_status = $branch_info->status_age ();
    if (! $is_closed)
    {
      $needed_by = $branch_info->needed_by_as_html ();
      if ($needed_by)
      {
  ?>
  <div>
    <?php echo $needed_by; ?>
  </div>
  <?php
      }
    }
  ?>
  <div>
    <span class="field"><?php
      $status_props = $branch_info->status_properties ();
      echo $this->app->get_text_with_icon($status_props->icon, $status_props->title, Sixteen_px);
    ?></span>
  <?php
    if ($is_closed)
    {
      $closer = $branch_info->closer ();
  ?>
    (<?php echo $branch_info->time_closed->format (); ?>
    by <?php echo $closer->title_as_link (); ?>
    after <?php echo $time_open->format (); ?>)
  <?php
    }
    else
    {
      if ($time_open->equals ($time_in_status))
      {
  ?>
  (<?php echo $time_open->format (); ?>)
  <?php
      }
      else
      {
  ?>
  (<?php echo $time_in_status->format (); ?>) <span class="notes">(open for <?php echo $time_open->format (); ?>)</span>
  <?php
      }
    }
  ?>
  </div>
  <?php if (! $is_closed ) { ?>
  <div>
    <?php
    $priority_props = $branch_info->priority_properties ();
    echo $this->app->get_text_with_icon($priority_props->icon, $priority_props->title, Sixteen_px);
    ?>
  </div>
  <?php } ?>
  <div>
    <div>
    <?php
      if (! $is_closed)
      {
        $assignee = $obj->assignee ();

        if ($assignee)
        {
          $text = 'Assigned to ' . $assignee->title_as_link ();
          $time_owned = $obj->assignee_age ();
          if (isset ($time_owned))
          {
            $text .= ' (' . $time_owned->format () . ')';
          }
        }
        else
        {
          $text = '<span class="notes">Not assigned</span>';
        }

        echo $this->app->get_text_with_icon($obj->get_assignee_icon_url(), $text, Sixteen_px);
      }
    ?>
    </div>
    <div style="clear: both"></div>
  </div>
  <?php
  }

  /**
   * @param JOB $obj Get the title formatter from this job.
   * Jobs format open jobs with the default 'field' style, whereas closed jobs
   * are formatted with no style (usually rendered as non-bold).
   * @return TITLE_FORMATTER
   * @access private
   */
  public function title_formatter ($obj)
  {
    $Result = parent::title_formatter ($obj);

    /** @var $branch_info JOB_BRANCH_INFO */
    $branch_info = $obj->main_branch_info ();
    if ($branch_info->is_closed ())
    {
      $Result->CSS_class = '';
    }
    $Result->max_visible_output_chars = 0;
    return $Result;
  }
}

/**
 * Display {@link JOB}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.4.1
 */
class JOB_SUMMARY_GRID extends PROJECT_ENTRY_SUMMARY_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Job';
}

?>