<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.2.0
 * @since 2.2.1
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
require_once ('webcore/sys/files.php');

/**
 * Return the host for a URL.
 */
define ('Url_part_host', 0x01);

/**
 * Return the path for a URL (Not including the host).
 */
define ('Url_part_path', 0x02);

/**
 * Return the name of the file for a URL (not including extension).
 */
define ('Url_part_name', 0x04);

/**
 * Return the extension for a URL.
 */
define ('Url_part_ext', 0x08);

/**
 * Return the query string for a URL.
 */
define ('Url_part_args', 0x10);

/**
 * Return the name and extension for a URL.
 */
define ('Url_part_file_name', 0x0C);

/**
 * Return the file name and arguments for a URL.
 */
define ('Url_part_no_host_path', 0x1C);

/**
 * Return the path and file name for a URL.
 */
define ('Url_part_no_host_args', 0x0E);

/**
 * Return the path, file name and arguments for a URL.
 */
define ('Url_part_no_host', 0x1E);

/**
 * Return the entire URL without the query string.
 */
define ('Url_part_no_args', 0x0F);

/**
 * Return the entire URL.
 */
define ('Url_part_all', 0x1F);

/**
 * Settings used by the {@link URL}.
 * The extra settings found here are best set through
 * {@link ENVIRONMENT::set_host_properties()} and are used by the conversion
 * functions, {@link url_to_file_name()}, {@link url_to_folder()} and {@link
 * file_name_to_url()}.
 * @see has_local_domain()
 * @package webcore
 * @subpackage sys
 * @version 3.2.0
 * @since 2.7.0
 */
class URL_OPTIONS extends FILE_OPTIONS
{
  /**
   * List of domain to document root mappings. Use {@link has_local_domain()} to
   * test whether a {@link URL} is considered local or not. The strings can be
   * regular expressions.
   * @var array[string,string]
   */
  public $domains;

  /**
   * The main domain for this web site.
   * @var string
   */
  public $main_domain;

  public function __construct ()
  {
    $this->domains = array ('http://localhost' => $_SERVER ['DOCUMENT_ROOT']);
  }
}

/**
 * URL concatenation and analysis.
 * @package webcore
 * @subpackage sys
 * @version 3.2.0
 * @since 2.2.1
 */
class URL
{
  /**
   * @param string $text Initial path.
   */
  public function __construct ($text)
  {
    $this->_text = urldecode ($text);
  }

  /**
   * @param string $text Path to use.
   */
  public function set_text ($text)
  {
    $this->_text = $text;
  }

  /**
   * Return the rendered URL.
   * @param boolean $clean_query_string If True, each argument in the query
   * string is examined and cleaned with {@link PHP_MANUAL#htmlspecialchars}.
   * @return string
   */
  public function as_text ($clean_query_string = false)
  {
    $Result = $this->_text;

    if ($clean_query_string)
    {
      list($text, $args) = $this->_extract_resource_and_args ();
      if ($args)
      {
        $qs_parts = explode ('&', $args);
        $new_parts = array ();
        foreach ($qs_parts as $qs_part)
        {
          $equal_pos = strpos ($qs_part, '=');
          if ($equal_pos !== false)
          {
            $new_parts [] = substr ($qs_part, 0, $equal_pos) . '=' . htmlspecialchars (substr ($qs_part, $equal_pos + 1));
          }
          else
          {
            $new_parts [] = $qs_part;
          }
        }
        $Result = $text . '?' . implode ('&', $new_parts);
      }
    }

    return $Result;
  }

  /**
   * Return the rendered URL, escaped for HTML.
   * @return string
   */
  public function as_html ()
  {
    return htmlentities ($this->_text);
  }

  /**
   * Just the domain name, without trailing delimeter.
   * @return string
   */
  public function domain ()
  {
    return extract_domain ($this->_text, $this->options ());
  }

  /**
   * Just the path without the file name.
   * Trailing '/' is included
   * @return string
   */
  public function path ()
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    $slash = $this->path_delimiter ();
    $slash_pos = strrpos ($text, $slash);
    if ($slash_pos !== false)
    {
      // slash is part of protocol (e.g. http://earthli.com)
      if (($slash_pos != 0) && ($text [$slash_pos - 1] == $slash))
      {
        return $text . $slash;
      }

      // slash is last character (e.g. http://earthli.com/users/)
      if ($slash_pos == (strlen ($text) - 1))
      {
        return $text;
      }

      // return everything before the slash (e.g. http://earthli.com/users/readme.txt)
      return substr ($text, 0, $slash_pos + 1);
    }
    else
    {
      if ($this->_extension_for($text) == '')
      {
        return $text . $slash;
      }
    }
    
