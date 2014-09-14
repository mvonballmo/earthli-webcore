<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @version 3.6.0
 * @since 2.2.1
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

if (! function_exists ('is_a'))
{
  /**
   * Is this object a descendent of this class?
   * This is only included if the PHP version doesn't already have this function.
   * @see PHP_MANUAL#is_a
   * @param object $object
   * @param string $class_name
   * @version 3.6.0
   * @since 2.2.1
   * @access private
   */
  function is_a ($object, $class_name)
  {
    $class_name = strtolower ($class_name);
    if (get_class ($object) == $class_name)
    {
      return true;
    }

    return is_subclass_of ($object, $class_name);
  }
}

if (! function_exists ('html_entity_decode'))
{
  /**
   * Remove HTML entities from the text.
   * This is only included if the PHP version doesn't already have this function.
   * @see PHP_MANUAL#html_entity_decode
   * @param object $text
   * @version 3.6.0
   * @since 2.2.1
   * @access private
   */
  function html_entity_decode ($text)
  {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return strtr ($text, $trans_tbl);
  }
}

if (! function_exists ('image_type_to_mime_type'))
{
  /**
   * Return a mime type for the PHP image type.
   * This is only included if the PHP version doesn't already have this function.
   * @see PHP_MANUAL#image_type_to_mime_type
   * @param integer $imagetype
   * @version 3.6.0
   * @since 2.2.1
   * @access private
   */
  function image_type_to_mime_type ($imagetype)
  {
    switch ($imagetype)
    {
    case 1:
      return 'image/gif';
    case 2:
      return 'image/jpeg';
    case 3:
      return 'image/png';
    case 4:
      return 'application/x-shockwave-flash';
    case 5:
      return 'image/psd';
    case 6:
      return 'image/bmp';
    case 7:
    case 8:
      return 'image/tiff';
    default:
      return 'unknown/unknown';
    }
  }
}

if (! function_exists ('file_get_contents'))
{
  /**
   * Return the contents of a text file.
   * This is only included if the PHP version doesn't already have this function.
   * @see PHP_MANUAL#file_get_contents
   * @param string $file_name
   * @version 3.6.0
   * @since 2.7.0
   * @access private
   */
  function file_get_contents ($file_name)
  {
    ob_start ();
    readfile ($file_name);
    $Result = ob_get_contents();
    ob_end_clean();
    return $Result;
  }
}

/**
 * Return the output of the {@link PHP_MANUAL#print_r} function.
 * @param object $value
 * @version 3.6.0
 * @since 2.6.0
 * @access private
 */
function print_r_capture ($value)
{
  ob_start ();
  print_r ($value);
  $Result = ob_get_contents();
  ob_end_clean();
  return $Result;
}

/**
 * Insert an item into a classic array.
 * Provides functionality mysteriously missing from PHP itself. Does not check
 * whether the index is valid for the given array.
 * @param array &$array Original array
 * @param integer $index Index into array
 * @param object $value Value to insert at 'index'
 * @return array
 */
function array_insert (&$array, $index, $value)
{
  $array = array_merge (array_slice ($array, 0, $index), array ($value), array_slice ($array, $index));
}

?>