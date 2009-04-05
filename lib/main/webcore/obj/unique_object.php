<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
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

/** */
require_once ('webcore/obj/named_object.php');

/**
 * An object with a unique id in a database
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */
abstract class UNIQUE_OBJECT extends NAMED_OBJECT
{
  /**
   * @var integer
   */
  public $id = 0;

  /**
   * Is the object stored in the database?
   * @return boolean
   */
  public function exists ()
  {
    return isset ($this->id) && ($this->id <> 0);
  }
  
  /**
   * Is this the same object?
   * @param UNIQUE_OBJECT $obj
   * @return boolean
   */
  public function equals ($obj)
  {
    return isset($obj) && ($obj->id == $this->id);
  }
  
  /**
   * Text to uniquely identify this object.
   * @return string
   */
  public function unique_id ()
  {
    return get_class ($this) . '_' . $this->id;
  }
  
  /**
   * Set up this object so it will {@link store()} a new object.
   * Ensures that {@link exists()} returns <code>False</code> and that all auto-
   * generated fields are reset.
   */
  public function initialize_as_new ()
  {
    $this->id = 0;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    $this->id = $db->f ('id');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    $tname = $this->table_name ();
    $storage->restrict ($tname, 'id');
    $storage->add ($tname, 'id', Field_type_integer, $this->id, Storage_action_none);
  }

  /**
   * Return a description for use in logging.
   * @return string
   */
  public function instance_description ()
  {
    $Result = parent::instance_description ();
    if ($this->exists ())
    {
      $Result .= ', id=' . $this->id;
    }
    else
    {
      $Result .= ', new';
    }
    return $Result;
  }

  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return 'id=' . $this->id;
  }

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function table_name ();

  /**
   * @return SQL_UNIQUE_STORAGE
   * @access private
   */
  protected function _make_storage ()
  {
    $class_name = $this->context->final_class_name ('SQL_UNIQUE_STORAGE', 'webcore/db/sql_unique_storage.php');
    return new $class_name ($this->context);
  }

  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    $tname = $this->table_name ();
    $this->db->logged_query ("DELETE LOW_PRIORITY FROM {$tname} WHERE id = $this->id");
    $this->id = 0;
  }
}

?>