<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/gui/content_object_grid.php');

/**
 * Displays {@link USER}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */
class USER_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * @param USER $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    ?>
    <div class="grid-item">
      <?php
      $this->_display_start_minimal_commands_block($obj);
      ?>
      <div style="float: left; margin-right: .5em">
        <?php echo $obj->icon_as_html (Thirty_two_px); ?>
      </div>
      <h3>
        <?php echo $this->obj_link ($obj); ?>
      </h3>
      <p class="detail">
        <?php echo $obj->real_name (); ?>
        <br>Registered on
        <?php
        $c = $obj->time_created;
        $f = $c->formatter ();
        $f->type = Date_time_format_short_date;

        echo $c->format ($f);
        ?></p>
      <div style="margin-top: 5px">
      <?php
        $menu = $this->context->make_menu ();

        $entry_types = $this->app->entry_type_infos ();
        $url = new URL ($obj->home_page ());

        foreach ($entry_types as $type_info)
        {
          $url->replace_argument ('panel', $type_info->id);
          $menu->append ($type_info->plural_title, $url->as_text ());
        }

        $url->replace_argument ('panel', 'comments');
        $menu->append ('Comments', $url->as_text ());

        $menu->display ();
      ?>
      </div>
      <div class="text-flow">
        <?php $this->_echo_text_summary ($obj); ?>
      </div>
      <?php
      $this->_display_finish_minimal_commands_block();
      ?>
    </div>
<?php
   }
}

/**
 * Displays {@link USER}s for a {@link SEARCH}.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.7.0
 */
class SELECT_USER_GRID extends USER_GRID
{
}

?>