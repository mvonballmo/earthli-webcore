<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

/**
 * Converts text to formatted plain text.
 * The text is generally in the correct format, but there is special handling for
 * the boundaries with embedded block elements.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PLAIN_TEXT_BLOCK_TRANSFORMER extends MUNGER_BLOCK_TRANSFORMER
{
  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger Activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    parent::activate ($munger, $value, $token);
    $this->_indent = $munger->num_spaces;
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
//    case Munger_first_data_block:
//    case Munger_only_data_block:
      case Munger_middle_data_block:
      if (! $this->strict_newlines && ($len > 0) && ($text [0] == "\n"))
      {
        $first_char = 1;
      }
      break;
    }

    switch ($this->_buffer_state)
    {
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
   * Transform given newlines to plain text boxes.
   * This interprets the user's intent to properly interpret which newlines were added
   * just for spacing around tags. The behavior should be predictable. There should be
   * no case in which whitespace 'magically' disappears; however, white-space is managed
   * so that property HTML-style paragraph blocking achieved, especially with nested lists
   * and other blocks.
   * @param MUNGER $munger The transformation context.
   * @param string $text The text to transform.
   * @access private
   */
  protected function _apply_transform ($munger, $text)
  {
    $text = $this->_trim ($text);
    $len = strlen ($text);

    /* Make sure each paragraph ends with two newlines. */

    if ($this->_buffer_state == Munger_first_data_block)
    {
      if (($len > 0) && ($text [$len - 1] != "\n"))
      {
        $text .= "\n\n";
      }
      elseif ($len > 1)
      {
        if ($text [$len - 2] != "\n")
        {
          $text .= "\n";
        }
      }
    }

    /* Make sure each block in a document ends with two newlines. */

    if (($this->_buffer_state == Munger_middle_data_block) ||
        ($this->_buffer_state == Munger_last_data_block))
    {
      if (($len > 0) && ($text [0] != "\n"))
      {
        $text = "\n\n" . $text;
      }
      elseif ($len > 1)
      {
        if ($text [1] != "\n")
        {
          $text = "\n" . $text;
        }
      }
      else
      {
        $text = "\n" . $text;
      }

      /* Make sure each block in a document ends with two newlines.
         Do not end the document with two newlines. */

      if ($this->_buffer_state == Munger_middle_data_block)
      {
        if ($text [strlen ($text) - 1] != "\n")
        {
          $text .= "\n\n";
        }
        elseif ($len > 1)
        {
          if ($text [strlen ($text) - 2] != "\n")
          {
            $text .= "\n";
          }
        }
        else
        {
          $text .= "\n";
        }
      }
    }

    return $munger->wrap ($text, $this->_indent);
  }

  /**
   * Amount of indenting to add when wrapping.
   * @var integer
   * @access private
   */
  protected $_indent = 0;
}

/**
 * Converts text to formatted plain text.
 * The text is generally in the correct format, but there is special handling for
 * the boundaries with embedded block elements.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_PARAGRAPH_TRANSFORMER extends PLAIN_TEXT_BLOCK_TRANSFORMER
{
  /**
   * Should every newline be used?
   * 
   * Since the default formatter does not have start and end tags, it also does not ignore
   * the first and last newlines in the text.
   * 
   * @var boolean
   */
  public $strict_newlines = true;

  /**
   * Returns transformed content.
   * @param MUNGER $munger The transformation context.
   * @return string
   */
  public function data ($munger)
  {
    $Result = parent::data ($munger);

    if ($Result && 
        (
          ($munger->force_paragraphs || (strpos ($Result, "\n") !== false)) &&
           (substr ($Result, -1) != "\n"))
          )
    {
      $Result .= "\n";
    }
    
    return $Result;
  }
}

/**
 * Surrounds paragraphed text with quotes.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.6.0
 * @access private
 */
