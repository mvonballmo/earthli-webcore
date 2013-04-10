<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.6.0
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
 * Used with {@link QUERY::restrict_by_op}.
 * Constant used for documentation only.
 */
define ('Operator_constants', '');
/**
 * Equals operator, used in comparisons.
 */
define ('Operator_equal', '=');
/**
 * Less than or equal operator, used in comparisons.
 */
define ('Operator_less_than_equal', '<=');
/**
 * Less than or equal operator, used in comparisons.
 */
define ('Operator_less_than', '<');
/**
 * Value is in a set.
 */
define ('Operator_in', 'IN');
/**
 * Value is not in a set.
 */
define ('Operator_not_in', 'NOT IN');

/**
 * Messages from the WebCore system are sent on this channel.
 * This is the generic channel used by core systems. Other sub-systems use more specific channels.
 * @access private
 */
define ('Msg_channel_system', 'System');

/**
 * Request post variables for an exception.
 * @see EXCEPTION_SIGNATURE::variables_for()
 */
define ('Var_type_post', '_POST');
/**
 * Request get variables for an exception.
 * @see EXCEPTION_SIGNATURE::variables_for()
 */
define ('Var_type_get', '_GET');
/**
 * Request cookie variables for an exception.
 * @see EXCEPTION_SIGNATURE::variables_for()
 */
define ('Var_type_cookie', '_COOKIE');
/**
 * Request file-upload variables for an exception.
 * @see EXCEPTION_SIGNATURE::variables_for()
 */
define ('Var_type_upload', '_FILES');

/**
 * Retrieves an array index without triggering a PHP notice.
 * @version 3.4.0
 * @since 2.2.1
 * @param array $arr
 * @param string $index
 * @param string $default_value Returns this value if the index does not exist.
 * @return string
 */
function read_array_index ($arr, $index, $default_value = '')
{
  if (isset ($arr) && is_array ($arr) && isset ($arr [$index]))
  {
    return $arr [$index];
  }
  return $default_value;
}

/**
 * Retrieves a request variable without triggering a PHP notice.
 * Guaranteed to return a value so that subsequent references will also not trigger PHP notices.
 *
 * If the actual value in the request is the empty string and the default value is not, then use
 * the default value. This makes the assumption that an explicitly empty value is the same as not
 * passing a value at all.
 * @version 3.4.0
 * @since 2.2.1
 * @param string $var_name
 * @param string $default_value
 * @return string
 */
function read_var ($index, $default_value = '')
{
  $Result = read_array_index ($_REQUEST, $index, $default_value);

  if (($default_value !== '') && ($Result === ''))
  {
    $Result = $default_value;
  }

  return $Result;
}

/**
 * Read the requested values and return a query string.
 * For example, if the url of the page is "index.php?b=1&c=2&d=3", then
 * <code>read_vars (array ('a', 'b', 'c'))</code> returns "b=1&c=2". Use this
 * function to gather page parameters for easy reposting.
 * @param array[string] $indexes
 * @return string
 */
function read_vars ($indexes)
{
  $Result = array ();
  foreach ($indexes as $index)
  {
    $value = read_var ($index);
    if ($value)
    {
      $Result [] = "$index=$value";
    }
  }
  return implode ('&', $Result);
}

/**
 * Remove empty entries from an array.
 * @version 3.4.0
 * @since 2.2.1
 * @param array $arr
 * @return array
 */
function trim_array ($arr)
{
  foreach ($arr as $key => $value)
  {
    if (! isset ($value) || $value == '')
    {
      unset ($arr [$key]);
    }
  }

  return $arr;
}

/**
 * Causes a fatal error in the page.
 * Actual handling is delegated to the handler passed in or a default exception
 * handler for the page. A fatal error can be raised with only a message, in which
 * case it's assumed to have happened at global scope.
 * @param string $message The error message
 * @param string $routine_name The name of the routine where the error occurred (can be empty)
 * @param string $class_name The name of the class where the error occurred (can be empty)
 * @param object $obj Reference to the object where the error occurred (can be empty)
 * @param EXCEPTION_HANDLER $handler The handler for this exception (can be empty)
 * @version 3.4.0
 * @since 2.2.1
 * @see set_default_exception_handler()
 * @see EXCEPTION_HANDLER
 */
function raise ($message, $routine_name = '', $class_name = '', $obj = null, $handler = null)
{
  if (! isset ($handler))
  {
    $handler = $GLOBALS ['_default_exception_handler'];
  }

  if (! isset ($handler))
  {
    $handler = new EXCEPTION_HANDLER ();
  }

  $handler->raise ($message, $routine_name, $class_name, $obj);
}

