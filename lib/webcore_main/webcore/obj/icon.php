<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/obj/renderable.php');

/**
 * An icon used by {@link FOLDER}s or {@link USER}s.
 * The user or folder will only use the {@link $url} property. Icons are displayed in a browser and
 * can be customized per deployment.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
 */
class ICON extends RENDERABLE
{
  /**
   * @var string
   */
  var $title;
  /**
   * @var string
   */
  var $category;
  /**
   * Points to the resource for this icon.
   * Source is resolved using {@link RESOURCE_MANAGER::resolve_icon_as_html()}, so it can
   * include aliases registered with the context.
   * @var string
   */
  var $url;

  /**
   * @return string
   */
  function icon_as_html ($size = '100px')
  {
    return $this->context->image_as_html ($this->_home_page (), $this->title, $size);
  }

  /**
   * @param DATABASE &$db Database from which to load values.
   */
  function load (&$db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->category = $db->f ('category');
    $this->url = $db->f ('url');
  }

  /**
   * @param SQL_STORAGE &$storage Store values to this object.
   */
  function store_to (&$storage)
  {
    parent::store_to ($storage);
    $tname = $this->_table_name ();
    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'category', Field_type_string, $this->category);
    $storage->add ($tname, 'url', Field_type_string, $this->url);
  }

  /**
   * @return string
   */
  function raw_title ()
  {
    return $this->title;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  function page_name ()
  {
    return $this->context->sized_icon ($this->url, '100px');
  }
  
  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  function page_arguments ()
  {
    return '';
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  function _table_name ()
  {
    return $this->app->table_names->icons;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_html_renderer:
        include_once ('webcore/gui/icon_renderer.php');
        return new ICON_RENDERER ($this->context);
      case Handler_commands:
        include_once ('webcore/cmd/icon_commands.php');
        return new ICON_COMMANDS ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>