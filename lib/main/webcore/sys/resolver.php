<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.0
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
require_once ('webcore/sys/system.php');
require_once ('webcore/sys/object_factory.php');
require_once ('webcore/sys/resources.php');

/**
 * Used internally to register {@link TYPE_INFO}s.
 * This string is prefixed to the class type and registered as a class name in
 * the class registry. See {@link RESOLVE::type_info_for()} for more
 * information.
 * @access private
 */
define ('Type_info_reg_prefix', '__type_info_');

/**
 * Manages lists of overridables for a particular context.
 * Retrieve classes using {@link final_class_name()}, {@link page_template_for()}
 * and resolve URLs using the API of the parent class, {@link RESOURCE_MANAGER}.
 * @package webcore
 * @subpackage sys
 * @version 3.4.0
 * @since 2.7.0
 */
class RESOLVER extends RESOURCE_MANAGER
{
  public function __construct ()
  {
    parent::__construct ();
    $this->classes = new OBJECT_FACTORY ();
    $this->_initialize_class_registry ();    
  }
  
  /**
   * Get the name of the plugin to use for a base class.
   * @see OBJECT_FACTORY::final_class_name()
   * @param string $class_name Unique name of the class to load.
   * @param string $file_name Location of 'class_name'.
   * @param string $context Distiguishes among multiple similar registrations.
   * @return string
   */
  public function final_class_name ($class_name, $file_name = '', $context = '')
  {
    return $this->classes->final_class_name ($class_name, $file_name, $context);
  }
  
  /**
   * Return the requested object, creating it if necessary.
   * The object is created with the registered class; 'this' is passed to the constructor.
   * @param string $singleton_name Unique name of the object to create.
   * @param string $class_name Unique name of the class to load.
   * @param string $file_name Location of 'class_name'.
   * @param string $context Distiguishes among multiple similar registrations.
   * @return object
   */
  public function find_or_create_singleton ($singleton_name, $class_name, $file_name = '', $context = '')
  {
    if (empty ($this->_singletons [$singleton_name]))
    {
      $this->_singletons [$singleton_name] = $this->make_object($singleton_name, $class_name, $file_name, $context);
    }
    return $this->_singletons [$singleton_name];
  }
  
  public function make_object ($id, $class_name, $file_name = '', $context = '')
  {
    $class_name = $this->final_class_name ($class_name, $file_name, $context);
    return new $class_name ($this);
  }

  /**
   * Return the name of the page template to use.
   * Returns the given name unless a replacement has been registered with 
   * {@link register_page_template()}.
   * @param string $page_name
   * @return string
   */
  public function page_template_for ($page_name)
  {
    if (isset ($this->_page_templates [$page_name]))
    {
      return $this->_page_templates [$page_name];
    }

    return $page_name;
  }

  /**
   * Is there a class registered for this id?
   * @param string $class_name
   * @param string $context
   * @return boolean
   */
  public function is_registered ($class_name, $context = '')
  {
    return $this->classes->is_registered ($class_name, $context);
  }

  /**
   * Register a plugin to use in place of another class.
   * The last plugin registered for a particular 'base_name' will be used.
   * @param string $id Unique id; usually the name of an existing base class.
   * @param string $class_name Name of the class to create for this id.
   * @param string $file_name Location of the definition for 'class_name'.
   * @param string $context Distiguishes among multiple similar registrations.
   */
  public function register_class ($id, $class_name, $file_name = '', $context = '')
  {
    $this->classes->register_class ($id, $class_name, $file_name, $context);
  }

  /**
   * Register a handler for a class.
   * This is essentially a synonym for {@link register_class()}, but reorders
   * the arguments to make the 'context' required.
   * @param string $id Name of the class for which a handler is being
   * registered.
   * @param string $handler Name of the handler to replace (see {@link
   * Handler_constants}).
   * @param string $class_name Name of the handler class.
   * @param string $file_name Location of the definition for 'class_name'
   */
  public function register_handler ($id, $handler, $class_name, $file_name = '')
  {
    $this->classes->register_class ($id, $class_name, $file_name, $handler);
  }

  /**
   * Register a replacement template for a base template file.
   * When {@link page_template_for()} makes a request for the page 'default', the 
   * page 'new' is returned instead.
   * @param string $default
   * @param string $new
   */
  public function register_page_template ($default, $new)
  {
    $this->_page_templates [$default] = $new;
  }

  /**
   * Return meta-information for the given class.
   * If there is no specific type info defined for the class, {@link TYPE_INFO} is returned.
   * @param string $class_name
   * @return TYPE_INFO
   */
  public function type_info_for ($class_name, $file_name = '')
  {
    /* Resolve the class, then check for type info for that class. */

    $final_class_name = $this->final_class_name ($class_name, $file_name);
    $type_info_name = $final_class_name . '_TYPE_INFO';
    $type_info_exists = class_exists ($type_info_name);

    /* Check for a plugin for the missing type info. */
     
    if (! $type_info_exists)
    {
      $type_info_name = $this->final_class_name ($type_info_name);
      $type_info_exists = class_exists ($type_info_name);
    }

    /* If the class was derived, look for the ancestor type information. */

    if ((! $type_info_exists) && ($final_class_name != $class_name))
    {
      $type_info_name = $class_name . '_TYPE_INFO';
      $type_info_exists = class_exists ($type_info_name);
    }
    
    /* If there is still no type information, use the default. */
    
    if (! $type_info_exists)
    { 
      $type_info_name = 'TYPE_INFO';
      log_message ("Using default type info for [$class_name]", Msg_type_debug_warning, Msg_channel_system);
    }
    return $this->find_or_create_singleton (Type_info_reg_prefix . $class_name, $type_info_name);
  }

  /**
   * Register plugins in {@link $classes} during initialization.
   * Descendents should register plugins in this method if the object to override
   * is created in the constructor.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
  }

  /**
   *  Plug-in registry for class overrides.
   * Use {@link final_class_name()} to retrieve the actual class name for objects, so
   * that the type can be overridden by a plugin. Use {@link register_class()} to add
   * a plugin.
   * @var OBJECT_FACTORY
   * @access private
   */
  protected $_classes;  
  /**
   *  Page template registry.
   * Use {@link page_template_for()} to retrieve a page template name. Use 
   * {@link register_page_template()} to add a template translation.
   * @var array[string,string]
   * @access private
   */
  protected $_page_templates;

  /**
   * @var array[string,object]
   * @access private
   */
  protected $_singletons;
}

?>