<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.4.0
 * @since 2.6.0
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

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Generates a URL to set a cookie value.
 * @package webcore
 * @subpackage util
 * @version 3.4.0
 * @since 2.6.0
 */
class STORED_OPTION extends WEBCORE_OBJECT
{
  /**
   * Name of the option.
   * @var string
   */
  public $name;

  /**
   * @param CONTEXT $context
   * @param string $name
   */
  public function __construct ($context, $name)
  {
    parent::__construct ($context);
    $this->name = $name;
  }

  /**
   * The current value of this option.
   * @return string
   */
  public function value ()
  {
    return $this->context->storage->value ($this->name);
  }

  /**
   * Add an argument to the return URL.
   * The page URL may not reflect the exact URL needed to return to this page
   * in the same state as when you left. If the option is set after a form is
   * submitted for a preview, the page URL has no query string arguments and
   * the set option page will not be able to return properly. Add the missing
   * arguments with this function.
   * @param string $key
   * @param string $value
   */
  public function add_argument ($key, $value)
  {
    $this->_args [$key] = $value;
  }

  /**
   * A text URL that sets the option to the given value.
   * Use this output when generating a javascript link. Use {@link setter_url_as_html()}
   * when generating directly into an HTML page.
   * @param string $value
   * @return string
   */
  public function setter_url_as_text ($value)
  {
    $url = $this->_url_for_value ($value);
    return $url->as_text ();
  }

  /**
   * An HTML URL that sets the option to the given value.
   * @see setter_url_as_text() for instructions on where to use each function.
   * @param string $value
   * @return string
   */
  public function setter_url_as_html ($value)
  {
    $url = $this->_url_for_value ($value);
    return $url->as_html ();
  }

  /**
   * Return a {@link URL} prepared for the given value.
   * @param string $value
   * @return URL
   * @access private
   */
  protected function _url_for_value ($value)
  {
    $set_option_path = $this->context->resolve_file ('{' . Folder_name_functions . '}set_option.php');
    $Result = new URL ($set_option_path);
    $Result->add_argument ('opt_name', $this->name);
    $Result->add_argument ('opt_value', $value);
    $Result->add_argument ('opt_page_context', $this->context->is_page);
    $last_page = urlencode ($this->env->url (Url_part_all));
    if (isset ($this->_args))
    {
      $url = new URL ($last_page);
      foreach ($this->_args as $key => $value)
      {
        $url->replace_argument ($key, $value);
      }
      $last_page = $url->as_text ();
    }
    $Result->add_argument ('last_page', $last_page);
    
    return $Result;
  }

  /**
   * Additional arguments to add to the return URL.
   * The url of the current page is used by default. More arguments can be added
   * with {@link add_argument()}.
   * @var string[]
   * @access private
   */
  protected $_args;
}