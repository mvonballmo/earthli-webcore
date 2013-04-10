<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.2.1
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
require_once ('webcore/sys/system.php');
require_once ('webcore/config/text_config.php');

/**
 * Enable this filter in a logger to debug the HTML formatter.
 * @access private
 */
define('Msg_channel_munger', 'Munger');

/**
 * A token representing text.
 * @access private
 */
define('Munger_token_text', 1);
/**
 * A token representing a start tag.
 * @access private
 */
define('Munger_token_start_tag', 2);
/**
 * A token representing an end tag.
 * @access private
 */
define('Munger_token_end_tag', 3);

/**
 * There is only one data element.
 * The formatter knows that it need not expect any more data. This helps
 * formatters that need to apply characters and wrapping tags in their
 * transformation.
 */
define('Munger_only_data_block', 0);
/**
 * First of at least two data elements.
 * If the formatter needs to make opening and closing tags, it knows to
 * make the opening tag, but not the closing one.
 */
define('Munger_first_data_block', 1);
/**
 * Not the first and not the last data element.
 * The formatter knows that a data element has ended, but also that another
 * data element will follow.
 */
define('Munger_middle_data_block', 2);
/**
 * Last of at least two data elements.
 * If the formatter needs to make opening and closing tags, it knows to
 * make the closing tag, but not the opening one.
 */
define('Munger_last_data_block', 3);

/**
 * Show no quotes.
 */
define('Munger_quote_style_none', 'none');
/**
 * Show one set of quotes around all content.
 */
define('Munger_quote_style_default', 'default');
/**
 * Show one set of quotes around all content.
 */
define('Munger_quote_style_single', 'single');
/**
 * Show each block in it's own quotes.
 */
define('Munger_quote_style_multiple', 'multiple');

/**
 * Constants used {@link MUNGER::$convert_mode}.
 * @see Munger_convert_none
 * @see Munger_convert_html_simple
 * @see Munger_convert_html_strict
 * @see Munger_convert_plain_text
 */
define('Munger_convert_constants', '');
/**
 * No conversion of extended characters.
 * @see Munger_convert_constants
 */
define('Munger_convert_none', 'none');
/**
 * Converts tag and ampersand characters to HTML entities.
 * @see MUNGER::$convert_mode
 * @see Munger_convert_constants
 */
define('Munger_convert_html_simple', 'simple');
/**
 * Converts all non-standard characters to HTML entities.
 * @see MUNGER::$convert_mode
 * @see Munger_convert_constants
 */
define('Munger_convert_html_strict', 'strict');
/**
 * Converts all non-standard characters to normalized equivalents.
 * @see MUNGER::$convert_mode
 * @see Munger_convert_constants
 */
define('Munger_convert_plain_text', 'text');

/**
 * A token representing either a tag or text.
 * These are created by the {@link MUNGER_TOKENIZER} as it parses its given input string.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_TOKEN
{
  /**
   * What kind of token is this?
   * This property is set by the {@link MUNGER_TOKENIZER} when it is created and should
   * be treated as read-only.
   * @see Munger_token_text, Munger_token_start_tag, Munger_token_end_tag
   * @var integer
   */
  public $type = Munger_token_text;

  /**
   * How much data is in this token?
   * Retrieve the contents of this token with {@link MUNGER_TOKEN::data()}. This allows
   * a token to be arbitrarily large without taking memory for its data until requested.
   */
  public $size = 0;

  /**
   * @param MUNGER_TOKENIZER $owner
   * @param string $data
   */
  public function __construct($owner, $data)
  {
    $this->_owner = $owner;
    $this->_input = $data;
  }

  /**
   * The raw text contents of the token.
   * If this is a tag, the open and close characters have already been stripped. Use
   * {@link MUNGER_TOKEN::as_tag()} to retrieve the entire tag again.
   * @var string
   */
  public function data()
  {
    if (!isset ($this->_data))
    {
      $this->_data = substr($this->_input, $this->_first, $this->size);
    }
    
    return $this->_data;
  }

  /**
   * Name of the tag.
   * Returns non-empty only if {@link MUNGER_TOKEN::$type} is {@link Munger_token_start_tag}
   * or {@link Munger_token_end_tag}
   * @return string
   */
  public function name()
  {
    if (!isset ($this->_name))
    {
      if ($this->is_start_tag())
      {
        $this->_name = strtok($this->tag_data(), " \t\n");
      }

      if ($this->is_end_tag())
      {
        $this->_name = $this->tag_data();
      }
    }

    if (isset($this->_name))
    { 
      return strtolower($this->_name);
    }
    
    return '';
  }

  /**
   * List of attributes found in the tag.
   * The default implementation parses out XML-style attributes, but this can be overridden.
   * Attributes are expected in key="value" format, seperated by white-space.
   * @return ARRAY[string, string]
   */
  public function attributes()
  {
    $Result = array ();

    if ($this->is_start_tag())
    {
      $name = $this->name();
      $name_len = strlen($name);

      if ($name_len != $this->size)
      {
        /* There is more than just the name in this tag, so use the remainder as
           the attributes. */

        $attrs_data = substr($this->tag_data(), $name_len, $this->size - $name_len);

        /* Now, we assume that the attributes are well-formed and XML-compliant. If
           they aren't, the behavior is undefined, but will return a list of *some*
           attributes or other. */

        /* Split the data at the double-quote mark. This creates a list of items like this:

           If the attributes are:

          a="one"  b="two"

          then we get:

          [ a=]
          [one]
          [ b=]
          [two]
        */

        $attrs = explode('"', $attrs_data);

        /* Iterate the list, treating every even entry as an attribute name and every
           odd entry as an attribute value. Only iterate if there are at least two
           entries and trim all white space from around each attribute name. If the item
           is an attribute name, trim the '=' sign from the right side as well. This makes
           sure that the '=' sign is removed first, then whitespace is trimmed, so that
           [attr   ="value"] is properly trimmed to [attr] instead of [attr   ]. */

        $num_attrs = sizeof($attrs);
        $attr_name = ''; // Compiler warning

        if ($num_attrs > 1)
        {
          $attr_idx = 0;
          while ($attr_idx < $num_attrs)
          {
            if ($attr_idx % 2 == 0)
            {
              $attr_name = trim(substr($attrs[$attr_idx], 0, -1));
            }
            else
            {
              $Result[$attr_name] = $attrs[$attr_idx];
            }

            $attr_idx += 1;
          }
        }
      }
    }

    return $Result;
  }

  /**
   * Return the tag without opening and closing characters.
   * This is used when extracting the name or attributes from a tag.
   * @return string
   */
  public function tag_data()
  {
    if (!isset ($this->_tag_data))
    {
      if ($this->is_start_tag())
      {
        $this->_tag_data = substr($this->_input, $this->_first + 1, $this->size - 2);
      }
      elseif ($this->is_end_tag()) 
      {
        $this->_tag_data = substr($this->_input, $this->_first + 2, $this->size - 3);
      }
    }

    return $this->_tag_data;
  }

  /**
   * @return boolean
   */
  public function is_start_tag()
  {
    return $this->type == Munger_token_start_tag;
  }

  /**
   * @return boolean
   */
  public function is_end_tag()
  {
    return $this->type == Munger_token_end_tag;
  }
  
  public function set_input($input)
  {
    $this->_input = $input;
  }

  /**
   * Update the token with new properties.
   * @param integer $first
   * @param integer $size
   * @param integer $type
   * @access private
   */
  public function set_properties($first, $size, $type)
  {
    $this->_data = null;
    $this->_tag_data = null;
    $this->_name = null;
    $this->_first = $first;
    $this->size = $size;
    $this->type = $type;
  }

  /**
   * Resize the referenced data buffer.
   * @param integer $size
   * @param integer $type
   * @access private
   */
  public function resize($size, $type)
  {
    $this->_data = null;
    $this->_tag_data = null;
    $this->_name = null;
    $this->size = $size;
    $this->type = $type;
  }

  /**
   * Is this the matching tag for this token?
   * @param MUNGER_TOKEN $token
   * @return boolean
   */
  public function matches($token)
  {
    return $token->name() == $this->name();
  }

  /**
   * Generator of this token.
   * @var MUNGER_TOKENIZER
   * @access private
   */
  protected $_owner;

  /**
   * Source string from which the token pulls its content.
   * This is a reference to the input string in the {@link MUNGER_TOKENIZER}.
   * Used with {@link MUNGER_TOKEN::$_first} and {@link MUNGER_TOKEN::$size} to generate
   * {@link MUNGER_TOKEN::data()}.
   * @var string
   * @access private
   */
  protected $_input;

  /**
   * Locally cached reference to the data.
   * If the data is generated internally (to retrieve the name or attributes), it is cached
   * here to avoid repeated calls to 'substr'.
   * @var string
   * @access private
   */
  protected $_data;

  /**
   * Locally cached reference to the tag data.
   * If the data is generated internally (to retrieve the name or attributes), it is cached
   * here to avoid repeated calls to 'substr'.
   * @var string
   * @access private
   */
  protected $_tag_data;

  /**
   * Locally cached reference to the name.
   * This avoids searching the string multiple times for a name.
   * @var string
   * @access private
   */
  protected $_name;

  /**
   * Index of first character for token in {@link MUNGER_TOKEN::$_input}.
   * @var integer
   * @access private
   */
  protected $_first = 0;
}

