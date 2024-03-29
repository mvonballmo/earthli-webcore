<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
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

/***/
require_once ('webcore/util/munger.php');
require_once ('webcore/util/tags.php');

/**
 * Generates HTML paragraphs and line-breaks from text.
 * This assumes that the container for this text is an HTML block element.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class HTML_BLOCK_TRANSFORMER extends MUNGER_BLOCK_TRANSFORMER
{
  /**
   * Should every newline be used?
   * Since the default formatter does not have start and end tags, it also does not ignore
   * the first and last newlines in the text.
   * @var boolean
   */
  public $strict_newlines = false;

  /**
   * Transform given newlines to HTML boxes.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_transform ($munger, $text)
  {
    if (strpos ($text, "\n") !== false)
    {
      $text = $this->_trim ($text);

      if ($text)
      {
        $Result = str_replace ("\n\n", "</p><p>", $text);
        $Result = str_replace ("\n", "<br>\n", $Result);
        $Result = str_replace ("</p><p>", "</p>\n<p>", $Result);
        $Result = '<p>' . $Result . "</p>\n";
        $Result = str_replace ("<p></p>", "<p>&nbsp;</p>", $Result);
        $Result = str_replace ("<br>\n</p>", "<br>\n&nbsp;</p>", $Result);
      }
      else
      {
        $Result = $text;
      }
    }
    else
    {
      $Result = $this->_apply_simple_transform ($munger, $text);
    }

    return $Result;
  }

  /**
   * Remove newlines according to tagging rules.
   * This layouter does its best to generate conforming text. It tracks the state of the buffer
   * within this block and applies formatting for newlines appropriately. Text is treated differently
   * depending on which other structures the block contains. If the text is a single line in the block,
   * then paragraph tags are only generated if desired (force_paragraphs is on). If the text is the first
   * in a series of text and nested blocks, it formats its newlines differently and will generate paragraph
   * tags.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _trim ($text)
  {
    $len = strlen ($text);
    $first_char = 0;
    $new_len = $len;

    switch ($this->_buffer_state)
    {
    case Munger_first_data_block:
    case Munger_only_data_block:
      if (! $this->strict_newlines && ($len > 0) && ($text [0] == "\n"))
      {
        $first_char = 1;
      }
      break;

    case Munger_middle_data_block:
    case Munger_last_data_block:
      if (($len > 0) && ($text [0] == "\n"))
      {
        $first_char = 1;
        if (($len > 1) && ($text [1] == "\n"))
        {
          $first_char = 2;
        }
      }
      break;
    }

    switch ($this->_buffer_state)
    {
    case Munger_first_data_block:
    case Munger_middle_data_block:
      if (($len > 0) && ($text [$len - 1] == "\n"))
      {
        $new_len = -1;
        if (($len > 1) && ($text [$len - 2] == "\n"))
        {
          $new_len = -2;
        }
      }
      break;

    case Munger_only_data_block:
    case Munger_last_data_block:
      if (! $this->strict_newlines && ($len > 0) && ($text [$len - 1] == "\n"))
      {
        $new_len = -1;
      }
      break;
    }

    if (($first_char > 0) || ($new_len != $len))
    {
      $text = substr ($text, $first_char, $new_len);
    }

    return $text;
  }

  /**
   * Transform text without newlines.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_simple_transform ($munger, $text)
  {
    return $text;
  }
}

/**
 * Surrounds paragraphed text with quotes.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class HTML_QUOTE_TRANSFORMER extends HTML_BLOCK_TRANSFORMER
{
  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The transformation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    parent::activate ($munger, $value, $token);
    if ($value)
    {
      $this->_quote_style = $this->_quote_style_from_token ($value, $token);
    }
  }

  /**
   * Transform given newlines to HTML boxes.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_transform ($munger, $text)
  {
    $text = $this->_apply_quotes ($text, $this->_quote_style, '&ldquo;', '&rdquo;');
    return parent::_apply_transform ($munger, $text);
  }

  /**
   * @var string
   * @access private
   */
  protected $_quote_style;
}

/**
 * Generates HTML paragraphs and line-breaks for text.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_PARAGRAPH_TRANSFORMER extends HTML_BLOCK_TRANSFORMER
{
  /**
   * Should every newline be used?
   * Since the default formatter does not have start and end tags, it also does not ignore
   * the first and last newlines in the text.
   * @var boolean
   */
  public $strict_newlines = true;

  /**
   * Transform text without newlines.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   */
  protected function _apply_simple_transform ($munger, $text)
  {
    if (($this->_buffer_state != Munger_only_data_block) || $munger->force_paragraphs)
    {
      return '<p>' . $text . "</p>\n";
    }

    return $text;
  }
}

/**
 * A block of items in an {@link HTML_LIST_TRANSFORMER}.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.6.0
 * @access private
 */
class HTML_LIST_TRANSFORMER_ITEM
{
  /**
   * Individual items.
   * @var string[]
   */
  public $items;

  /**
   * Is this list part of a previous list?
   * If True, it does not need to open an item tag.
   * @var boolean
   */
  public $was_open;

  /**
   * Is the last item in this list open?
   * If True, it does not need to close the last item tag.
   * @var boolean
   */
  public $is_open;
}

