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
require_once ('webcore/gui/grid.php');

/**
 * Display a list of {@link COMPONENT}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.5.0
 * @since 1.7.0
 */
class COMPONENT_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $box_style = 'object-in-list';

  /**
   * @var string
   */
  public $object_name = 'Component';

  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean Show creator/modifier with releases?
   */
  public $show_user = true;

  /**
   * @var boolean Show project for release?
   */
  public $show_folder = false;

  /**
   * @var boolean Show branch for release?
   */
  public $show_branch = false;

  /**
   * @param COMPONENT $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
?>
  <div class="grid-item">
    <div class="minimal-commands">
      <?php $this->_draw_menu_for ($obj, Menu_size_minimal); ?>
    </div>
    <div class="minimal-commands-content">
    <h3>
      <?php echo $this->app->get_text_with_icon($obj->icon_url, $obj->title_as_link (), '20px'); ?>
    </h3>
  <?php
    $menu = $this->context->make_menu ();

    $entry_types = $this->app->entry_type_infos ();
    $url = new URL ($obj->home_page ());

    foreach ($entry_types as $type_info)
    {
      $panel_name = strtolower ($type_info->plural_title);
      $url->replace_argument ('panel', $panel_name);
      $menu->append ($type_info->plural_title, $url->as_html ());
    }

    $url->replace_argument ('panel', 'comments');
    $menu->append ('Comments', $url->as_html ());

    $menu->display ();
  ?>
  </div>
<?php
  }
}
?>