/**
 * Processes a string into tag and text tokens.
 * Tags are assumes to have a single start character, which is followed by an identifier
 * composed of letters, followed by a single end character or whitespace. The default tag
 * style is an XML tag, but the key characters can be changed. A single token is either a
 * string of text or a tag delineated by the start and end characters. If no identifier is
 * found after the start tag character, it is assumed to be part of the content and is
 * treated as text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_TOKENIZER extends RAISABLE
{
  /**
   * Character used to open a tag.
   * @var character
   */
  public $open_tag_char = '<';

  /**
   * Character used to close a tag.
   * @var character
   */
  public $close_tag_char = '>';

  /**
   * Character used to indicate a closing tag.
   * @var character
   */
  public $end_tag_char = '/';

  public function __construct()
  {
    $this->_current_token = $this->_make_token();
  }

  /**
   * Initialize with the input string.
   * @param string $input
   */
  public function set_input($input)
  {
    $this->_input = $input;
    $this->_current_token->set_input($input);
    $this->_pos = 0;
    $this->_start_of_text_block = 0;
    $this->_size = strlen($input);
  }

  /**
   * Are there any tokens left to process?
   * @return boolean
   */
  public function tokens_available()
  {
    return $this->_pos !== false;
  }

  /**
   * Contents of last token read with {@link MUNGER_TOKENIZER::read_next_token()}.
   * @return MUNGER_TOKEN
   */
  public function current_token()
  {
    return $this->_current_token;
  }

  /**
   * Load the next token into {@link MUNGER_TOKENIZER::current_token()}.
   */
  public function read_next_token()
  {
    // find the next open tag character in the input stream.

    $this->_pos = strpos($this->_input, $this->open_tag_char, $this->_pos);

    if ($this->_pos !== false)
    {
      // if this isn't the last character in the input

      if ($this->_pos < $this->_size - 1)
      {
        $first_tag_char = $this->_input[$this->_pos + 1];

        if ($this->_is_tag_character($first_tag_char))
        {
          $end_of_tag = strpos($this->_input, $this->close_tag_char, $this->_pos + 1);

          if ($end_of_tag !== false)
          {
            // We have a definite whole tag coming up in the input stream. If
            // the current text block is non-empty, set that as the next token,
            // otherwise, extract the token.

            if ($this->_pos != $this->_start_of_text_block)
            {
              $this->_set_current_token($this->_start_of_text_block, $this->_pos - $this->_start_of_text_block, Munger_token_text);

              $this->_start_of_text_block = $this->_pos;
            }
            else
            {
              if ($first_tag_char == $this->end_tag_char)
              {
                $type = Munger_token_end_tag;
              }
              else
              {
                $type = Munger_token_start_tag;
              }

              $this->_set_current_token($this->_pos, $end_of_tag - $this->_pos + 1, $type);

              // If there is more text remaining, go to the next character
              // and mark the start of the next text block

              if ($end_of_tag < ($this->_size - 1))
              {
                $this->_pos = $end_of_tag +1;
                $this->_start_of_text_block = $this->_pos;
              }
              else
              {
                $this->abort();
              }
            }
          }
          else // no close tag found
          {
            $this->_set_last_token();
          }
        }
        else
        {
          if ($first_tag_char == $this->open_tag_char)
          {
            if ($this->_pos != $this->_start_of_text_block)
            {
              $this->_set_current_token($this->_start_of_text_block, $this->_pos - $this->_start_of_text_block, Munger_token_text);

              $this->_start_of_text_block = $this->_pos;
            }
            else
            {
              $this->_pos += 1;
              $this->_set_current_token($this->_pos, 1, Munger_token_text);
              $this->_pos += 1;
              $this->_start_of_text_block = $this->_pos;
            }
          }
          else
          {
            $this->_pos += 1;
            $this->read_next_token();
          }
        }
      }
      else // open tag was final character
      {
        $this->_set_last_token();
      }
    }
    else // no more tags
    {
      $this->_set_last_token();
    }
  }

  /**
   * Sets state so that {@link MUNGER_TOKENIZER::tokens_available()} is False.
   */
  public function abort()
  {
    $this->_pos = false;
  }

  /**
   * Creates a token from the remaining string and aborts.
   * @access private
   */
  protected function _set_last_token()
  {
    $this->_set_current_token($this->_start_of_text_block, $this->_size - $this->_start_of_text_block, Munger_token_text);
    $this->abort();
  }

  /**
   * Is this a valid tag character?
   * @param character $char
   * @return boolean
   */
  protected function _is_tag_character($char)
  {
    return preg_match('&[/a-zA-Z_]&', $char);
  }

  /**
   * Create a token.
   * Only called once during construction. Properties are updated as each new token
   * is detected.
   * @return MUNGER_TOKEN
   * @access private
   */
  protected function _make_token()
  {
    return new MUNGER_TOKEN($this, $this->_input);
  }

  /**
   * Set the current token.
   * Called internally from {@link MUNGER_TOKENIZER::read_next_token()}
   * @param $first
   * @param $count
   * @param $type
   * @access private
   */
  protected function _set_current_token($first, $count, $type)
  {
    $this->_current_token->set_properties($first, $count, $type);
  }

  /**
   * The string being consumed.
   * @var string
   * @access private
   */
  protected $_input;

  /**
   * Position within the {@link MUNGER_TOKENIZER::$_input}.
   * @var integer
   * @access private
   */
  protected $_pos = 0;

  /**
   * Length of {@link MUNGER_TOKENIZER::$_input}.
   * @var integer
   * @access private
   */
  protected $_size = 0;

  /**
   * Position of first character of current text-block.
   * @var integer
   * @access private
   */
  protected $_start_of_text_block = 0;

  /**
   * Reference to the current token.
   * @var MUNGER_TOKEN
   * @access private
   */
  protected $_current_token;
}

/**
 * Base class for all munger utilities.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.3.0
 * @since 2.7.1
 */
class MUNGER_TOOL extends RAISABLE
{
}

/**
 * Converts tag text to an output format.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.3.0
 * @since 2.4.0
 * @abstract
 */
abstract class MUNGER_REPLACER extends MUNGER_TOOL
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   * @abstract
   */
  public abstract function transform($munger, $token);
}

/**
 * Converts raw text to an output format.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.3.0
 * @since 2.7.0
 * @abstract
 */
abstract class MUNGER_CONVERTER extends MUNGER_TOOL
{
  /**
   * @var boolean
   */
  public $enabled = true;

  /**
   * Convert the text to an output format.
   * Calls {@link _convert()} if {@link $enabled} is <c>True</c>.
   * @param MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   */
  public function convert($munger, $text)
  {
    if ($this->enabled)
    {
      return $this->_convert($munger, $text);
    }
    
    return $text;
  }
  
  /**
   * Reset the converter to its default values.
   * Called by the munger to make sure that the converters don't maintain state from previous transformations.
   */
  public function reset()
  {
    if (!isset($this->_initial_enabled))
    {
      $this->_initial_enabled = $this->enabled;
    }
    
    $this->enabled = $this->_initial_enabled;
  }