/**
 * Generates HTML list items for newlines in text.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_LIST_TRANSFORMER extends MUNGER_LIST_TRANSFORMER
{
  /**
   * Convert an {@link HTML_LIST_TRANSFORMER_ITEM} to text.
   * @param HTML_LIST_TRANSFORMER_ITEM $item
   * @return string
   * @access private
   */
  protected function _item_to_text ($item)
  {
    $Result = join ("</li>\n<li>", $item->items);
    if ($item->is_open && ! $item->was_open)
    {
      $Result = "\n<li>" . $Result;
    }
    elseif ($item->was_open && ! $item->is_open)
    {
      $Result = $Result . '</li>';
    }
    elseif (! $item->is_open)
    {
      $Result = "\n<li>" . $Result . '</li>';
    }
    return str_replace ("<li></li>", "<li>&nbsp;</li>", $Result);
  }

  /**
   * Create a {@link HTML_LIST_TRANSFORMER_ITEM} from text.
   * @param string $text
   * @param boolean $item_was_open
   * @return HTML_LIST_TRANSFORMER_ITEM
   * @access private
   */
  protected function _make_item ($text, $item_was_open)
  {
    $Result = new stdClass();
    $Result->items = explode ("\n", $text);
    
    foreach ($Result->items as &$item)
    {
      $item = ltrim ($item, " \t");
    }
    
    $Result->was_open = $item_was_open;
    $Result->is_open = $this->_item_is_open;
    
    return $Result;
  }

  /**
   * Transform preprocessed text into a list.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @param boolean $item_was_open
   * @return string
   * @access private
   */
  protected function _transform_to_list ($munger, $text, $item_was_open)
  {
    if ($this->_buffer_state == Munger_only_data_block)
    {
      $Result = $this->_item_to_text ($this->_make_item ($text, $item_was_open));
    }
    else
    {
      if (isset ($this->_last_item))
      {
        $Result = $this->_item_to_text ($this->_last_item);
      }
      else
      {
        $Result = '';
      }

      $item = $this->_make_item ($text, $item_was_open);
      if ($this->_buffer_state == Munger_last_data_block)
      {
        $Result .= $this->_item_to_text ($item);
      }
      else
      {
        $this->_last_item = $item;
      }
    }
    return $Result;
  }

  /**
   * Post-process text generated from another transformer.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string
   * @return string
   * @access private
   */
  protected function _transformer_text ($munger, $text)
  {
    $text = parent::_transformer_text ($munger, $text);
    if ($this->_last_item)
    {
      if (sizeof ($this->_last_item->items) > 1)
      {
        $last_item_text = array_pop ($this->_last_item->items);
        $Result = $this->_item_to_text ($this->_last_item);
        if ($Result && ! $this->_last_item->is_open && $this->_last_item->was_open)
        {
          $Result .= '<li>';
        }
      }
      else
      {
        $last_item_text = $this->_last_item->items [0];
        $Result = '';
      }

      $last_item_text = trim ($last_item_text);
      if ($last_item_text)
      {
        $last_item_text = '<div>' . $last_item_text . $text . '</div>';
      }
      else
      {
        $last_item_text = $text;
      }

      if (! $this->_last_item->was_open)
      {
        $last_item_text = "\n<li>$last_item_text";
      }
      if (! $this->_last_item->is_open)
      {
        $last_item_text .= '</li>';
      }

      $Result .= $last_item_text;

      $this->_last_item = null;
      return $Result;
    }

    return "\n<li>" . $text . "</li>";
  }

  /**
   * @access private
   * @var HTML_LIST_TRANSFORMER_ITEM
   */
  protected $_last_item = null;
}

/**
 * Generates an HTML definition list.
 * Generates alternating defition terms and definitions for newlines in the text.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class HTML_DEFINITION_LIST_TRANSFORMER extends MUNGER_DEFINITION_LIST_TRANSFORMER
{
  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The transformation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    parent::activate ($munger, $value, $token);
    $attrs = $token->attributes ();
    $this->_term_class = read_array_index ($attrs, 'dt_class');
    $this->_definition_class = read_array_index ($attrs, 'dd_class');
  }

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @return string
   * @access private
   */
  protected function _build_as_definition_term ($munger, $line)
  {
    if ($this->_term_class)
    {
      return "<dt class=\"$this->_term_class\">$line</dt>\n";
    }

    return "<dt>$line</dt>\n";
  }

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @return string
   * @access private
   */
  protected function _build_as_definition_body ($munger, $line)
  {
    if ($this->_definition_class)
    {
      return "<dd class=\"$this->_definition_class\">$line</dd>\n";
    }

    return "<dd>$line</dd>\n";
  }

  /**
   * @var string
   * @access private
   */
  protected $_term_class;

  /**
   * @var string
   * @access private
   */
  protected $_definition_class;
}

/**
 * Replace a tag, retaining the 'style' property.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.6.0
 * @since 2.5.0
 */
class HTML_BASIC_REPLACER extends MUNGER_BASIC_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    $Result = parent::transform ($munger, $token);

    if ($token->is_start_tag ())
    {
      $attrs = $token->attributes ();
      $style = read_array_index ($attrs, 'style');
      if ($style)
      {
        $Result = substr ($Result, 0, strlen ($Result) - 1) . ' style="' . $style . '">';
      }
    }

    return $Result;
  }
}

/**
 * Adds a link to a footnote, numbering automatically.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_FOOTNOTE_REFERENCE_REPLACER extends MUNGER_FOOTNOTE_REFERENCE_REPLACER
{
  /**
   * Format the reference to the given footnote number.
   *
   * @param MUNGER $munger The munger that generated the call; cannot be null.
   * @param MUNGER_TOKEN $token The token being processed; cannot be null.
   * @param MUNGER_FOOTNOTE_INFO $info The footnote to format; cannot be null.
   * @return string
   * @access private
   */
  protected function _format_reference ($munger, $token, $info)
  {
    return '<a href="' . $munger->resolve_url('#' . $info->name_to) . '" id="' . $info->name_from . '" class="footnote-number" title="Jump to footnote.">[' . $info->number . ']</a>';
  }
}

/**
 * Links a block of text to a previous footnote reference.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_FOOTNOTE_TEXT_REPLACER extends MUNGER_FOOTNOTE_TEXT_REPLACER
{
  /**
   * Format the text for the given footnote number.
   * 
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token The token that triggered the transformation.
   * @param MUNGER_FOOTNOTE_INFO $info
   * @return string
   * @access private
   */
  protected function _format_text ($munger, $token, $info)
  {
    if ($token->is_start_tag ())
    {
      return '<div class="footnote-reference"><span id="' . $info->name_to . '" class="footnote-number">[' . $info->number . ']</span> ';
    }

    return '<a href="#' . $info->name_from . '" class="footnote-return" title="Jump back to reference.">&#8617;</a></div>';
  }
}

/**
 * Replace punctuation constructs with HTML entities.
 * Replaces '---', '--', '1/2', '3/4', '1/4', '...', '(tm)', '(c)', '(r)', ' x
 * '.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.6.0
 * @since 2.7.0
 */
class HTML_PUNCTUATION_CONVERTER extends MUNGER_CONVERTER
{
  /**
   * Table of punctuation mappings to apply in {@link _convert()}
   * @var string[]
   */
  public $punctuation_table = array (
    '---' => '—',
    '--' => '–',
    '1/2' => '½',
    '1/4' => '¼',
    '3/4' => '¾',
    '...' => '…',
    '(tm)' => '™',
    '(c)' => '©',
    '(R)' => '®',
    ' x ' => ' × ',
    ' - ' => ' − ',
    '(-cmd)' => '⌘',    // Command-key icon for Apple
    '(-shift)' => '⇧',  // Shift-key icon
    '(-enter)' => '⏎',  // Enter-key icon
    '(-esc)' => '⎋',    // Esc-key icon
    '(-tab)' => '⇥',    // Tab-key icon
    '(-del)' => '⇥',    // Delete-key icon
    '(-opt)' => '⌥',    // Option-key icon
    '(-eject)' => '⏏'   // Eject-key icon
  );

  /**
   * Convert the text to an output format.
   * @param MUNGER $munger
   * @param string $text
   * @return string
   * @access private
   */
  protected function _convert ($munger, $text)
  {
    return strtr ($text, $this->punctuation_table);
  }
}

/**
 * Replace straight quotes with smart quotes.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.6.0
 * @since 2.7.1
 */
class HTML_SMART_QUOTE_CONVERTER extends MUNGER_CONVERTER
{
  /**
   * Characters considered to be white-space.
   * A quote after one of these characters should be a left quote.
   */
  public $white_space_chars = array (' ', "\n", "\t");

