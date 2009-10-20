<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
 * @since 2.5.0
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
require_once ('webcore/forms/delete_form.php');

/**
 * Confirm and delete {@link ATTACHMENT}s.
 * Deleting marks a database object as deleted, but does not remove it from the database.
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
 * @since 2.5.0
 */
class DELETE_ATTACHMENT_FORM extends DELETE_OBJECT_IN_FOLDER_FORM
{
  /**
   * @param FOLDER $folder Deleting content from this folder.
   * @param string $set_name
   */
  public function __construct ($folder, $set_name)
  {
    parent::__construct ($folder, $set_name);

    $field = new TEXT_FIELD ();
    $field->id = 'type';
    $field->title = 'Type';
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param ATTACHMENT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('type', $obj->type);
  }
}

?>