  /**
   * Convert the text to an output format.
   * @param MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _convert($munger, $text);
  
  protected $_initial_enabled;
}

/**
 * Converts output text based on the {@link MUNGER::$convert_mode} flag.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.3.0
 * @since 2.7.1
 */
class MUNGER_HTML_CONVERTER extends MUNGER_CONVERTER
{
  /**
   * Convert the text to an output format.
   * @param MUNGER $munger The conversion context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _convert($munger, $text)
  {
    switch ($munger->convert_mode)
    {
      case Munger_convert_html_simple:
        return htmlspecialchars($text, ENT_NOQUOTES);
      case Munger_convert_html_strict:
        $options = global_text_options();
        return $options->convert_to_html_entities($text);
      case Munger_convert_plain_text:
        return $text;
      default :
        return $text;
    }
  }
}

/**
 * Converts a tag token to text, ignoring all attributes.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_BASIC_REPLACER extends MUNGER_REPLACER
{
  /**
   * @param string $start_tag
   * @param string $end_tag
   */
  public function __construct($start_tag, $end_tag)
  {
    $this->_start_tag = $start_tag;
    $this->_end_tag = $end_tag;
  }

  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform($munger, $token)
  {
    if ($token->is_start_tag())
    {
      return $this->_start_tag;
    }

    return $this->_end_tag;
  }

  /**
   * @var string
   * @access private
   */
  protected $_start_tag;
  protected $_end_tag;
}

/**
 * Processes page tags within text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_PAGE_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform($munger, $token)
  {
    if ($token->is_start_tag())
    {
      $attrs = $token->attributes();
      $title = read_array_index($attrs, 'title');
      $munger->_start_page($title);
    }

    return '';
  }
}

/**
 * Applies commands to the formatter as it is formatting.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.1
 * @access private
 */
class MUNGER_MACRO_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @return string
   */
  public function transform($munger, $token)
  {
    if ($token->is_start_tag())
    {
      $attrs = $token->attributes();
      $convert = read_array_index($attrs, 'convert');
      $flags = explode(',', $convert);
      if (is_array($flags))
      {
        $converters = $munger->converters();
        foreach ($flags as $flag)
        {
          if (substr($flag, 0, 1) == '-')
          {
            $flag = substr($flag, 1);
            $enabled = false;
          }
          elseif (substr($flag, 0, 1) == '+')
          {
            $flag = substr($flag, 1);
            $enabled = true;
          } 
          else
          {
            $enabled = true;
          }

          switch ($flag)
          {
            case 'all':
            {
              foreach ($converters as $name => &$converter)
              {
                if ($name != 'tags')
                {
                  $converter->enabled = $enabled;
                }
              }
              
              break;
            }
            default:
            {
              if (isset ($converters[$flag]))
              {
                $converters[$flag]->enabled = $enabled;
              }
            }
          }
        }
      }
    }

    return '';
  }
}

/**
 * Adds a link to a footnote, numbering automatically.
 * Works together with {@link MUNGER_FOOTNOTE_TEXT_REPLACER} to process
 * footnotes in text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 * @abstract
 */
abstract class MUNGER_FOOTNOTE_REFERENCE_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token The token being processed; cannot be null.
   * @return string
   */
  public function transform($munger, $token)
  {
    if ($token->is_start_tag())
    {
      $munger->inc_footnote_references();
      $info = $munger->current_reference_footnote_info();
      return $this->_format_reference($munger, $token, $info);
    }
    
    return '';
  }

  /**
   * Format the reference to the given footnote number.
   * 
   * @param MUNGER $munger The munger that generated the call; cannot be null.
   * @param MUNGER_TOKEN $token The token being processed; cannot be null.
   * @param MUNGER_FOOTNOTE_INFO $info The footnote to format; cannot be null.
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _format_reference($munger, $token, $info);
}

/**
 * Links a block of text to a previous footnote reference.
 * Works together with {@link MUNGER_FOOTNOTE_REFERENCE_REPLACER} to process
 * footnotes in text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.0
 * @access private
 * @abstract
 */
abstract class MUNGER_FOOTNOTE_TEXT_REPLACER extends MUNGER_REPLACER
{
  /**
   * Convert the given token to the output format.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token The token that triggered the transformation.
   * @return string
   */
  public function transform($munger, $token)
  {
    if ($token->is_start_tag())
    {
      $munger->inc_footnote_texts();
    }
    
    $info = $munger->current_text_footnote_info();
    
    return $this->_format_text($munger, $token, $info);
  }

  /**
   * Format the text for the given footnote number.
   * 
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token The token that triggered the transformation.
   * @param MUNGER_FOOTNOTE_INFO $info
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _format_text($munger, $token, $info);
}

/**
 * Controls generation of a text stream in a tag context.
 * @package webcore
 * @subpackage text
 * @access private
 * @version 3.3.0
 * @since 2.4.0
 * @abstract
 */
abstract class MUNGER_TRANSFORMER extends MUNGER_TOOL
{
  /**
   * Returns transformed content.
   * 
   * Guarantees that all content added with {@link add_text()} or {@link add_transformer()} is
   * transformed and merged correctly.
   * 
   * @param MUNGER $munger The data context.
   * @return string
   */
  public function data($munger)
  {
    if ($this->_buffer_state != Munger_only_data_block)
    {
      $this->_buffer_state = Munger_last_data_block;
    }

    $this->_process_raw_text($munger);

    return $this->_processed_text;
  }

  /**
   * Add un-transformed text.
   * @param string $data
   */
  public function add_text($data)
  {
    $this->_raw_text .= $data;
  }
  
  /**
   * Return the un-transformed text.
   *
   * @return string
   */
  public function raw_text()
  {
    return $this->_raw_text;
  }

  /**
   * Add the contents of another transformer.
   * Any untransformed text has to be transformed, then this object's contents are added to the
   * internal transformed buffer. The 'start_text' and 'end_text' are the tags that generated the
   * nested transformer. They are to be added to the current transformer and are not to be transformed
   * with the transformer they spawned. That's why they are added here, before and after the results
   * of 'transformer'.
   * @param MUNGER_TRANSFORMER $transformer
   * @param string $start_text
   * @param string $end_text
   */
  public function add_transformer($munger, $transformer, $start_text, $end_text)
  {
    if ($this->_buffer_state == Munger_only_data_block)
    {
      $this->_buffer_state = Munger_first_data_block;
    }
    else
    {
      $this->_buffer_state = Munger_middle_data_block;
    }

    $this->_process_raw_text($munger);
    
    $data = $transformer->data($munger);
    $new_text = $this->_transformer_text($munger, $start_text . $data . $end_text);
    $this->_processed_text = $this->_merge_text ($this->_processed_text, $new_text);
  }

  /**
   * Clear all the processed and unprocessed content.
   */
  public function clear()
  {
    $this->_processed_text = '';
    $this->_raw_text = '';
    $this->_buffer_state = Munger_only_data_block;
  }

  /**
   * Set this as the active or inactive transformer.
   * Descendent classes can use this to perform necessary processing when they are pushed or
   * popped from the transformation stack.
   * @param MUNGER $munger The activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate($munger, $value, $token)
  {
    $this->clear ();
  }

  /**
   * Transform any unprocessed text.
   * Processes text added with {@link add_text()} since the last time this function was called.
   * @param MUNGER $munger The processing context.
   * @access private
   */
  protected function _process_raw_text($munger)
  {
    if ($this->_raw_text)
    {
      $this->_processed_text = $this->_merge_text ($this->_processed_text, $this->_apply_transform($munger, $this->_raw_text));
      $this->_raw_text = '';
    }
  }
  
  /**
   * Add the new text to the existing text.
   * 
   * Override this function in order to elide or introduce newlines where needed.
   *
   * @param string $existing_text
   * @param string $new_text
   * @return string
   * @access private
   */
  protected function _merge_text($existing_text, $new_text)
  {
    return $existing_text . $new_text;
  }

  /**
   * Post-process text generated from another transformer.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @access private
   */
  protected function _transformer_text($munger, $text)
  {
    return $text;
  }

  /**
   * Transform raw text.
   * @access private
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @abstract
   */
  protected abstract function _apply_transform($munger, $text);

  /**
   * Buffer of unprocessed text.
   * The transformer adds all text to this buffer and will only render it to
   * {@link MUNGER_TRANSFORMER::$_processed_text} when another transformer's text
   * is added or when {@link MUNGER_TRANSFORMER::data()} is requested.
   * @var string
   * @access private
   */
  protected $_raw_text = '';

  /**
   * Buffer holding already-processed text.
   * Text processed by {@link MUNGER_TRANSFORMER::_apply_transform()} is added to this buffer.
   * @var string
   * @access private
   */
  protected $_processed_text = '';

