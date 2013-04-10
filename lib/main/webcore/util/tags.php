<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.7.0
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

/**
 * Creates an HTML tag string.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.5.0
 */
class HTML_TAG_BUILDER
{
  /**
   * Start creating a tag called 'name'.
   * @param string $name
   */
  public function set_name ($name)
  {
    $this->_text = '<' . $name;
  }

  /**
   * Add an attribute to the tag being contructed.
   * @param string $name
   * @param string $value
   */
  public function add_attribute ($name, $value)
  {
    if ($value)
    {
      $text_options = global_text_options ();
      $this->_text .= ' ' . $name . '="' . $text_options->convert_to_html_attribute ($value) . '"';
    }
  }

  /**
   * Add an attribute using an array value.
   * The value in the array associated with the 'name' key is used here.
   * @param string $name
   * @param array $values
   */
  public function add_array_attribute ($name, $array, $default_value = '')
  {
    $this->add_attribute ($name, read_array_index ($array, $name, $default_value));
  }

  /**
   * Get the HTML-formatted tag
   * @return string
   */
  public function as_html ()
  {
    return $this->_text . '>';
  }

  /**
   * @var string
   * @access private
   */
  protected $_text;
}

/**
 * Creates a CSS style for use as a tag property.
 * @package webcore
 * @subpackage text
 * @version 3.3.0
 * @since 2.6.1
 */
class CSS_STYLE_BUILDER
{
  /**
   * Start creating a style using 'CSS' initially.
   * @param string $name
   */
  public function set_text ($CSS)
  {
    $this->_text = $CSS;
  }

  /**
   * Remove all styles.
   */
  public function clear ()
  {
    $this->_text = '';
  }

  /**
   * Add an attribute to the style being contructed.
   * @param string $name
   * @param string $value
   */
  public function add_attribute ($name, $value)
  {
    if ($value)
    {
      if ($this->_text)
      {
        $this->_text .= '; ';
      }
       
      $text_options = global_text_options ();
 
      $this->_text .= $name . ': ' . $text_options->convert_to_html_attribute ($value);
    }
  }
  
  /**
   * Add an attribute using an array value.
   * The value in the array associated with the 'name' key is used here.
   * @param string $name
   * @param array $values
   */
  public function add_array_attribute ($name, $array, $default_value = '')
  {
    $this->add_attribute ($name, read_array_index ($array, $name, $default_value));
  }

  /**
   * Add pre-formatted CSS to the style.
   * @param string $CSS
   */
  public function add_text ($CSS)
  {
    if ($CSS)
    {
      if ($this->_text)
      {
        $this->_text .= '; ';
      }
      $this->_text .= $CSS;
    }
  }

  /**
   * Get the CSS style as text.
   * @return string
   */
  public function as_text ()
  {
    return $this->_text;
  }
  
  /**
   * Are there styles assigned?
   * @return boolean
   */
  public function is_empty ()
  {
    return empty ($this->_text);
  }

  /**
   * @var string
   * @access private
   */
  protected $_text;
}

?>
