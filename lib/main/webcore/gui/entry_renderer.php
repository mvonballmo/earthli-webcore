<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('webcore/gui/content_object_renderer.php');

/**
 * Render details for {@link ENTRY} objects.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.6.0
 */
class ENTRY_RENDERER extends CONTENT_OBJECT_RENDERER
{
  /**
   * @param ENTRY $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display($obj, $options = null)
  {
    switch ($this->handler_type)
    {
      case Handler_source_renderer:
        $this->display_as_source ($obj, $options);
        break;
      default:
        parent::display($obj, $options);
    }
  }

  /**
   * Outputs the object as HTML.
   * @param ENTRY $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $this->_echo_html_user_information ($obj);
    $this->_echo_html_description ($obj);
  }

  /**
   * Outputs the object as content source (e.g. munger text).
   * @param ENTRY $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display_as_source ($obj, $options = null)
  {
    if (isset ($options))
    {
      $this->_options = $options;
    }
    $this->_display_as_source ($obj);
  }

  /**
   * Outputs the object as content source (e.g. munger text).
   *
   * @param ENTRY $obj
   * @access private
   * @abstract
   */
  protected function _display_as_source ($obj)
  {
    include_once('webcore/util/munger_highlighter.php');
    $highlighter = new MUNGER_HIGHLIGHTER();

    echo '<h1>Title</h1>';
    echo '<p class="source">' . $highlighter->highlight($obj->title) . '</p>';
    echo '<h1>Description</h1>';
    echo '<div class="source">' . $highlighter->highlight($obj->description) . '</div>';
  }
}

/**
 * Render details for {@link DRAFTABLE_ENTRY} objects.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
 */
class DRAFTABLE_ENTRY_RENDERER extends ENTRY_RENDERER
{
  /**
   * Shows creator/modifier in HTML.
   * @param DRAFTABLE_ENTRY $entry
   * @access private
   */
  protected function _echo_html_users ($entry)
  {
    if ($entry->time_published->is_valid ())
    {
      $this->_echo_html_user ('Published', $entry->publisher (), $entry->time_published);
      if ($entry->modified ())
      {
        $this->_echo_html_user ('Updated', $entry->modifier (), $entry->time_modified);
      }
    }
    else
    {
      parent::_echo_html_users ($entry);
    }
  }

  /**
   * Show created/updated information in plain text.
   * @param DRAFTABLE_ENTRY $entry
   * @access private
   */
  protected function _echo_plain_text_users ($entry)
  {
    if ($entry->time_published->is_valid ())
    {
      $this->_echo_plain_text_user ('Published', $entry->publisher (), $entry->time_published);
      if ($entry->modified ())
      {
        $this->_echo_plain_text_user ('Updated', $entry->modifier (), $entry->time_modified);
      }
    }
    else
    {
      parent::_echo_plain_text_users ($entry);
    }
  }
}

/**
 * Render {@link COMMENT}s and {@link ATTACHMENT}s for an {@link ENTRY}.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.7.0
 */
class ENTRY_ASSOCIATED_DATA_RENDERER extends HANDLER_RENDERER
{
  /**
   * Draws {@link COMMENT}s and {@link ATTACHMENT}s.
   * @param ENTRY $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $folder = $obj->parent_folder ();
    if ($this->login->is_allowed (Privilege_set_attachment, Privilege_view, $folder))
    {
      $attachment_query = $obj->attachment_query ();
      if ($attachment_query->size ())
      {
  ?>
  <h2 id="attachments">
    Attachments
  </h2>
  <div class="grid-content">
  <?php
    $class_name = $this->app->final_class_name ('ATTACHMENT_GRID', 'webcore/gui/attachment_grid.php');
    /** @var $grid ATTACHMENT_GRID */
    $grid = new $class_name ($this->app);
    $grid->set_ranges (3, 3);
    $grid->pager->page_anchor = 'attachments';
    $grid->pager->page_number_var_name = 'attachment_page_number';
    $grid->set_query ($attachment_query);
    $grid->display ();
  ?>
  </div>
  <?php
      }
    }

    if ($this->login->is_allowed (Privilege_set_comment, Privilege_view, $folder))
    {
      $com_query = $obj->comment_query ();

      if ($com_query->size ())
      {
        $class_name = $this->app->final_class_name ('COMMENT_LIST_RENDERER', 'webcore/gui/comment_renderer.php');
        /** @var $com_renderer COMMENT_LIST_RENDERER */
        $com_renderer = new $class_name ($com_query, $obj);
      ?>
      <h2 id="comments" class="clear-both">
        Comments
      </h2>
      <?php
        $commands = $com_renderer->make_commands();
        if ($commands->num_executable_commands() > 0)
        {
          ?>
          <div class="button-content">
            <span class="field"><?php echo $com_renderer->size(); ?></span> Replies
            <?php
            $menu_renderer = $this->app->make_menu_renderer();
            $menu_renderer->content_mode = Menu_show_as_buttons;
            $menu_renderer->display ($commands);
            ?>
          </div>
      <?php
        }
      ?>
      <div class="grid-content">
        <?php $com_renderer->display (); ?>
      </div>
      <?php
      }
    }
  }
}

?>