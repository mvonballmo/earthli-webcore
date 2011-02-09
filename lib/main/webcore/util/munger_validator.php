<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * Records a tag's position in a block of text.
 * Used by the {@link MUNGER_VALIDATOR} to determine whether there are well-formedness errors and to
 * generate error messages.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 * @access private
 */
class MUNGER_VALIDATOR_TAG
{
  /**
   * Token for the tag.
   * @var MUNGER_TOKEN
   */
  public $token;

  /**
   * Line number for the token.
   * @var integer
   */
  public $line_number;

  /**
   * Column in the line for the token.
   * @var integer
   */
  public $column;

  /**
   * @param MUNGER_TOKEN $token
   * @param integer $line_number
   * @param integer $column
   */
  public function __construct ($token, $line_number, $column)
  {
    $this->token = clone($token);
    $this->line_number = $line_number;
    $this->column = $column;
  }
}

/**
 * Describes a tag for the validator.
 * Used by the {@link MUNGER_VALIDATOR} to validate a known tag.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 * @access private
 */
class MUNGER_VALIDATOR_TAG_INFO
{
  /**
   * Does this tag have an end tag?
   * @var boolean
   */
  public $has_end_tag = true;

  /**
   * Table of properties accepted by this tag.
   * @var array[string]
   */
  public $properties = array ();

  /**
   * @param boolean $has_end_tag
   * @param array[string] $properties
   */
  public function __construct ($has_end_tag, $properties = null)
  {
    $this->has_end_tag = $has_end_tag;

    if (isset ($properties))
    {
      foreach ($properties as $prop)
      {
        $this->properties [$prop] = true;
      }
    }
  }

  /**
   * Validate the token against this tag's rules.
   * @param MUNGER_VALIDATOR $validator
   * @param MUNGER_TOKEN $token
   */
  public function validate ($validator, $token)
  {
    $attrs = $token->attributes ();
    foreach (array_keys($attrs) as $key)
    {
      if (! isset ($this->properties [strtolower($key)]))
      {
        $validator->add_error ('Invalid attribute [' . $key . '] for [' . $token->name() . ']', $token);
      }
    }
  }
}

/**
 * A validation error marker.
 * Generated by the {@link MUNGER_VALIDATOR}.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.1
 */
class MUNGER_VALIDATION_ERROR
{
  /**
   * Error message in plain text format.
   * @var string
   */
  public $message;

  /**
   * Line at which the error occurred.
   * @var integer
   */
  public $line_number;

  /**
   * Column at which the error occurred.
   * @var integer
   */
  public $column;

  /**
   * Token that generated the error.
   * @var MUNGER_TOKEN
   */
  public $token;
}

function sort_validation_errors ($err1, $err2)
{
  if ($err1->line_number == $err2->line_number)
  {
    if ($err1->column == $err2->column)
    {
      return 0;
    }
    return ($err1->column < $err2->column) ? -1 : 1;
  }

  return ($err1->line_number < $err2->line_number) ? -1 : 1;
}

