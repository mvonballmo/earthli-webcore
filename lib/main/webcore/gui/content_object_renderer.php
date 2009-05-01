<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.1.0
 * @since 2.5.0
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
require_once ('webcore/gui/auditable_renderer.php');

/**
 * Render details for {@link CONTENT_OBJECT} objects.
 * @package webcore
 * @subpackage renderer
 * @version 3.1.0
 * @since 2.5.0
 */
class CONTENT_OBJECT_RENDERER extends AUDITABLE_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    parent::_display_as_html ($obj);
    $this->_echo_html_description ($obj);
  }

  /**
   * Emits the "description" field as HTML.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _echo_html_description ($obj)
  {
    $this->_echo_text_as_html ($obj, $obj->description);
  }

  /**
   * Outputs the object as plain text.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    parent::_display_as_plain_text ($obj);
    $this->_echo_plain_text_description ($obj);
  }

  /**
   * Emits the "description" field as plain text.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _echo_plain_text_description ($obj)
  {
    $this->_echo_text_as_plain_text ($obj, $obj->description);
  }

  /**
   * Emit a piece of text as plain text.
   * Used standard formatting provided by the {@link NAMED_OBJECT::html_formatter()}
   * and settings from {@link _prepare_formatter()}.
   * 
   * @param NAMED_OBJECT $obj
   * @param string $text
   * @private
   */
  protected function _echo_text_as_html ($obj, $text)
  {
    if ($text)
    {
      $munger = $obj->html_formatter ();
      $this->_prepare_formatter ($munger);
  ?>
      <div class="text-flow">
  <?php
      echo $munger->transform ($text, $obj);
  ?>
      </div>
  <?php
    }
  }

  /**
   * Emit a piece of text as plain text.
   * Used standard formatting provided by the {@link NAMED_OBJECT::plain_text_formatter()}
   * and settings from {@link _prepare_formatter()}.
   * 
   * @param NAMED_OBJECT $obj
   * @param string $text
   * @private
   */
  protected function _echo_text_as_plain_text ($obj, $text)
  {
    if ($text)
    {
      $munger = $obj->plain_text_formatter ();
      $this->_prepare_formatter ($munger);
      $munger->right_margin = $this->_options->right_margin;
      echo $this->line ($munger->transform ($text, $obj));
    }
  }

  /**
   * Apply default formatting properties.
   * Used by both the HTML and plain text formatters.
   * 
   * @param MUNGER $munger
   * @access private
   */
  protected function _prepare_formatter ($munger)
  {
    $munger->max_visible_output_chars = $this->_options->preferred_text_length;
    $munger->force_paragraphs = true;
  }
}

?>