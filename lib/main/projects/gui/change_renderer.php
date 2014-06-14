<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.7.0
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
require_once ('projects/gui/project_entry_renderer.php');

/**
 * Render details for a {@link CHANGE}.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.7.0
 */
class CHANGE_RENDERER extends PROJECT_ENTRY_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param CHANGE $entry
   * @access private
   */
  protected function _display_as_html ($entry)
  {
?>
<div>
  <table class="basic columns left-labels">
    <tr>
      <th>Kind</th>
      <td>
        <?php
        $props = $entry->kind_properties ();

        echo $this->context->get_icon_with_text($props->icon, Sixteen_px, $entry->kind_as_text());
        ?>
      </td>
    </tr>
    <?php
      if ($entry->component_id)
      {
        $comp = $entry->component ();
    ?>
    <tr>
      <th>Component</th>
      <td>
        <?php
        echo $this->app->get_icon_with_text($comp->icon_url, Sixteen_px, $comp->title_as_link());
        ?>
      </td>
    </tr>
    <?php
      }
    ?>
    <tr>
      <th>Job</th>
      <td>
<?php
    $job = $entry->job ();
    if ($job)
    {
      $t = $job->title_formatter ();
      $t->css_class = '';
      echo $job->title_as_link ($t);
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
      <th>Created</th>
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
      <th>Modified</th>
      <td><?php echo $entry->time_modified->format (); ?> by <?php echo $modifier->title_as_link (); ?></td>
    </tr>
    <?php
      }
    }

    if ($entry->files)
    {
?>
    <tr>
      <th>
        Files
      </th>
      <td>
<?php
      echo $entry->files_as_html ();
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
   * @param CHANGE $obj
   * @param CHANGE_BRANCH_INFO $branch_info
   * @access private
   */
  protected function _echo_html_branch_info ($obj, $branch_info)
  {
    $applier = $branch_info->applier ();
    echo 'Applied on ' . $branch_info->time_applied->format () . ' by ' . $applier->title_as_link ();
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
   * @param CHANGE $entry
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
   * @param CHANGE $entry
   * @param CHANGE_BRANCH_INFO $branch_info
   */
  protected function _echo_plain_text_branch_info ($entry, $branch_info)
  {
    $applier = $branch_info->applier ();
    echo '    Applied ' . $this->time ($branch_info->time_applied) . ' by ' . $applier->title_as_plain_text ();
  }

  /**
   * Show information for a branch's release.
   * @param CHANGE_BRANCH_INFO $branch_info
   */
  protected function _echo_plain_text_branch_release_info ($branch_info)
  {
    echo 'Next release';
  }

  /**
   * Outputs the object for print preview.
   * @param CHANGE $entry
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display_as_printable ($entry, $options = null)
  {
    $this->_hide_files = ! $this->_options->show_files;
    parent::display_as_printable ($entry);
  }

  /**
   * Show files when rendered?
   * The print preview can toggle this value.
   * @var boolean
   * @access private
   */
  protected $_hide_files = false;
}

?>