  /**
   * Characters that are followed by left quotes.
   * A quote after a non white-space character is converted to a right quote,
   * unless it is in the following array. The Unicode characters mentioned in
   * the sample at [http://www.pensee.com/dunham/smartQuotes.html] are commented
   * for now.
   * @var string[]
   */
  public $left_chars = array ('(', '[', '{', '<', '=', ';', '.', ',', '"', '\''/*0x00AB, 0x3008, 0x300A*/);

  public function reset()
  {
    parent::reset();
    $this->_last_char = null;
    $this->_double_quote_open = false;
    $this->_single_quote_open = false;
  }
  
  /**
   * Convert the text to an output format.
   * @param MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _convert ($munger, $text)
  {
    if ($text)
    {
      $Result = '';

      $last = $this->_last_char;
      if (empty ($last))
      {
        $last = substr($munger->current_raw_text (), -1);
        if ($last === false)
        {
          $last = null;
        }
      }

      /* break the string into pieces starting with a quote character,
       * with an optional, non-quote piece at the beginning.
       */
      $pattern = '/([^"\']+|["\'][^"\']*)/';
      $segments = null;
      preg_match_all ($pattern, $text, $segments);
      $segments = $segments [0];

      foreach ($segments as $segment)
      {
        $char = $segment [0];

        if (($char == "'") || ($char == '"'))
        {
          if (strlen($segment) > 1)
          {
            $next = $segment[1];
          }
          else
          {
            $next = null;
          }

          if (is_numeric($last) && is_numeric($next))
          {
            // 9'9 or 9"9
            $Result .= $char;
          }
          elseif (is_numeric($last) && ($char == '"') && !$this->_double_quote_open)
          {
            // 9"
            $Result .= $char;
          }
          elseif (is_numeric($last) && ($char == '\'') && !$this->_single_quote_open)
          {
            // 9'
            $Result .= $char;
          }
          elseif (! isset ($last) || ($last === '') || in_array ($last, $this->white_space_chars) || 
            ((in_array ($last, $this->left_chars) && (($char != '"') || !$this->_double_quote_open)) && (($char != '\'') || !$this->_single_quote_open)))
          {
            // Add left quote
            if ($char == '"')
            {
              $Result .= '&ldquo;';
              $this->_double_quote_open = true;
            }
            else
            {
              $Result .= '&lsquo;';
              $this->_single_quote_open = true;
            }
          }
          else
          {
            // Add right quote
            if ($char == '"')
            {
              $Result .= '&rdquo;';
              $this->_double_quote_open = false;
            }
            else
            {
              $Result .= '&rsquo;';
              $this->_single_quote_open = false;
            }
          }

          // Append everything but the first character
          $Result .= substr ($segment, 1);
        }
        else
        {
          $Result .= $segment;
        }

        $last = substr ($segment, -1);
        if ($last === false)
        {
          $last = null;
        }
      }

      $this->_last_char = $last;

      return $Result;
    }

    return $text;
  }

  /**
   * @var string
   * @access private
   */
  protected $_last_char = '';

  protected $_double_quote_open = false;

  protected $_single_quote_open = false;
}

/**
 * Replace ligatures with UNICODE characters.
 * Replaces 'ffi', 'ffl', 'ff', 'fi', 'fl'.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.6.0
 * @since 2.7.1
 */
class HTML_LIGATURE_CONVERTER extends MUNGER_CONVERTER
{
  /**
   * Table of punctuation mappings to apply in {@link _convert()}
   * @var string[]
   */
  public $punctuation_table = array ( 'ffi' => '&#xfb03;'
                                 , 'ffl' => '&#xfb04;'
                                 , 'ff' => '&#xfb00;'
                                 , 'fi' => '&#xfb01;'
                                 , 'fl' => '&#xfb02;'
                                 );

  /**
   * Convert the text to an output format.
   * @param MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _convert ($munger, $text)
  {
    return strtr ($text, $this->punctuation_table);
  }
}

/**
 * Highlights certain words
 * Replaces '--' with '&mdash';.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.6.0
 * @since 2.7.0
 */
class HTML_HIGHLIGHT_CONVERTER extends MUNGER_CONVERTER
{
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

  /**
   * Convert the text to an output format.
   * @param HTML_MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _convert ($munger, $text)
  {
    if ($munger->highlighted_words)
    {
      $text = REGULAR_EXPRESSION::select_words ($text, $munger->highlighted_words, $this->highlight_prefix, $this->highlight_suffix);
    }

    return $text;
  }
}

/**
 * General support for alignment, captions and authors.
 * Used by images, boxes, quotes and divs. See {@link _open_content_area()} and
 * {@link _close_content_area()} for more information.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_BASE_REPLACER extends MUNGER_REPLACER
{
  /**
   * The main tag to use, by default.
   * Used by {@link transform()} to provide default behavior.
   * @var string
   */
  public $main_tag = 'div';
  
  /**
   * The CSS class to use for the {@link $main_tag}.
   * 
   * @var string
   */
  public $main_css_class = '';

  /**
   * True if the main tag is a block element.
   * @var boolean
   */
  public $is_block = true;

  /**
   * True if the main tag has an end tag.
   * @var boolean
   */
  public $has_end_tag = true;

  /**
   * Default classes for the inner container.
   * @var string
   */
  public $css_classes = '';

