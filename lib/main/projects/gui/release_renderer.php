<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.6.0
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
 * Render details for a {@link RELEASE}.
 * @package projects
 * @subpackage gui
 * @version 3.6.0
 * @since 1.7.0
 */
class RELEASE_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param RELEASE $obj
   * @access private
   */
  protected function _display_as_html($obj)
  {
    if ($this->_options->show_as_summary)
    {
      $this->_echo_text_as_html($obj, $obj->summary);
      $this->_echo_details_as_html($obj);
    }
    else
    {
      $this->_echo_details_as_html($obj);
      $this->_echo_html_description($obj);
    }

    $this->_echo_html_user_information($obj, 'info-box-bottom');
  }

  /**
   * Shows testing/ship dates for a release in HTML.
   * @param RELEASE $obj
   */
  protected function _echo_details_as_html($obj) 
  {
    $status = $obj->status ();
?>
    <table class="basic columns left-labels">
      <tr>
        <th>Test Date</th>
        <td><?php echo $status->test->as_html(); ?></td>
      </tr>
      <tr>
        <th>Ship Date</th>
        <td><?php echo $status->ship->as_html(); ?></td>
      </tr>
    </table>
<?php
  }

  /**
   * Outputs the object as plain text.
   * @param object $obj
   * @access private
   */
  protected function _display_as_plain_text($obj) 
  {
    $status = $obj->status ();
    echo $this->line ('Test Date: ' . $status->test->as_plain_text ());
    echo $this->par ('Ship Date: ' . $status->ship->as_plain_text ());
    
    $this->_echo_plain_text_description($obj);
    $this->_echo_plain_text_user_information($obj);
  }
}

?>