/**
 * A validator for a tag-based format.
 * This is well-formedness validator for tag-based syntaxes; anything in an XML-style format,
 * with open and close tags, delineated by open and close characters for the tag data.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class MUNGER_VALIDATOR extends MUNGER_PARSER
{
  /**
   * List of errors.
   * Retains the errors generated by the last call to {@link validate()}.
   * @var array[MUNGER_VALIDATION_ERROR]
   */
  public $errors = array ();

  /**
   * Validate the input string against the registered tag libraries.
   * Check {@link $errors} afterwards for a list of errors that occurred.
   * @var string $input
   */
  public function validate ($input)
  {
    $this->errors = array ();
    $this->_line_number = 1;
    $this->_column = 0;
    $this->_process ($input);

    while (($tag = array_pop ($this->_open_tags)))
    {
      $name = $tag->token->name ();
      if (isset($this->_known_tags [$name]) && $this->_known_tags [$name]->has_end_tag)
      {
        $this->add_error ('Missing end tag for [' . $tag->token->name() . '].', $tag->token, $tag->line_number, $tag->column);
      }
    }
    usort ($this->errors, 'sort_validation_errors');
  }

  /**
   * Add a tag that will be validated.
   * All other tags are ignored by the validator and assumed to be textual input.
   * @param string $name
   * @param MUNGER_VALIDATOR_TAG_INFO $info
   * @access private
   */
  public function register_known_tag ($name, $info)
  {
    $this->_known_tags [strtolower ($name)] = $info;
  }

  /**
   * Consume the given token.
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _process_token ($token)
  {
    parent::_process_token ($token);
    $this->_update_position ($token);
  }

  /**
   * Should this tag be converted to text?
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _treat_as_text ($token)
  {
    return ! isset ($this->_known_tags [$token->name ()]);
  }

  /**
   * Validate a start tag token.
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_start_tag ($token)
  {
    $name = $token->name ();
    if (isset ($this->_known_tags [$name]))
    {
      $tag_info = $this->_known_tags [$name];
      $tag_info->validate ($this, $token);
      if ($tag_info->has_end_tag)
      {
        array_push ($this->_open_tags, new MUNGER_VALIDATOR_TAG ($token, $this->_line_number, $this->_column));
      }
    }
  }

  /**
   * Validate an end tag token.
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _transform_as_end_tag ($token)
  {
    $name = $token->name ();
    if (isset ($this->_known_tags [$name]))
    {
      if ($this->_known_tags [$name]->has_end_tag)
      {
        $tag = array_pop ($this->_open_tags);
        if (isset ($tag))
        {
          if (! $tag->token->matches ($token))
          {
            $this->add_error ('Unexpected end tag ' . $tag->token->name() . '.', $token);
            /* Put the tag back on the stack, so it can be matched if the correct
             * tag shows up.
             */
            array_push ($this->_open_tags, $tag);
          }
        }
        else
        {
          $this->add_error ('Unexpected end tag [' . $name . '] (no tags are open).', $token);
        }
      }
      else
      {
        $this->add_error ('Tag [' . $name . '] cannot have an end tag.', $token);
      }
    }
  }

  /** Transform a token as text.
   * @param MUNGER_TOKEN $token
   * @access private */
  protected function _transform_as_text ($token)
  {
  }

  /** 
   * Add an error to the result.
   * @param string $msg
   * @param MUNGER_TOKEN $token
   * @param integer $line
   * @param integer $col
   * @access private
   */
  public function add_error ($msg, $token, $line = null, $col = null)
  {
    if (! isset ($line))
    {
      $line = $this->_line_number;
    }
    if (! isset ($col))
    {
      $col = $this->_column;
    }

    $error = new MUNGER_VALIDATION_ERROR ();
    $error->message = $msg;
    $error->token = $token;
    $error->line_number = $line;
    $error->column = $col + 1;

    $this->errors [] = $error;
  }

  /**
   * Track the line number and column in the text.
   * @param MUNGER_TOKEN $token
   * @access private
   */
  protected function _update_position ($token)
  {
    $text = $token->data ();
    $pos = strpos ($text, "\n");

    if ($pos !== false)
    {
      $last_pos = $pos + 1;

      while ($pos !== false)
      {
        $this->_line_number += 1;
        $this->_column = 1;
        $pos = strpos ($text, "\n", $last_pos);
        if ($pos !== false)
        {
          $last_pos = $pos + 1;
        }
      }

      $this->_column += strlen ($text) - $last_pos;
    }
    else
    {
      $this->_column += strlen ($text);
    }
  }

  /**
   * Stack of tags found in the input stream.
   * @var array[MUNGER_TAG]
   * @access private
   */
  protected $_open_tags = array ();

  /**
   * @var array[string,MUNGER_VALIDATOR_TAG_INFO]
   * @access private
   */
  protected $_known_tags;
  
  /**
   * Current line number.
   * @see _update_position()
   * @access private
   */
  protected $_line_number;

  /**
   * Current column in the line.
   * @see _update_position()
   * @access private
   */
  protected $_column = 0;
}

/**
 * Common base for all validators, single- or multi-line.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.1
 */