  /**
   * Tracks the internal state of the processed buffer.
   * Descendents can use this to determine how to respond depending on how many pieces of data there are.
   * @var integer
   * @access private
   */
  protected $_buffer_state = Munger_only_data_block;
}

/**
 * Performs no conversion on text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_NOP_TRANSFORMER extends MUNGER_TRANSFORMER
{
  /**
   * Transform raw text.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_transform($munger, $text)
  {
    return $text;
  }
}

/**
 * Strips at most one leading and one trailing newline.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 3.0.0
 * @access private
 */
class MUNGER_PREFORMATTED_TRANSFORMER extends MUNGER_TRANSFORMER
{
  /**
   * Transform raw text.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_transform($munger, $text)
  {
    switch ($this->_buffer_state)
    {
    case Munger_first_data_block:
      if ($text)
      {
        $first_char = $text[0];
        if ($first_char == "\n")
        {
          $text = substr($text, 1);
        }
      }
      break;

    case Munger_only_data_block:
      if ($text)
      {
        $first_char = $text[0];
        if ($first_char == "\n")
        {
          $text = substr($text, 1);
        }
      }

      if ($text)
      {
        $last_char = substr($text, -1);
        if ($last_char == "\n")
        {
          $text = substr($text, 0, -1);
        }
      }
      break;

    case Munger_last_data_block:
      if ($text)
      {
        $last_char = substr($text, -1);
        if ($last_char == "\n")
        {
          $text = substr($text, 0, -1);
        }
      }
      break;
    }

    return $text;
  }
}

/**
 * Converts newlines for blocks (paragraphs, etc.)
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 * @access private
 * @abstract
 */
abstract class MUNGER_BLOCK_TRANSFORMER extends MUNGER_TRANSFORMER
{
  /**
   * Should every newline be used?
   * 
   * If False, if the first or last character in a block is a newline, it is assumed to be
   * there for spacing away from the tag and is dropped.
   * 
   * @var boolean
   */
  public $strict_newlines = false;

  /**
   * The default style to use when no quote style is otherwise specified.
   *
   * @var string
   */
  public $default_quote_style = Munger_quote_style_default;

  /**
   * Remove newlines according to tagging rules.
   * 
   * This layouter does its best to generate conforming text. It tracks the state of the buffer
   * within this block and applies formatting for newlines appropriately. Text is treated differently
   * depending on which other structures the block contains. If the text is a single line in the block,
   * then paragraph tags are only generated if desired (force_paragraphs is on). If the text is the first
   * in a series of text and nested blocks, it formats its newlines differently and will generate paragraph
   * tags.
   * 
   * @param string $text
   * @return string
   * @abstract
   */
  protected abstract function _trim($text);

  /**
   * Read the quote style from a token.
   * @param boolean $value
   * @param MUNGER_TOKEN $token Token that caused the activation.
   * @return string
   * @access private
   */
  protected function _quote_style_from_token($value, $token)
  {
    $Result = '';
    if ($value)
    {
      $attrs = $token->attributes();
      $Result = read_array_index($attrs, 'quote_style');
      if (!$Result)
      {
        $Result = $this->default_quote_style;
      }
    }

    return $Result;
  }

  /**
   * Transform given newlines to HTML boxes.
   * @param string $text
   * @return string
   * @access private
   */
  protected function _apply_quotes($text, $quote_style, $open_quote, $close_quote)
  {
    switch ($quote_style)
    {
      case Munger_quote_style_default:
      {
        $text = str_replace("\n\n", "\n\n" . $open_quote, $text);
        return $open_quote . $text . $close_quote;
      }
      case Munger_quote_style_multiple:
      {
        $text = str_replace("\n\n", $close_quote . "\n\n" . $open_quote, $text);
        return $open_quote . $text . $close_quote;
      }
      case Munger_quote_style_single:
      {
        switch ($this->_buffer_state)
        {
          case Munger_first_data_block:
            return $open_quote . $text;
          case Munger_only_data_block:
            return $open_quote . $text . $close_quote;
          case Munger_middle_data_block:
            return $text;
          case Munger_last_data_block:
            return $text . $close_quote;
        }
      }
      default:
        return $text;
    }
  }
}

/**
 * Provides default cleanup for list contents.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 * @abstract
 */
abstract class MUNGER_LIST_TRANSFORMER extends MUNGER_TRANSFORMER
{
  /**
   * Prepare buffer for transformation to a list.
   * The first and last newlines in a list are ignored. Newlines abutting formatting blocks within the list
   * are maintained.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @return string
   */
  protected function _apply_transform($munger, $text)
  {
    /* Eat any spaces and tabs at the end so we can check if the item ends in a newline. */
    $text = rtrim($text, " \t");

    $item_was_open = $this->_item_is_open;

    /* An item is open if it is not the last item and if it does not end in a newline. */
    $this->_item_is_open = ((($this->_buffer_state == Munger_first_data_block) || ($this->_buffer_state == Munger_middle_data_block)) && (substr($text, -1) != "\n"));

    $len = strlen($text);
    $first_char = 0;
    $num_chars = $len;

    if ($text[0] == "\n")
    {
      $first_char = 1;
    }
    if ($text[$len -1] == "\n")
    {
      $num_chars = -1;
    }

    if (($first_char > 0) || ($num_chars != $len))
    {
      $text = substr($text, $first_char, $num_chars);
    }
    
    if ($text)
    {
      $text = $this->_transform_to_list($munger, $text, $item_was_open);
    }

    return $text;
  }

  /**
   * Transform preprocessed text into a list.
   * The 'item_was_open' parameter indicates whether a list item has already been started. If this is
   * the case, then this list should be generated as a continuation of that one, rather than starting
   * a new one. In the HTML renderer, this means that a new list-item should not be started in that case.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @param boolean $item_was_open
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _transform_to_list($munger, $text, $item_was_open);

  /**
   * Indicates whether an item has been left 'open' in this list.
   * This means that a block of raw text was processed, but the last item was left 'open', so that
   * the list item continues after the intervening transformation block is added, rather than having
   * two separate lists on each side of the block.
   *
   * This is two lists without an 'open' item in the first list:
   *
   * 1. Item 1
   *   1. Item 2
   *   2. Item 3
   * 2. Item 4
   * 3. Item 5
   *
   * This is two lists with an 'open' item in the first list:
   *
   * 1. Item 1
   *   1. Item 2
   *   2. Item 3
   *    Item 4
   * 2. Item 5
   *
   * Note how the item started by 'Item 1' continues after the nested list.
   * @var boolean
   * @access private
   */
  protected $_item_is_open = false;
}

/**
 * Base class for generating definition lists.
 * Generates alternating defition terms and definitions for newlines in the text.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.6.0
 * @access private
 * @abstract
 */
abstract class MUNGER_DEFINITION_LIST_TRANSFORMER extends MUNGER_LIST_TRANSFORMER
{
  /**
   * Set this as the active or inactive transformer.
   * @param MUNGER $munger The activation context.
   * @param boolean $value True if the transformer is being activated.
   * @param MUNGER_TOKEN $token Token that caused the activation.
   */
  public function activate($munger, $value, $token)
  {
    if ($value)
    {
      $this->_term_needed = true;
    }
  }

  /**
   * Post-process text generated from another transformer.
   * @param MUNGER $munger The transformation context.
   * @param string
   * @access private
   */
  protected function _transformer_text($munger, $text)
  {
    return $this->_build_definition_part($munger, $text);
  }

  /**
   * Transform preprocessed text into a list.
   * Calls {@link _build_definition_part()} for each newline-delimited piece of text.
   * @param MUNGER $munger The transformation context.
   * @param string $text
   * @param boolean $item_was_open
   * @return string
   * @access private
   */
  protected function _transform_to_list($munger, $text, $item_was_open)
  {
    $Result = '';
    $text = ltrim($text, " \t");
    $lines = explode("\n", $text);

    foreach ($lines as $line)
    {
      $Result .= $this->_build_definition_part($munger, ltrim($line));
    }

    return $Result;
  }

  /**
   * Transform to a term or body.
   * Uses {@link _build_as_definition_term()} and {@link _build_as_definition_body()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @access private
   */
  protected function _build_definition_part($munger, $line)
  {
    if ($this->_term_needed)
    {
      $Result = $this->_build_as_definition_term($munger, $line);
    }
    else
    {
      $Result = $this->_build_as_definition_body($munger, $line);
    }

    $this->_term_needed = !$this->_term_needed;

    return $Result;
  }

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @access private
   * @abstract
   */
  protected abstract function _build_as_definition_term($munger, $line);

  /**
   * Transform to a term or body.
   * Called from {@link _build_definition_part()}.
   * @param MUNGER $munger The transformation context.
   * @param string $line
   * @access private
   * @abstract
   */
  protected abstract function _build_as_definition_body($munger, $line);