    return '';
  }

  /**
   * Just the path without the file name.
   * Trailing '/' is included
   * @return string
   */
  public function path_without_domain ()
  {
    return strip_domain($this->path(), $this->options());
  }

  /**
   * Just the file name with extension.
   * @return string
   */
  public function name ()
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    $slash = $this->path_delimiter ();
    $slash_pos = strrpos ($text, $slash);
    if ($slash_pos >= 0)
    {
      if ($slash_pos === false)
      {
        return $text;
      }

      // slash is part of protocol (e.g. http://earthli.com)
      if (($slash_pos != 0) && ($text [$slash_pos - 1] == $slash))
      {
        return '';
      }

      return substr ($text, $slash_pos + 1);
    }

    return $text;
  }

  /**
   * Return text after the last delimiter.
   * @return string
   */
  public function name_with_query_string ()
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    return $this->_text_for_resource_and_args ($this->name (), $args);
  }

  /**
   * Just the file name without the extension.
   * @return string
   */
  public function name_without_extension ()
  {
    $name = $this->name ();
    $ext_len = strlen ($this->extension ());
    if ($ext_len)
    {
      return substr ($name, 0, - $ext_len - 1);
    }

    return $name;
  }

  /**
   * Just the file extension.
   * @return string
   */
  public function extension ()
  {
    return $this->_extension_for ($this->name ());
  }

  /**
   * Just the query string.
   * This is the portion after the '?'. Can be empty.
   * @return string
   */
  public function query_string ()
  {
    $args_pos = strpos ($this->_text, '?');
    if ($args_pos)
    {
      return substr ($this->_text, $args_pos + 1);
    }
    
    return '';
  }

  /**
   * The default separator for folders.
   * @return char
   * @see options()
   */
  public function path_delimiter ()
  {
    $opts = $this->options ();
    return $opts->path_delimiter;
  }

  /**
   * Default option for this object.
   * @return FILE_OPTIONS
   * @see path_delimiter()
   */
  public function options ()
  {
    return global_url_options ();
  }

  /**
   * Replace the file extension.
   * @param string $ext
   */
  public function replace_extension ($ext)
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    $curr_ext = $this->_extension_for ($text);
    if ($curr_ext)
    {
      $text = substr ($text, 0, - strlen ($curr_ext) - 1);
    }
    else
    {
      if (ends_with_delimiter ($text, $this->options ()))
      {
        $text = substr ($text, 0, -1);
      }
    }
    if ($ext)
    {
      $text = $text . '.' . $ext;
    }
    $this->_text = $this->_text_for_resource_and_args ($text, $args);
  }

  /**
   * Add the text to the file name.
   * Used to generate thumbnail names.
   */
  public function append_to_name ($name)
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    $parts = explode ('.', $text);
    if (sizeof ($parts) > 1)
    {
      $last = array_pop ($parts);
      $text = implode ('.', $parts) . $name . '.' . $last;
    }
    else
    {
      $text .= $name;
    }
    $this->_text = $this->_text_for_resource_and_args ($text, $args);
  }

  /**
   * Replace the file name, preserving extension and query string.
   * @param string $name
   */
  public function replace_name ($name)
  {
    $path = $this->path ();
    $qs = $this->query_string ();
    $ext = $this->extension ();
    $this->_text = $path . $name;
    if ($ext)
    {
      $this->_text .= '.' . $ext;
    }
    if ($qs)
    {
      $this->_text .= '?' . $qs;
    }
  }

  /**
   * Replace the file name and extension.
   * Path and query arguments are retained.
   * @param string $name
   */
  public function replace_name_and_extension ($name)
  {
    $path = $this->path ();
    $qs = $this->query_string ();
    $this->_text = $path . $name;
    if ($qs)
    {
      $this->_text .= '?' . $qs;
    }
  }

  /**
   * Replace the entire query string.
   * @param string $query
   */
  public function replace_query_string ($query)
  {
    $qs = $this->query_string ();
    if ($qs)
    {
      $this->_text = substr ($this->_text, 0, - strlen ($qs) - 1);
    }
    if ($query)
    {
      $this->_text .= '?' . $query;
    }
  }

  /**
   * Removes the file name from the URL.
   */
  public function strip_name ()
  {
    $this->_text = $this->path ();
  }

  /**
   * Removes the domain from the URL.
   */
  public function strip_domain ()
  {
    $this->_text = strip_domain ($this->_text, $this->options ());
  }

  /**
   * Removes the protocol from the URL.
   */
  public function strip_protocol ()
  {
    $this->_text = strip_protocol ($this->_text);
  }

  /**
   * Add a url argument.
   * @param string $arg
   */
  public function add_argument ($name, $value)
  {
    if (is_bool ($value) && ! $value)
    {
      $value = 0;
    }
    $this->add_arguments ("$name=$value");
  }

  /**
   * Add pre-built arguments.
   * Does not check whether the arguments already exist. Use {@link
   * replace_arguments()} instead.
   * @param string $args
   */
  public function add_arguments ($args)
  {
    if ($args)
    {
      if (strpos ($this->_text, '?') !== false)
      {
        $this->_text .= '&' . $args;
      }
      else
      {
        $this->_text .= '?' . $args;
      }
    }
  }

  /**
   * Add pre-built arguments, replacing existing values.
   * Use {@link add_arguments()} to add without checking existing arguments.
   * Empty values are removed from the string. Use this function to integrate a
   * chunk of an existing query string.
   * @param string $args_as_string String of query string-style arguments
   * (key1=value&key2=value...)
   */
  public function replace_arguments ($args_as_string)
  {
    if ($args_as_string)
    {
      $pairs = explode ('&', $args_as_string);
      foreach ($pairs as $pair)
      {
        @list ($n, $v) = explode ('=', $pair);
        $args [strtolower ($n)] = $v;
      }

      $this->_integrate_into_query_string ($args);
    }
  }

  /**
   * Replace (or add) a url argument.
   * If the value is empty, the argument is removed. Use {@link
   * replace_arguments()} to add multiple values (or a chunk of an existing
   * query string).
   * @param string $arg_name
   * @param string $arg_value
   */
  public function replace_argument ($name, $value)
  {
    $this->_integrate_into_query_string (array ($name => $value));
  }

  /**
   * Removes 'num' folders from the path.
   * @param integer $num
   */
  public function go_back ($num = 1)
  {
    $name = $this->name_with_query_string ();
    $this->strip_name ();
    $this->append (str_repeat ('..' . $this->path_delimiter (), $num));
    if ($name)
    {
      $this->_text .= $name;
    }
  }

  /**
   * Append a url to the current one
   * Handles separate merging and resolves all '..' marks
   * @param string $url
   */
  public function append ($url)
  {
    $opts = $this->options ();
    $is_file = is_file_name ($url, $opts);
    $this->_text = join_paths ($this->_text, $url, $opts);
    if (! $is_file && ! $this->ends_with_delimiter ())
    {
      $this->_text .= $opts->path_delimiter;
    }
  }

  /**
   * Append a url to a copy of this one.
   * Returns a copy of this url instead of modifying it.
   * @param string $url
   * @return string
   */
  public function appended_as_text ($url)
  {
    // CLONE
    $copy = $this;
    $copy->append ($url);
    return $copy->as_text ();
  }

  /**
   * Append a folder to the URL.
   * File name is preserved if present.
   * @param string $folder
   */
  public function append_folder ($folder)
  {
    list($text, $args) = $this->_extract_resource_and_args ();
    if ($text)
    {
      $sep = $this->path_delimiter ();
      $parts = explode ($sep, $text);
      $last = array_pop ($parts);
      $text = implode ($sep, $parts) . $sep . $folder . $sep . $last;
    }
    $this->_text = $this->_text_for_resource_and_args ($text, $args);
  }

  /**
   * Prepends a url to the current one
   * Only prepends if the current url is not already fully-qualified
   * @param string $url
   */
  public function prepend ($url)
  {
    if (! $this->has_domain ())
    {
      $base = $this->_text;
      $this->_text = $url;
      $this->append ($base);
    }
  }
  
  public function make_relative_to ($url)
  {
    list($text, $args) = $this->_extract_resource_and_args ();    
    $this->_text = $this->_text_for_resource_and_args (path_between ($url, $text), $args);
  }

  /**
   * Does the path include a drive or domain?
   * @see has_domain()
   * @param string $domain If not empty, checks for the given domain only.
   * @return boolean
   */
  public function has_domain ($domain = '')
  {
    return has_domain ($this->_text, $domain, $this->options ());
  }

  /**
   * Does the path point to a local url?
   * @see has_domain()
   * @return boolean
   */
  public function has_local_domain ()
  {
    return has_local_domain ($this->_text, $this->options ());
  }

  /**
   * Make sure the path ends in a delimiter.
   * @return string
   */
  public function ensure_ends_with_delimiter ()
  {
    if (! $this->ends_with_delimiter ())
    {
      $opts = $this->options ();
      $this->_text .= $opts->path_delimiter;
    }
  }

  /**
   * Is the URL terminated by a delimiter?
   * @access private
   * @return boolean
   */
  public function ends_with_delimiter ()
  {
    return ends_with_delimiter ($this->_text, $this->options ());
  }

  /**
   * Return the resource and query string.
   * @return array[string] The first entry is the resource, then second is the query string. Either may be empty.
   * @access private
   */
  protected function _extract_resource_and_args ()
  {
    $args_pos = strpos ($this->_text, '?');
    if ($args_pos !== false)
    {
      $Result [] = substr ($this->_text, 0, $args_pos);
      $Result [] = substr ($this->_text, $args_pos + 1);
    }
    else
    {
      $Result [] = $this->_text;
      $Result [] = '';
    }

    return $Result;
  }

  /**
   * Return a location for these parameters.
   * @param string $text
   * @param string $args
   * @access private
   */
  protected function _text_for_resource_and_args ($text, $args)
  {
    $Result = $text;

    if ($args)
    {
      if ($text)
      {
        $Result = $text . '?' . $args;
      }
      else
      {
        $Result = $args;
      }
    }

    return $Result;
  }

  /**
   * Return the extension for the given file name.
   * @param string $text May have a path, but may not have a query string.
   * @access private
   */
  protected function _extension_for ($text)
  {
    $opts = $this->options ();
    $dot_pos = strrpos ($text, '.');
    $sep_pos = strrpos ($text, $opts->path_delimiter);
    if (($dot_pos !== false) && (($sep_pos === false) || ($dot_pos > $sep_pos)))
    {
      return substr ($text, $dot_pos + 1);
    }
    
    return '';
  }

  /**
   * Add arguments, replacing existing values.
   * Used by {@link replace_arguments()} and {@link replace_argument()}.
   * @param array[string,string] $new_args Key/value pairs to insert into the
   * query string.
   * @access private
   */
  protected function _integrate_into_query_string ($new_args)
  {
    @list ($url, $query) = explode ('?', $this->_text);
    if (! isset ($query))
    {
      foreach ($new_args as $name => $value)
      {
        if ($value !== '')
        {
          $this->add_argument ($name, $value);
        }
      }
    }
    else
    {
      $this->_text = $url;
      $current_args = explode ('&', $query);
      foreach ($current_args as $arg)
      {
        @list ($n, $v) = explode ('=', $arg);
        $n = strtolower ($n);
        if (isset ($new_args [$n]))
        {
          if ($new_args [$n] !== '')
          {
            $this->add_argument ($n, $new_args [$n]);
          }
          unset ($new_args [$n]);
        }
        else
        {
          $this->add_arguments ($arg);
        }
      }

      foreach ($new_args as $name => $value)
      {
        if ($value !== '')
        {
          $this->add_argument ($name, $value);
        }
      }
    }
  }
}

