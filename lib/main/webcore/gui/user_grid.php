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
 *
 * Copyright (c) 2002-2014 Marco Von Ballmoos
 *
 * This file is part of earthli WebCore.
 *
 * earthli WebCore is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * earthli WebCore is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with earthli WebCore; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For more information about the earthli WebCore, visit:
 *
 * http://www.earthli.com/software/webcore
 ****************************************************************************/

/** */
require_once('webcore/gui/content_object_grid.php');

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
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->even_columns = false;
  }

  /**
   * @param USER $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    $this->_display_start_minimal_commands_block($obj);

    if ($obj->icon_url)
    {
      $this->context->start_icon_container($obj->icon_url, Thirty_two_px);
    }
    ?>
    <h3>
      <?php echo $this->obj_link($obj); ?>
    </h3>
    <?php
    if ($obj->icon_url)
    {
      $this->context->finish_icon_container();
    }
    ?>
    <p><?php echo $obj->real_name(); ?></p>
    <p>Registered on
      <?php
      $c = $obj->time_created;
      $f = $c->formatter();
      $f->type = Date_time_format_short_date;

      echo $c->format($f);
      ?>
    </p>
    <?php
    $menu = $this->context->make_menu();

    $entry_types = $this->app->entry_type_infos();
    $url = new URL ($obj->home_page());

    foreach ($entry_types as $type_info)
    {
      $url->replace_argument('panel', $type_info->id);
      $menu->append($type_info->plural_title, $url->as_text());
    }

    $url->replace_argument('panel', 'comments');
    $menu->append('Comments', $url->as_text());

    $menu->display();
    ?>
    <div class="text-flow">
      <?php $this->_echo_text_summary($obj); ?>
    </div>
    <?php
    $this->_display_finish_minimal_commands_block();
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