  /**
   * Convert the given token to the output format.
   * @param HTML_MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $attributes = $token->attributes ();
      $Result = $this->_open_outer_area ($munger, $attributes, $this->is_block);
      if (! $this->has_end_tag)
      {
        $Result .= $this->_close_outer_area ($munger, $this->is_block);
      }
      
      return $Result;
    }

    return $this->_close_outer_area ($munger, $this->is_block);
  }

  /**
   * Renders open tag(s) for the container.
   * Usually called from {@link transform()}. Generates HTML tags for the
   * "author", "caption", "align", "width", "style" and "class" properties. This
   * may generate multiple containers to allow for proper placement of the
   * author and caption. In all cases, the "class", "style", "width" are applied
   * to the innermost container (in that order), while "align" is always applied
   * to the outermost container. Calls {@link _render_content()} to create the
   * content inside all of the extra containers.
   * @see _close_outer_area()
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes Attributes of a tag; retrieved from the
   * token.
   * @param boolean $is_block If true, uses DIV tags for extra containers;
   * otherwise, SPAN tags are used.
   * @return string
   * @access private
   */
  protected function _open_outer_area ($munger, $attributes, $is_block)
  {
    $outer_css = $munger->make_style_builder ();

    $alignment = read_array_index ($attributes, 'align');

    $clear_class = '';
    $clear = read_array_index ($attributes, 'clear');
    switch ($clear)
    {
      case 'left':
        if ($alignment == 'right-column')
        {
          $clear_class = 'clear-both';
        }
        else
        {
          $clear_class = 'clear-left';
        }
        break;
      case 'right':
        if ($alignment == 'left-column')
        {
          $clear_class = 'clear-both';
        }
        else
        {
          $clear_class = 'clear-right';
        }
        break;
      case 'both':
        $clear_class = 'clear-both';
        break;
      default:
        if ($alignment == 'left-column')
        {
          $clear_class = 'clear-left';
        }
        elseif ($alignment == 'right-column')
        {
          $clear_class = 'clear-right';
        }
        break;
    }

    $align_class = '';

    switch ($alignment)
    {
	    case 'left-column':
	    case 'left':
        $align_class = 'align-left';
	      break;
	    case 'right-column':
	    case 'right':
        $align_class = 'align-right';
	      break;
	    case 'center':
        $align_class = 'align-center';
	      break;
    }

    $inner_css = $munger->make_style_builder ();
    $inner_css->add_text (read_array_index ($attributes, 'style'));

    $inner_class = read_array_index ($attributes, 'class');
    if ($this->css_classes)
    {
      if ($inner_class)
      {
        $inner_class = $this->css_classes . ' ' . $inner_class;
      }
      else
      {
        $inner_class = $this->css_classes;
      }
    }

    $this->_caption = $this->_calculate_caption ($munger, $attributes);

    $width = $this->_calculate_width ($munger, $attributes);
    $outer_css->add_attribute ('width', $width);

    if ($this->_has_outer_area ())
    {
      if ($is_block)
      {
        $builder = $munger->make_tag_builder ('div');
      }
      else
      {
        $builder = $munger->make_tag_builder ('span');

        if ($width)
        {
          $inner_css->add_attribute ('width', $width);
          /* Use the table display style to prevent large captions from
           * making inline tags wider. Center-aligned tags already have
           * this style; it is invalid CSS to add it again.
           */
          if ($alignment != 'center')
          {
            $outer_css->add_attribute ('display', 'table');
          }
        }
      }

      $outer_class = '';
      if ($align_class)
      {
        $outer_class .= ' ' . $align_class;
      }

      if ($clear_class)
      {
        $outer_class .= ' ' . $clear_class;
      }

      $caption = $this->_get_caption ($is_block, true);
      $builder->add_attribute ('style', $outer_css->as_text ());
      $builder->add_attribute ('class', $outer_class);
      $outer_css->clear ();
      $inner = $this->_open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class);
      if ($is_block)
      {
        $tag = '<div class="auto-content-block">' . $caption;
      }
      else
      {
        $tag = '<span class="auto-content-inline">' . $caption;
      }

      $Result = $builder->as_html () . $tag . $inner;
    }
    else
    {
      if ($align_class)
      {
        $inner_class .= ' ' . $align_class;
      }

      if ($clear_class)
      {
        $inner_class .= ' ' . $clear_class;
      }

      $Result = $this->_open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class);
    }

    return $Result;
  }

  /**
   * Returns the caption for this element, formatted according to the given parameters.
   * @param boolean $is_block If true, the top of the container is being formatted.
   * @param $is_top
   * @return string
   * @access private
   */
	protected function _get_caption($is_block, $is_top) 
	{
	  switch ($this->_caption_position)
    {
	    case 'top':
        $caption_needed = $is_top;
        break; 
	    case 'both':
	    	$caption_needed = true;
	      break;
	    default:  
        $caption_needed = !$is_top;
        break;
    }
    
    if ($caption_needed)
    {
      if ($is_block)
      {
        return '<div class="auto-content-caption">' . $this->_caption . '</div>';
      }
      
      return '<span class="auto-content-caption">' . $this->_caption . '</span>';
    }

    return '';
	}

  /**
   * Renders close tag(s) for the container.
   * Calls {@link _close_inner_area()} to close all tag-specific tags, then
   * renders closing tags for the caption.
   * @see _open_outer_area()
   * @param MUNGER $munger The transformation context.
   * @param boolean $is_block If true, uses DIV tags for extra containers;
   * otherwise, SPAN tags are used.
   * @return string
   * @access private
   */
  protected function _close_outer_area ($munger, $is_block)
  {
    $Result = $this->_close_inner_area ($munger);

    if ($this->_has_outer_area ())
    {
    	$caption = $this->_get_caption($is_block, false);
      if ($is_block)
      {
        $Result .= '</div>' . $caption . '</div>';
      }
      else
      {
        $Result .= '</span>' . $caption . '</span>';
      }
    }

    return $Result;
  }

  /**
   * True if an outer container was generated for this tag.
   * @return boolean
   * @access private
   */
  protected function _has_outer_area ()
  {
    return $this->_caption;
  }

  /**
   * Render the open tag.
   * Called by {@link _open_outer_area()} with parameters containing pre-
   * calculated values for common values. Should be closed with {@link
   * _close_inner_area()}.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @see _close_inner_area()
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
    $builder = $munger->make_tag_builder ($this->main_tag);
    
    if (! empty ($this->main_css_class))
    {
    	$css_class = $this->main_css_class . ' ' . $inner_class;
    }
    else
    {
    	$css_class = $inner_class; 
    }

    $align = read_array_index ($attributes, 'align');
    if (isset($align))
    {
      $css_class .= ' ' . $align;
    }

    $builder->add_attribute ('class', $css_class);
    $outer_css->add_text ($inner_css->as_text ());
    $builder->add_attribute ('style', $outer_css->as_text ());

    return $builder->as_html ();
  }

  /**
   * Render the closing tag.
   * Called by {@link _close_outer_area()}.
   * @see _open_inner_area()
   * @param MUNGER $munger The transformation context.
   * @return string
   * @access private
   */
  protected function _close_inner_area ($munger)
  {
    return '</' . $this->main_tag . '>';
  }

  /**
   * Return the width to use for the tag.
   * @param MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @return string
   * @access private
   */
  protected function _calculate_width ($munger, $attributes)
  {
    return read_array_index ($attributes, 'width');
  }

  /**
   * Read a value from the attributes list and convert it for placement
   * within an HTML tag attribute value.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param string $index Index into the attributes array.
   * @return string
   * @access private
   */
  protected function _read_attribute ($attributes, $index)
  {
    return $this->_convert_to_attribute (read_array_index ($attributes, $index));
  }

  /**
   * Convert a value for placement within an HTML tag attribute value.
   * @param string $value The value to convert.
   * @return string
   * @access private
   */
  protected function _convert_to_attribute ($value)
  {
    $text_options = global_text_options ();
    return $text_options->convert_to_html_attribute($value);
  }

  /**
   * Create the caption from various attributes.
   * Uses the "author", "caption", "source" and "href" to link the
   * caption or author and optionally note the source (using the domain
   * of the "href").
   * 
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @return string
   * @access private
   */
  protected function _calculate_caption ($munger, $attributes)
  {
    $caption = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'caption'));
    $author = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'author'));
    $date = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'date'));
    $this->_caption_position = read_array_index ($attributes, 'caption-position');

    $href = $this->_convert_to_attribute($munger->resolve_url($this->_url_for_source ($attributes)));
    if ($href)
    {
      if ($caption)
      {
        $caption = '<a href="' . $href . '">' . $caption . '</a>';
      }
      elseif ($date)
      {
        $date = '<a href="' . $href . '">' . $date . '</a>';
      }
      elseif ($author)
      {
        $author = '<a href="' . $href . '">' . $author . '</a>';
      }
    }

    $source = $this->_calculate_source ($munger, $attributes, $href);

    return $this->_calculate_suffix ($caption, $author, $date, $source);
  }

  /**
   * Return the formatted source as a link or text.
   * 
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attrs
   * @param string $href
   * @return string
   * @access private
   */
  protected function _calculate_source ($munger, $attrs, $href)
  {
    $Result = $munger->apply_non_html_converters($this->_read_attribute ($attrs, 'source'));
    if ($Result && $href)
    {
      $url = new URL ($href);
      $domain = $url->domain ();
      $Result = "<a href=\"http://$domain/\">$Result</a>";
    }
    return $Result;
  }

  /**
   * Return the formatted suffix for a caption.
   * Returns output of the form: "CAPTION by AUTHOR on DATE (SOURCE)"
   * @param string $caption Base of the suffix
   * @param string $author Added to the caption, if provided.
   * @param string $date Added to the caption, if provided.
   * @param string $source Added to the caption, if provided.
   * @return string
   * @access private
   */
  protected function _calculate_suffix ($caption, $author, $date, $source)
  {
    $Result = $caption;
    if ($author)
    {
      $author = "<cite>$author</cite>";
      if ($Result)
      {
        $Result = rtrim($Result) . ' by ' . $author;
      }
      else
      {
        $Result = '&mdash;' . $author;
      }
    }
    if ($date)
    {
      if ($Result)
      {
        $is_year = is_numeric ($date);
        $has_comma = strpos ($date, ",") != 0;

        $separator = "on";
        if ($is_year || !$has_comma)
        {
          $separator = "in";
        }

        $Result = rtrim($Result) . ' ' . $separator . ' ' . $date;
      }
      else
      {
        $Result = $date;
      }
    }
    if ($source)
    {
      $source = "<cite>$source</cite>";
      if ($Result)
      {
        $Result = rtrim($Result) . ' (' . $source . ')';
      }
      else
      {
        $Result = $source;
      }
    }

    return rtrim($Result);
  }

  /**
   * Return the URL to use for calculating the source.
   * @param string[] $attributes
   * @return string
   * @access private
   */
  protected function _url_for_source ($attributes)
  {
    return read_array_index ($attributes, 'href');
  }

  /**
   * True if the last open tag had a caption.
   * Set by {@link _open_outer_area()} and used by {@link _close_outer_area()}
   * to determine how many close tags to add.
   * 
   * @var string
   * @access private
   */
  protected $_caption;

  /**
   * Determines the desired position of the caption for this container: "top", "bottom" or "both".
   * 
   * @var string
   * @access private
   */
  protected $_caption_position;
}

