<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
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

/**
 * File settings used by the various file and url functions.
 * @package webcore
 * @subpackage sys
 * @version 3.6.0
 * @since 2.5.0
 */
class FILE_OPTIONS
{
  /**
   * Separates two directories in the file system.
   * @var string
   */
  public $path_delimiter = '/';

  /**
   * Indicates the current directory in the file system.
   * @var string
   */
  public $current_folder = '.';

  /**
   * Separates two lines in a text file.
   * @var string
   */
  public $end_of_line = "\n";

  /**
   * Access mode for new folders.
   * Used by {@link ensure_path_exists()}.
   * @var integer
   */
  public $default_access_mode = 0777;

  /**
   * List of characters to convert <i>from</i> in a file name.
   * {@link normalize_file_id()} replaces these characters with their
   * corresponding entry in {@link $target_chars}.
   * @var string
   */
  public $source_chars = '��������������������������������������������';

  /**
   * List of characters to convert <i>to</i> in afile name.
   * Each character replaces its corresponding entry in {@link
   * $source_chars} if found in a file name.
   * @var string */
  public $target_chars = 'aaaaeeeeiiiioooouuuuAAAAEEEEIIIIOOOOUUUUnNcC';

  /**
   * Longest possible file name.
   * @var integer
   */
  public $max_name_length = 255;

  /**
   * String containing all valid file name characters.
   * @var string
   */
  public $valid_file_chars = 'a-zA-Z0-9,._\+\()\-';

  /**
   * Collapse successive invalid characters into one replacement character.
   * @var boolean
   */
  public $collapse_invalid_chars = true;

  /**
   * Single character used to replace all invalid file name characters.
   * @var string
   */
  public $replacement_char = '_';

  /**
   * Use only lower-case for normalized paths.
   * This avoids path anamolies in case-sensitive file systems (e.g. UNIX-based)
   * that arise when the same path is entered with different case and results in
   * a new directory and/or path.
   * @var boolean
   */
  public $normalized_ids_are_lower_case = true;

  /**
   * Returns true is the path should have a leading separator.
   * Automatically return True for UNIX paths.
   * @return boolean
   */
  public function path_starts_with_delimiter ()
  {
    return $this->path_delimiter == '/';
  }
}

/**
 * Get the collapsed, absolute representation.
 * 
 * Removes any references to the {@link FILE_OPTIONS::$current_folder} and
 * converts folder separators to the {@link FILE_OPTIONS::$path_delimiter}.
 * 
 * @param string $path
 * @param FILE_OPTIONS $opts
 * @return string
 */
function make_canonical ($path, $opts = null)
{
  if (! isset ($opts)) 
  { 
    $opts = global_file_options (); 
  }
  
  $sep = $opts->path_delimiter;
  $curr = $opts->current_folder;
    
  // '\' is a reserved character in PCRE (introduces an escaped character); escape it.
  if ($sep == '\\')
  {
    $sep = '\\' . $sep;
  }
  
  // '.' is a reserved character in PCRE (matches any character but a newline); escape it.
  if ($curr == '.')
  {
    $curr = '\\' . $curr;
  }
  
  return preg_replace (
    array (
      "&\\\\|/&",                          // Find '/' and '\' separators 
      "&([^$curr]|^)(?:[$curr][$sep])+&",  // Find chain of leading or embedded './' that are not '../' 
      "&(?:[$sep][$curr])+$&"              // Find chain of trailing '/.'
    ), 
    array (
      $sep,                                // Replace with the given separator
      '\1',                                // Remove the chain of './' 
      $sep                                 // Replace with a single separator
    ), 
    $path
  );
}

/**
 * Append 'path2' to 'path1'.
 * 
 * Resolves all '..' marks contained in 'path2', removing folders from the end of 'path1'
 * as necessary. 'path2' will be adjusted to conform to the delimiter used.
 * 
 * @param string $path1
 * @param string $path2
 * @param FILE_OPTIONS $opts
 * @return string
 */
