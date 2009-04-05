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
require_once ('webcore/gui/folder_renderer.php');

/**
 * Render details for a {@link PROJECT}.
 * @package projects
 * @subpackage gui
 * @version 3.1.0
 * @since 1.7.0
 */
class PROJECT_RENDERER extends FOLDER_RENDERER
{
  /**
   * Display details for a project in HTML.
   * @param PROJECT $obj
   */
  protected function _echo_html_content ($obj)
  {
    if (! $this->_options->show_as_summary || ! $obj->is_organizational ())
    {
      $this->_echo_details_as_html ($obj);
    }
    $this->_echo_html_descriptions ($obj);
    $this->_echo_html_user_information ($obj, 'info-box-bottom');
  }

  /**
   * Shows trunk/branch/etc. for a project in HTML.
   * @param PROJECT $obj
   */
  protected function _echo_details_as_html ($obj)
  {
  ?>
  <table cellpadding="2" cellspacing="0">
  <?php
    if (! $obj->is_organizational ())
    {
      if ($obj->exists ())
      {
        $trunk = $obj->trunk ();
        $latest_release = $trunk->latest_release ();
        if ($latest_release)
        {
          $latest_text = $latest_release->title_as_link ();
        }
        else
        {
          $latest_text = 'Not released';
        }
          
        $pending_release_query = $obj->release_query ();
        $pending_release_query->set_up_pending ();
        $next_release = $pending_release_query->first_object ();
        if ($next_release)
        {
          $next_text = $next_release->title_as_link ();
        }
        else
        {
          $next_text = 'None planned';        
        }
  ?>
      <tr>
        <td class="label">Trunk</td>
        <td><?php echo $trunk->title_as_link (); ?></td>
      </tr>
      <tr>
        <td class="label">Latest</td>
        <td><?php echo $latest_text; ?></td>
      </tr>
      <tr>
        <td class="label">Next</td>
        <td><?php echo $next_text; ?></td>
      </tr>
  <?php
      }
      else
      {
  ?>
      <tr>
        <td class="label">Default branch</td>
        <td><?php echo $trunk->title_as_html (); ?></td>
      </tr>
  <?php
      }
    }

    if (! $this->_options->show_as_summary)
    {
      $options = $obj->options ();
  ?>
    <tr>
      <td class="label">Assignees</td>
      <td>
      <?php
        switch ($options->assignee_group_type)
        {
        case Project_user_registered_only:
          echo 'Allow only registered users';
          break;
        case Project_user_all:
          echo 'Allow all users';
          break;
        case Project_user_group:
          $group = $options->assignee_group ();
          echo 'Allow only users from ' . $group->title_as_link ();
          break;
        }
      ?>
      </td>
    </tr>
    <tr>
      <td class="label">Reporters</td>
      <td>
      <?php
        switch ($options->reporter_group_type)
        {
        case Project_user_registered_only:
          echo 'Allow only registered users';
          break;
        case Project_user_all:
          echo 'Allow all users';
          break;
        case Project_user_group:
          $group = $options->reporter_group ();
          echo 'Allow only users from ' . $group->title_as_link ();
          break;
        }
      ?>
      </td>
    </tr>
    <tr>
      <td class="label">Releases</td>
      <td>
      <?php
        if (! $options->seconds_until_deadline)
        {
          echo 'Show no warnings for deadlines';
        }
        else
        {
          echo 'Show warning <span class="field">' . $options->release_warning_description () . '</span> before deadline.';
        }
      ?>
    </td>
  </tr>
  <?php
        if (! $obj->defines_options ())
        {
  ?>
    <tr>
      <td></td>
      <td class="notes">
  <?php 
          $definer = $options->definer ();
          echo 'Options are inherited from ' . $definer->title_as_link ();
        }
  ?>
      </td>
    </tr>
  <?php
      }
  ?>
  </table>
<?php
  }

  /**
   * Display details for a project in plain text.
   * @param PROJECT $obj
   */
  protected function _display_as_plain_text ($obj)
  {
    parent::_display_as_plain_text ($obj);
  }
}

?>