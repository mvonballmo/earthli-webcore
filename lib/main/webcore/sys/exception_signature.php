<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
 * @since 2.6.0
 * @access private
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

/**
 * Information about the error condition.
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
 * @since 2.5.0
 * @access private
 */
class EXCEPTION_SIGNATURE
{
  /**
   * Exception scope (class name, function name or 'global').
   * @var string
   */
  public $scope;

  /**
   * Is the dynamic class different than the scope?
   * If the dynamic type is other than the class in which the exception-generating method
   * is defined, this flag is set.
   * @var boolean
   */
  public $is_derived_type;

  /**
   * If non-empty, the routine that generated the exception.
   * This can be empty if the exception happened in a global routine or scope.
   * @var string
   */
  public $routine_name;

  /**
   * If non-empty, the routine is a method of this class.
   * This can be empty if the exception happened in a global routine or scope.
   * @var string
   */
  public $class_name;

  /**
   * Name of the dynamic class that generated the exception.
   * This can be empty if the exception happened in a global routine or scope.
   * @var string
   */
  public $dynamic_class_name;

  /**
   * Page in which the exception occurred.
   * @var string
   */
  public $page_name;

  /**
   * Name and version of the application.
   * The exception handler page may not instantiate the application that raised the exception, so
   * pass along this information manually.
   * @var string
   */
  public $application_description;

  /**
   * Message issued with the exception.
   * @var string
   */
  public $message;

  /**
   * Record values from the given exception state.
   * Use this method to store the characteristics of an exception, then use {@link as_query_string()} to
   * send the signature to an error handler page.
   * @param string $msg
   * @param string $routine_name May be empty.
   * @param string $class_name May be empty.
   * @param object $obj May be empty.
   */
  public function load_from_exception ($msg, $routine_name, $class_name, $obj)
  {
    $this->message = $msg;
    $this->routine_name = $routine_name;
    $this->class_name = $class_name;

    if (isset ($obj) && isset ($obj->env))
    {
      $this->page_name = $obj->env->url ();
    }
    else
    {
      $this->page_name = "http://{$_SERVER ['HTTP_HOST']}{$_SERVER ['SCRIPT_NAME']}";
    }

    if (isset ($obj) && isset ($obj->app))
    {
      $this->application_description = $obj->app->description ();
    }

    if ($class_name)
    {
      $this->scope = "$class_name.$routine_name";
      if (isset ($obj))
      {
        $this->dynamic_class_name = strtoupper (get_class ($obj));
      }

      $this->is_derived_type = isset ($this->dynamic_class_name) && ($this->dynamic_class_name != $class_name);
    }
    else if ($routine_name)
 {
   $this->scope = $routine_name;
 }
    else
    {
      $this->scope = 'global scope';
    }
  }

  /**
   * Load signature from the page request.
   * Loads the signature on a page that displays an exception that occurred elsewhere. The request url
   * should have been created with {@link as_query_string()} or in a form that included the values returned
   * from {@link as_array()}.
   */
  public function load_from_request ()
  {
    $this->page_name = $this->_decode_value (read_var ('page_name'));
    $this->application_description = $this->_decode_value (read_var ('application_description'));
    $this->class_name = $this->_decode_value (read_var ('class_name'));
    $this->dynamic_class_name = $this->_decode_value (read_var ('dynamic_class_name'));
    $this->routine_name = $this->_decode_value (read_var ('routine_name'));
    $this->message = $this->_decode_value (read_var ('error_message'));

    $this->_add_vars_for (Var_type_post);
    $this->_add_vars_for (Var_type_get);
    $this->_add_vars_for (Var_type_cookie);
    $this->_add_vars_for (Var_type_upload);
  }
  
  /**
   * @return boolean
   */
  public function exists ()
  {
    return true;
  }

  /**
   * Return a list of values for this exception.
   * This is the list of values available for this type when the exception occurred.
   * @param string $type Can be {@link Var_type_post}, {@link Var_type_get}, {@link Var_type_cookie} or {@link Var_type_upload}.
   * @return string[]
   */
  public function variables_for ($type)
  {
    if (isset ($this->_variables [$type]))
    {
      return $this->_variables [$type];
    }
    
    return null;
  }

  /**
   * Flattened URL-ready version of this signature.
   * Ensures that the entire exception state, including cookie, post and other information is properly encoded
   * in a format that fits within URL restrictions.
   * @return string
   */
  public function as_query_string ()
  {
    $params = $this->as_array ();
    $Result = '';
    
    foreach ($params as $name => $value)
    {
      if (! empty ($Result))
      {
        $Result .= '&';
      }
      $Result .= $name . '=' . $value;
    }

    return $Result;
  }