/**
 * Manages urls for the server file system.
 * @package webcore
 * @subpackage sys
 * @version 3.2.0
 * @since 2.5.0
 */
class FILE_URL extends URL
{
  /**
   * Creates a URL that is treated as a server-local file.
   * '/' is automatically converted to the local form, so you can freely
   * base a file system path on an internet URL.
   * @param CONTEXT $context
   * @param string $text Initial path.
   */
  public function __construct ($text)
  {
    parent::__construct ($text);
    $this->_text = str_replace ('/', $this->path_delimiter (), $this->_text);
  }

  /**
   * Transform the file name, if necessary, into a legal id for the server file system.
   * @see normalize_path()
   */
  public function normalize ()
  {
    $this->_text = normalize_path ($this->_text);
  }

  /**
   * Is this a legal id for the server file system?
   * @see is_valid_path()
   * @return boolean
   */
  public function is_valid ()
  {
    return is_valid_path ($this->_text);
  }

  /**
   * Does the file exist on the server?
   * @return boolean
   */
  public function exists ()
  {
    return file_exists ($this->_text);
  }

  /**
   * Attempt to create the path to this resource, if it does not exist.
   * Not guaranteed to succeed. Test with {@link exists()} to determine
   * if it succeeded. Will only create the directories; use {@link ensure_exists()}
   * to create the file, if one is included in the URL.
   * @param boolean $normalize_allowed If True, illegal file ids are converted before attempting creation.
   */
  public function ensure_path_exists ($normalize_allowed = true)
  {
    if ($normalize_allowed)
    {
      $this->normalize ();
    }

    ensure_path_exists ($this->path ());
  }

