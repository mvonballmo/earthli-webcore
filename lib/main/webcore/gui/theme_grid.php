<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
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
require_once('webcore/gui/grid.php');

/**
 * Displays {@link THEME}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.5.0
 */
class THEME_GRID extends STANDARD_GRID
{
  /**
   * @var boolean
   */
  public $is_chooser = false;

  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class .= ' content-sized-tiles';
  }

  /**
   * @param THEME $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    if ($this->is_chooser)
    {
      $this->_display_start_overlay_container();
      $this->_display_start_bottom_right_overlay();
      ?>
      <a class="button" href="#" onclick="set_main_theme ('<?php echo $obj->id; ?>'); return false;"
         title="Choose this theme"><?php echo $obj->title_as_html(); ?></a>
      <?php
      $this->_display_finish_bottom_right_overlay();
      $this->_draw_thumbnail($obj);
      $this->_display_finish_overlay_container();
    }
    else
    {
      $this->_display_start_overlay_commands($obj);
      $this->_draw_thumbnail($obj);
      ?>
      <h3>
        <?php echo $obj->title_as_html(); ?>
      </h3>
      <?php
      $this->_display_finish_overlay_commands();
    }
  }

  /**
   * @param THEME $obj
   */
  private function _draw_thumbnail($obj)
  {
    ?>
    <div class="image-without-text">
      <a href="<?php echo $obj->snapshot_name(); ?>"><img src="<?php echo $obj->snapshot_thumbnail_name(); ?>"
                                                          alt="<?php echo $obj->title_as_plain_text(); ?>"></a>
    </div>
  <?php
  }
}

?>