/**
 * Sets the default exception handler for the page.
 * @param EXCEPTION_HANDLER $handler The default exception handler
 * @version 3.4.0
 * @since 2.2.1
 * @see raise()
 */
function set_default_exception_handler ($handler)
{
  $GLOBALS ['_default_exception_handler'] = $handler;
}

function hook_php_error_handler ()
{
  set_error_handler ('__php_error_handler');
}

/**
 * Called when PHP encounters an error.
 * Only used if {@link hook_php_error_handler()} is called to override PHP
 * error handling.
 * @see PHP_MANUAL#set_error_handler
 * @param integer $type Type of error.
 * @param string $msg
 * @param string $file_name
 * @param integer $line_no
 */
function __php_error_handler ($type, $msg, $file_name, $line_no)
{
  if ($file_name)
  {
    if ($line_no)
    {
      $msg = "[$file_name - line $line_no]: $msg";
    }
    else
    {
      $msg = "[$file_name]: $msg";
    }
  }

  switch ($type)
  {
    case E_ERROR:
    case E_PARSE:
    case E_CORE_ERROR:
    case E_COMPILE_ERROR:
      raise ($msg, 'PHP');
      break;
    case E_CORE_WARNING:
    case E_COMPILE_WARNING:
      log_message ($msg, Msg_type_warning, Msg_channel_system);
      break;
  }
}

/**
 * Thrown when a value does not match one of the values in a switch statement.
 * 
 * @see function raise()
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.1
 */
class UNKNOWN_VALUE_EXCEPTION extends Exception
{
  public function __construct($value) 
  {
    $this->_value = $value;
  }
  
  public function message()
  {
    return "Unknown value [$this->_value]";
  }

  private $_value;
}

/**
 * Thrown when a method is not implemented (but was not marked as abstract).
 * 
 * @see function raise()
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.1
 */
class METHOD_NOT_IMPLEMENTED_EXCEPTION extends Exception
{
  public function message()
  {
    return "Method not implemented.";
  }
}

/**
 * Defines a class that is connected to a context with an exception handler.
 * This allows groups of classes to use different exception handlers.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.2.1
 */
class RAISABLE
{
  /**
   * Passes the error to a shared exception handler.
   * @param string $message The error message
   * @param string $routine_name The name of the routine where the error occurred
   * @param string $class_name The name of the class where the error occurred
   * @access private
   */
  public function raise ($message, $routine_name, $class_name)
  {
    raise ($message, $routine_name, $class_name, $this, $this->_exception_handler ());
  }

  /**
   * Generates a method-not-defined error.
   * @param string $routine_name The name of the routine where the error occurred
   * @param string $class_name The name of the class where the error occurred
   * @access private
   */
  public function raise_deferred ($routine_name, $class_name)
  {
    $this->raise ('deferred function is not implemented.', $routine_name, $class_name);
  }

  /**
   * Raise an error if the object is not the requested type.
   * @param object $obj
   * @param string $class_name
   * @access private
   */
  public function raise_if_not_is_a ($obj, $expected_class, $routine_name, $class_name)
  {
    if (! ($obj instanceof $expected_class))
    {
      $this->raise ('[' . get_class ($obj) . "] is not a [$expected_class]", $routine_name, $class_name);
    }
  }

  /**
   * Passes the error to a shared exception handler if the condition is true.
   * @param boolean $condition The condition to check
   * @param string $message The error message
   * @param string $routine_name The name of the routine where the error occurred
   * @param string $class_name The name of the class where the error occurred
   * @access private
   */
  public function assert ($condition, $message, $routine_name, $class_name)
  {
    if (! $condition)
    {
      $this->raise ($message, $routine_name, $class_name);
    }
  }

  /**
   * Stop execution with a message.
   * Similar to {@link PHP_MANUAL#die}, but allows logging and exception
   * handling to function properly.
   * @param string $msg
   */
  public function halt ($msg = 'Process halted.')
  {
    raise ($msg, '[unknown]', get_class ($this), $this, $this->_exception_handler ());
  }

  /**
   * Determines whether a value is an integer or not.
   * This will return false if it is not. To validate an integer with an exception,
   * use {@link validate_as_integer()} instead.
   * @param object $value The integer prospect
   * @param boolean $allow_empty Is an empty value interpreted as 0?
   * @return object Returns false if it's not an integer. Returns the integer otherwise (or 0 if empty).
   * @access private
   */
  public function validate_as_integer_silent ($value, $allow_empty = true)
  {
    if ($allow_empty && ! $value)
    {
      $value = 0;
    }

    if (is_numeric ($value) && (strpos($value, 'e') === FALSE))
    {
      return $value;
    }

    return false;
  }