/**
 * Format a basic block using a DIV.
 * Includes the width by default.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_DIV_REPLACER extends HTML_BASE_REPLACER
{
  /**
   * @param string $classes
   */
  public function __construct ($classes = '')
  {
    $this->css_classes = $classes;
  }
}

/**
 * Format a block quote with basic properties.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_BLOCK_QUOTE_REPLACER extends HTML_DIV_REPLACER
{
  /** The main tag to use, by default.
   * Overriding this with "blockquote" has strange effects, therefore it has
   * been set back to the inherited "div" value.
   * @var string */
  public $main_tag = 'blockquote';

  /**
   * Render the open tag.
   *
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param $inner_class string CSS classes to apply to the inner container.
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
  	return parent::_open_inner_area($munger, $attributes, $outer_css, $inner_css, $inner_class) . '<div>';
  }

  /**
   * Render the closing tag.
   *
   * @param MUNGER $munger The transformation context.
   * @return string
   * @access private
   */
  protected function _close_inner_area ($munger)
  {
    return '</div></' . $this->main_tag . '>';
  }
}

/**
 * Format a preformatted block with basic properties.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.8.0
 * @access private */
class HTML_PREFORMATTED_BLOCK_REPLACER extends HTML_DIV_REPLACER
{
  /** The main tag to use, by default.
   * @var string */
  public $main_tag = 'pre';
}

/**
 * Format an HTML box using minimal markup.
 * Supports the following properties:
 * <ul><li>align [left, center, right]: floats the box or centers it.</li>
 * <li>title [string]: formats a title bar for the box with this string in it.</li>
 * <li>width [CSS width]: Specifies the width of the box. Useful for floated boxes.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_BOX_REPLACER extends HTML_DIV_REPLACER
{
  /**
   * Render the beginning of the tag.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param $inner_class string CSS classes to apply to the inner container.
   * @see _close_inner_area()
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
    $builder = $munger->make_tag_builder ($this->main_tag);
    $builder->add_attribute ('class', 'chart');
    $builder->add_attribute ('style', $outer_css->as_text ());
    $Result = $builder->as_html ();

    $title = $this->_read_attribute ($attributes, 'title');
    if ($title)
    {
      $Result .= "<h3 class=\"chart-title\">$title</h3>";
    }

    if ($inner_class)
    {
      $inner_class = 'chart-body ' . $inner_class;
    }
    else
    {
      $inner_class = 'chart-body';
    }

    $builder = $munger->make_tag_builder ($this->main_tag);
    $builder->add_attribute ('class', $inner_class);
    $builder->add_attribute ('style', $inner_css->as_text ());
    $Result .= $builder->as_html ();

    return $Result;
  }

  /**
   * Render the actual content of the tag.
   * @param MUNGER $munger The transformation context.
   * @return string
   * @access private
   */
  protected function _close_inner_area ($munger)
  {
    return '</' . $this->main_tag . '></' . $this->main_tag . '>';
  }
}

/**
 * Format an HTML box using minimal markup.
 * Supports the following properties:
 * <ul><li>align [left, center, right]: floats the box or centers it.</li>
 * <li>title [string]: formats a title bar for the box with this string in it.</li>
 * <li>width [CSS width]: Specifies the width of the box. Useful for floated boxes.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_MUNGER_CODE_REPLACER extends HTML_PREFORMATTED_BLOCK_REPLACER
{
  /**
   * Render the beginning of the tag.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @see _close_inner_area()
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
    $this->_quotes_were_enabled = $munger->set_converter_enabled('quotes', false);

    return parent::_open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class) . '<code>';
  }

  /**
   * Render the actual content of the tag.
   * @param MUNGER $munger The transformation context.
   * @return string
   * @access private
   */
  protected function _close_inner_area ($munger)
  {
    $munger->set_converter_enabled('quotes', $this->_quotes_were_enabled);
        
    return '</code>' . parent::_close_inner_area ($munger);
  }

  protected $_quotes_were_enabled;
  protected $_use_highlighting;
}

