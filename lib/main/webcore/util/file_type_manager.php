<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
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

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Manages supported files types.
 * This abstract interface can be implemented to read supported file type information
 * from various sources.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 * @abstract
 */
abstract class FILE_TYPE_MANAGER extends WEBCORE_OBJECT
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->_load ();
  }

  /**
   * Return an html icon for the given file information.
   * If the mime type is empty or not found, the extension is used. If the extension is not found,
   * {@link $default_icon} is returned.
   * @param string $mime_type
   * @param string $extension
   * @param string $size
   * @return string
   */
  public function icon_as_html ($mime_type, $extension, $size = Thirty_two_px)
  {
    if ($mime_type)
    {
      $title = $mime_type;
    }
    else if ($extension)
    {
      $title = $extension;
    }
    else
    {
      $title = ' ';
    }

    return $this->context->resolve_icon_as_html($this->icon_url($mime_type, $extension), $size, $title);
  }

  /**
   * Return the base path to an icon for the specified file type.
   * @param string $mime_type
   * @param string $extension
   * @return string
   */
  public function icon_url ($mime_type, $extension)
  {
    if (isset ($this->_mime_types [$mime_type]))
    {
      return $this->_mime_types [$mime_type];
    }
    else if (isset ($this->_extensions [$extension]))
    {
      return $this->_extensions [$extension];
    }

    return $this->_default_icon_url;
  }

  /**
   * Load supported file type information.
   * @access private
   * @abstract
   */
  protected abstract function _load ();

  /**
   * Icon url used when an unregistered type is requested.
   * @var string
   * @access private
   */
  protected $_default_icon_url;
  
  /**
   * Maps mime types to icon urls.
   * @var string[]
   * @access private
   */
  protected $_mime_types;
  
  /**
   * Maps extensions to icon urls.
   * @var string[]
   * @access private
   */
  protected $_extensions;
}

/**
 * Loads file types from an INI file.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */
class INI_FILE_TYPE_MANAGER extends FILE_TYPE_MANAGER
{
  /**
   * Load supported file type information.
   * @access private
   * @abstract
   */
  protected function _load ()
  {
    $file_name = $this->context->config_file_name ('file_types.ini');
    if ($file_name)
    {
      $config = parse_ini_file ($file_name, true);

      $this->_extensions = read_array_index ($config, 'extensions');
      $this->_mime_types = read_array_index ($config, 'mime_types');
      $general = read_array_index ($config, 'general');
      $this->_default_icon_url = read_array_index ($general, 'default');
    }
  }
}

?>