  /**
   * Raises an exception is 'value' is not an integer.
   * Cast $i as an integer (if it's not an integer, raise an exception)
   * This is used to validate arguments to databases so that hacks of the form 'URL?id=1 OR 1=1' don't work
   * The function dies immediately so that any dependent SQL doesn't reveal the inner workings of the database.
   * @param object $value The integer prospect
   * @param boolean $allow_empty Is an empty value interpreted as 0?
   * @return object Returns the integer (or 0 if empty).
   * @see RAISABLE::validate_as_integer_silent
   * @access private
   */
  public function validate_as_integer ($value, $allow_empty = true)
  {
    $validated_value = $this->validate_as_integer_silent ($value, $allow_empty);
    if ($validated_value !== false)
    {
      return $validated_value;
    }

    $value = htmlentities($value);
    $this->raise ("[$value] is not an integer.");
  }

  /**
   * Return a custom exception handler for this object.
   * @return EXCEPTION_HANDLER
   * @access private
   */
  protected function _exception_handler ()
  {
    return null;
  }
}

/**
 * Identifies error with object/class/routine scope.
 * @see function raise()
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.2.1
 */
class EXCEPTION_HANDLER
{
  /**
   * Generates a plain-text page halt with the error information.
   * @param string $message The error message
   * @param string $routine_name The name of the routine where the error occurred (can be empty)
   * @param string $class_name The name of the class where the error occurred (can be empty)
   * @param object $obj Reference to the object where the error occurred (can be empty)
   */
  public function raise ($message, $routine_name, $class_name, $obj)
  {
    $sig = $this->signature ($message, $routine_name, $class_name, $obj);

    if ($sig->is_derived_type)
    {
      $this->dispatch ($sig, "Fatal error in $sig->dynamic_class_name => $sig->scope: $message");
    }
    else
    {
      $this->dispatch ($sig, "Fatal error in $sig->scope: $message");
    }
  }

  /**
   * Builds the error condition context
   * @param string $message Message issued with the exception.
   * @param string $routine_name The name of the routine where the error occurred (can be empty)
   * @param string $class_name The name of the class where the error occurred (can be empty)
   * @param object $obj Reference to the object where the error occurred (can be empty)
   * @return EXCEPTION_SIGNATURE
   * @access private
   */
  public function signature ($message, $routine_name, $class_name, $obj)
  {
    include_once ('webcore/sys/exception_signature.php');
    $Result = new EXCEPTION_SIGNATURE ($message, $routine_name, $class_name, $obj);
    $Result->load_from_exception ($message, $routine_name, $class_name, $obj);
    return $Result;
  }

  /**
   * Dispatches the error message to the handler.
   * Override in descendants to use the same message, but a different delivery mechanism. The
   * default behavior issues a PHP {@link die()} command, which stops page-processing.
   * @param EXCEPTION_SIGNATURE $sig
   * @param string $msg Pre-formatted error message.
   * @access private
   */
  public function dispatch ($sig, $msg)
  {
    die ($msg);
  }
}

/**
 * Meta-information for a {@link WEBCORE_OBJECT}
 * Used to be able to generically handle lists of varying entries.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.5.0
 * @access private
 */
class TYPE_INFO
{
  /**
   * Type name, unique within the application.
   * @var string
   */
  public $id = 'object';

  /**
   * Title for a single object.
   * @var string
   */
  public $singular_title = 'Object';

  /**
   * Title for more than one of these objects.
   * @var string
   */
  public $plural_title = 'Objects';

  /**
   * Object is reachable via this URL.
   * May not be set for some objects.
   * @var string
   */
  public $home_page = '';

  /**
   * Resource location inside the {@link Folder_name_icons} folder.
   * May be empty.
   * @var string
   */
  public $icon = '{icons}buttons/new_object';

  /**
   * @var string
   */
  public $edit_page = 'edit_object.php';

  /**
   * Return the number with the title attached as units.
   * Uses {@link $plural_title} or {@link $singular_title}.
   */
  public function format_amount ($num)
  {
    if ($num == 1)
    {
      $Result = $num . ' ' . $this->singular_title;
    }
    else
    {
      $Result = $num. ' ' . $this->plural_title;
    }
    return $Result;
  }

  /**
   * Gets a unique id for the given object.
   *
   * @param WEBCORE_OBJECT $obj
   */
  public function unique_id ($obj)
  {
    return '';
  }
}

?>