class MUNGER_BASE_VALIDATOR extends MUNGER_VALIDATOR
{
  public function __construct ()
  {
    parent::__construct ();

    $standard_tag_info = new MUNGER_VALIDATOR_TAG_INFO (true, array ('class', 'style', 'title'));
    $this->register_known_tag ('span', $standard_tag_info);
    $this->register_known_tag ('i', $standard_tag_info);
    $this->register_known_tag ('b', $standard_tag_info);
    $this->register_known_tag ('n', $standard_tag_info);
    $this->register_known_tag ('c', $standard_tag_info);
    $this->register_known_tag ('hl', $standard_tag_info);
    $this->register_known_tag ('var', $standard_tag_info);
    $this->register_known_tag ('kbd', $standard_tag_info);
    $this->register_known_tag ('dfn', $standard_tag_info);
    $this->register_known_tag ('abbr', $standard_tag_info);
    $this->register_known_tag ('cite', $standard_tag_info);
    $this->register_known_tag ('macro', new MUNGER_VALIDATOR_TAG_INFO (false, array('convert')));
  }
}

/**
 * Default validation for {@link HTML_MUNGER} and {@link PLAIN_TEXT_MUNGER}.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class MUNGER_DEFAULT_TITLE_VALIDATOR extends MUNGER_BASE_VALIDATOR
{
}

/**
 * Default validation for {@link HTML_MUNGER} and {@link PLAIN_TEXT_MUNGER}.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class MUNGER_DEFAULT_TEXT_VALIDATOR extends MUNGER_BASE_VALIDATOR
{
  public function __construct ()
  {
    parent::__construct ();

    $standard_tag_info = new MUNGER_VALIDATOR_TAG_INFO (true);
    $base_tag_props = array ('class', 'style', 'author', 'href', 'source', 'date');
    $div_tag_props = array_merge ($base_tag_props, array ('caption', 'align', 'width', 'clear', 'caption-position'));
    $div_tag_info = new MUNGER_VALIDATOR_TAG_INFO (true, $div_tag_props);
    $quote_tag_info = new MUNGER_VALIDATOR_TAG_INFO (true, array_merge ($div_tag_props, array ('quote_style')));
    $asset_tag_props = array_merge ($div_tag_props, array ('title', 'src', 'format', 'attachment'));

    $this->register_known_tag ('h', new MUNGER_VALIDATOR_TAG_INFO (true, array ('level')));
    $this->register_known_tag ('div', $div_tag_info);
    $this->register_known_tag ('clear', new MUNGER_VALIDATOR_TAG_INFO (false));
    $this->register_known_tag ('pre', $div_tag_info);
    $this->register_known_tag ('box', new MUNGER_VALIDATOR_TAG_INFO (true, array_merge ($div_tag_props, array ('title'))));
    $this->register_known_tag ('code', $div_tag_info);
    $this->register_known_tag ('iq', new MUNGER_VALIDATOR_TAG_INFO (true, array ('author', 'source')));
    $this->register_known_tag ('bq', $quote_tag_info);
    $this->register_known_tag ('pullquote', $quote_tag_info);
    $this->register_known_tag ('abstract', $quote_tag_info);
    $this->register_known_tag ('ul', $standard_tag_info);
    $this->register_known_tag ('ol', $standard_tag_info);
    $this->register_known_tag ('dl', new MUNGER_VALIDATOR_TAG_INFO (true, array ('dt_class', 'dd_class', 'style')));
    $this->register_known_tag ('fn', new MUNGER_VALIDATOR_TAG_INFO (false));
    $this->register_known_tag ('ft', $standard_tag_info);
    $this->register_known_tag ('hr', new MUNGER_VALIDATOR_TAG_INFO (false, array ('style')));
    $this->register_known_tag ('a', new MUNGER_VALIDATOR_TAG_INFO (true, array_merge ($base_tag_props, array ('title'))));
    $this->register_known_tag ('anchor', new MUNGER_VALIDATOR_TAG_INFO (false, array ('id')));
    $this->register_known_tag ('img', new MUNGER_VALIDATOR_TAG_INFO (false, array_merge ($asset_tag_props, array ('scale'))));
    $this->register_known_tag ('media', new MUNGER_VALIDATOR_TAG_INFO (false, array_merge ($asset_tag_props, array ('height', 'args'))));
    $this->register_known_tag ('page', new MUNGER_VALIDATOR_TAG_INFO (false, array ('title')));
  }
}

?>
