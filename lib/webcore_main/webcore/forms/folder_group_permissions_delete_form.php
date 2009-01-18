<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/forms/delete_form.php');

/**
 * Delete {@link PERMISSIONS} for a {@link USER} from a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 */
class FOLDER_GROUP_PERMISSIONS_DELETE_FORM extends DELETE_FORM
{
  var $show_object_as_link = FALSE;

  /**
   * @param APPLICATION &$app Main application.
   */
  function FOLDER_GROUP_PERMISSIONS_DELETE_FORM (&$app)
  {
    DELETE_FORM::DELETE_FORM ($app);

    $field = new INTEGER_FIELD ();
    $field->id = 'group_id';
    $field->title = 'Group ID';
    $field->required = TRUE;
    $field->min_value = 1;
    $field->visible = FALSE;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from these permissions.
   * @param PERMISSIONS &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('id', $obj->folder_id);
    $this->set_value ('group_id', $obj->ref_id);
  }
}
?>