  /**
   * Attempt to create the file or path, if it does not exist.
   * Not guaranteed to succeed. Test with {@link exists()} to determine
   * if it succeeded. Works just like {@link ensure_path_exists()} if the
   * URL does not include a file name.
   * @param boolean $normalize_allowed If True, illegal file ids are converted before attempting creation.
   */
  public function ensure_file_exists ($normalize_allowed = true)
  {
    if (! is_file ($this->_text))
    {
      $opts = $this->options ();
      $this->ensure_path_exists ($normalize_allowed);
      if (is_file_name ($this->_text, $opts))
      {
        fopen ($this->_text, 'w+');
        chmod ($this->_text, $opts->default_access_mode);
      }
    }
  }

  /**
   * Return true if there is that path is fully expanded.
   * @return boolean
   */
  public function has_root ()
  {
    return has_root ($this->_text, $this->options ());
  }

  /**
   * Default options for this object.
   * @return FILE_OPTIONS
   */
  public function options ()
  {
    return global_file_options ();
  }

  public function write_text_file ($text)
  {
    $this->ensure_path_exists ();
    write_text_file ($this->as_text (), $text);
  }
}

/**
 * Make sure the path includes the given protocol.
 * Strips the existing protocol using {@link strip_protocol()} if necessary.
 * Performs no check to ensure a domain exists.
 * @param string $f
 * @param string $protocol
 * @return string
 */