class PLAIN_TEXT_QUOTE_TRANSFORMER extends PLAIN_TEXT_BLOCK_TRANSFORMER
{
  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The activation context.
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
   * Transform given newlines to plain text boxes.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_transform ($munger, $text)
  {
    $text = $this->_apply_quotes ($text, $this->_quote_style, '"', '"');
    return parent::_apply_transform ($munger, $text);
  }

  /**
   * @var string
   * @access private
   */
  protected $_quote_style;
}

/**
 * Formats a URL link to plain text.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>href: URL to which to link.</li>
 * <li>author: Name of the author of the content that is linked. Formatted as 'by author' after the text.</li>
 * <li>format: specifies which pieces to render (can be 'url', 'all', 'none').</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_LINK_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $attrs = $token->attributes ();

      $this->_format = read_array_index ($attrs, 'format', 'all');
      $this->_author = read_array_index ($attrs, 'author');

      $this->_title = read_array_index ($attrs, 'title');
      $this->_href = $munger->resolve_url (read_array_index ($attrs, 'href'));

      return '"';
    }
    else
    {
      $Result = '"';
      if ($this->_author)
      {
        $Result .= ' by ' . $this->_author;
      }
      if (($this->_format == 'all') || ($this->_format == 'url'))
      {
        $Result .= " <$this->_href>";
      }

      if (($this->_format == 'all') && ($this->_title))
      {
        $Result .= " ($this->_title)";
      }

      return $Result;
    }
  }

  /**
   * Specifies which parts of the link to show.
   * Can be 'all', 'none', or 'url'. 'all' shows the link and the title after the link's content text.
   * 'url' shows only the title. 'none' shows only the content text.
   * @var string
   * @access private
   */
  protected $_format;

  /**
   * The author of the link.
   * Will be displayed after the content text, if specified.
   * e.g. <a href="http://www.earthli.com/news/view_article.php?id=234" author="Marco">Article</a>
   * would yield: Article by Marco <http://www.earthli.com/news/view_article.php?id=234>.
   * @var string
   * @access private
   */
  protected $_author;

  /**
   * @var string
   * @access private
   */
  protected $_title;

  /**
   * @var string
   * @access private
   */
  protected $_href;
}

/**
 * Formats an image for plain text.
 * Supports the following properties:
 * <ul><li>title: full title of the link. Used in the 'title' attribute of the link tag.</li>
 * <li>class: CSS class to associate with the link.</li>
 * <li>src: URL of the image itself.</li>
 * <li>href: URL to which to link the image.</li>
 * <li>format: specifies which pieces to render (can be 'url', 'all', 'none').</li></ul>
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_MEDIA_REPLACER extends MUNGER_REPLACER
{
  /**
   * @param string $default_title
   */
  public function __construct ($default_title)
  {
    $this->_default_title = $default_title;
  }

  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $Result = '';
      $attrs = $token->attributes ();

      $format = read_array_index ($attrs, 'format');
      if (empty ($format))
      {
        $format = 'alt';
      }

      if ($format != 'none')
      {
        // Use 'title' as a fallback for 'alt' and 'src' as a fallback for 'href'.

        $title = read_array_index ($attrs, 'title');
        $alt = read_array_index ($attrs, 'alt', $title);
        $src = read_array_index ($attrs, 'src');
        $href = read_array_index ($attrs, 'href', $src);

        if (! $alt)
        {
          $alt = $this->_default_title ();
        }

        $Result = "[$alt]";

        if ((($format == 'all') || ($format == 'url')) && ! empty ($href))
        {
          $Result .= " <$href>";
        }

        if (($format == 'all') && $title && ($title != $alt))
        {
          $Result .= " ($title)";
        }
      }

      return $Result;
    }
    
    return '';
  }

  /**
   * Used if no "alt" or "title" tag is specified.
   * @return string
   * @access private
   */
  protected function _default_title ()
  {
    return $this->_default_title;
  }

  /**
   * @var string
   * @access private
   */
  protected $_default_title;
}

/**
 * Adds a link to a footnote, numbering automatically.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.7.1
 * @access private
 */