  /**
   * @var boolean
   * @access private
   */
  protected $_term_needed;
}

/**
 * Represents a tag found in the input stream.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 * @access private
 */
class MUNGER_TAG
{
  /**
   * Token for the tag.
   * @var MUNGER_TOKEN
   */
  public $token;

  /**
   * Converted text for the tag.
   * This text is prepared beforehand so that tags that can be nested are
   * replaced in the order that they are found (e.g. header tags). This also
   * ensures consistent handling for tags with and without transformers.
   */
  public $text;

  /**
   * Current transformer when this token was found.
   * If the tag forces a transformer change, this holds the previous one so that it can
   * be restored when the end tag for this tag is found.
   * @var MUNGER_TRANSFORMER
   */
  public $transformer;

  /**
   * @param MUNGER_TOKEN $token
   * @param MUNGER_TRANSFORMER $transformer
   */
  public function __construct($token, $transformer, $text)
  {
    $this->token = clone($token);
    $this->transformer = $transformer;
    $this->text = $text;
  }
}

/**
 * Implements a tag-seeking and processing algorithm.
 * This is a simple parser that uses the {@link MUNGER_TOKENIZER} to process text. It defines the basic
 * iteration for {@link MUNGER} and {@link MUNGER_VALIDATOR}
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class MUNGER_PARSER extends RAISABLE
{
  public function __construct()
  {
  }

  /**
   * Apply the tag-seeking algorithm to 'input'.
   * @var string $input
   * @access private
   */
  protected function _process($input)
  {
    $this->_nesting_level = 0;
    $prepared_input = $this->_prepare_input($input);
    $this->_process_current_nesting_level($prepared_input);
  }

  protected function _process_current_nesting_level($input)
  {
    if ($this->_nesting_level == sizeof($this->_tokenizers))
    {
      $this->_tokenizers[] = $this->_make_tokenizer();
    }
    
    $this->_process_given_tokenizer($input, $this->_tokenizers[$this->_nesting_level]);
  }

  /**
   * Apply the tag-seeking algorithm to 'input'.
   * @var string $input
   * @access private
   */
  protected function _process_given_tokenizer($input, $tokenizer)
  {
    $tokenizer->set_input($input);
    while ($tokenizer->tokens_available())
    {
      $tokenizer->read_next_token();
      $token = $tokenizer->current_token();
      $this->_process_token($token);
    }
  }

  /**
   * Consume the given token.
   * Called from {@link _process()} for each token found by the {@link
   * $_tokenizer}.
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _process_token($token)
  {
    if (($token->type == Munger_token_text))
    {
      $this->_transform_as_text($token);
    }
    elseif ($this->_treat_as_text($token))
    {
      /* If the tag should be displayed as text, process the initial tag
       * character, then try looking for tags again. This allows for
       * nested tags as found in highlighted HTML samples.
       */
      $text = substr($token->data(), 1);
      $token->resize(1, Munger_token_text);
      $this->_transform_as_text($token);
      $this->_nesting_level += 1;
      $this->_process_current_nesting_level($text);
      $this->_nesting_level -= 1;
    }
    elseif ($token->is_start_tag()) 
    {
      $this->_transform_as_start_tag($token);
    }
    else
    {
      $this->_transform_as_end_tag($token);
    }
  }

  /**
   * @return MUNGER_TOKENIZER
   */
  public function  _current_tokenizer()
  {
    return $this->_tokenizers[$this->_nesting_level];
  }

  /**
   * Perform pre-processing on the input to consume.
   * Called from {@link _process()} for before initializing the {@link
   * $_tokenizer}.
   * @var string $input
   * @access private
   */
  protected function _prepare_input($input)
  {
    return str_replace("\r", "", $input);
  }

  /**
   * Should this tag be converted to text?
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _treat_as_text($token)
  {
    return false;
  }

  /**
   * Transform a token as text.
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_text($token)
  {
  }

  /**
   * Transform a start tag token.
   * This is called when a tag is not automatically treated as text. Use this function to either
   * strip unwanted tags, to convert tags into an appropriate output format, or to perform actions
   * triggered by certain tags (like changing formatters, etc.).
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_start_tag($token)
  {
  }

  /**
   * Transform an end tag token.
   * This is called when a tag is not automatically treated as text. Use this function to either
   * strip unwanted tags, to convert tags into an appropriate output format, or to perform actions
   * triggered by certain tags (like changing formatters, etc.).
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_end_tag($token)
  {
  }

  /**
   * Create a tokenizer.
   * Only called once during construction.
   * @return MUNGER_TOKENIZER
   * @access private
   */
  protected function _make_tokenizer()
  {
    return new MUNGER_TOKENIZER();
  }

  /**
   * Retrieves tokens from the input stream.
   * @var array[MUNGER_TOKENIZER]
   * @access private
   */
  protected $_tokenizers;

  /**
   * The current nesting level of tokenization.
   * Provides support for detecting nested tags, as found in HTML samples.
   * Acts as an index into the array of tokenizers.
   * @var integer
   * @access private
   */
  protected $_nesting_level;
}

/**
 * Single page of output generated by the munger.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 * @access private
 */
class MUNGER_PAGE
{
  /**
   * Title of the page.
   * This is extracted from the page tag and may be empty.
   * @var string
   */
  public $title;

  /**
   * Contents of the page.
   * @var string
   */
  public $text;
}

/**
 * A text formatter for a tag-based format.
 * This is flexible parser for tag-based syntaxes; anything in an XML-style format, with open and
 * close tags, delineated by open and close characters for the tag data. The accepted tags, desired
 * text length, highlighted words and output format can all be customized.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.4.0
 */
class MUNGER extends MUNGER_PARSER
{
  /**
   * Return, at most, this many visible characters from a transformation.
   * The default of '0' returns the entire transformed input.
   * @var integer*/
  public $max_visible_output_chars = 0;

  /**
   * Allow break inside a word when truncating to {@link $max_visible_output_chars}?
   * If this is true, the algorithm will return at most 'max_visible_output_chars'. If this is
   * false, it may return more. The algorithm first scans back for a spacs, but if it doesn't
   * find one, it scans <em>forward</em> to find a space.
   * @var boolean
   */
  public $break_inside_word = false;

  /**
   * Take out all tags from the input?
   * If this is true, then only tags placed in the string by a replacer or munger will be left
   * in the output. All other tags will be stripped out. If this is false, then all other tags
   * are transformed to the destination output as text.
   * @var boolean
   */
  public $strip_unknown_tags = false;

  /**
   * Force paragraphs on all transformed text?
   * The munger will transform the text, replacing all newlines with output-specific version.
   * However, if this value is True, a paragraph will always be generated, regardless of whether
   * the content has a new line or not. This is useful for generating text that is a separate
   * block regardless of content. Note: if the input text is empty, no paragraphs are generated.
   * @var boolean
   */
  public $force_paragraphs = true;

  /**
   * Specify how normal text is to be transformed.
   * Most outputters will default to {@link Munger_html_conversion_none}, but
   * can use {@link Munger_html_format_simple} or {@link
   * Munger_html_format_strict} if the content is going to show up in an HTML
   * page anyway.
   *
   * This conversion takes place <i>before</i> any of the converters registered
   * with {@link register_converter()} are processed, so that entities
   * inserted by a converter are not then also converted.
   * @see Munger_convert_constants
   * @var string
   */
  public $convert_mode = Munger_convert_none;

  /**
   * Add an extension to replace 'name' tags.
   * @param string $name
   * @param MUNGER_REPLACER
   * @access private
   */
  public function register_replacer($name, $replacer, $has_end_tag = true)
  {
    $this->_replacers[strtolower($name)] = $replacer;
    $this->register_known_tag($name, $has_end_tag);
  }

  /**
   * Add an extension to control formatting within 'name' tags.
   * @param string $name
   * @param MUNGER_TRANSFORMER
   * @access private
   */
  public function register_transformer($name, $transformer)
  {
    $this->_transformers[strtolower($name)] = $transformer;
    $this->register_known_tag($name, true);
  }

  /**
   * Add an extension to convert raw text.
   * Use the name to uniquely identify and replace converters, if desired.
   * @param string $name
   * @param MUNGER_CONVERTER
   * @access private
   */
  public function register_converter($name, $converter)
  {
    $this->_converters[strtolower($name)] = $converter;
  }

  /**
   * Return the map of registered converters.
   * @see MUNGER_CONVERTER
   * @return array[string,MUNGER_CONVERTER]
   */
  public function converters()
  {
    return $this->_converters;
  }
  