function ensure_has_protocol ($f, $protocol)
{
  if (! has_protocol ($f, $protocol))
  {
    if (has_protocol ($f))
    {
      strip_protocol ($f);
    }
    
    return $protocol . '://' . $f;
  }
  
  return $f;
}

/**
 * Does the path contain a protocol?
 * @see strip_protocol()
 * @param string $f The path to check.
 * @param string $protocol If not empty, checks for the given protocol only.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function has_protocol ($f, $protocol = '')
{
  $Result = strpos ($f, ':') > 0;
  if ($Result && $protocol)
  {
    $Result = strpos ($f, $protocol) === 0;
  }
  
  return $Result;
}

/**
 * Remove any drive or domain information.
 * @see has_domain()
 * @param string $f The path to check.
 * @return string
 */
function strip_protocol ($f)
{
  $colon_pos = strpos ($f, ':');
  if ($colon_pos !== false)
  {
    $f = substr ($f, $colon_pos + 3);
  }
  return $f;
}

/**
 * Remove any drive or domain information.
 * @see has_domain()
 * @param string $f The path to check.
 * @return array[string,string]
 */
function split_protocol ($f)
{
  $colon_pos = strpos ($f, ':');
  if ($colon_pos !== false)
  {
    $Result [] = substr ($f, 0, $colon_pos + 3);
    $Result [] = substr ($f, $colon_pos + 3);
  }
  else
  {
    $Result [] = '';
    $Result [] = $f;
  }

  return $Result;
}

/**
 * Return the domain for the given URL.
 * May be empty.
 * @param string $f
 * @return string
 */
function extract_domain ($f, $opts = null)
{
  $colon_pos = strpos ($f, ':');
  if ($colon_pos !== false)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }

    $slash_pos = strpos ($f, $opts->path_delimiter, $colon_pos + 3);
    if ($slash_pos > $colon_pos)
    {
      $result = substr ($f, $colon_pos + 3, $slash_pos - $colon_pos - 3);
    }
    else
    {
      $result = substr ($f, $colon_pos + 3);
    }

    return $result;
  }

  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }
  $slash_pos = strpos ($f, $opts->path_delimiter);
  if ($slash_pos !== false)
  {
    return substr ($f, 0, $slash_pos);
  }
  return $f;
}

