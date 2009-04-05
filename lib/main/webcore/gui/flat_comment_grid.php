<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/gui/printable_comment_grid.php');

/**
 * Displays {@link COMMENT}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.1.0
 * @since 2.2.1
 */
class FLAT_COMMENT_GRID extends PRINTABLE_COMMENT_GRID
{
  /**
   * @var string
   */
  public $box_style = '';

  /**
   * Used when printing to shut off pagination
   * @var boolean
   */
  public $show_paginator = true;

  /**
   * @param COMMENT $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $this->_draw_comment_contents ($obj);
  }

  /**
   * Draws a single comment.
   * Allows descendants to use this single method for rendering the comment.
   * @param COMMENT $obj
   * @access private
   */
  protected function _draw_comment_contents ($obj)
  {
    $creator = $obj->creator ();
?>
    <div class="info-box-top">
      <?php
        if ($creator->icon_url)
        {
      ?>
      <div style="float: left; margin-right: .5em">
        <?php echo $creator->icon_as_html (); ?>
      </div>
      <?php
        }
      ?>
      <div>
        <?php
          if ($this->show_controls)
          {
            if ($obj->equals ($this->_comment))
            {
              $this->_draw_menu_for ($obj);
            }
            else
            {
              $this->_draw_menu_for ($obj, Menu_size_compact);
            }
          }
        ?>
        <div style="margin-bottom: .25em">
          <?php echo $obj->icon () . ' ' . $obj->title_as_link (); ?>
        </div>
        <?php
          if ($this->show_user_info)
          {
        ?>
        <div class="detail" style="margin-left: 2em">
          by <?php echo $creator->title_as_link (); ?> - <?php echo $obj->time_created->format (); ?>
        </div>
        <?php
          }
        ?>
      </div>
      <div style="clear: both"></div>
    </div>
    <div class="description">
<?php
    echo $obj->description_as_html ();

    if ($obj->modified () && $this->show_user_info)
    {
      $modifier = $obj->modifier ();
    ?>
      <p class="detail" style="text-align: right">Updated by <?php echo $modifier->title_as_link (); ?> - <?php echo $obj->time_modified->format (); ?></p>
  <?php
    }
  ?>
    </div>
<?php
    $attachment_query = $obj->attachment_query ();

    $num_attachments = $attachment_query->size ();
    if ($num_attachments)
    {
      $class_name = $this->app->final_class_name ('ATTACHMENT_GRID', 'webcore/gui/attachment_grid.php');
      $grid = new $class_name ($this->app);
      $grid->set_ranges (3, 3);
      $grid->set_query ($attachment_query);
      $grid->display ();
    }
  }

  /**
   * Get the list of objects for the requested page.
   * @return array[COMMENT]
   * @access private
   */
  protected function _get_objects ()
  {
    // if printing, then don't paginate, return all objects

    if ($this->show_paginator)
    {
      return $this->_query->objects ();
    }
    else
    {
      if ($this->_comment)
      {
        // return only the comments attached to this comment

        $this->_query->restrict ('(com.parent_id = ' . $this->_comment->id . ' OR com.id = ' . $this->_comment->id . ')');
        return parent::_get_objects ($this->_query);
      }

      return parent::_get_objects ($this->_query);
    }
  }
}

?>