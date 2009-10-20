<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
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

/***/
require_once ('webcore/util/munger.php');

/**
 * A munger that strips tags.
 * 
 * This is tag-stripper for tag-based syntaxes; anything in an XML-style format,
 * with open and close tags, delineated by open and close characters for the tag data.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.1.0
 * @since 3.2.0
 */
class MUNGER_STRIPPER extends MUNGER_PARSER
{
  /**
   * Take out all tags from the input?
   * If this is true, then unregistered tags are also stripped; otherwise, unregistered tags
   * are left in the output.
   * @var boolean
   */
  public $strip_unknown_tags = false;
  
  /**
   * Validate the input string against the registered tag libraries.
   * Check {@link $errors} afterwards for a list of errors that occurred.
   * @var string $input
   * @return string
   */
  public function strip ($input)
  {
    $this->_output = "";
    
    $this->_process ($input);
    
    return $this->_output;
  }

  /**
   * Add a tag that will be stripped.
   * All other tags are ignored by the stripper and assumed to be textual input.
   * @param string $name
   * @param boolean $has_end_tag
   * @access private
   */
  public function register_known_tag($name, $has_end_tag)
  {
    $this->_known_tags[strtolower($name)] = $has_end_tag;
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
    return $input;
  }

  /**
   * Should this tag be converted to text?
   * @var MUNGER_TOKEN $token
   * @access private
   */
  protected function _treat_as_text ($token)
  {
    return ! isset ($this->_known_tags [$token->name ()]) && !$this->strip_unknown_tags;
  }

  /** Transform a token as text.
   * @param MUNGER_TOKEN $token
   * @access private */
  protected function _transform_as_text ($token)
  {
    $this->_output .= $token->data();
  }

  /**
   * @var array[string,MUNGER_VALIDATOR_TAG_INFO]
   * @access private
   */
  protected $_known_tags;

  /**
   * @var string
   * @access private
   */
  protected $_output;
}

/**
 * Common base for all tag-strippers, single- or multi-line.
 * @package webcore
 * @subpackage text
 * @version 3.1.0
 * @since 3.2.0
 */
class MUNGER_BASE_STRIPPER extends MUNGER_STRIPPER
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
    $this->register_known_tag ('var', true);
    $this->register_known_tag ('kbd', true);
    $this->register_known_tag ('dfn', true);
    $this->register_known_tag ('abbr', true);
    $this->register_known_tag ('cite', true);
    $this->register_known_tag ('macro', false);
  }
}

/**
 * Default stripper for {@link HTML_MUNGER} and {@link PLAIN_TEXT_MUNGER}.
 * @package webcore
 * @subpackage text
 * @version 3.1.0
 * @since 3.2.0
 */
class MUNGER_DEFAULT_TITLE_STRIPPER extends MUNGER_BASE_STRIPPER
{
}

?>