<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.4.0
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
 * An object which can be rendered in different ways.
 * Use the {@link handler_for()}
 * @package webcore
 * @subpackage obj
 * @version 3.5.0
 * @since 2.5.0
 * @abstract
 */
abstract class RENDERABLE extends WEBCORE_OBJECT
{
  /**
   * Return handler objects for different tasks.
   * The object determines the most appropriate object for handling the
   * requested functionality. Typical requests are {@link Handler_print}, {@link
   * Handler_html}, {@link Handler_menu} and more. If no plugin is registered,
   * the request is passed to {@link _default_handler_for()}. If there is a
   * plugin, the property "handler_type" is set to the value of this parameter,
   * so objects that handle more than one type know which one to render.
   * @see Handler_constants
   * @param string $handler_type Specific functionality required.
   * @param object $options Optional set of parameters used to create the
   * handler. The {@link OBJECT_RENDERER} handlers use {@link
   * OBJECT_RENDERER_OPTIONS} here.
   * @return object
   */
  public function handler_for ($handler_type, $options = null)
  {
    $base_name = $this->_base_name ();
    $class_name = $this->context->final_class_name ($base_name, '', $handler_type);
    
    if ($class_name != $base_name)
    {
      $Result = new $class_name ($this, $options);
    }
    else
    {
      $Result = $this->_default_handler_for ($handler_type, $options);
    }
    
    if (isset ($Result))
    {
      $Result->handler_type = $handler_type;
    }
    
    return $Result;
  }
  
  /**
   * Return default handler objects for supported tasks.
   * If there is no plugin registered for a handler, this function creates
   * a default one. May return <code>null</code>. {@link handler_for()} already
   * checks for plugins, so objects here can be created directly (without using
   * {@link APPLICATION::final_class_name()}).
   * @see Handler_constants
   * @param string $handler_type Specific functionality required.
   * @param object $options Optional set of parameters used to create the
   * handler. The {@link OBJECT_RENDERER} handlers use {@link
   * OBJECT_RENDERER_OPTIONS} here.
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_menu:
        return $this->context->make_menu_renderer ();
      case Handler_location:
        include_once ('webcore/gui/location_renderer.php');
        return new LOCATION_RENDERER ($this->context);
      default:
        return null;
    }
  }
  
  /**
   * Name under which plugins are registered.
   * {@link handler_for()} searches for associated handlers using this as the
   * base class and the "handler type" as the context in {@link APPLICATION::
   * final_class_name()}.
   * @return string
   * @access private
   */
  protected function _base_name ()
  {
    return get_class ($this);
  }
}