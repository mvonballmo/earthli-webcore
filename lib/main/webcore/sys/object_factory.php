<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
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
 * Produces instances of registered classes.
 * A class registers its name and file with a unique identifier. When {@link make_object()}
 * is called with that identifier, the factory loads the file and creates an instance of the
 * class.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class OBJECT_FACTORY extends RAISABLE
{
  /**
   * Register a class with the factory.
   * If a class was already registered for that tag, it is replaced.
   * @param string $id Unique id; usually the name of an existing base class.
   * @param string $class_name Name of the class to create for this id.
   * @param string $file_name Location of the definition for 'class_name'.
   * @param string $context Distiguishes among multiple similar registrations.
   */
  function register_class ($id, $class_name, $file_name = '', $context = '')
  {
    $item = new stdClass();
    $item->class_name = $class_name;
    $item->file_name = $file_name;
    $item->context = $context;
    $this->_registered_classes [$this->_id_for ($id, $context)] = $item;
  }

  /**
   * Get the name of the plugin to use for a base class.
   * The 'class_name' uniquely identifies the class to create. Returns the 
   * plugin registered using {@link register_class()}, or the given 'class_name'
   * if there is none (including the given file name, if non-empty). If a
   * 'context' is specified, the 'class_name' associated with that specific
   * context is searched first. If none is registered, it returns the context-
   * free association, as specified above.
   * @param string $class_name Unique name of the class to load.
   * @param string $file_name Location of 'class_name'.
   * @param string $context Distiguishes among multiple similar registrations. 
   * @return string
   */
  function final_class_name ($class_name, $file_name = '', $context = '')
  {
    if ($context)
    {
      $id = $this->_id_for ($class_name, $context);
      if (! isset ($this->_registered_classes [$id]))
      {
        $id = $this->_id_for ($class_name);
      }
    }
    else
    {
      $id = $this->_id_for ($class_name);
    }
    
    if (isset ($this->_registered_classes [$id]))
    {
      $class_name = $this->_load_and_return_class ($id, $file_name);
    }
    else
    {
      if ($file_name) 
        include_once ($file_name);        
    }

    return $class_name;
  }
  
  /**
   * Is there a class registered for this id?
   * Pass a context to restrict to a particular registration. 
   * @param string $class_name
   * @param string $context
   * @return boolean
   */
  function is_registered ($class_name, $context = '')
  {
    return isset ($this->_registered_classes [$this->_id_for ($class_name, $context)]);
  }

  /**
   * Return the registration id for the given class and context.
   * Used by {@link final_class_name()} to resolve a context, if given.
   * @see is_registered()
   * @param string $class_name
   * @param string $context
   * @return string
   */
  function id_for_class_in_context ($class_name, $context)
  {
    return $class_name . '_{' . $context . '}';   
  }
  
  /**
   * Return the list of classes with the given prefix.
   * Each class is resolved to its final class and the file for the class is
   * included.
   * @param string $prefix
   * @return array[string]
   */
  function classes_with_prefix ($prefix)
  {
    $prefix = $this->_id_for ($prefix);
    $Result = array ();
    foreach ($this->_registered_classes as $id => $item)
    {
      if (strpos ($id, $prefix) === 0)
      {
        $Result [] = $this->final_class_name ($item->class_name, $item->file_name);
      }
    }
    return $Result;
  }

  /**
   * Create a normalized is for the class and context.
   * @param string $class_name
   * @param string $context
   * @return string
   * @access private
   */
  function _id_for ($class_name, $context = '')
  {
    if ($context)
    {
      $Result = $class_name . '_{' . $context . '}';
    }
    else
    {
      $Result = $class_name;
    }
    return strtoupper ($Result);
  }
  
  /**
   * Load the desired class and file.
   * Includes the given file only if the class is not registered or if the
   * registered class has not file. 
   * @param string $class_name 
   * @param string $file_name
   * @return string 
   * @access private
   */
  function _load_and_return_class ($class_name, $file_name)
  {
    /* Include the registered file, if available. Otherwise,
     * include the given file.
     */
    
    if (! empty ($this->_registered_classes [$class_name]->file_name))
    {
      include_once ($this->_registered_classes [$class_name]->file_name);
    }
    else
    {
      if ($file_name)
      {
        include_once ($file_name);
      }
    }
    
    return $this->_registered_classes [$class_name]->class_name;
  }

  /**
   * @var array[string,OBJECT_FACTORY_ITEM]
   * @see OBJECT_FACTORY_ITEM
   * @access private
   */
  protected $_registered_classes;
}

/**
 * Identifies name and location of a class.
 * Used by {@link OBJECT_FACTORY} to store registered classes.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class OBJECT_FACTORY_ITEM
{
  /**
   * Name of the class to create.
   * @var string
   */
  public $class_name;
  /**
   * File name containing the class description.
   * @var string
   */
  public $file_name;
}

?>