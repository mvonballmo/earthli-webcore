<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
 * Calls a global function.
 * Use this class as a parameter callback when you want to support either object methods
 * or global functions as a callback. Object methods are supported with descendent class
 * {@link CALLBACK_METHOD}.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class CALLBACK
{
  /**
   * Name of the global function to call.
   * @var string
   */
  var $name;

  /**
   * @var string
   */
  function CALLBACK ($name)
  {
    $this->name = $name;
  }

  /**
   * Execute the function with the given arguments.
   * @param array $args
   * @return mixed
   */
  function execute ($args = null)
  {
    $name = $this->name;
    $num_args = sizeof ($args);

    switch ($num_args)
    {
    case 0:
      return $name ($this);
    case 1:
      return $name ($args[0]);
    case 2:
      return $name ($args[0], $args[1]);
    case 3:
      return $name ($args[0], $args[1], $args[2]);
    case 3:
      return $name ($args[0], $args[1], $args[2], $args[3]);
    }
  }
}

/**
 * Calls a specific object's method.
 * Pass this class to a function expecting a {@link METHOD} when you want to route the
 * callback to a specific object.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class CALLBACK_METHOD extends CALLBACK
{
  /**
   * @param string $name
   * @param object $obj
   */
  function CALLBACK_METHOD ($name, &$obj)
  {
    CALLBACK::CALLBACK ($name);
    $this->_obj =& $obj;
  }

  /**
   * Execute the method with the given arguments.
   * @param array $args
   * @return mixed
   */
  function execute ($args = null)
  {
    $name = $this->name;
    $num_args = sizeof ($args);

    switch ($num_args)
    {
    case 0:
      return $this->_obj->$name ();
    case 1:
      return $this->_obj->$name ($args[0]);
    case 2:
      return $this->_obj->$name ($args[0], $args[1]);
    case 3:
      return $this->_obj->$name ($args[0], $args[1], $args[2]);
    case 3:
      return $this->_obj->$name ($args[0], $args[1], $args[2], $args[3]);
    }
  }

  /**
   * @var object
   * @access private
   */
  var $_obj;
}

/**
 * Manages and executes a list of {@link CALLBACK}s.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class CALLBACK_LIST
{
  /**
   * Add a callback to the list of listeners.
   * @param CALLBACK $callback
   */
  function add_item ($callback)
  {
    $this->_items [] = $callback;
  }
  
  /**
   * Execute each item in the list with the given arguments.
   * @param array $args
   * @return mixed
   */
  function execute ($args = null)
  {
    if (! $this->_executing)
    {
      $this->_executing = TRUE;
      foreach ($this->_items as $item)
        $item->execute ($args);
      $this->_executing = FALSE;
    }
  }
  
  /**
   * List of items in the list. 
   * @var array[CALLBACK]
   * @access private
   */
  var $_items = array ();
  /**
   * Set internally when iterating the list.
   * If this flag is set, calls to {@link execute()} are ignored.
   * @access private
   */
  var $_executing = FALSE;
}

?>