function join_paths ($path1, $path2, $opts = null)
{
  if ($path2)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }

    $sep = $opts->path_delimiter;

    $path1 = make_canonical ($path1, $opts);
    $path2 = make_canonical ($path2, $opts);

    if (strstr ($path2, '..'))
    {
      $folders = explode ($sep, $path2);
      $bases = explode ($sep, $path1);

      while (isset ($bases [sizeof ($bases) - 1]) && ($bases [sizeof ($bases) - 1] == ''))
      {
        array_pop ($bases);
      }

      if (sizeof ($bases))
      {
        while (isset ($folders [0]) && ($folders [0] == '..'))
        {
          array_shift ($folders);
          array_pop ($bases);
        }
      }

      $path1 = join ($sep, $bases);
      if (sizeof ($folders))
      {
        $path2 = implode ($sep, $folders);
      }
      else
      {
        $path2 = '';
      }
    }

    if (! $path1)
    {
      $Result = $path2;
    }
    else
    {
      if ($path2)
      {
        if (begins_with_delimiter ($path2, $opts))
        {
          if (ends_with_delimiter ($path1, $opts))
          {
            $Result = $path1 . substr ($path2, 1);
          }
          else
          {
            $Result = $path1 . $path2;
          }
        }
        else
        {
          if (ends_with_delimiter ($path1, $opts))
          {
            $Result = $path1 . $path2;
          }
          else
          {
            $Result = $path1 . $sep . $path2;
          }
        }
      }
      else
      {
        $Result = $path1;
      }
    }
  }
  else
  {
    $Result = $path1;
  }

  return $Result;
}

/**
 * Calculate a path between 'from' and 'to'.
 * Both 'to' and 'from' should be absolute paths and are assumed to be on the
 * same server. If the paths are the same, an empty string is returned (no path
 * modification needed). If there is no common path, 'to' is returned. If 'from'
 * is a sub-path of 'to', a chain of '..' directories is returned. If 'to' is a
 * sub-path of 'from', 'from' is removed from the path in order to make it
 * relative.
 *
 * <b>Examples:</b>
 * <code>path_between ('/a/b/c/', '/a/b/c/') is ''
 * path_between ('/a/b/c/', '/a/b/') is '../'
 * path_between ('/a/b/c/', '/a/') is '../../'
 * path_between ('/a/b/', '/a/b/c/') is 'c/'
 * path_between ('/a/', '/a/b/c/') is 'b/c/'
 * path_between ('/a/d/c', '/a/b/c/') is '/a/b/c/'
 * </code>
 *
 * @param string $from
 * @param string $to
 * @param FILE_OPTIONS $opts
 * @return string
 */
function path_between ($from, $to, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }

  $from = make_canonical ($from, $opts);
  $to = make_canonical ($to, $opts);
  
    
  if (empty($to) || empty($from))
  {
    return $to;
  }
  
  $Result = $to;
  
  if (strpos ($from, $to) === 0)
  {
    // target is subset of source
    
    if (strlen ($from) != strlen ($to))
    {
      $Result = '';
      $sub_url = substr ($from, strlen ($to));
      $sub_folders = explode ($opts->path_delimiter, $sub_url);
      foreach ($sub_folders as $f)
      {
        if ($f)
        {
          $Result .= '..' . $opts->path_delimiter;
        }
      }
      
      if ($Result == '')
      {
        $Result = $opts->current_folder;
      }
      elseif (ends_with_delimiter($Result, $opts) && !ends_with_delimiter($to))
      {
        $Result = substr($Result, 0, -1);
      }
    }
    else 
    {
      $Result = $opts->current_folder . $opts->path_delimiter;
    }
  }
  else if (strpos ($to, $from) === 0)
  {
    // source is subset of target
    
    $Result = substr ($to, strlen ($from));

    if ((strlen($Result) > 1) && ($Result[0] == $opts->path_delimiter))
    {
      $Result = substr($Result, 1);
    }
    elseif ($Result == $opts->path_delimiter)
    {
      $Result = $opts->current_folder . $opts->path_delimiter; 
    }
  }
  elseif (($from[0] == $to[0]) && ($from[0] == $opts->path_delimiter))
  {
    // check for partial containment of rooted paths
    
    $to_folders = explode ($opts->path_delimiter, trim($to, $opts->path_delimiter));
    $from_folders = explode ($opts->path_delimiter, trim($from, $opts->path_delimiter));
    
    $common_root_folder_count = 0;
    $count = min(array(sizeof($to_folders), sizeof($from_folders)));
    
    for ($index = 0; $index < $count; $index += 1)
    {
      if ($from_folders[$index] != '')
      {
        if ($to_folders[$index] == $from_folders[$index])
        {
          $common_root_folder_count += 1;
        }
        else 
        {
          break;
        }
      }
    }
    
    if ($common_root_folder_count > 0)
    {
      $leader = str_repeat('..' . $opts->path_delimiter, sizeof($from_folders) - $common_root_folder_count);

      $Result = $leader . join($opts->path_delimiter, array_slice($to_folders, $common_root_folder_count));
      
      if (ends_with_delimiter($to, $opts))
      {
        $Result .= $opts->path_delimiter;
      }
    }
  }

  return $Result;
}

