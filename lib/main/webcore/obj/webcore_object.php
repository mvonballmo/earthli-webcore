<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.2.1
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

/** */
require_once ('webcore/sys/system.php');

/**
 * This is the base class for many of the objects in the WebCore library.
 * Most of these objects will be tied to an application and will require
 * use of database or environment features. The constructor accepts a {@link
 * CONTEXT}; If the type passed is an APPLICATION, then the {@link $login} and
 * {@link $db} are initialized from there, otherwise <code>db</code> is
 * initialized from the {@link PAGE} object passed in and {@link $app} and
 * <code>login</code> are left blank.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.2.1
 */
class WEBCORE_OBJECT extends RAISABLE
{
  /**
   * @param CONTEXT &$context Page or application object to which this one belongs.
   */
  function WEBCORE_OBJECT (&$context)
  {
    $this->assert (isset ($context), "[context] cannot be empty.", 'WEBCORE_OBJECT', 'WEBCORE_OBJECT');

    $this->context =& $context;
    $this->env =& $context->env;
    $this->db =& $context->database;

    if ($context->is_page)
    {
      $this->page =& $context;
    }
    else
    {
      $this->app =& $context;
      $this->page =& $context->page;
      $this->login =& $context->login;
    }

    $this->env->num_webcore_objects++;

    if ($this->env->log_class_names)
    {
      log_message ("[{$this->env->num_webcore_objects}] Created [" . $this->instance_description () . ']', Msg_type_debug_info, Msg_channel_system);
    }
  }

  /**
   * Return meta-information about this class.
   * @return ENTRY_TYPE_INFO
   */
  function &type_info ()
  {
    return $this->context->type_info_for (get_class ($this));
  }
  
  /**
   * Return a description for use in logging.
   * @return string
   */
  function instance_description ()
  {
    return get_class ($this);
  }

  /**
   * Deep-copy this object.
   * If you simply make a copy of the object, references to aggregated objects are retained because
   * PHP only makes a shallow copy. Imagine you have variable 'a1' of class A with a property 'b' of class B and some integer
   * and string properties. If you simply use PHP's copy mechanism like this 'a2 = a1', you get a2 with all simple properties
   * copied. However, a2 and a1 share the reference to 'b' because PHP only copies the reference. In this function, you should
   * explicitly use the PHP assignment operator to make a copy of all sub-objects.
   * @return WEBCORE_OBJECT
   */
  function make_clone ()
  {
    if (is_php_5 ())
    {
      return clone ($this);
    }

    $Result = $this;
    $Result->_copy_from ($this);
    return $Result;
  }

  /**
   * Any queries initiated by this object run in a separate connection.
   * This is necessary when querying from within a loop over other queries
   * objects. In that case, the inner query cannot share a query id with
   * the outer query.
   */
  function ensure_has_own_database_connection ()
  {
    $new_db = $this->db->make_clone ();
    unset ($this->db);
    $this->db = $new_db;
  }

  /**
   * Return the context's exception handler
   * @return EXCEPTION_HANDLER
   * @access private
   */
  function _exception_handler ()
  {
    if (isset ($this->context->exception_handler))
    {
      return $this->context->exception_handler;
    }
  }

  /**
   * Copy properties from the given object. 
   * Override to deep-copy any references when an object is cloned. For example,
   * {@link DATE_TIME} objects are copied as references by PHP5, but should be
   * fully-cloned copies themselves. Called from {@link __clone()}.
   * @param WEBCORE_OBJECT $other
   * @access private
   */
  function _copy_from ($other)
  {
  }
  
  /**
   * Called in PHP5 when cloning an object. Calls {@link
   * _initialize_clonable_fields()} to create copies of all references not
   * cloned by the default shallow copy.
   */
  function __clone ()
  {
    $this->_copy_from ($this);
  }

  /**
   * reference to the context (usually a PAGE or APPLICATION)
    * @var CONTEXT
    * @access private
    */
  var $context = null;
  /**
   * Shortcut to global environment.
    * @var ENVIRONMENT
    * @access private
    */
  var $env = null;
  /**
   * Shortcut to global page object.
    * @var PAGE
    * @access private
    */
  var $page = null;
  /**
   * Reference to the shared database.
    * @var DATABASE
    * @access private
    */
  var $db = null;
  /**
   * Reference to the application.
    * Can be empty if there is no application.
    * @var APPLICATION
    * @access private
    */
  var $app = null;
  /**
   * Shortcut to the 'login' in the application.
    * Can be empty if there is no application.
    * @var USER
    * @access private
    */
  var $login = null;
}

?>