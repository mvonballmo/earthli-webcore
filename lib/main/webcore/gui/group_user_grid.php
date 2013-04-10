<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link USER}s from a {@link GROUP}.
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.2.1
 */
class GROUP_USER_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $object_name = 'user';

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * @var string
   */
  public $width = '';

  /**
   * @param GROUP $group Show users from this group.
   */
  public function __construct ($group)
  {
    parent::__construct ($group->app);
    $this->_group_id = $group->id;
    $this->_controls_renderer = $this->app->make_controls_renderer ();
  }

  /**
   * @param USER $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    echo $obj->title_as_link ();

    if ($this->login->is_allowed (Privilege_set_group, Privilege_modify))
    {
      echo "</td>\n<td>";
      echo $this->_controls_renderer->button_as_html ('Remove...', 'delete_user_from_group.php?id=' . $this->_group_id . '&name=' . $obj->title, '{icons}buttons/delete');
    }
  }

  /**
   * @access private
   */
  protected function _draw_footer ()
  {
    if ($this->login->is_allowed (Privilege_set_group, Privilege_modify))
    {
?>
  <tr>
    <td></td>
    <td>
      <?php
        echo $this->_controls_renderer->button_as_html ('Add...', 'add_user_to_group.php?id=' . $this->_group_id, '{icons}buttons/add');
      ?>
    </td>
  </tr>
<?php
    }
  }

  /**
   * @access private
   */
  protected function _draw_empty_grid ()
  {
    parent::_draw_empty_grid ();

    if ($this->login->is_allowed (Privilege_set_group, Privilege_modify))
    {
      $this->_start_grid ();
?>
  <tr>
    <td>
      <?php
        echo $this->_controls_renderer->button_as_html ('Add...', 'add_user_to_group.php?id=' . $this->_group_id, '{icons}buttons/add');
      ?>
    </td>
  </tr>
<?php
      $this->_finish_grid ();
    }
  }

  /**
   * @var CONTROLS_RENDERER
   * @access private
   */
  protected $_controls_renderer;
}
?>