/**
 * Make sure the given path begins with a delimiter.
 * Does not modify an empty path.
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return string
 */
function ensure_begins_with_delimiter ($f, $opts = null)
{
  if ($f)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    if (! begins_with_delimiter ($f, $opts))
    {
      $Result = $opts->path_delimiter . $f;
    }
    else
    {
      $Result = $f;
    }
    return $Result;
  }
  
  return $f;
}

/**
 * Make sure the given path ends with a delimiter.
 * Neither modifies an empty path nor checks whether the path is a filename.
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return string
 */
function ensure_ends_with_delimiter ($f, $opts = null)
{
  if ($f)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    if (! ends_with_delimiter ($f, $opts))
    {
      $f = $f . $opts->path_delimiter;
    }
    else
    {
      $f = $f;
    }
  }
  return $f;
}

/**
 * Does the given path begin with a delimiter?
 * Use this as a check to determine whether a path represents a 'root' or not.
 * @see ends_with_delimiter()
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function begins_with_delimiter ($f, $opts = null)
{
  if ($f)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    
    return $f[0] == $opts->path_delimiter;
  }
  
  return false;
}

/**
 * Does the given path end with a delimiter?
 * @see begins_with_delimiter()
 * @see ensure_ends_with_delimiter()
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function ends_with_delimiter ($f, $opts = null)
{
  if ($f)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    return substr ($f, -1) == $opts->path_delimiter;
  }
  
  return $f;
}

/**
 * Does the path include the root?
 * Returns <code>True</code> there is a Windows drive or if the name begins with
 * a delimiter and {@link FILE_OPTIONS:: path_starts_with_delimiter()} is true.
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function has_root ($f, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }
  $path_starts_with_delimiter = $opts->path_starts_with_delimiter();
  return ($path_starts_with_delimiter && begins_with_delimiter ($f, $opts)) ||
         (! $path_starts_with_delimiter && (strpos ($f, ':') == 1));
}

/**
 * Find out if the path ends in a file name.
 * Looks for a '.' after the last delimiter and assumes that it's a file extension.
 * @param string $f The path to check.
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function is_file_name ($f, $opts = null)
{
  $dot_pos = strrpos ($f, '.');
  if ($dot_pos !== false)
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }
    $slash_pos = strrpos ($f, $opts->path_delimiter);
    return $slash_pos < $dot_pos;
  }
  
  return false;
}

/**
 * Create a full path out of the given fragment.
 * Prepends the current directory if the fragment is not already
 * a full path.
 * @param string $f
 * @param FILE_OPTIONS $opts
 * @return string
 */
function ensure_has_full_path ($f, $opts = null)
{
  if (! has_root ($f, $opts))
  {
    $Result = join_paths (getcwd (), $f, $opts);
  }
  else
  {
    $Result = $f;
  }
  return $Result;
}

/**
 * Create a valid file name based on 'file_name'.
 * @param string $file_name
 * @param FILE_OPTIONS $opts
 * @return string
 * @see normalize_file_id()
 */
function normalize_file_name ($file_name, $opts = null)
{
  return normalize_file_id ($file_name, $opts);
}

/**
 * Create a valid path based on 'path'.
 * @param string $path
 * @param FILE_OPTIONS $opts
 * @return string
 * @see normalize_file_id()
 */