  /**
   * Set a converter's enabled state, returning the previous state.
   *
   * @param string $name The name of the converter.
   * @param boolean $value The value to set.
   * @return boolean
   */
  public function set_converter_enabled($name, $value)
  {
    if (isset($this->_converters) && isset($this->_converters[$name]))
    {
      $Result = $this->_converters[$name]->enabled;
      $this->_converters[$name]->enabled = $value;
      
      return $Result;
    }
    
    return false;
  }

  /**
   * Add a tag that will be accepted by the munger.
   * The second parameter indicates whether the tag stands alone or whether it should
   * be pushed to the stack in anticipation of an end tag.
   * @param string $name
   * @param boolean $has_end_tag
   * @access private
   */
  public function register_known_tag($name, $has_end_tag)
  {
    $this->_known_tags[strtolower($name)] = $has_end_tag;
  }

  /**
   * Resolve the given address to a full url.
   * Override this function in order to perform post-processing on a URL before inserting it into
   * the text stream.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url($url, $root_override = null)
  {
    if ($url && isset ($this->_context_object))
    {
      return $this->_context_object->resolve_url($url, $root_override);
    }
    
    return $url;
  }

  /**
   * Get a unique id for the text being formatted.
   * Used to create fixed anchor names for footnotes that will also avoid naming
   * collisions when multiple objects are formatted into a page.
   * @return string
   */
  public function unique_id_for_context()
  {
    if (isset ($this->_context_object))
    {
      if (method_exists($this->_context_object, "unique_id"))
      {
        return $this->_context_object->unique_id();
      }
      
      return get_class($this->_context_object);
    }
    
    return rand();
  }

  /**
   * Get a context for the text being formatted.
   * Used to add scripts or stylesheets to the page.
   * @return CONTEXT
   */
  public function context_for_context()
  {
    if (isset ($this->_context_object))
    {
      return $this->_context_object->context;
    }
    
    return null;
  }

  /**
   * Indicate that a footnote reference was found in the text.
   * {@link MUNGER_FOOTNOTE_REFERENCE_REPLACER} calls this function when it is
   * triggered by an embedded tag. Use {@link current_footnote_name()} and
   * {@link current_footnote_number()} to retrieve information about footnotes.
   * @see inc_footnote_texts()
   */
  public function inc_footnote_references()
  {
    $this->_num_footnote_references += 1;
    $this->_add_footnote_if_needed($this->_num_footnote_references);
  }

  /**
   * Indicate that a footnote body was found in the text.
   * {@link MUNGER_FOOTNOTE_TEXT_REPLACER} calls this function when it is
   * triggered by an embedded tag. Use {@link current_footnote_name()} and
   * {@link current_footnote_number()} to retrieve information about footnotes.
   * @see inc_footnote_references()
   */
  public function inc_footnote_texts()
  {
    $this->_num_footnote_texts += 1;
    $this->_add_footnote_if_needed($this->_num_footnote_texts);
  }

  /**
   * The information to use for the current footnote reference.
   * Used {@link inc_footnote_references()} to guarantee unique names for
   * footnotes in a page.
   * @return MUNGER_FOOTNOTE_INFO
   */
  public function current_reference_footnote_info()
  {
    return $this->_footnote_infos[$this->_num_footnote_references - 1];
  }

  /**
   * The information to use for the current footnote reference.
   * Used {@link inc_footnote_references()} to guarantee unique names for
   * footnotes in a page.
   * @return MUNGER_FOOTNOTE_INFO
   */
  public function current_text_footnote_info()
  {
    return $this->_footnote_infos[$this->_num_footnote_texts - 1];
  }

  /**
   * The raw text buffer for the current transformation context.
   * Used by the smart quoting feature to determine the last character in the
   * context in order to determine whether to use a left or right quote.
   * @return string
   */
  public function current_raw_text()
  {
    return $this->_current_transformer->raw_text();
  }

  /**
   * Create an output string from the given input.
   * @param string $input
   * @param object $context_object Transformers use this context to resolve
   * links and other data.
   * @return string
   */
  public function transform($input, $context_object = null)
  {
    $this->_paginated = false;
    $this->_transform($input, $context_object);
    
    if (isset ($this->_pages[0]))
    {
      return $this->_pages[0]->text;
    }

    return '';
  }

  /**
   * Return a list of pages for the given text.
   * @param string $input
   * @param object $context_object Transformers use this context to resolve
   * links and other data.
   * @return string
   */
  public function transform_pages($input, $context_object = null)
  {
    $this->_paginated = true;
    $this->_transform($input, $context_object);
    return $this->_pages;
  }

  /**
   * Restore the munger to initial conditions.
   * Called from {@link _transform()} before processing text.
   * @access private
   */
  protected function _reset()
  {
    $this->_pages = array ();
    $this->_num_footnote_references = 0;
    $this->_num_footnote_texts = 0;
    $this->_footnote_infos = array ();
    $this->_current_visible_chars = 0;
    $this->_current_maximum_chars = 0;
    $this->_nesting_level = 0;
    $this->_current_transformer = $this->_default_transformer;
    
    foreach ($this->_converters as &$converter)
    {
      $converter->reset();
    }
  }

  /**
   * Render the input according to this object's settings.
   * Generates output as an array of strings representing pages.
   * @var string $input
   * @param object $context_object Transformers use this context to resolve
   * links and other data.
   * @return string
   */
  protected function _transform($input, $context_object = null)
  {
    $this->_reset();

    $this->_context_object = $context_object;
    if ($this->max_visible_output_chars)
    {
      $this->_current_maximum_chars = $this->max_visible_output_chars;
    }
    else
    {
      $this->_current_maximum_chars = strlen($input);
    }

    $this->_process($input);
    $this->_close_open_tags();
    $this->_finish_page();
  }

  /**
   * Should this tag be converted to text?
   * Return False from this function to transform the tag as non-text input. Return
   * True to transform it as text and add to the output stream.
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _treat_as_text($token)
  {
    return !isset ($this->_known_tags[$token->name()]) && !$this->strip_unknown_tags;
  }

  /**
   * Transform a token as text.
   * This will make sure to abort tokenization if {@link $max_visible_output_chars} has
   * been reached, truncating the text in the best way possible.
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_text($token)
  {
    $num_chars = $token->size;
    $text = $token->data();
    $truncated = ($num_chars + $this->_current_visible_chars) > $this->_current_maximum_chars;

    if ($truncated)
    {
      $num_chars = $this->_truncated_length($text, $num_chars);
      if ($num_chars != $token->size)
      {
        $text = substr($text, 0, $num_chars);
      }
    }

    $this->_add_text_to_output($this->_as_output_text($text));
    $this->_current_visible_chars += $num_chars;

    if ($truncated || (($this->_current_visible_chars >= $this->_current_maximum_chars) && $this->_tokenizers[$this->_nesting_level]->tokens_available()))
    {
      $this->_truncate();
    }
  }

  /**
   * Perform all necessary cleanup when truncated.
   * This action will unroll the tag stack and display an indicator to show that the
   * text was truncated. Tokenization is aborted.
   * @access private
   */
  protected function _truncate()
  {
    while ($this->_nesting_level >= 0)
    {
      $t = $this->_current_tokenizer();
      $t->abort();
      $this->_nesting_level -= 1;
    }
    $this->_nesting_level = 0;
    $this->_close_open_tags();
  }

  /**
   * A new page tag was found in the text.
   * This is ignored if the munger is not tracking pages.
   * @see _finish_page()
   * @param string $title
   * @access private
   */
  public function _start_page($title)
  {
    if ($this->_paginated)
    {
      $this->_finish_page();
      $page = new stdClass();
      $page->title = $title;
      $this->_pages[] = $page;
    }
  }

  /**
   * A close page tag was found in the text.
   * This is ignored if the munger is not tracking pages.
   * @see _start_page()
   * @param string $title
   * @access private
   */
  public function _finish_page()
  {
    $output = $this->_current_transformer->data($this);
    $this->_current_transformer->clear();
    if ($output)
    {
      if (sizeof($this->_pages))
      {
        $this->_pages[sizeof($this->_pages) - 1]->text = $output;
      }
      else
      {
        $page = new stdClass();
        $page->title = '';
        $page->text = $output;
        $this->_pages[] = $page;
      }
    }
  }
  
