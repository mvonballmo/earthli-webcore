<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.7.0
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

/***/
require_once ('webcore/obj/named_object.php');

/**
 * Version information for a framework.
 * This can be an {@link APPLICATION} or an {@link ENVIRONMENT}.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.7.0
 */
class FRAMEWORK_INFO extends NAMED_OBJECT
{
  /**
   * Unique id for the framework.
   * Uses a reverse domain name notation, like "com.earthli.news". Used with
   * {@link APPLICATION::$framework_id}.
   * @var string
   */
  public $title;

  /**
   * Version of the database.
   * @var string
   */
  public $database_version;

  /**
   * Version of the software.
   * Set with {@link set_software()}.
   * @var string
   */
  public $software_version;
  
  /**
   * Does this object exist?
   * @return boolean
   */
  public function exists () 
  {
    return ! $this->_version_not_found && ! empty ($this->database_version); 
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->title;
  }
  
  /**
   * @return boolean
   */
  public function needs_upgrade ()
  {
    return $this->database_version != $this->software_version; 
  }
  
  /**
   * Return an icon for the state of this version.
   * Must set the {@link $software_version} first.
   * @param string $size Can be any CSS value that corresponds to an icon
   * suffix.
   * @return string
   */
  public function icon_as_html ($size = '16px')
  {
    if ($this->_version_not_found || ! $this->database_version)
    {
      $icon = '{icons}indicators/error';
      $title = 'Error';
    }
    elseif ($this->needs_upgrade ())
    {
      $icon = '{icons}indicators/warning';
      $title = 'Warning';
    }
    else
    {
      $icon = '{icons}buttons/select';
      $title = 'Ok';
    }
    
    return $this->app->resolve_icon_as_html ($icon, $title, $size);
  }

  /**
   * Return an icon for the state of this version.
   * Must set the {@link $software_version} first.
   * @return string
   */
  public function icon_url ()
  {
    if ($this->_version_not_found || ! $this->database_version)
    {
      return '{icons}indicators/error';
    }
    elseif ($this->needs_upgrade ())
    {
      return '{icons}indicators/warning';
    }

    return '{icons}buttons/select';
  }

  /**
   * Return a message for the state of this version.
   * Must set the {@link $software_version} first.
   * @return string
   */
  public function message ()
  {
    if ($this->_version_not_found || ! $this->database_version)
    {
      return 'Database version not available.';    
    }
    elseif ($this->needs_upgrade ())
    {
      return 'Database upgrade required.';    
    }

    return 'Database is up-to-date.';    
  }
  
  /**
   * Return the full name of the framework.
   * @param boolean $use_software_version
   * @return string
   */
  public function description ($use_software_version = true)
  {
    if ($use_software_version)
    {
      return $this->title . ' ' . $this->software_version;
    }

    return $this->context->get_text_with_icon($this->icon_url(), ' ' . $this->title . ' ' . $this->database_version . ' &mdash; ' . $this->message (), '16px');
  }
  
  /**
   * Set the software version and information.
   * @param object $obj An {@link APPLICATION} or an {@link ENVIRONMENT}.
   */
  public function set_software ($obj)
  {
    $this->title = $obj->framework_id;
    $this->software_version = $obj->version;
  }
  
  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->database_version = $db->f ('version');
    $this->_version_not_found = empty ($this->database_version);
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    $tname = $this->app->table_names->versions;
    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'version', Field_type_string, $this->database_version);
    $storage->restrict ($tname, 'title');
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return 'index.php';
  }
  
  /**
   * Remove the object from the database.
   * Called from {@link purge()} which already checks that the object is
   * in the database (using {@link exists()}).
   * @param PURGE_OPTIONS $options
   * @access private
   * @abstract
   */
  protected function _purge ($options)
  {
    $tname = $this->table_name ();
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tname} WHERE title = '$this->title'");
    $this->database_version = null;
  }
  
  /**
   * Set when reading from the database. Will cause {@link exists()} to return
   * <code>False</code> even if {@link $database_version} is set later.
   * @var boolean 
   * @access private
   */
  protected $_version_not_found = true;
}

/**
 * Version information for an application and library.
 * Used to display the information in the configuration page.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.7.0
 */
class APPLICATION_CONFIGURATION_INFO extends WEBCORE_OBJECT
{
  /**
   * Version and name of application.
   * @var FRAMEWORK_INFO
   */
  public $app_info;

  /**
   * Version and name of library.
   * @var FRAMEWORK_INFO
   */
  public $lib_info;

  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    $this->load_from ($app);
  }
  
  /**
   * Initialize all versions for this app and environment.
   * @param APPLICATION $app
   */
  public function load_from ($obj)
  {
    if ($this->db->table_exists ($this->app->table_names->versions))
    {
      $class_name = $this->app->final_class_name ('FRAMEWORK_INFO_QUERY', 'webcore/db/framework_info_query.php');
      $query = new $class_name ($this->app);
      $this->app_info = $query->info_for ($obj);
      $query->clear_restrictions ();
      $this->lib_info = $query->info_for ($this->env);
    }
    
    if (! isset ($this->app_info))
    {
      $this->app_info = $this->_make_object ();
      switch ($obj->framework_id)
      {
        case 'com.earthli.albums':
          $this->app_info->database_version = '2.8.0';
          break;
        case 'com.earthli.news':
          $this->app_info->database_version = '2.7.0';
          break;
        case 'com.earthli.projects':
          $this->app_info->database_version = '1.8.0';
          break;
        case 'com.earthli.recipes':
          $this->app_info->database_version = '1.6.0';
          break;
      }
    }
    
    if (! isset ($this->lib_info))
    {
      $this->lib_info = $this->_make_object ($this->env->title);
      $this->lib_info->database_version = '2.6.0';
    }

    $this->app_info->set_software ($obj);
    $this->lib_info->set_software ($this->env);
  }
  
  /**
   * @return APPLICATION INFO
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->context->final_class_name ('FRAMEWORK_INFO');
    return new $class_name ($this->context);
  }
}

?>