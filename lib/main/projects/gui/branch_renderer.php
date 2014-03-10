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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for a {@link BRANCH}.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.7.0
 */
class BRANCH_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param BRANCH $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $this->_echo_details_as_html ($obj);
    echo $obj->description_as_html ();
    $this->_echo_html_user_information ($obj, 'info-box-bottom');
  }

  /**
   * Shows parent/latest release for a branch in HTML.
   * @param BRANCH $obj
   */
  protected function _echo_details_as_html ($obj)
  {
?>
  <table class="basic columns left-labels">
    <tr>
      <th>State</th>
      <td>
        <?php
        echo $this->context->get_text_with_icon($obj->state_icon_url(), $obj->state_as_string(), '16px');
        ?>
      </td>
    </tr>
<?php
    $parent_release = $obj->parent_release ();
    if ($parent_release)
    {
      $parent_branch = $parent_release->branch ();
?>
    <tr>
      <th>Parent</th>
      <td>
      <?php
        echo $parent_branch->title_as_link ();
        if ($this->_options->show_as_summary)
        {
          echo '<div style="margin-left: .75em">' . $this->app->display_options->object_separator;
          echo $parent_release->title_as_link () . '</div>';
        }
        else
        {
          echo $this->app->display_options->object_separator;
          echo $parent_release->title_as_link ();
        }
      ?>
      </td>
    </tr>
<?php
    }

    if ($obj->exists ())
    {
      $latest_release = $obj->latest_release ();
      if ($latest_release)
      {
        $rel_text = $latest_release->title_as_link ();
      }
      else
      {
        $rel_text = 'not released';
      }
?>
    <tr>
      <th>Latest</th>
      <td><?php echo $rel_text; ?></td>
    </tr>
<?php

      $next_release = $obj->next_release ();
      if ($next_release)
      {
        $rel_text = $next_release->title_as_link ();
      }
      else
      {
        $rel_text = 'none planned';
      }
?>
    <tr>
      <th>Next</th>
      <td><?php echo $rel_text; ?></td>
    </tr>
<?php
    }
?>
  </table>
<?php
  }

  /**
   * Outputs the object as plain text.
   * @param BRANCH $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    $this->_echo_plain_text_user_information ($obj);

    $release = $obj->parent_release ();
    if (isset ($release))
    {
      $branch = $release->branch ();
      echo $this->par ($branch->title_as_link () . $this->app->display_options->object_separator . $release->title_as_link ());
    }

    echo $obj->description_as_plain_text ();
  }
}

?>