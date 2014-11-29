<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.5.0
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

/**
 * A munger that strips tags.
 * 
 * This is a highlighter for a tag-based syntax; anything in an XML-style format,
 * with open and close tags, delineated by open and close characters for the tag data.
 * 
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 3.6.0
 */
class MUNGER_HIGHLIGHTER extends MUNGER_PARSER
{
  /**
   * Validate the input string against the registered tag libraries.
   * Check {@link $errors} afterwards for a list of errors that occurred.
   * @var string $input
   * @return string
   */
  public function highlight ($input)
  {
    $this->_output = "";
    
    $this->_process ($input);
    
    return $this->_output;
  }

  /**
   * Perform pre-processing on the input to consume.
   * Called from {@link _process()} for before initializing the {@link $_tokenizer}.
   * @var string $input
   * @return string
   * @access private
   */
  protected function _prepare_input($input)
  {
    return $input;
  }

  /**
   * Should this tag be converted to text?
   * @var MUNGER_TOKEN $token
   * @return bool
   * @access private
   */
  protected function _treat_as_text ($token)
  {
    return false;
  }

  /** Transform a token as text.
   * @param MUNGER_TOKEN $token
   * @access private */
  protected function _transform_as_text ($token)
  {
    $this->_output .= $token->data();
  }

  protected function _transform_as_start_tag($token)
  {
    $tag_name = $token->name();
    $attributes = $token->attributes();

    $this->_output .= '<span class="tag">&lt;';
    $this->_output .= '<span class="tag-name">' . $tag_name . '</span>';

    foreach ($attributes as $key => $value)
    {
      $this->_output .= ' <span class="attribute-name">' . $key . '</span><span class="symbol">="</span><span class="attribute-value">' . $value . '</span><span class="symbol">"</span>';
    }

    $this->_output .= '&gt;</span>';
  }

  protected function _transform_as_end_tag($token)
  {
    $tag_name = $token->name();
    $this->_output .= '<span class="tag">&lt;/';
    $this->_output .= '<span class="keyword">' . $tag_name . '</span>';
    $this->_output .= '&gt;</span>';
  }

  /**
   * @var string
   * @access private
   */
  protected $_output;
}