class PLAIN_TEXT_FOOTNOTE_REFERENCE_REPLACER extends MUNGER_FOOTNOTE_REFERENCE_REPLACER
{
  /**
   * Format the reference to the given footnote number.
   * @param MUNGER $munger The munger that generated the call; cannot be null.
   * @param MUNGER_TOKEN $token The token being processed; cannot be null.
   * @param MUNGER_FOOTNOTE_INFO $info The footnote to format; cannot be null.
   * @return string
   * @access private
   */
  protected function _format_reference ($munger, $token, $info)
  {
    return " [$info->number]";
  }
}

/**
 * Links a block of text to a previous footnote reference.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.7.0
 * @access private
 */
class PLAIN_TEXT_FOOTNOTE_TEXT_REPLACER extends MUNGER_FOOTNOTE_TEXT_REPLACER
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
      $munger->increase_indent_by (4);
      return "[$info->number] ";
    }
    
    $munger->decrease_indent_by (4);
    return '';
  }
}

/**
 * Converts a list to plain text.
 * Ordered an unordered lists are supported.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_LIST_TRANSFORMER extends MUNGER_LIST_TRANSFORMER
{
  /**
   * Text to use for bullets, by default.
   * @var string
   */
  public $default_mark = '*';

  /**
   * Amount to indent at each level.
   * @var string
   */
  public $spaces_to_indent = 2;

  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    if ($value)
    {
      $munger->increase_indent_by ($this->spaces_to_indent);
    }
    else
    {
      $munger->decrease_indent_by ($this->spaces_to_indent);
    }

    $this->_indent = $munger->num_spaces;
    $this->_depth = $munger->num_indents;
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
    if ($this->_item_is_open && ! $item_was_open)
    {
      $prefix = $this->_make_mark ();
    }
    elseif ($item_was_open && ! $this->_item_is_open)
    {
      $prefix = $this->_make_mark (' ');
    }
    elseif (! $this->_item_is_open)
    {
      $prefix = $this->_make_mark ();
    }
    else
    {
      $prefix = $this->_make_mark (' ');
    }

    $Result = $prefix . $this->_generate_list_items ($munger, $text);

    /* Chop off the leading newline if this is the first or only data block.
       Assume the paragraph handled it. */

    if (($this->_depth == 1) && (($this->_buffer_state == Munger_first_data_block) ||
                                 ($this->_buffer_state == Munger_only_data_block)))
    {
      $Result = substr ($Result, 1);
    }

    return $Result;
  }

  /**
   * Format the list items themselves.
   * @param MUNGER $munger The generation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _generate_list_items ($munger, $text)
  {
    $Result = '';
    $text = ltrim ($text, " \t");  // right side is already trimmed in 'apply_transform'.

    $lines = explode ("\n", $text);
    
    $index = 0;
    $count = sizeof ($lines);
    while ($index < $count)
    {
      $Result .= $munger->wrap (ltrim ($lines [$index]), $this->_indent + strlen ($this->_last_mark));
      if ($index < $count - 1)
      {
        $Result .= $this->_make_mark ();
      }
      $index += 1;
    }

    return $Result;
  }

  /**
   * Create the 'mark' for the bullet.
   * Uses the {@link $default_mark} if none is specified.  Spacing is added to adjust for the
   * depth of this list and all parent lists.
   * @param string $mark Optional mark to use instead of the default.
   * @access private
   */
  protected function _make_mark ($mark = null)
  {
    if (! isset ($mark))
    {
      $mark = $this->default_mark;
    }
    $mark .= ' ';
    $this->_last_mark = $mark;
    return "\n" . str_repeat (' ', $this->_indent) . $mark;
  }

  /**
   * Stored depth of this list.
   * All transformers update the state found in the munger, using
   * {@link TEXT_MUNGER::increase_indent_by()} and {@link TEXT_MUNGER::decrease_indent_by()}.
   * Each list stores its depth and indent when activated (by {@link activate()}) because
   * the global values may reflect a different nesting level when the list is actually transformed.
   * @see $_indent
   * @var integer
   */
  protected $_depth;

  /**
   * Amount of indenting to add when wrapping.
   * @see $_depth
   * @var integer
   * @access private
   */
  protected $_indent = 0;
}

