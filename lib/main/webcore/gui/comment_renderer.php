<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for a {@link COMMENT}.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */
class COMMENT_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * Outputs the comment as HTML.
   * @param COMMENT $obj
   * @access private
   */
  function _display_as_html ($obj)
  {
    $creator = $obj->creator ();
?>
    <div class="info-box-top">
      <div style="float: left; margin-right: .5em">
        <?php echo $obj->icon (); ?>
      </div>
      <div style="float: left">
        <div class="detail">
          Created by <?php echo $creator->title_as_link (); ?> - <?php echo $obj->time_created->format (); ?>
        </div>
      </div>
      <div style="clear: both"></div>
    </div>
    <div class="description">
<?php
      $this->_echo_subscribe_status ($obj);
      echo $obj->description_as_html ();
?>
    </div>
<?php
    if ($obj->modified ())
    {
      $modifier = $obj->modifier ();
?>
    <p class="detail" style="text-align: right">Updated by <?php echo $modifier->title_as_link (); ?> - <?php echo $obj->time_modified->format (); ?></p>
<?php
    }
  }

  /**
   * Outputs the comment as plain text.
   * @param COMMENT $obj
   * @access private
   */
  function _display_as_plain_text ($obj)
  {
    $this->_echo_plain_text_description ($obj);
    $this->_echo_plain_text_user_information ($obj, FALSE);
  }

  /**
   * Shows the subscription status for this object.
   * @param COMMENT $obj
   * @access private
   */
  function _echo_subscribe_status ($obj)
  {
    $this->_echo_html_subscribed_toggle ($obj, 'subscribe_to_comment.php?id=' . $obj->id, Subscribe_comment);
  }
}

/**
 * Show comments in a flat list.
 */
define ('Comment_render_flat', 'flat');
/**
 * Show comments in a threaded list.
  * This nests comments within each other, according to attachment. All 'child' comments appear
  * under their 'parent', following creation order as much as possible.
  */
define ('Comment_render_threaded', 'threaded');

/**
 * Displays a tree of {@link COMMENT}s in flat or threaded mode.
 * Handles setup and display of a menu for switching modes and the display of the list or tree itself.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.5.0
 */
class COMMENT_LIST_RENDERER extends WEBCORE_OBJECT
{
  /**
   * @param QUERY $com_query Show comments from this query.
    * @param ENTRY|COMMENT $obj Entry or comment on whose page to render comments.
    * @param string $default_mode Default mode for comment display (Can be 'Comment_render_flat' or 'Comment_render_threaded').
    */
  function COMMENT_LIST_RENDERER ($com_query, $obj, $default_mode = Comment_render_threaded)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($obj->app);

    $this->_comment_query = $com_query;
    $this->_obj = $obj;
    if (is_a ($this->_obj, 'COMMENT'))
    {
      $this->_comment = $this->_obj;
    }

    $this->comment_mode = read_var ('comment_mode', $default_mode);
  }

  /**
   * Render the menu selector for switching modes.
   */
  function display_menu ()
  {
    if ($this->_comment_query->size () > 1)
    {
?>
  <div class="menu-bar-top">
<?php
      $menu = $this->context->make_menu ();
      $menu->renderer->alignment = Menu_align_default;
  
      switch ($this->comment_mode)
      {
      case Comment_render_flat:
        $menu->append ('Show Threaded', $this->_obj->home_page () . "&comment_mode=threaded#comments");
        $menu->append ('Show Flat', '', '', TRUE);
        break;
      case Comment_render_threaded:
        $menu->append ('Show Threaded', '', '', TRUE);
        $menu->append ('Show Flat', $this->_obj->home_page () . "&comment_mode=flat#comments");
        break;
      }
      
      $menu->display ();
?>
    <span class="field"><?php echo $this->_comment_query->size (); ?></span> Replies
  </div>
<?php
    }    
  }

  /**
   * Render the selected comments in the chosen mode.
   */
  function display ()
  {
    switch ($this->comment_mode)
    {
    case Comment_render_flat:
      $class_name = $this->context->final_class_name ('FLAT_COMMENT_GRID', 'webcore/gui/flat_comment_grid.php');
      $grid = new $class_name ($this->app, $this->_comment);
      $grid->set_ranges (20, 1);
      break;
    case Comment_render_threaded:
      $class_name = $this->context->final_class_name ('THREADED_COMMENT_GRID', 'webcore/gui/threaded_comment_grid.php');
      $grid = new $class_name ($this->app, $this->_comment);
      $grid->set_ranges (10, 1);
      break;
    }

    $grid->paginator->page_anchor = 'comments';
    $grid->paginator->page_number_var_name = 'comment_page_number';
    $grid->set_query ($this->_comment_query);
    $grid->display ();
  }

  /**
   * @var ENTRY|COMMENT
    * @access private
    */
  protected $_obj;
  /**
   * @var COMMENT
    * @access private
    */
  protected $_comment;
  /**
   * @var QUERY
    * @access private
    */
  protected $_comment_query;
}
?>