/**
 * Base class for an inline asset, like an image or video.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>src: URL for the image itself.</li>
 * <li>href: URL to which to link. Adds a link wrapper around the image.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_INLINE_ASSET_REPLACER extends HTML_BASE_REPLACER
{
  /**
   * True if the main tag is a block element.
   * @var boolean
   */
  public $is_block = false;

  /**
   * True if the main tag has an end tag.
   * @var boolean
   */
  public $has_end_tag = false;

  /**
   * Render the closing tag.
   * @see _open_inner_area()
   * @param MUNGER $munger The transformation context.
   * @return string
   * @access private
   */
  protected function _close_inner_area ($munger)
  {
    return '';
  }

  /**
   * Return the URL to use for calculating the source.
   * @param string[] $attributes
   * @return string
   * @access private
   */
  protected function _url_for_source ($attributes)
  {
    $attachment_name = read_array_index ($attributes, 'attachment');
    if ($attachment_name)
    {
      $Result = '{att_link}/' . $attachment_name;
    }
    else
    {
      $Result = read_array_index ($attributes, 'href');
    }
    return $Result;
  }
}

/**
 * Formats a image for HTML.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>src: URL for the image itself.</li>
 * <li>href: URL to which to link. Adds a link wrapper around the image.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_IMAGE_REPLACER extends HTML_INLINE_ASSET_REPLACER
{
  /**
   * Render the open tag for the image and link.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @see _close_inner_area()
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
    $attachment_name = read_array_index ($attributes, 'attachment');
    if ($attachment_name)
    {
      $src = '{att_thumb}/' . $attachment_name;
      $href = '{att_link}/' . $attachment_name;
    }
    else
    {
      $src = $this->_read_attribute ($attributes, 'src');
      $href = $this->_read_attribute ($attributes, 'href');
    }

    $title = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'title'));
    $alt = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'alt', $title));
    if (! $alt)
    {
      $alt = ' ';
    }

    $builder = $munger->make_tag_builder ('img');
    $builder->add_attribute ('title', $title);
    $builder->add_attribute ('src', $munger->resolve_url ($src));
    $builder->add_attribute ('alt', $alt);
    $builder->add_attribute ('class', $inner_class);
    $inner_css->add_text ($outer_css->as_text ());
    $builder->add_attribute ('style', $inner_css->as_text ());

    $Result = $builder->as_html ();

    if ($href)
    {
      $builder = $munger->make_tag_builder ('a');
      $builder->add_attribute ('href', $munger->resolve_url ($href));
      $Result = $builder->as_html () . $Result . '</a>';
    }

    return $Result;
  }

  /**
   * Return the width to use for the tag.
   * @param MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @return string
   * @access private
   */
  protected function _calculate_width ($munger, $attributes)
  {
    /* Prefer scale over width, discarding invalid scale values.
     * Retrieve width from the image only if scale is set or
     * width is not set and an outer area was generated (we want
     * to constrain the caption to the width of the image). */

    $scale = read_array_index ($attributes, 'scale');
    $Result = read_array_index ($attributes, 'width');
    if ($scale)
    {
      if (substr ($scale, -1, 1) == '%')
      {
        $scale = substr ($scale, 0, -1);
      }

      if (! is_numeric ($scale))
      {
        $scale = '';
      }
    }

    if (($scale) || (! $Result && $this->_has_outer_area ()))
    {
      $attachment_name = read_array_index ($attributes, 'attachment');
      if ($attachment_name)
      {
        $src = '{att_thumb}/' . $attachment_name;
      }
      else
      {
        $src = read_array_index ($attributes, 'src');
      }
      $url = new URL ($munger->resolve_url ($src, Force_root_on));
      if (! $url->has_domain () || $url->has_local_domain ())
      {
        include_once ('webcore/util/image.php');
        $metrics = new IMAGE_METRICS ();
        $metrics->set_url ($url->as_text ());
        if ($metrics->loaded ())
        {
          if ($scale)
          {
            $Result = ceil(($metrics->original_width * $scale) / 100) . 'px';
          }
          else
          {
            $Result = $metrics->original_width . 'px';
          }
        }
        elseif ($scale)
        {
          $Result = $scale . '%';
        }
      }
    }

    return $Result;
  }
}

/**
 * Formats a video or media for HTML.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>src: URL for the image itself.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 * @access private
 */
class HTML_MEDIA_REPLACER extends HTML_INLINE_ASSET_REPLACER
{
  /**
   * Render the open tag for the image and link.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @see _close_inner_area()
   * @return string
   * @access private
   */
  protected function _open_inner_area ($munger, $attributes, $outer_css, $inner_css, $inner_class)
  {
    /* Get the source URL and format it according to media type. */
    $attachment_name = read_array_index ($attributes, 'attachment');
    if ($attachment_name)
    {
      $src = '{att_link}/' . $attachment_name;
    }
    else
    {
      $src = read_array_index ($attributes, 'src');
    }

    return $this->_render_asset ($munger, $munger->resolve_url ($src), $attributes, $inner_css, $outer_css, $inner_class);
  }

  /**
   * Return the width to use for the tag.
   * @param MUNGER $munger The transformation context.
   * @param string[] $attributes List of attributes for the tag (retrieved from the token).
   * @return string
   * @access private
   */
  protected function _calculate_width ($munger, $attributes)
  {
    return read_array_index ($attributes, 'width', '450px');
  }

  /**
   * Return a representation for this url and attributes.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string $src The url to the movie.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @return string
   * @access private
   */
  protected function _render_asset ($munger, $src, $attributes, $inner_css, $outer_css, $inner_class)
  {
    return $this->_asset_as_movie ($munger, $src, $attributes, $inner_css, $outer_css, $inner_class);
  }

  // TODO Make a "youtube" tag

  /**
   * Return a control to display the movie.
   * @param HTML_MUNGER $munger The transformation context.
   * @param string $src The url to the movie.
   * @param string[] $attributes List of attributes for the tag
   * (retrieved from the token).
   * @param CSS_STYLE_BUILDER $outer_css Styles intended for the bounding
   * container; includes alignment and width.
   * @param CSS_STYLE_BUILDER $inner_css Styles intended for the inner
   * container; includes additional style and properties.
   * @param string $inner_class CSS classes to apply to the inner container.
   * @return string
   * @access private
   */
  protected function _asset_as_movie ($munger, $src, $attributes, $inner_css, $outer_css, $inner_class)
  {
    $builder = $munger->make_tag_builder ('embed');
    $builder->add_array_attribute ('title', $attributes);
    $builder->add_attribute ('class', $inner_class);
    $builder->add_attribute ('src', $src);
    $builder->add_attribute ('type', 'application/x-shockwave-flash');
    $inner_css->add_text ($outer_css->as_text ());
    $inner_css->add_array_attribute ('height', $attributes, '350px');
    $builder->add_attribute ('style', $inner_css->as_text ());
    $builder->add_attribute ('FlashVars', read_array_index ($attributes, 'args'));

    return $builder->as_html ();
  }

