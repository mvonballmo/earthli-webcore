<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.1.0
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

/***/
require_once ('webcore/util/plain_text_munger.php');

/**
 * Strips all tags and returns raw plain text.
 * Used by the {@link MUNGER_SUMMARIZER} to generate an initial text.
 * @package webcore
 * @subpackage text
 * @version 3.1.0
 * @since 2.4.0
 */
class TEXT_STRIPPER extends TEXT_MUNGER
{
  /**
   * Return, at most, this many visible characters from a transformation.
   * @var integer*/
  public $max_visible_output_chars = 0;

  /**
   * Allow break inside a word when truncating to {@link $max_visible_output_chars}?
   * @var boolean
   */
  public $break_inside_word = false;

  /**
   * Take out all tags from the input?
   * @var boolean
   */
  public $strip_unknown_tags = true;

  /**
   * Force paragraphs on all transformed text?
   * @var boolean
   */
  public $force_paragraphs = true;
  
  public function TEXT_STRIPPER ()
  {
    TEXT_MUNGER::TEXT_MUNGER ();
    $this->_default_transformer = new MUNGER_NOP_TRANSFORMER ($this);
  }
}

/**
 * Highlights search text in a block of text.
 * Creates a search engine-like summary of a block of text and a list of search
 * words. All tags and formatting a stripped and the search words are
 * highlighted in areas whose size is determined by {@link $context_size}. Use
 * {@link $max_visible_output_chars} to limit the amount of text returned
 * (though this limit is applied to the finished text instead of aborting
 * parsing when the limit is reached). Use {@link $break_inside_word} to control
 * whether the summary blocks have entire words.
 * @package webcore
 * @subpackage text
 * @version 3.1.0
 * @since 2.7.0
 */
class MUNGER_SUMMARIZER
{
  /**
   * Number of characters to return on each side of a term.
   * @var integer
   */
  public $context_size = 20;

  /**
   * Return, at most, this many visible characters from a transformation.
   * @var integer*/
  public $max_visible_output_chars = 0;

  /**
   * Allow break inside a word when truncating to {@link $max_visible_output_chars}?
   * @var boolean
   */
  public $break_inside_word = false;

  /**
   * Open each highlighted section with this text.
   * @var string
   */
  public $highlight_prefix = '<span class="highlight">';

  /**
   * Close each highlighted section with this text.
   * @var string
   */
  public $highlight_suffix = '</span>';
  
  public function transform ($text, $phrase)
  {
    if (! isset ($this->_stripper))
    {
      $this->_stripper = new TEXT_STRIPPER ();
    }
    if (! isset ($this->_reg_exp))
    {
      $this->_reg_exp = new REGULAR_EXPRESSION ();
    }
    $Result = $this->_stripper->transform ($text);
    $Result = $this->_reg_exp->extract_words ($Result, $phrase, $this->context_size, $this->max_visible_output_chars, $this->break_inside_word);
    $Result = $this->_reg_exp->select_words ($Result, $phrase, $this->highlight_prefix, $this->highlight_suffix);
    return $Result; 
  }
  
  /**
   * @var TEXT_STRIPPER 
   * @access private
   */
  protected $_stripper;

  /**
   * @var REGULAR_EXPRESSION
   * @access private
   */
  protected $_reg_exp;
}

?>