  /**
   * Applies all enabled conversions to the given "text".
   * 
   * @param string $text
   * @param array[string] $skip_names
   * @return string
   */
  public function apply_converters($text, $skip_names = array())
  {
    foreach ($this->_converters as $name => $converter)
    {
    	if (!in_array($name, $skip_names))
    	{
        $text = $converter->convert($this, $text);
    	}
    }
    
    return $text;
  }
  
  /**
   * Force output of open tags.
   * This is called when text is truncated or the transformation is complete to ensure well-formedness.
   */
  protected function _close_open_tags()
  {
    while (($tag = array_pop($this->_open_tags)))
    {
      $this->_transform_block($tag, $this->_end_tag_token_for($tag->token));
    }
  }

  /**
   * Generate a 'fake' end tag for this token.
   * This is used for generating end tags for tags left on the stack when text is truncated.
   * @param MUNGER_TOKEN $token
   * @return MUNGER_TOKEN
   * @access private
   */
  protected function _end_tag_token_for($token)
  {
    $t = $this->_current_tokenizer();
    $data = $t->open_tag_char . $t->end_tag_char . $token->name() . $t->close_tag_char;
    $Result = new MUNGER_TOKEN($t, $data);
    $Result->set_properties(0, strlen($data), Munger_token_end_tag);
    
    return $Result;
  }

  /**
   * Transform a start tag token.
   * This is called when a tag is not automatically treated as text. Use this function to either
   * strip unwanted tags, to convert tags into an appropriate output format, or to perform actions
   * triggered by certain tags (like changing formatters, etc.).
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_start_tag($token)
  {
    /* If the tag is not registered, then either add it as text, or strip it out,
     * depending on the 'strip_unknown_tags' setting. If it is registered,
     * determine what type of tag it is and react accordingly.
     */

    $name = $token->name();

    if (isset ($this->_known_tags[$name]))
    {
      if ($this->_known_tags[$name])
      {
        /* Assign the current tag's token and see if there is a special transformer
         * for content inside this tag. If there is, assign it as the current
         * transformer, otherwise indicate that the tag has no transformer and
         * add its text to the output.
         */

        if (isset ($this->_transformers[$name]))
        {
          array_push($this->_open_tags, new MUNGER_TAG($token, $this->_current_transformer, $this->_tag_as_text($token)));
          $this->_current_transformer = clone($this->_transformers[$name]);
          $this->_current_transformer->activate($this, true, $token);
        } 
        else
        {
          array_push($this->_open_tags, new MUNGER_TAG($token, null, ''));
          $this->_add_text_to_output($this->_tag_as_text($token));
        }
      } 
      else
      {
        $this->_add_text_to_output($this->_tag_as_text($token));
      }
    } 
    else
    {
      if (!$this->strip_unknown_tags)
      {
        $this->_add_text_to_output($this->_tag_as_text($token));
      }
    }
  }

  /**
   * Transform an end tag token.
   * This is called when a tag is not automatically treated as text. Use this function to either
   * strip unwanted tags, to convert tags into an appropriate output format, or to perform actions
   * triggered by certain tags (like changing formatters, etc.).
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_end_tag($token)
  {
    $name = $token->name();

    if (isset ($this->_known_tags[$name]))
    {
      /* If end tags are not expected, ignore this tag. */

      if ($this->_known_tags[$name])
      {
        do
        {
          /* Output end tags until the stack is empty or a match for the current end
           * tag is found. This algorithm guarantees that the tag output remains
           * well-formed.
           */

          $tag = array_pop($this->_open_tags);
          if (isset ($tag))
          {
            $matches = $tag->token->matches($token);
            if (!$matches)
            {
              $this->_transform_block($tag, $this->_end_tag_token_for($tag->token));
            }
            else
            {
              $this->_transform_block($tag, $token);
            }
          }
        } while (isset ($tag) && !$matches);
      }
    } 
    else
    {
      if (!$this->strip_unknown_tags)
      {
        $this->_add_text_to_output($this->_tag_as_text($token));
      }
    }
  }

  /**
   * Handle a completed tag block.
   * This is called once an end tag token is found for an open tag.
   * @param MUNGER_TAG $tag
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_block($tag, $token)
  {
    if (isset ($tag->transformer))
    {
      $tag->transformer->add_transformer($this, $this->_current_transformer, $tag->text, $this->_tag_as_text($token));
      $this->_current_transformer->activate($this, false, $token);
      $this->_current_transformer = $tag->transformer;
    } 
    else
    {
      $this->_add_text_to_output($this->_tag_as_text($token));
    }
  }

  /**
   * Truncate a piece of text according to white-space rules.
   * This function finds the most elegant break point in a string, attempting to satisfy as best
   * as possible the {@link $max_visible_output_chars} and {@link $break_inside_word}
   * rules. The maximum allowed characters can be exceeded if there is no whitespace in the text before
   * the optimal break. This avoids truncating texts to nothing when the maximum size is very small.
   * @param string $text
   * @param integer $num_chars Number of characters in the string.
   * @param integer $optimal_break Position of optimal break character in string.
   * @return integer
   * @access private
   */
  protected function _truncated_length($text, $num_chars)
  {
    $optimal_break = $this->_current_maximum_chars - $this->_current_visible_chars;

    // (1) If breaking inside a word is allowed, get the optimally-sized text.

    if ($this->break_inside_word)
    {
      $Result = $optimal_break;
    }
    else
    {
      // (2) If the optimal break sits on a white-space character, use it.

      if (strpos(" \t\n", $text[$optimal_break -1]) !== false)
      {
        $Result = $optimal_break -1;
      }
      else
      {
        $trunc_text = substr($text, 0, $optimal_break);

        /* (3) Search *back* from the desired break for white-space. Here we
         * reverse the string in order to use 'strtok'. This guarantees that the
         * search returns the first white-space. The other approach is to use
         * 'strrpos' three times (for ' ', '\t' and '\n') and use the smallest
         * value.
         */

        $rev_trunc_text = strrev($trunc_text);
        $word = strtok($rev_trunc_text, " \t\n");

        $trunc_len = strlen($trunc_text);
        $word_len = strlen($word);

        if ($word_len < $trunc_len)
        {
          $Result = $trunc_len - $word_len -1;
        }
        else
        {
          // (4) Search *forward* from the desired break for white-space.

          $space = strpos($text, ' ', $optimal_break);
          if ($space !== false)
          {
            $Result = $space;
          }
          else
          {
            // (5) No spaces found in text, so use the whole text.

            $Result = $num_chars;
          }
        }
      }
    }

    return $Result;
  }

  /**
   * Are there open, nested transformers?
   * @return boolean
   * @access private
   */
  protected function _using_default_transformer()
  {
    $Result = true;
    $tags = $this->_open_tags;
    while ($Result && ($tag = array_pop($tags)))
    {
      $Result = !isset ($tag->transformer);
    }
    return $Result;
  }

  /**
   * Add text to the current output buffer.
   * @param string $text
   * @access private
   */
  protected function _add_text_to_output($text)
  {
    $this->_current_transformer->add_text($text);
  }

  /**
   * Convert the given text to the output format.
   * This will strip or convert all tags to an escaped format.
   * @param string $text
   * @access private
   */
  protected function _as_output_text($text)
  {
    return $this->apply_converters($text);
  }

  /**
   * Return the output buffer.
   * Called from {@link transform()} to return the result of the transformation.
   * @return string
   * @access private
   */
  protected function _output()
  {
    return $this->_current_transformer->data($this);
  }

  /**
   * Replace a tag with its actual content.
   * If the tag is known, then this uses the {@link MUNGER_REPLACER}s list. If it is unknown, it
   * calls {@link _known_tag_as_text()}, which transforms non-replaced known tags.
   * @param MUNGER_TOKEN
   * @return string
   * @access private
   */
  protected function _tag_as_text($token)
  {
    $name = $token->name();
    if (isset ($this->_known_tags[$name]))
    {
      if (isset ($this->_replacers[$name]))
      {
        $rep = $this->_replacers[$name];
        
        return $rep->transform($this, $token);
      } 

      return $this->_known_tag_as_text($token);
    } 
    else
    {
      if (!$this->strip_unknown_tags)
      {
        return $token->data();
      }
    }
    
    return '';
  }

  /**
   * Replace a known tag with its actual content.
   * @param MUNGER_TOKEN
   * @return string
   * @access private
   */
  protected function _known_tag_as_text($token)
  {
    return $token->data();
  }

  /**
   * Create a footnote record if the number requires it.
   * @param integer $num
   * @access private
   */
  protected function _add_footnote_if_needed($num)
  {
    if ($num > sizeof($this->_footnote_infos))
    {
      $info = new stdClass();
      $n = 'footnote_' . $this->unique_id_for_context() . '_' . $num;
      $info->name_from = $n . '_ref';
      $info->name_to = $n . '_body';
      $info->number = $num;
      $this->_footnote_infos[] = $info;
    }
  }

  /**
   * List of tag replacers.
   * These transform tags into the correct output format.
   * @var array[MUNGER_REPLACER]
   * @see MUNGER_REPLACER
   * @access private
   */
  protected $_replacers;

  /**
   * List of text formatters.
   * These transform raw text into the correct output format; transformers are tied to tags so that
   * enclosing text in tags can trigger a different type of formatting. There are used to handle newlines
   * in different tag contexts.
   * @var array[MUNGER_TRANSFORMER]
   * @see MUNGER_TRANSFORMER
   * @access private
   */
  protected $_transformers;

  /**
   * List of content converters.
   * These convert a piece of non-tag text to a final format. All registered
   * converters are executed against every piece of non-tag text.
   * @see array[MUNGER_CONVERTER]
   * @access private
   */
  protected $_converters = array();

  /**
   * The first transformer for any piece of text.
   * @var MUNGER_TRANSFORMER
   * @access private
   */
  protected $_default_transformer;

  /**
   * @var array[string]
   * @access private
   */
  protected $_known_tags;

  /**
   * Stack of tags found in the input stream.
   * @var array[MUNGER_TAG]
   * @access private
   */
  protected $_open_tags = array ();

  /**
   * The current transformer during a transformation.
   * @var MUNGER_TRANSFORMER
   * @access private
   */
  protected $_current_transformer;

  /**
   * Number of visible (non-tag) characters in the transformed buffer.
   * @var integer
   * @access private
   */
  protected $_current_visible_chars;

  /**
   * Maximum number of allowed visible (non-tag) characters in the transformed buffer.
   * @var integer
   * @access private
   */
  protected $_current_maximum_chars;

  /**
   * Text is transformed for this object.
   * URL requests are piped to this object for final resolution. The object must define a
   * method called 'resolve_url', like {@link NAMED_OBJECT::resolve_url()}.
   * @var object
   * @access private
   */
  protected $_context_object;

  /**
   * Content is broken into pages at designated page breaks.
   * @var boolean
   * @access private
   */
  protected $_paginated = 0;

  /**
   * List of pages generated during the transformation.
   * If either {@link $_paginated} is False, there is always only one page.
   * @see MUNGER_PAGE
   * @var array[MUNGER_PAGE]
   * @access private
   */
  protected $_pages;

  /**
   * Current number of footnote references in the text.
   * Value is reset to 0 when {@link transform()} is called.
   * @var integer
   * @access private
   */
  protected $_num_footnote_references = 0;

  /**
   * Current number of footnote bodies in the text.
   * Value is reset to 0 when {@link transform()} is called.
   * @var integer
   * @access private
   */
  protected $_num_footnote_texts = 0;

  /**
   * Unique names for footnotes in the text.
   * Randomly generated during text transformation to ensure uniqueness within a
   * page. Access using {@link current_footnote_reference_name()} or {@link
   * current_footnote_text_name()}.
   * @var array[MUNGER_FOOTNOTE_INFO]
   * @access private
   */
  protected $_footnote_infos;
}

