<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.1.0
 * @since 2.7.0
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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Edit or create an {@link ALBUM_ENTRY}.
 * @package albums
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class ALBUM_ENTRY_FORM extends ENTRY_FORM
{
  /**
   * @param ALBUM $folder Album in which to add or edit the picture.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new DATE_TIME_FIELD ();
    $field->id = 'day';
    $field->title = 'Day';
    $field->required = true;
    $field->sticky = true;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this picture.
   * @param ALBUM_ENTRY $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('day', $obj->date);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->load_from_client ('day', $this->_folder->first_day);
  }

  /**
   * Does this form hold valid data for this picture?
   * @param ALBUM_ENTRY $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->_folder->is_valid_date ($this->value_for ('day')))
    {
      $fd_fmt = $this->_folder->format_date ($this->_folder->first_day);
      $ld_fmt = $this->_folder->format_date ($this->_folder->last_day);

      $this->record_error ('day', "Please make sure that the day is between $fd_fmt and $ld_fmt.");
    }
  }

  /**
   * Store the form's values to this object.
   * @param ALBUM_ENTRY $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);
    $obj->date = $this->value_for ('day');
  }
}

?>