function normalize_path ($path, $opts = null)
{
  $Result = '';

  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }

  $parts_to_process = explode ($opts->path_delimiter, $path);
  if (sizeof ($parts_to_process))
  {
    /* If the first part is a Windows drive, use it without normalizing it. */

    $part = $parts_to_process [0];
    if (! $opts->path_starts_with_delimiter () && (strlen ($part) == 2) && ($part [1] == ':'))
    {
      $processed_parts [] = array_shift ($parts_to_process);
    }

    /* Process all other path parts normally. */

    foreach ($parts_to_process as $part)
    {
      $processed_parts [] = normalize_file_id ($part, $opts);
    }

    $Result = implode ($opts->path_delimiter, $processed_parts);
  }

  return strtolower ($Result);
}

/**
 * Create a valid identifier for a file system or URL.
 * Characters which are not allowed in a file name are removed. Optimally, the
 * routine returns 'part' itself. Characters with diacritical marks (umlauts, etc.)
 * are mapped to their plain equivalents.
 * @see global_file_options()
 * @see normalize_file_name()
 * @see normalize_path()
 * @param string $part
 * @param FILE_OPTIONS $opts
 * @return string
 */
function normalize_file_id ($part, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }
  
  // Enforce name length
  $Result = substr ($part, 0, $opts->max_name_length);
  
  // Try to replace invalid chars with similar-looking replacements
  $Result = strtr ($Result, $opts->source_chars, $opts->target_chars);
  
  // Replace all remaining invalid characters with a standard replacement
  $Result = preg_replace ('/[^' . $opts->valid_file_chars . ']/', $opts->replacement_char, $Result);
  
  if ($opts->collapse_invalid_chars)
  {
    $Result = preg_replace ('/[' . $opts->replacement_char . ']+/', $opts->replacement_char, $Result);
  }
  
  if ($opts->normalized_ids_are_lower_case)
  {
    $Result = strtolower ($Result);
  }
  
  return $Result;
}

/**
 * Returns a readable version of the given size.
 * Translates 2000000 to 2MB, 2400 to 2.4KB etc.
 * @param integer $size
 * @param bool $force_kb
 * @throws UNKNOWN_VALUE_EXCEPTION
 * @return string
 */
function file_size_as_text ($size, $force_kb = false)
{
  $places = 0;
  while (($size / 1024) > 1)
  {
    $places += 1;
    $size = $size / 1024;
  }

  $size = round ($size, 1);

  switch ($places)
  {
  case 0:
    if ($force_kb | ($size < 1024))
    {
      return $size . ' Bytes';
    }

    return round ($size / 1024, 1) . ' KB';
  case 1:
    return $size . ' KB';
  case 2:
    return $size . ' MB';
  case 3:
    return $size . ' GB';
  case 4:
    return $size . ' TB';
  default:
    throw new UNKNOWN_VALUE_EXCEPTION($places);
  }
}

/**
 * Convert a text version of a file size.
 * Interprets "MB" and "M" as megabytes, "GB" and "G" as gigabytes and "KB" and "K"
 * as kilobytes.
 * @param string
 * @throws UNKNOWN_VALUE_EXCEPTION
 * @return integer
 */
function text_to_file_size ($text)
{
  if (is_numeric ($text))
  {
    return $text;
  }

  $pieces = new stdClass();
  if (preg_match ('/([0-9]+)([MB]|[M]|[GB]|[G]|[KB]|[K]|[TB]|[T])/', $text, $pieces))
  {
    switch ($pieces [2])
    {
    case 'K':
    case 'KB':
      return $pieces [1] * 1024;
    case 'M':
    case 'MB':
      return $pieces [1] * pow (1024, 2);
    case 'G':
    case 'GB':
      return $pieces [1] * pow (1024, 3);
    case 'T':
    case 'TB':
      return $pieces [1] * pow (1024, 4);
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($pieces [2]);
    }
  }
  
  return $text;
}

/**
 * Returns true if this path is normalized.
 * Does not check whether the path exists. Only checks whether it is a legal
 * path using {@link normalize_path()} to determine whether the name needs
 * any changes.
 * @param string $path
 * @param FILE_OPTIONS $opts
 * @return boolean
 */
function is_valid_path ($path, $opts = null)
{
  if (isset ($path) && $path)
  {
    if ($this->normalized_ids_are_lower_case)
    {
      $path = strtolower ($path);
    }
    
    return $path == normalize_path ($path, $opts);
  }
  
  return false;
}