/**
 * Information about a foonote in a {@link MUNGER}.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.1
 */
class MUNGER_FOOTNOTE_INFO
{
  /** 
   * The unique identifier for the footnote description.
   * Used in the link to "jump to" the description from a reference.
   * 
   * @var string
   */
  public $name_to;

  /** 
   * The unique identifier for the footnote reference.
   * Used in the link to "jump back" to the reference from the description.
   *   
   * @var string
   */
  public $name_from;

  /** The number of the footnote.
   * 
   * @var integer
   */
  public $number;
}

/**
 * Utility class for handling regular-expressions.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class REGULAR_EXPRESSION
{
  /**
   * Apply a prefix and suffix to a set of words in the given text.
   * Used by the {@link HTML_MUNGER} to provide search term highlighting.
   * 
   * @param string $text Text to search.
   * @param string $phrase Space-separated list of words.
   * @param string $prefix Prepended to each found word.
   * @param string $suffix Appended to each found word.
   * @return string
   */
  public static function select_words($text, $phrase, $prefix, $suffix)
  {
    if ($phrase)
    {
      $preg_phrase = REGULAR_EXPRESSION::sanitize_words($phrase);

      /* Break into separate words and build a list of regular expressions. */

      $words = explode(' ', trim($preg_phrase));
      foreach ($words as $word)
      {
        $preg_words[] = "/(^|[^A-Za-z0-9])($word)([^A-Za-z0-9]|$)/i";
      }

      /* Replace the words, passing in the array so that the regexp engine in PHP
         is only initialized once. */

      return preg_replace($preg_words, "\\1$prefix\\2$suffix\\3", $text);
    }

    return $text;
  }
  
  /**
   * Return a Perl-compatible version of the given phrase with all regular-expression
   * special characters escaped.
   *
   * @param string $phrase The phrase to sanitize.
   * @return string
   */
  public static function sanitize_words ($phrase)
  {
    /* Each special character is separated by a '|'. */
    
    return preg_replace('/([\\|\(|\)|\[|\]|\.|\/|\||\*|\+])/', '\\\\$1', $phrase);
  }

  /**
   * Remove all non-search term text from the given text.
   * Use this function to provide a condensed version of text which only
   * contains the search terms and some context characters. Uses {@link
   * select_words()} to wrap the search terms in <??><?/?> tags; these tags are
   * then removed along with any extraneous text.
   * @param string $text Text to search.
   * @param string $phrase Space-separated list of words.
   * @param integer $prefix Number of characters to each side of the word to
   * retain.
   * @param integer $suffix Maximum length of the text.
   * @return string
   */
  public function extract_words($text, $phrase, $context_size = 20, $max_length = 0, $break_inside_word = false)
  {
    $text = $this->select_words($text, $phrase, '<??>', '<?/?>');
    $includes_head = false;
    $fragments = array ();
    $start_pos = strpos($text, '<??>');

    while ($start_pos !== false)
    {
      $start_pos = $start_pos;
      $end_pos = strpos($text, '<?/?>', $start_pos +4);
      if ($end_pos === false)
      {
        $end_pos = strlen($text);
      }

      if (($start_pos - $context_size) <= 0)
      {
        $includes_head = true;
        $prefix = substr($text, 0, $start_pos);
      } else
      {
        if ($break_inside_word)
        {
          $prefix = substr($text, $start_pos - $context_size, $context_size);
        }
        else
        {
          // Search backwards from a specific position in the string; specify
          // that position as a negative value to ensure the search is in the
          // the left-hand part of the string.
          
          $offset = (strlen($text) - ($start_pos - $context_size));
          $space = strrpos ($text, ' ', - $offset);
          if ($space === false) { $space = 0; }
          
          $includes_head = ($space === 0);
          $prefix = substr($text, $space, $start_pos - $space);
        }
      }

      $word = substr($text, $start_pos +4, $end_pos - $start_pos -4);

      if ($break_inside_word)
      {
        $suffix = substr($text, $end_pos +5, $context_size);
        $end_pos = $end_pos + $context_size;
      } 
      else
      {
        $space = @ strpos($text, ' ', $end_pos +5 + $context_size);
        if ($space === false)
        {
          $space = strlen($text);
        }
        $suffix = substr($text, $end_pos +5, $space - $end_pos -5);
        $end_pos = $space;
      }

      $fragments[] = $prefix . $word . $suffix;
      /* Ignore offset past end of string error. */
      $start_pos = @ strpos($text, '<??>', $end_pos +5);
    }

    if (sizeof($fragments))
    {
      $Result = '';
      if (!$includes_head)
      {
        $Result = '...';
      }
      $Result .= implode(' ... ', $fragments);

      if (($max_length > 0) && ($max_length < strlen($Result)))
      {
        if ($break_inside_word)
        {
          $Result = substr($Result, 0, $max_length) . '...';
        }
        else
        {
          $space = strpos($Result, ' ', $max_length -1);
          if ($space !== false)
          {
            $Result = substr($Result, 0, $space) . '...';
          }
          elseif ($end_pos != strlen($text)) 
          {
            $Result .= '...';
          }
        }
      }
      elseif ($end_pos != strlen($text)) 
      {
        $Result .= '...';
      }

      return $Result;
    }
    
    return '';
  }
}

?>