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
require_once('webcore/gui/printable_comment_grid.php');

/**
 * Displays {@link COMMENT}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.2.1
 */
class FLAT_COMMENT_GRID extends PRINTABLE_COMMENT_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   * @param COMMENT $comment Comments belong to this comment (can be empty).
   */
  public function __construct($context, $comment)
  {
    parent::__construct($context, $comment);

    $this->show_pager = true;
  }

  /**
   * @param COMMENT $obj
   * @access private
   */
  protected function _draw_box($obj)
  {
    $this->_draw_comment_contents($obj);
  }

  /**
   * Draws a single comment.
   * Allows descendants to use this single method for rendering the comment.
   * @param COMMENT $obj
   * @access private
   */
  protected function _draw_comment_contents($obj)
  {
    $this->_display_start_minimal_commands_block($obj);
    ?>
    <h3><?php echo $obj->title_as_link(); ?></h3>
    <div class="info-box-top">
    <?php
    $props = $obj->icon_properties();
    $this->context->start_icon_container($props->icon, Fifteen_px);
    if ($this->show_user_info)
    {
      $creator = $obj->creator();
      if ($creator->icon_url)
      {
        $this->context->start_icon_container($creator->icon_url, Sixteen_px);
      }
      ?>
        <?php
        echo $creator->title_as_link() . ' &ndash; ' . $obj->time_created->format();

        if ($obj->modified())
        {
          $modifier = $obj->modifier();
          ?>
          (updated by <?php echo $modifier->title_as_link(); ?> &ndash; <?php echo $obj->time_modified->format(); ?>)
        <?php
        }
        ?>
      </div>
      <?php
      if ($creator->icon_url)
      {
        $this->context->finish_icon_container();
      }
    }
    $this->context->finish_icon_container();
    ?>
    <div class="text-flow">
      <?php echo $obj->description_as_html(); ?>
    </div>
    <?php
    $attachment_query = $obj->attachment_query();

    $num_attachments = $attachment_query->size();
    if ($num_attachments)
    {
      $class_name = $this->app->final_class_name('ATTACHMENT_GRID', 'webcore/gui/attachment_grid.php');
      /** @var $grid ATTACHMENT_GRID */
      $grid = new $class_name ($this->app);
      $grid->set_page_size (Default_page_size);
      $grid->set_query($attachment_query);
      $grid->display();
    }
    ?>
    <?php
    $this->_display_finish_minimal_commands_block();
  }

  /**
   * Get the list of objects for the requested page.
   * @return COMMENT[]
   * @access private
   */
  protected function _get_objects()
  {
    // if printing, then don't paginate, return all objects

    if ($this->show_pager)
    {
      return $this->_query->objects();
    }
    else
    {
      if ($this->_comment)
      {
        // return only the comments attached to this comment

        $this->_query->restrict('(com.parent_id = ' . $this->_comment->id . ' OR com.id = ' . $this->_comment->id . ')');

        return parent::_get_objects($this->_query);
      }

      return parent::_get_objects($this->_query);
    }
  }
}

?>