  /**
   * An HTML-form that recreates the exception.
   * Pass in an array of name/value pairs which will be included in the form (e.g. for specifying
   * debugging values). Forms with file uploading will not be properly recreated (since the browser
   * actually needs a selected file in order to do mark a field as an uploaded file).
   * $param string $name
   * @param $params array An array of parameters to include in the simulated form submission.
   * @param $name string The name of the form to simulate
   * @internal param $array string[][]
   * @return string
   */
  public function as_form ($params, $name)
  {
    $Result = '<form id="' . $name . '" style="display: inline" action="' . $this->page_name . '" method="POST"><div style="display: inline">' . "\n";

    if (sizeof ($params))
    {
      foreach ($params as $name => $value)
      {
        $Result .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
      }
    }

    $fields = $this->variables_for (Var_type_post);
    if (isset ($fields))
    {
      foreach ($fields as $name => $value)
      {
        if (! array_key_exists ($name, $params))
        {
          $Result .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
        }
      }
    }

    $fields = $this->variables_for (Var_type_get);
    if (isset ($fields))
    {
      foreach ($fields as $name => $value)
      {
        if (! array_key_exists ($name, $params))
        {
          $Result .= "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
        }
      }
    }

    $Result .= "</div></form>\n";

    return $Result;
  }
  
  /**
   * List of all name/value pairs for all properties.
   * Allows this signature to be easily encoded in a form (e.g. by creating a hidden field for each pair
   * in this array). Encoding in a form is useful if the error handler page wants to send the signature
   * further or to build a form that can recreate the error.
   * @return string[]
   */
  public function as_array ()
  {
    $Result ['page_name'] = $this->_encode_value ($this->page_name);
    $Result ['application_description'] = $this->_encode_value ($this->application_description);
    $Result ['class_name'] = $this->_encode_value ($this->class_name);
    $Result ['dynamic_class_name'] = $this->_encode_value ($this->dynamic_class_name);
    $Result ['routine_name'] = $this->_encode_value ($this->routine_name);
    $Result ['error_message'] = $this->_encode_value ($this->message);

    $this->_add_values_from (Var_type_post, $Result);
    $this->_add_values_from (Var_type_get, $Result);
    $this->_add_values_from (Var_type_cookie, $Result);
    $this->_add_values_from (Var_type_upload, $Result);

    return $Result;
  }

  protected function _encode_value ($value)
  {
    return urlencode (str_replace (array ("\r", "\n", '&'), array ('', '[LF]', '[AMP]'), $value));
  }
  
  protected function _decode_value ($value)
  {
    return urldecode (str_replace (array ('[LF]', '[AMP]'), array ("\n", '&'), $value));
  }

  /**
   * Add variables for this type to the array.
   * Used by {@link as_array()} to include information about the post/get/cookie state when the
   * exception occurred.
   * @param string $title Name of the array to get. Can be {@link Var_type_post}, {@link Var_type_get}, {@link Var_type_cookie} or {@link Var_type_upload}.
   * @param string[] $to_array
   * @access private
   */
  protected function _add_values_from ($title, $to_array)
  {
    if (isset ($this->_variables))
    {
      if (isset ($this->_variables [$title]))
      {
        $arr = $this->_variables [$title];
      }
    }
    else
    {
      global $$title;
      $arr = $$title;
    }

    if (isset ($arr) && sizeof ($arr))
    {
      foreach ($arr as $name => $value)
      {
        if (is_array ($value))
        {
          $to_array [$title . $name] = $this->_encode_value(join (',', $value));
        }
        else
        {
          $to_array [$title . $name] = $this->_encode_value($value);
        }
      }
    }
  }

  /**
   * Retrieves values applicable to this type from the page request.
   * Used when reconstructing the exception from another page.
   * @param string $type Can be {@link Var_type_post}, {@link Var_type_get}, {@link Var_type_cookie} or {@link Var_type_upload}.
   * @access private
   */
  protected function _add_vars_for ($type)
  {
    $variables = array();

    switch ($type)
    {
      case Var_type_cookie:
        $variables = $_COOKIE;
        break;
      case Var_type_get:
        $variables = $_GET;
        break;
      case Var_type_post:
        $variables = $_POST;
        break;
      case Var_type_upload:
        $variables = $_FILES;
        break;
    }

    foreach ($variables as $name => $value)
    {
      $this->_variables [$type][$name] = $this->_decode_value ($value);
    }
  }

  /**
   * Global variable state at time of exception.
   * @var array[]
   * @access private
   */
  protected $_variables;
}