  /**
   * Return a link to the movie if it cannot be displayed.
   * @param string $src The url to the movie.
   * @return string
   * @access private
   */
  protected function _asset_as_link ($src)
  {
    return '<a href="' . $src . '">Click to play movie</a>';
  }
}

/**
 * Formats a URL link for HTML.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>author: Who wrote the page that is linked? (shown as 'by (author)' after the link)</li>
 * <li>source: Shows this name as a second link in parentheses, using only the domain name of the
 *  'href' property.</li>
 * <li>href: URL to which to link.</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 * @access private
 */
class HTML_LINK_REPLACER extends HTML_BASE_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param HTML_MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $attributes = $token->attributes ();

      $author = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'author'));
      $date = $munger->apply_non_html_converters($this->_read_attribute ($attributes, 'date'));
      $href = $munger->resolve_url (read_array_index ($attributes, 'href'));
      $source = $this->_calculate_source ($munger, $attributes, $href);

      $this->_suffix = $this->_calculate_suffix (' ', $author, $date, $source);

      $builder = $munger->make_tag_builder ('a');
      $builder->add_attribute ('href', $href);
      $builder->add_array_attribute ('title', $attributes);
      $builder->add_array_attribute ('class', $attributes);
      $builder->add_array_attribute ('style', $attributes);

      return $builder->as_html ();
    }

    return '</a>' . $this->_suffix;
  }

  /**
   * Name to show as root of link.
   * If this is specified, the domain name is extracted and displayed in parentheses as a link
   * with this text.
   * e.g. <a href="http://www.earthli.com/news/view_article.php?id=234" source="Earthli">Article</a>
   * would yield <a href="http://www.earthli.com/news/view_article.php?id=234">Article</a>
   * (<a href="http://www.earthli.com/">Earthli</a>).
   * @var string
   * @access private
   */
  protected $_source;

  /**
   * Name to show as author of link.
   * If this is specified, this text is shown with an 'author' style after the link.
   * e.g. <a href="http://www.earthli.com/news/view_article.php?id=234" author="Marco">Article</a>
   * would yield <a href="http://www.earthli.com/news/view_article.php?id=234">Article</a>
   * by <span class="author">Marco</span>.
   * @var string
   * @access private
   */
  protected $_author;

  /**
   * Actual contents of the link.
   * Maintained internally to use when formatting the {@link $_source} property.
   * @var string
   * @access private
   */
  protected $_href;

  /**
   * The text to include after the end tag of the link.
   * @var string
   * @access private
   */
  protected $_suffix;
}

/**
 * Formats an anchor for HTML.
 * Supports the following properties:
 * <ul><li>id: ID for the anchor</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.8.0
 * @access private
 */
class HTML_ANCHOR_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param HTML_MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    $attrs = $token->attributes ();

    $id = read_array_index ($attrs, 'id');

    $builder = $munger->make_tag_builder ('span');
    $builder->add_attribute ('id', $id);
    return $builder->as_html () . '</span>';
  }
}

/**
 * Formats a heading for HTML.
 * Supports the following properties:
 * <ul><li>level: Kind of heading; analogous to HTML heading level. Default is level 3. </li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class HTML_HEADING_REPLACER extends MUNGER_REPLACER
{
  /**
   * Heading level to use if none is specified.
   * @var integer
   */
  public $default_level = 2;

  /**
   * Convert the given token to the output format.
   * @param HTML_MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $attributes = $token->attributes ();

      $this->_level = read_array_index ($attributes, 'level');

      if (! is_numeric ($this->_level))
      {
        $this->_level = $this->default_level;
      }

      $builder = $munger->make_tag_builder ("h$this->_level");
      $builder->add_array_attribute ('class', $attributes);
      $builder->add_array_attribute ('style', $attributes);

      return $builder->as_html();
    }

    return "</h$this->_level>";
  }

  /**
   * @var integer
   * @access private
   */
  protected $_level;
}

/**
 * Formats common tag-based format to HTML.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.2.1
 */
class HTML_MUNGER extends MUNGER
{
  /**
   * URL for the location of the full text.
   * Used only if the text must be truncated.
   * @var string
   */
  public $complete_text_url;

  /**
   * Space-separated list of words to highlight.
   * @var string
   */
  public $highlighted_words = '';

  /**
   * Shared instance used by {@link MUNGER_REPLACER}s and {@link MUNGER_TRANSFORMER}s.
   * @param string $name Name of the tag to start creating.
   * @return HTML_TAG_BUILDER
   */
  public function make_tag_builder ($name)
  {
    $Result = new HTML_TAG_BUILDER ();
    $Result->set_name ($name);
    
    return $Result;
  }

  /**
   * Shared instance used by {@link MUNGER_REPLACER}s and {@link MUNGER_TRANSFORMER}s.
   * @param string $css The initial CSS to use.
   * @internal param string $name Initialize with this CSS fragment.
   * @return CSS_STYLE_BUILDER
   */
  public function make_style_builder ($css = '')
  {
    $Result = new CSS_STYLE_BUILDER ();
    $Result->set_text ($css);
    
    return $Result;
  }

  /**
   * Resolve the given address to a full url.
   * Convert all HTML special characters to their escaped equivalents.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url ($url, $root_override = null)
  {
    return parent::resolve_url ($url, $root_override);
  }

  public function apply_non_html_converters($text)
  {
    static $names_to_skip = array('tags');
    
    return $this->apply_converters($text, $names_to_skip);    
  }

  /**
   * Show ellipses after the text to indicate truncation.
   * The ellipses are shown before the tag stack is dumped to ensure deep nesting.
   * @access private
   */
  protected function _truncate ()
  {
    $this->_add_text_to_output ('...' . $this->_link ());
    parent::_truncate ();
  }

  /**
   * @param string $name
   * @access private
   */
  protected function _add_as_end_tag_to_output ($name)
  {
    $t = $this->_current_tokenizer ();
    $this->_add_text_to_output ($t->open_tag_char . $t->end_tag_char . $name . $t->close_tag_char);
  }

  /**
   * Return a link to the source text for truncated text.
   * If the algorithm has to truncate the text, this returns a formatted link to the url
   * where the full text can be found.
   * @see HTML_MUNGER::$page_location
   * @return string
   * @access private
   */
  protected function _link ()
  {
    if ($this->complete_text_url)
    {
      return " [<a class=\"complete-text-link\" href=\"$this->complete_text_url\">More</a>]";
    }
    
    return '';
  }
}