/**
 * Make sure the directory exists, if possible.
 * Use {@link PHP_MANUAL#is_dir} to check whether the path was created. There
 * are several reasons why the function may not succeed:
 * <ul>
 * <li>The path is not legal; use {@link normalize_path()} to generate a valid
 * path for the server file system.</li>
 * <li>The path is excluded by an "open_base_dir" directive in the PHP
 * configuration.</li>
 * <li>The server process user does not have rights to create the requested
 * path.</li>
 * </ul>
 * @param string $path
 * @param FILE_OPTIONS $opts
 */
function ensure_path_exists ($path, $opts = null)
{
  if (! file_exists ($path))
  {
    if (! isset ($opts))
    {
      $opts = global_file_options ();
    }

    $path = str_replace ('\\', $opts->path_delimiter, $path);
    $path = str_replace ('/', $opts->path_delimiter, $path);

    /* Hack here to make sure that UNIX-base systems have
     * their leading separator.
     */

    if ($opts->path_starts_with_delimiter ())
    {
      $path_to_create = $opts->path_delimiter;
    }
    else
    {
      $path_to_create = '';
    }

    foreach (explode ($opts->path_delimiter, $path) as $folder)
    {
      if ($folder)
      {
        $path_to_create .= $folder . $opts->path_delimiter;
        if (! file_exists ($path_to_create))
        {
          @mkdir ($path_to_create, $opts->default_access_mode);
          @chmod ($path_to_create, $opts->default_access_mode);
        }
      }
    }
  }
}

/**
 * Store the text in the given file name.
 * Does <b>not</b> use {@link ensure_path_exists()} to force the directory--it
 * must exist beforehand. Fails silently if permission is denied. Use {@link
 * PHP_MANUAL#is_file} to determine if the file was stored. If the file already
 * exists, it is overwritten. Permissions are set to the {@link
 * FILE_OPTIONS::$default_access_mode} because the web server is creating the
 * file (otherwise the file would be read-only for other users).
 * @param string $file_name
 * @param string $text
 * @param FILE_OPTIONS $opts
 */
function write_text_file ($file_name, $text, $opts = null)
{
  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }
  $f = @fopen ($file_name, 'w');
  @fwrite ($f, $text);
  @fclose ($f);
  @chmod ($file_name, $opts->default_access_mode);
}

/**
 * Return the server system temp directory.
 * @return string
 */
function temp_folder ()
{
  $name = tempnam ('', 'WebCore');
  unlink ($name);
  $url = new FILE_URL ($name);
  $url->strip_name ();
  return $url->as_text ();
}

/**
 * Return the list of files for the given path.
 * @param string $base_path Must be a full path.
 * @param string $path_to_prepend Prepended to each file.
 * @param bool $recurse If true, include all sub-folders.
 * @param FILE_OPTIONS $opts
 * @return string[]
 */
function file_list_for ($base_path, $path_to_prepend = '', $recurse = false, $opts = NULL)
{
  if (! isset ($opts))
  {
    $opts = global_file_options ();
  }
  
  $base_path = ensure_ends_with_delimiter ($base_path, $opts);

  $Result = array ();
  if (($handle = @opendir ($base_path)))
  {
    while (($name = readdir ($handle)) != false)
    {
      if ($name [0] != '.')
      {
        if (is_dir (join_paths ($base_path, $name)))
        {
          if ($recurse)
          {
            $Result = array_merge ($Result, file_list_for (join_paths ($base_path, $name, $opts), join_paths ($path_to_prepend, $name, $opts), $recurse, $opts));
          }
        }
        else
        {
          $Result [] = join_paths ($path_to_prepend, $name);
        }
      }
    }
    closedir ($handle);
  }
  
  return $Result;
}

/**
 * Returns the global file options.
 * Used when no options are passed to a file function.
 * @return FILE_OPTIONS
 * @access private
 */
function global_file_options ()
{
  global $_g_file_options;
  if (! isset ($_g_file_options))
  {
    $_g_file_options = new FILE_OPTIONS ();
  }
  return $_g_file_options;
}

/**
 * Cached copy of file options.
 * Accessed using {@link global_file_options()}.
 * @global FILE_OPTIONS
 * @access private
 */
$_g_file_options = null;

?>