/**
 * Converts text to a numbered list.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_NUMERIC_LIST_TRANSFORMER extends PLAIN_TEXT_LIST_TRANSFORMER
{
  /**
   * Amount to indent at each level.
   * @var string
   */
  public $spaces_to_indent = 3;

  /**
   * Create the 'mark' for the bullet.
   * Uses the {@link $default_mark} if none is specified.  Spacing is added to adjust for the
   * depth of this list and all parent lists.
   * @param string $mark Optional mark to use instead of the default.
   * @access private
   */
  protected function _make_mark ($mark = null)
  {
    if (! isset ($mark))
    {
      $this->_current_mark += 1;
      $mark = $this->_current_mark . '.';
    }
    else
    {
      $mark = str_repeat (' ', strlen ($this->_current_mark) + 1);
    }

    return parent::_make_mark ($mark);
  }

  /**
   * Current item number within this list.
   * @var integer
   * @access private
   */
  protected $_current_mark = 0;
}

/**
 * Links a block of text to a previous footnote reference.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.7.1
 * @access private
 */
class PLAIN_TEXT_FOOTNOTE_TEXT_TRANSFORMER extends PLAIN_TEXT_LIST_TRANSFORMER
{
  /**
   * Amount to indent at each level.
   * @var string
   */
  public $spaces_to_indent = 3;

  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger Activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    parent::activate ($munger, $value, $token);
    $this->_indent = 0;
  }
    
  /**
   * Create the 'mark' for the bullet.
   * Uses the {@link $default_mark} if none is specified.  Spacing is added to adjust for the
   * depth of this list and all parent lists.
   * @param string $mark Optional mark to use instead of the default.
   * @access private
   */
  protected function _make_mark ($mark = null)
  {
    if (! isset ($mark))
    {
      $this->_current_mark += 1;
      
      if ($this->_current_mark == 1)
      {
        $mark = '[' . $this->_current_mark . ']';
      }
      else 
      {
        $mark = str_repeat (' ', strlen ($this->_current_mark));
      }
    }
    else
    {
      $mark = str_repeat (' ', strlen ($this->_current_mark));
    }

    return parent::_make_mark ($mark);
  }

  /**
   * Current item number within this list.
   * @var integer
   * @access private
   */
  protected $_current_mark = 0;
}

/**
 * Generates an HTML definition list.
 * Generates alternating defition terms and definitions for newlines in the text.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PLAIN_TEXT_DEFINITION_LIST_TRANSFORMER extends MUNGER_DEFINITION_LIST_TRANSFORMER
{
  /**
   * Amount to indent at each level.
   * @var string
   */
  public $spaces_to_indent = 3;

  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate ($munger, $value, $token)
  {
    parent::activate ($munger, $value, $token);
    if ($value)
    {
      $munger->increase_indent_by ($this->spaces_to_indent);
    }
    else
    {
      $munger->decrease_indent_by ($this->spaces_to_indent);
    }
    $this->_indent = $munger->num_spaces;
  }

  /**
   * Returns transformed content.
   * @param MUNGER $munger
   * @return string
   */
  public function data ($munger)
  {
    return rtrim (parent::data ($munger));
  }
  
  /**
   * Add the new text to the existing text.
   * 
   * Overridden to ensure that there are only two newlines between elements; nested blocks can possibly
   * introduce extra newlines when end tags are introduced.
   *
   * @param string $existing_text
   * @param string $new_text
   * @return string
   * @access private
   */
  protected function _merge_text($existing_text, $new_text)
  {
    if (!empty($existing_text))
    {
      $existing_text = rtrim ($existing_text, "\n");
      $new_text = ltrim ($new_text, "\n");
      $existing_text .= "\n\n";
    }  
    
    return $existing_text . $new_text;
  }

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @access private
   */
  protected function _build_as_definition_term ($munger, $line)
  {
    return "$line\n";
  }

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The generation context.
   * @param string $line
   * @access private
   */
  protected function _build_as_definition_body ($munger, $line)
  {
    $Result = "\n\n";
    if ($line)
    {
      $line = $munger->wrap ($line, $this->_indent);
      if ($line [0] == "\n")
      {
        $Result = $line . $Result;
      }
      else
      {
        $Result = "\n" . str_repeat (' ', $this->spaces_to_indent) . $line . $Result;
      }
    }
    return $Result;
  }

  /**
   * Amount of indenting to add when wrapping.
   * @var integer
   * @access private
   */
  protected $_indent = 0;
}

