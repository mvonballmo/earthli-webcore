<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.1.0
 * @since 1.7.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Render details for a {@link CHANGE}.
 * @package projects
 * @subpackage gui
 * @version 3.1.0
 * @since 1.7.0
 */
class CHANGE_RENDERER extends PROJECT_ENTRY_RENDERER
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
<div class="info-box-top">
  <table cellpadding="2" cellspacing="0" style="margin-left: 0px">
    <tr>
      <td class="label">Kind</td>
      <td><?php echo $entry->kind_icon () . ' ' . $entry->kind_as_text (); ?></td>
    </tr>
    <?php
      if ($entry->component_id)
      {
        $comp = $entry->component ();
    ?>
    <tr>
      <td class="label">Component</td>
      <td><?php echo $comp->icon_as_html ('16px') . ' ' . $comp->title_as_link (); ?></td>
    </tr>
    <?php
      }
    ?>
    <tr>
      <td class="label">Job</td>
      <td>
<?php
    $job = $entry->job ();
    if ($job)
    {
      $t = $job->title_formatter ();
      $t->CSS_class = '';
      echo '(' . $job->title_as_link ($t) . ')';
    }
    else
    {
?>
        (None)
<?php
    }
?>
      </td>
    </tr>
<?php
    if (! $this->_hide_users)
    {
?>
    <tr>
      <td class="label">Created</td>
      <td>
        <?php echo $entry->time_created->format (); ?>
        by <?php
          $creator = $entry->creator ();
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
      <td class="label">Modified</td>
      <td><?php echo $entry->time_modified->format (); ?> by <?php echo $modifier->title_as_link (); ?></td>
    </tr>
    <?php
      }
    }

    if (! $this->_hide_files && $entry->files)
    {
      /* Make a copy. */
      $layer = $this->app->make_layer ();
      $layer->margin_top = '0px';
      $layer->name = "id_{$entry->id}_details";
      $layer->visible = ! $this->app->dhtml_allowed ();
?>
    <tr>
      <td class="label">
        <?php if (! $layer->visible) $layer->draw_toggle (); ?> Files
      </td>
      <td>
        <?php echo $entry->num_files (); ?>
      </td>
    </tr>
    <tr>
      <td></td>
      <td>
<?php
      $layer->start ();
      echo $entry->files_as_html ();
      $layer->finish ();
?>
      </td>
    </tr>
<?php
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
   * @param PROJECT_ENTRY $obj
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_html_branch_info ($obj, $branch_info)
  {
    $applier = $branch_info->applier ();
    echo 'Applied ' . $branch_info->time_applied->format () . ' by ' . $applier->title_as_link ();
  }

  /**
   * Show information for a branch's release.
   * @param CHANGE_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_html_branch_release_info ($branch_info)
  {
    echo 'Next release';
  }

  /**
   * Outputs the object as plain text.
   * @param object $entry
   * @access private
   */
  protected function _display_as_plain_text ($entry)
  {
    echo $this->line ('[Kind]: ' . $entry->kind_as_text ());

    $job = $entry->job ();
    if (isset ($job))
    {
      echo $this->line ('[Job]: ' . $job->title_as_plain_text ());
    }

    $this->_echo_plain_text_user_information ($entry);
    $this->_echo_branches_as_plain_text ($entry);
    $this->_echo_plain_text_description ($entry);
    $this->_echo_plain_text_extra_description ($entry);
  }

  /**
   * Show information for this branch
   * @param PROJECT_ENTRY $entry
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   */
  protected function _echo_plain_text_branch_info ($entry, $branch_info)
  {
    $applier = $branch_info->applier ();
    echo '    Applied ' . $this->time ($branch_info->time_applied) . ' by ' . $applier->title_as_plain_text ();
  }

  /**
   * Show information for a branch's release.
   * @param PROJECT_ENTRY_BRANCH_INFO $branch_info
   */
  protected function _echo_plain_text_branch_release_info ($branch_info)
  {
    echo 'Next release';
  }

  /**
   * Outputs the object for print preview.
   * @param JOB $entry
   */
  public function display_as_printable ($entry)
  {
    $this->_hide_files = ! $this->_options->show_files;
    parent::display_as_printable ($entry);
  }

  /**
   * Show files when renderered?
   * The print preview can toggle this value.
   * @var boolean
   * @access private
   */
  protected $_hide_files = false;
}

?>