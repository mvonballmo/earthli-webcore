<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
 * @since 2.7.0
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
require_once ('webcore/gui/selectable_grid.php');

/**
 * Displays {@link CONTENT_OBJECT}s from a {@link QUERY}.
 * Shows associated folder and creator information.
 * @package webcore
 * @subpackage grid
 * @version 3.2.0
 * @since 2.7.0*/
abstract class CONTENT_OBJECT_GRID extends SELECTABLE_GRID
{
  /**
   * Used to draw the entry's title in each cell.
   * @param CONTENT_OBJECT $obj
   * @return TITLE_FORMATTER
   * @access private
   */
  public function title_formatter ($obj)
  {
    $Result = $obj->title_formatter ();
    $Result->max_visible_output_chars = 0;

    if (! empty ($this->app->search_text))
    {
      $Result->add_argument ('search_text', $this->app->search_text);
    }

    return $Result;
  }

  /**
   * Format the link to include search arguments..
   * @see title_formatter()
   * @param CONTENT_OBJECT $obj
   * @return string
   * @access private
   */
  public function obj_link ($obj)
  {
    $t = $this->title_formatter ($obj);
    return $obj->title_as_link ($t);
  }

  /**
   * Return the block of text to summarize.
   * Search words and their contexts are extracted and highlighted using a
   * {@link MUNGER_SUMMARIZER}.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _echo_text_summary ($obj)
  {
    if ($this->app->search_text)
    {
      $class_name = $this->app->final_class_name ('MUNGER_SUMMARIZER', 'webcore/util/munger_summarizer.php');
      $summarizer = new $class_name ();
      $summarizer->max_visible_output_chars = 500;
      echo '<p>';    
      echo $summarizer->transform ($this->_text_to_summarize ($obj), $this->app->search_text);
      echo '</p>';
    }    
  }
  
  /**
   * Return the block of text to summarize.
   * Called from {@link _echo_text_summary()}. Override in descendents to return
   * more text.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _text_to_summarize ($obj)
  {
    return $obj->description;
  }
}

?>