/**
 * Provides default formatting and replacement for {@link HTML_TEXT_MUNGER}
 * and {@link HTML_TITLE_MUNGER}.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.8.0
 */
class HTML_BASE_MUNGER extends HTML_MUNGER
{
  public function __construct ()
  {
    parent::__construct ();

    $this->register_known_tag ('span', true);
    $this->register_replacer ('i', new MUNGER_BASIC_REPLACER ('<em>', '</em>'));
    $this->register_replacer ('b', new MUNGER_BASIC_REPLACER ('<strong>', '</strong>'));
    $this->register_replacer ('n', new MUNGER_BASIC_REPLACER ('<small class="notes">', '</small>'));
    $this->register_replacer ('c', new MUNGER_BASIC_REPLACER ('<code>', '</code>'));
    $this->register_replacer ('hl', new MUNGER_BASIC_REPLACER ('<strong class="highlight">', '</strong>'));
    $this->register_known_tag ('del', true);  // deleted text
    $this->register_known_tag ('var', true);  // program variables
    $this->register_known_tag ('kbd', true);  // keyboard input
    $this->register_known_tag ('dfn', true);  // defining instance of a term
    $this->register_known_tag ('abbr', true);  // abbreviation
    $this->register_known_tag ('cite', true);  // citations of other sources
    $this->register_known_tag ('sub', true);  // subscript
    $this->register_known_tag ('sup', true);  // superscript
    $this->register_replacer ('macro', new MUNGER_MACRO_REPLACER (), false);
    
    $this->register_converter ('tags', new MUNGER_HTML_CONVERTER ());
    $this->register_converter ('punctuation', new HTML_PUNCTUATION_CONVERTER ());
    $this->register_converter ('quotes', new HTML_SMART_QUOTE_CONVERTER ());
    $this->register_converter ('highlight', new HTML_HIGHLIGHT_CONVERTER ());
  }
}

/**
 * Formats longer texts into HTML blocks.
 * Provides default support for mapping lists, preformatted, quoted and box blocks to standard HTML.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.2.1
 */
class HTML_TEXT_MUNGER extends HTML_BASE_MUNGER
{
  public function __construct ()
  {
    parent::__construct ();

    $this->_default_transformer = new HTML_PARAGRAPH_TRANSFORMER ();

    $nop_transformer = new MUNGER_PREFORMATTED_TRANSFORMER ();
    $list_transformer = new HTML_LIST_TRANSFORMER ();
    $block_transformer = new HTML_BLOCK_TRANSFORMER ();
    $quote_transformer = new HTML_QUOTE_TRANSFORMER ();
    $no_quote_transformer = new HTML_QUOTE_TRANSFORMER ();
    $no_quote_transformer->default_quote_style = Munger_quote_style_none;
    $shell_replacer = new HTML_PREFORMATTED_BLOCK_REPLACER ();
    $shell_replacer->main_css_class = 'shell';

    /** @var THEMED_PAGE $page */
    global $Page;
    $page = $Page;

    $this->register_transformer ('h', $nop_transformer);
    $this->register_replacer ('h', new HTML_HEADING_REPLACER ());
    $this->register_transformer ('div', $block_transformer);
    $this->register_replacer ('div', new HTML_DIV_REPLACER ());
    $this->register_transformer ('info', $block_transformer);
    $this->register_replacer ('info', new HTML_DIV_REPLACER('caution'));
    $this->register_transformer ('warning', $block_transformer);
    $this->register_replacer ('warning', new HTML_DIV_REPLACER('warning')); // new MUNGER_BASIC_REPLACER ($page->get_begin_message('warning', 'div'), $page->get_end_message('div'))
    $this->register_transformer ('error', $block_transformer);
    $this->register_replacer ('error', new HTML_DIV_REPLACER('error'));
    $this->register_transformer ('div', $block_transformer);
    $this->register_replacer ('clear', new MUNGER_BASIC_REPLACER ('<span class="clear-both"></span>', ''));
    $this->register_transformer ('pre', $nop_transformer);
    $this->register_replacer ('pre', new HTML_PREFORMATTED_BLOCK_REPLACER ());
    $this->register_transformer ('box', $block_transformer);
    $this->register_replacer ('box', new HTML_BOX_REPLACER ());
    $this->register_transformer ('code', $nop_transformer);
    $this->register_replacer ('code', new HTML_MUNGER_CODE_REPLACER ());
    $this->register_replacer ('iq', new MUNGER_BASIC_REPLACER ('<span class="quote-inline">&ldquo;', '&rdquo;</span>'));
    $this->register_transformer ('shell', $nop_transformer);
    $this->register_replacer ('shell', $shell_replacer);
    $this->register_transformer ('bq', $quote_transformer);
    $this->register_replacer ('bq', new HTML_BLOCK_QUOTE_REPLACER ('quote quote-block'));
    $this->register_transformer ('pullquote', $no_quote_transformer);
    $this->register_replacer ('pullquote', new HTML_BLOCK_QUOTE_REPLACER ('quote pullquote'));
    $this->register_transformer ('abstract', $no_quote_transformer);
    $this->register_replacer ('abstract', new HTML_BLOCK_QUOTE_REPLACER ('quote abstract'));
    $this->register_transformer ('ul', $list_transformer);
    $this->register_transformer ('ol', $list_transformer);
    $this->register_transformer ('dl', new HTML_DEFINITION_LIST_TRANSFORMER ());
    $this->register_replacer ('dl', new MUNGER_BASIC_REPLACER ('<dl>', '</dl>'));
    $this->register_replacer ('fn', new HTML_FOOTNOTE_REFERENCE_REPLACER (), false);
    $this->register_transformer ('ft', $block_transformer);
    $this->register_replacer ('ft', new HTML_FOOTNOTE_TEXT_REPLACER ());
    $this->register_replacer ('hr', new HTML_BASIC_REPLACER ('<hr>', ''), false);
    $this->register_replacer ('a', new HTML_LINK_REPLACER ());
    $this->register_replacer ('anchor', new HTML_ANCHOR_REPLACER (), false);
    $this->register_replacer ('img', new HTML_IMAGE_REPLACER (), false);
    $this->register_replacer ('media', new HTML_MEDIA_REPLACER (), false);
    $this->register_replacer ('page', new MUNGER_PAGE_REPLACER (), false);
    
    $lig = new HTML_LIGATURE_CONVERTER ();
    $lig->enabled = false;
    $this->register_converter ('ligature', $lig);

  }
}

/**
 * Formats single-line texts to HTML.
 * HTML paragraphs and other blocks are not generated with this formatter.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.4.0
 */
class HTML_TITLE_MUNGER extends HTML_BASE_MUNGER
{
  /**
   * @var boolean
   */
  public $strip_unknown_tags = false;

  /**
   * @var boolean
   */
  public $force_paragraphs = false;

  public function __construct ()
  {
    parent::__construct ();
    $this->_default_transformer = new MUNGER_NOP_TRANSFORMER ();
  }
}