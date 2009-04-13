<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage obj
 * @version 3.1.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/obj/multi_type_entry.php');

/**
 * A generic album entry.
 * Maintains a date and other housekeeping code for {@link JOURNAL}s and {@link PICTURE}s.
 * @abstract
 * @package albums
 * @subpackage obj
 * @version 3.1.0
 * @since 2.5.0
 */
abstract class ALBUM_ENTRY extends MULTI_TYPE_ENTRY
{
  /**
   * When did this entry happen?
   * @var DATE_TIME
   */
  public $date;

  /**
   * @param ALBUM_APPLICATION $app Main application.
   */
  public function ALBUM_ENTRY ($app)
  {
    MULTI_TYPE_ENTRY::MULTI_TYPE_ENTRY ($app);
    $this->date = $app->make_date_time ();
  }

  /**
   * Format the date according to folder options.
   * Local times (generated with JavaScript) are <i>never</i> used.
   * @return string
   */
  public function date_as_string ()
  {
    $f = $this->parent_folder ();
    return $f->format_date ($this->date);
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->date->set_from_iso ($db->f ('date'));
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $storage->add ($this->table_name (), 'date', Field_type_date_time, $this->date);
    
    $folder = $this->parent_folder ();
    $folder->include_entry ($this);
  }

  /**
   * Copy properties from the given object. 
   * @param ALBUM_ENTRY $other
   * @access private
   */
  protected function copy_from ($other)
  {
    parent::copy_from($other);
    $this->date = clone ($other->date);
  }
  
  /**
   * @param PURGE_OPTIONS $options
   * @access private
   */
  protected function _purge ($options)
  {
    parent::_purge ($options);
    
    $folder = $this->parent_folder ();
    $folder->refresh_dates (true);
  }
}

?>