/**
 * Preserves preformatted text.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 * @access private
 */
class PLAIN_TEXT_PREFORMATTED_TRANSFORMER extends MUNGER_TRANSFORMER
{
  /**
   * Transform for preformatted text.
   * This will strip at most 2 newlines off the end of the preformatted text; the next
   * container will add space after this container anyway.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   */
  protected function _apply_transform ($munger, $text)
  {
    $len = strlen ($text);
    $first_char = 0;
    $num_chars = $len;

    switch ($this->_buffer_state)
    {
    case Munger_last_data_block:
    case Munger_only_data_block:
      if ($text [$len - 1] == "\n")
      {
        if (($len > 1) && ($text [$len - 2] == "\n"))
        {
          $num_chars = -2;
        }
        else
        {
          $num_chars = -1;
        }
      }
      break;
    }

    if (($first_char > 0) || ($num_chars != $len))
    {
      $text = substr ($text, $first_char, $num_chars);
    }

    return $text;
  }
}

/**
 * Format a nice CSS-style box using minimal markup.
 * Supports the following properties:
 * <li>title [string]: formats a title bar for the box with this string in it.</li>
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class PLAIN_TEXT_BOX_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      $attrs = $token->attributes ();

      $this->_title = read_array_index ($attrs, 'title');
      if ($this->_title)
      {
        $border = str_repeat ('-', strlen ($this->_title) + 4);
        return "$border\n| $this->_title |\n$border\n\n";
      }
    }
    else
    {
      if ($this->_title)
      {
        return "\n" . str_repeat ('-', strlen ($this->_title) + 4);
      }
    }
    
    return '';
  }
}

/**
 * Format a horizontal divider.
 * Supports the following properties:
 * <li>title [string]: formats a title bar for the box with this string in it.</li>
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.7.0
 * @access private
 */
class PLAIN_TEXT_HORIZONTAL_RULE_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform ($munger, $token)
  {
    if ($token->is_start_tag ())
    {
      return str_repeat ('-', $munger->available_width ()) . "\n";
    }
    
    return '';
  }
}

/**
 * Replace punctuation constructs with HTML entities.
 * Replaces '--' with '&mdash;', '1/2' with '&frac12;' and so on.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.4.0
 * @since 2.7.1
 */