/**
 * Does the path contain any domain information?
 * Returns true if the a Windows file name specifies a drive or if a URL has
 * a protocol and domain.
 * @see strip_domain()
 * @param string $f The path to check.
 * @param string $domain If not empty, checks for the given domain only. Can be
 * a regular expression.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function has_domain ($f, $domain = '', $opts = null)
{
  $Result = strpos ($f, ':');
  if ($Result && $domain)
  {
    $Result = strpos ($f, $domain, $Result + 3) === $Result + 3;
    if (! $Result)
    {
      $Result = preg_match ('/' . $domain . '/', extract_domain ($f, $opts));
    }
  }

  return $Result;
}

/**
 * Remove any drive or domain information.
 * @see has_domain()
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return string
 */
function strip_domain ($f, $opts = null)
{
  $colon_pos = strpos ($f, ':');
  if ($colon_pos !== false)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    $slash_pos = strpos ($f, $opts->path_delimiter, $colon_pos + 3);
    $f = substr ($f, $slash_pos);
  }
  return $f;
}

/**
 * Check whether 'f' is on the current server.
 * Checks the list of local domains registered in the options until it finds a
 * match.
 * @param string $f
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function has_local_domain ($f, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_url_options ();
  }
  if (has_domain ($f, '', $opts))
  {
    $matches = null;
    foreach ($opts->domains as $url => $document_root)
    {
      $is_match = preg_match ('/^' . str_replace('/', '\\/', $url) . '/', $f, $matches);
      if ($is_match)
      {
        return array ($matches[0], $document_root);
      }
    }
  }
  return false;
}

/**
 * Convert a file name to a url on this server.
 * Returns empty if the file is not in the server's document root.
 * @param string $f
 * @param FILE_OPTIONS $opts
 * @return string
 */
function file_name_to_url ($f, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_url_options ();
  }

  foreach ($opts->domains as $url_root => $document_root)
  {
    $pos = strpos ($f, $document_root);
    if ($pos === 0)
    {
      $Result = substr ($f, strlen (ensure_ends_with_delimiter ($document_root)) - 1);
      $Result = str_replace ('\\', $opts->path_delimiter, $Result);
      return  join_paths($url_root, $Result, $opts);
    }
  }

  return '';
}

/**
 * Convert a url to a local file name.
 * Useful for manipulating server-local files as files instead of URLs (e.g.
 * images or uploaded files). Prepends {@link FILE_OPTIONS::$document_root}
 * to potentially valid URLs (see validation clauses below).
 *
 * <ul>
 * <li>If 'f' has a domain, it must match the one in {@link
 * FILE_OPTIONS::$domain}.</li>
 * <li>If {@link FILE_OPTIONS::$url_root} is non-empty, the path must begin with
 * that folder.</li>
 * <li>Returns empty if neither of the two cases above is satisfied.</li>
 * </ul>
 *
 * @param string $f
 * @param FILE_OPTIONS $opts
 * @return string
 */
function url_to_file_name ($f, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_url_options ();
  }

  list($local_domain, $document_root) = has_local_domain ($f, $opts);

  if ($local_domain)
  {
    return join_paths ($document_root, substr($f, strlen($local_domain)));
  }

  return '';
}

/**
 * Convert a url to a local folder.
 * Automatically removes the domain name and will only work for the server file system.
 * Useful for calculating paths for uploaded files.
 * @see UPLOADER
 * @param string $f
 * @param FILE_OPTIONS $opts
 * @param boolean $create_if_not_found Creates the folder if it doesn't exist.
 * @return string
 * @see ensure_path_exists()
 */
function url_to_folder ($f, $opts = null, $create_if_not_found = true)
{
  $Result = url_to_file_name ($f, $opts);
  if ($Result && $create_if_not_found && ! is_dir ($Result))
  {
    ensure_path_exists ($Result);
  }
  return $Result;
}

/**
 * Returns an email address without '@' or '.'.
 * Given 'bob@net.com', it returns 'bob at net dot com'.
 * @param string $email
 * @return string
 */
function scramble_email ($email)
{
  return str_replace ('.', ' [dot] ', str_replace ('@', ' [at] ', $email));
}

/**
 * Returns the global url options.
 * Used by {@link URL} to pass to file functions.
 * @return FILE_OPTIONS
 * @access private
 */
function global_url_options ()
{
  global $_g_url_options;
  if (! isset ($_g_url_options))
  {
    $_g_url_options = new URL_OPTIONS ();
    $_g_url_options->path_delimiter = '/';
  }
  return $_g_url_options;
}

/**
 * Cached copy of file options.
 * Accessed using {@link global_file_options()}.
 * @global FILE_OPTIONS
 * @access private
 */
$_g_url_options = null;

?>