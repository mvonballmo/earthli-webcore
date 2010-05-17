<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.2.1
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

/** */
require_once ('webcore/sys/client_storage.php');

/**
 * Interface to loading/storing client-side cookies.
 * The interface should is the same as the other storage mechanism, sessions,
 * so that an application can freely switch from one to the other. Cookies
 * expire at session end, by default.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.2.1
 */
class COOKIE extends CLIENT_STORAGE
{
  /**
   * Cookies are available from this path.
   * If no path is set, it defaults to the path for the current page.
   * @var string
   */
  public $path = '';

  /**
   * Cookies are available from this domain.
   * If no domain is set, it defaults to the domain for the current page.
   * @var string
   */
  public $domain = '';

  /**
   * Adjust the value of {@link $path}.
   * @deprecated Set the {@link $path} property directly.
   * @param string $p
   */
  public function set_path ($p)
  {
    $this->path = $p;
  }

  /**
   * Load the requested value from storage.
   * @param string $key
   * @return string
   * @access private
   */
  protected function _read ($key)
    {
    if (isset ($_COOKIE [$key]))
    {
      return $_COOKIE [$key];
    }
    
    return null;
  }

  /**
   * Is this value in local storage?
   * @param string $key
   * @return boolean
   * @access private
   */
  protected function _exists ($key)
  {
    return isset ($_COOKIE [$key]);
  }

  /**
   * Add the requested value to storage.
   * @param string $key
   * @param string $value
   * @access private
   */
  protected function _add ($key, $value)
  {
    $_COOKIE [$key] = $value;
  }

  /**
   * Store the requested value to client storage.
   * @param string $key
   * @param string $value
   * @param integer
   * @access private
   */
  protected function _write ($key, $value)
  {
    setcookie ($key, $value, max (0, $this->expire_date->as_php ()), $this->path, $this->domain);
  }

  /**
   * Remove the requested value from client storage.
   * @param string $key
   * @access private
   * @abstract
   */
  protected function _clear ($key)
  {
    setcookie ($key, '', max (0, time () - (86400)), $this->path, $this->domain);
  }

  /**
   * Stores new values locally so that subsequent lookups don't read
   * the stale value out of the page cookie.
   * @var array[string,string]
   * @access private
   */
  protected $_values = array ();

  /**
   * Contains cookies when storing multiple values.
   * Use {@link start_multiple_value()} to start storing to a list of
   * values. Use {@link finish_multiple_value()} to write the accumulated
   * values to a single cookie.
   * @var array[string,string]
   * @access private
   */
  protected $_multiple_values;

  /**
   * Multiple values will be stored to this key.
   * @var string
   * @access private
   */
  protected $_multiple_value_key;
}

?>