class PLAIN_TEXT_PUNCTUATION_CONVERTER extends MUNGER_CONVERTER
{
  /**
   * Table of punctuation mappings to apply in {@link _convert()}
   * @var array[string,string]
   */
  public $punctuation_table = array ( '---' => ' -- '
	                                  , '(S,)' => 'S'     // Turkish S with cedilla
	                                  , '(s,)' => 's'     // Turkish s with cedilla
	                                  , '(C,)' => 'C'     // Turkish C with cedilla
	                                  , '(c,)' => 'c'     // Turkish c with cedilla
	                                  , '(i-)' => 'i'     // Turkish i without dot
	                                  , '(g-)' => 'g'     // Turkish g (silent)
	                                  , '(I.)' => 'I'     // Turkish I with dot
	                                  , '(Z-)' => 'Z'     // Slavic Z with a caron    
	                                  , '(z-)' => 'z'     // Slavic z with a caron    
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
 * Converts tagged text to formatted plain text.
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.5.0
 */
class TEXT_MUNGER extends MUNGER
{
  /**
   * Depth of indenting.
   * Used by the {@link PLAIN_TEXT_LIST_TRANSFORMER}.
   * @var integer
   */
  public $num_indents;

  /**
   * Current indenting level.
   * @var integer
   * @access private
   */
  public $num_spaces = 0;

  /**
   * Wrap all text to this right margin.
   * @var integer
   */
  public $right_margin = 72;

  /**
   * Disables wrapping in paragraphs when True.
   * The {@link PLAIN_TEXT_DEFINITION_LIST_TRANSFORMER} sets this to prevent nested
   * paragraphs from wrapping their text. The list takes care of all wrapping when it
   * is generated.
   */
  public $disable_default_wrapping = false;

  public function increase_indent_by ($num_spaces)
  {
    $this->num_indents += 1;
    $this->num_spaces += $num_spaces;
  }

  public function decrease_indent_by ($num_spaces)
  {
    $this->num_indents -= 1;
    $this->num_spaces -= $num_spaces;
    if ($this->num_spaces < 0)
    {
      log_message ('Indent level dropped below zero.', Msg_type_warning, Msg_channel_munger);
    }
  }

  /**
   * Wrap the text to a right margin.
   * Transformers can adjust the left margin with {@link increase_indent_by()}
   * and {@link descrease_indent_by()}.
   * @param string $text
   * @return string
   */
  public function wrap ($text, $left_margin)
  {
    $desired_width = $this->right_margin - $left_margin;

    $non_indent_text = ltrim ($text);
    $newline_pos = strpos ($non_indent_text, "\n");
    $wrap_needed = ($newline_pos === false);

    if (! $wrap_needed)
    {
      $space_pos = strpos ($non_indent_text, ' ');
      $wrap_needed = ($space_pos < $newline_pos) && ($newline_pos > $desired_width);
      while (($newline_pos !== false) && ! $wrap_needed)
      {
        $non_indent_text = substr ($non_indent_text, $newline_pos + 1);
        $non_indent_text = ltrim ($non_indent_text);
        $newline_pos = strpos ($non_indent_text, "\n");
        $space_pos = strpos ($non_indent_text, ' ');
        $wrap_needed = (($space_pos < $newline_pos) && ($newline_pos > $desired_width)) || ($newline_pos === false);
      }
    }

    if ($wrap_needed)
    {
      $text = wordwrap ($text, $desired_width);
      if ($left_margin)
      {
        //$text = str_replace ("\n", "\n" . str_repeat (' ', $left_margin), $text);
        
        $text = preg_replace("/\n([^\n])/", "\n" . str_repeat (' ', $left_margin) . "\\1", $text);
      }
    }
    elseif ($left_margin)
    {
      //$text = str_replace ("\n", "\n" . str_repeat (' ', $left_margin), $text);
      
      $text = preg_replace("/\n([^\n])/", "\n" . str_repeat (' ', $left_margin) . "\\1", $text);
    }

    return $text;
  }

  /**
   * Return the amount of space between margins.
   * Adjusted for the {@link $right_margin} and {@link $num_spaces}.
   * @return integer
   */
  public function available_width ()
  {
    return $this->right_margin - $this->num_spaces;
  }

  /**
   * Create an output string from the given input.
   * @var string $input
   * @return string
   */
  public function transform ($input, $context_object = null)
  {
    $this->num_indents = 0;
    $this->num_spaces = 0;
    return parent::transform ($input, $context_object);
  }

  /**
   * Replace a known tag with its actual content.
   * @param MUNGER_TOKEN
   * @return string
   * @access private
   */
  protected function _known_tag_as_text ($token)
  {
    return '';
  }

  /**
   * Show ellipses after the text to indicate truncation.
   * @access private
   */
  protected function _truncate ()
  {
    parent::_truncate ();
    $this->_add_text_to_output ('...');
  }
}

/**
 * Converts tagged text to formatted plain text with a base set of tags and converters.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 3.1.0
 */
class PLAIN_TEXT_BASE_MUNGER extends TEXT_MUNGER
{
  public function __construct ()
  {
    parent::__construct ();

    $this->register_known_tag ('span', true);
    $this->register_known_tag ('i', true);
    $this->register_known_tag ('b', true);
    $this->register_known_tag ('n', true);
    $this->register_known_tag ('c', true);
    $this->register_known_tag ('hl', true);
    $this->register_known_tag ('del', true);
    $this->register_known_tag ('var', true);  // program variables
    $this->register_known_tag ('kbd', true);  // keyboard input
    $this->register_known_tag ('dfn', true);  // defining instance of a term
    $this->register_known_tag ('abbr', true);  // abbreviation
    $this->register_known_tag ('cite', true);  // citations of other sources
    $this->register_replacer ('macro', new MUNGER_MACRO_REPLACER (), false);
    
    $this->register_converter ('tags', new MUNGER_HTML_CONVERTER ());
    $this->register_converter ('punctuation', new PLAIN_TEXT_PUNCTUATION_CONVERTER ());
  }
}

/**
 * Formats "munger"-formatted text to plain text.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.4.0
 */
class PLAIN_TEXT_MUNGER extends PLAIN_TEXT_BASE_MUNGER
{
  public function __construct ()
  {
    parent::__construct ();

    $this->_default_transformer = new PLAIN_TEXT_PARAGRAPH_TRANSFORMER ();
    $block_transformer = new PLAIN_TEXT_BLOCK_TRANSFORMER ();

    $this->register_transformer ('h', $block_transformer);
    $this->register_replacer ('h', new MUNGER_BASIC_REPLACER ('[', ']'));
    $this->register_transformer ('div', $block_transformer);
    $this->register_known_tag ('clear', true);
    $this->register_transformer ('pre', new PLAIN_TEXT_PREFORMATTED_TRANSFORMER ());
    $this->register_transformer ('box', $block_transformer);
    $this->register_replacer ('box', new PLAIN_TEXT_BOX_REPLACER ());
    $this->register_transformer ('code', $block_transformer);
    $this->register_replacer ('iq', new MUNGER_BASIC_REPLACER ('"', '"'));
    $this->register_transformer ('bq', new PLAIN_TEXT_QUOTE_TRANSFORMER ());
    $this->register_transformer ('pullquote', new PLAIN_TEXT_QUOTE_TRANSFORMER ());
    $this->register_transformer ('abstract', new PLAIN_TEXT_QUOTE_TRANSFORMER ());
    $this->register_transformer ('ul', new PLAIN_TEXT_LIST_TRANSFORMER ());
    $this->register_transformer ('ol', new PLAIN_TEXT_NUMERIC_LIST_TRANSFORMER ());
    $this->register_transformer ('dl', new PLAIN_TEXT_DEFINITION_LIST_TRANSFORMER ());
    $this->register_replacer ('fn', new PLAIN_TEXT_FOOTNOTE_REFERENCE_REPLACER (), false);
    //$this->register_replacer ('ft', new PLAIN_TEXT_FOOTNOTE_TEXT_REPLACER ());
    $this->register_transformer('ft', new PLAIN_TEXT_FOOTNOTE_TEXT_TRANSFORMER());
    $this->register_replacer ('hr', new PLAIN_TEXT_HORIZONTAL_RULE_REPLACER ());
    $this->register_replacer ('a', new PLAIN_TEXT_LINK_REPLACER ());
    $this->register_known_tag ('anchor', true);
    $this->register_replacer ('img', new PLAIN_TEXT_MEDIA_REPLACER ('image'), false);
    $this->register_replacer ('media', new PLAIN_TEXT_MEDIA_REPLACER ('media'), false);
    $this->register_replacer ('page', new MUNGER_PAGE_REPLACER ());
  }
}

/**
 * Formats single-line "munger"-formatted text to plain text.
 * 
 * All blocks and most formatting tags are removed.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.4.0
 * @since 2.6.1
 */
class PLAIN_TEXT_TITLE_MUNGER extends PLAIN_TEXT_BASE_MUNGER
{
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

?>