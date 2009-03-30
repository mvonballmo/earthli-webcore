<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/gui/object_renderer.php');

/**
 * Show no comments in print preview.
 */
define ('Print_comments_off', 'no_comments');
/**
 * Show no comments in threaded mode in print preview.
 */
define ('Print_comments_threaded', 'threaded_comments');
/**
 * Show no comments in flat mode in print preview.
 */
define ('Print_comments_flat', 'flat_comments');

/**
 * Knows how to draw an {@link ENTRY}.
 * Descendants specialize the rendering to show new fields.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class ENTRY_PRINT_RENDERER extends WEBCORE_OBJECT
{
  /**
   * Reference to the containing print preview object.
   * @var PRINT_PREVIEW
   * @access private
   */
  public $preview;

  /**
   * @param PRINT_PREVIEW $preview Render an entry for this print preview.
   */
  public function ENTRY_PRINT_RENDERER ($preview)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($preview->app);
    $this->preview = $preview;
  }

  /**
   * Render the given object to the print preview.
   * @param ENTRY $entry
   * @access private
   */
  public function draw ($entry)
  {
    $this->_draw_object ($entry);
    $this->_draw_comments ($entry);
  }

  /**
   * Draw the object itself.
   * Called from {@link draw()}
   * @param ENTRY $entry
   * @access private
   */
  protected function _draw_object ($entry)
  {
    $this->_draw_title ($entry);

    $renderer = $entry->handler_for (Handler_print_renderer, $this->preview->options);
    $renderer->display ($entry);
  }

  /**
   * Draw the comments for the object.
   * Called from {@link draw()}.
   * @param ENTRY $entry
   * @access private
   */
  protected function _draw_comments ($entry)
  {
    if ($this->preview->options->show_comments != Print_comments_off)
    {
      $com_query = $entry->comment_query ();
      $num_comments = $com_query->size ();

      if ($num_comments)
      {
        $this->_draw_comments_start ($num_comments);
        $this->_draw_comments_body ($entry, $com_query);
        $this->_draw_comments_finish ($num_comments);
      }
    }
  }

  /**
   * Called from '_draw_comments' before drawing any comments.
   * @see ENTRY_PRINT_RENDERER::_draw_comments()
   * @param integer $num_comments
   * @access private
   */
  protected function _draw_comments_start ($num_comments)
  {
?>
<h2><?php echo $num_comments; ?> Comments</h2>
<div>&nbsp;</div>
<?php
  }

  /**
   * Draw the comments for this entry.
   * @see ENTRY_PRINT_RENDERER::_draw_comments()
   * @param ENTRY $entry
   * @param ENTRY_COMMENT_QUERY $com_query
   * @access private
   */
  protected function _draw_comments_body ($entry, $com_query)
  {
    if (! isset ($this->grid))
    {
      $no_comment = null;
      if ($this->preview->options->show_comments == Print_comments_flat)
      {
        include_once ('webcore/gui/flat_comment_grid.php');
        $this->grid = new FLAT_COMMENT_GRID ($this->app, $no_comment);
      }
      else
      {
        include_once ('webcore/gui/threaded_comment_grid.php');
        $this->grid = new THREADED_COMMENT_GRID ($this->app, $no_comment);
      }

      $this->grid->show_user_info = $this->preview->options->show_users;
      $this->grid->show_paginated = false;  // print all comments
      $this->grid->show_controls = false;    // don't show modification controls
      $this->grid->set_ranges (20, 1);  // ranges irrevelevant because pagination is off
    }

    $this->grid->set_query ($com_query);
    $this->grid->display ();
  }

  /**
   * Called from '_draw_comments' after drawing all comments.
   * @see ENTRY_PRINT_RENDERER::_draw_comments()
   * @param integer $num_comments
   * @access private
   */
  protected function _draw_comments_finish ($num_comments) {}

  /**
   * Draw the title for the object.
   * Object renderers don't draw the title, so the print preview does that.
   * @param ENTRY $entry
   */
  protected function _draw_title ($entry)
  {
?>
  <h2><?php echo $entry->title_as_link (); ?></h2>
<?php
  }
}

/**
 * Handles displaying lists of {@link ENTRY}s for print preview.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.2.1
 */
class PRINT_PREVIEW extends WEBCORE_OBJECT
{
  /**
   * Printing options, specified by the user.
   * @var PRINT_RENDERER_OPTIONS
   */
  public $options;

  public function PRINT_PREVIEW ($app)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($app);

    $this->options = $this->_make_print_options ();
    $this->options->load_from_request ();
  }

  /**
   * Render the print preview.
   * @param array[ENTRY] $entries
   */
  public function display ($entries)
  {
    $dhtml_opt = $this->app->display_options->use_DHTML;
    $this->app->display_options->use_DHTML = false;

    $i = 0;
    $c = sizeof ($entries);

    if ($c)
    {
      $this->_draw_start ();

      while ($i < $c)
      {
        $entry = $entries [$i];
        $this->draw_object ($entry);
        $i++;

        if ($i <= $c - 1)
        {
          $this->_draw_separator ();
        }
      }

      $this->_draw_finish ();
    }

    $this->app->display_options->use_DHTML = $dhtml_opt;
  }

  /**
   * Draw this entry (called from 'display')
   * @see PRINT_PREVIEW::display()
   * @param ENTRY $entry
   * @access private
   */
  public function draw_object ($entry)
  {
    $renderer = $this->_renderer_for ($entry);
    $renderer->draw ($entry);
  }

  /**
   * Horizontal separator between items
   * @access private
   */
  protected function _draw_separator ()
  {
?>
<p>&nbsp;</p>
<div class="horizontal-separator" style="clear: both"></div>
<p>&nbsp;</p>
<?php
  }

  /**
   * Start drawing the print preview.
   * @access private
   */
  protected function _draw_start ()
  {
    $this->app->display_options->overridden_max_title_size = 150;
  }

  /**
   * Finish drawing the print preview.
   * @access private
   */
  protected function _draw_finish ()
  {
?>
<div>&nbsp;</div>
<?php
  }

  /**
   * Get a renderer from cache, if possible.
   * @param ENTRY $entry
   * @return ENTRY_PRINT_RENDERER
   * @access private
   */
  protected function _renderer_for ($entry)
  {
    $class_name = strtoupper (get_class ($entry));

    if (isset($this->_renderers [$class_name]))
    {
      return $this->_renderers [$class_name];
    }
    
    return $this->_make_renderer ($class_name);
  }

  /**
   * @return PRINT_RENDERER_OPTIONS
   * @access private
   */
  protected function _make_print_options ()
  {
    return new PRINT_RENDERER_OPTIONS ($this->app);
  }

  /**
   * Make a new renderer for this class.
   * @param string $class_name
   * @return ENTRY_PRINT_RENDERER
   * @access private
   */
  protected function _make_renderer ($class_name)
  {
    return new ENTRY_PRINT_RENDERER ($this);
  }
}

/**
 * Printing options for {@link ENTRY} objects in a {@link FOLDER}.
 * @package webcore
 * @subpackage gui
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PRINT_RENDERER_OPTIONS extends OBJECT_RENDERER_OPTIONS
{
  /**
   * Show comments with {ENTRY} objects?
   * Can be {@link Print_comments_off}, {@link Print_comments_threaded} or {@link Print_comments_flat}.
   * @var string
   */
  public $show_comments = Print_comments_off;

  /**
   * Load values from the HTTP request.
   */
  public function load_from_request ()
  {
    parent::load_from_request ();
    $this->show_comments = read_var ('show_comments');
  }
}

?>