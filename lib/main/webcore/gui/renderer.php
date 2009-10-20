<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.2.0
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Rendering functions for text and HTML.
 * @package webcore
 * @subpackage renderer
 * @version 3.2.0
 * @since 2.5.0
 * @abstract
 */
class RENDERER extends WEBCORE_OBJECT
{
  /**
   * Returns a plain text line.
   * There is a newline at the end, but no extra spacing as in {@link _par()}.
   * @param string $text
   * @return string
   */
  public function line ($text = '')
  {
    return $text . $this->env->file_options->end_of_line;
  }

  /**
   * Returns a plain text paragraph.
   * Makes space at the bottom of the text.
   * @param string $text
   * @return string
   */
  public function par ($text = '')
  {
    return $text . $this->env->file_options->end_of_line . $this->env->file_options->end_of_line;
  }

  /**
   * Return a plain text separator.
   * Returns 'len' copies of 'char' as a line.
   * @param string $char
   * @param integer $len
   * @return string
   */
  public function sep ($char = '-', $len = 72)
  {
    return $this->line (str_repeat ($char, $len));
  }

  /**
   * Format a {@link DATE_TIME} as plain text.
   * @param DATE_TIME $t
   * @param integer $type Override the default formatting type.
   * @return string
   */
  public function time ($t, $type = null)
  {
    $f = $t->formatter ();
    $f->clear_flags ();
    if (isset ($type))
    {
      $f->type = $type;
    }
    return $t->format ($f);
  }
}

/**
 * Renders plain text labelled lists (tables).
 * Manages two-column tables with a label on the left and content on the right. Labels can
 * be {@link $right_aligned} or left-aligned. Use {@link add_item()} to add rows, then call
 * {@link display()} to write out the table.
 * @package webcore
 * @subpackage renderer
 * @version 3.2.0
 * @since 2.5.0
 * @access private
 */
class TEXT_TABLE_RENDERER
{
  /**
   * If true, labels are aligned to the right.
   * @var @boolean
   */
  public $right_aligned = true;

  /**
   * @param RENDERER $renderer Use this renderer to display the finished table.
   */
  public function __construct ($renderer)
  {
    $this->_renderer = $renderer;
  }

  /**
   * Add a row to the table.
   * @param string $label
   * @param string $content
   */
  public function add_item ($label, $content)
  {
    $item = null; // Compiler warning
    $item->label = $label;
    $item->content = $content;
    $this->_items [] = $item;
    $this->_max_label_length = max (strlen ($label), $this->_max_label_length);
  }

  /**
   * Add an empty row.
   */
  public function add_separator ()
  {
    $this->add_item ('', '');
  }

  /**
   * Show the finished table.
   */
  public function display ()
  {
    foreach ($this->_items as $item)
    {
      $label = $item->label;
      $content = $item->content;

      if ($label || $content)
      {
        $padding = str_repeat (' ', $this->_max_label_length - strlen ($label));
        if ($this->right_aligned)
        {
          $text = $padding . '[' . $label . ']: ' . $content;
        }
        else
        {
          $text = "[$label]: $padding$content";
        }
      }
      else
      {
        $text = '';
      }

      echo $this->_renderer->_line ($text);
    }
  }

  /**
   * Records the biggest label added to the table.
   * @var integer
   * @access private
   */
  protected $_max_label_length = 0;

  /**
   * List of label/column pairs in the table.
   * @var array[string][string]
   * @access private
   */
  protected $_items;
}

?>