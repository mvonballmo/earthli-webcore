<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
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
require_once ('webcore/forms/purge_form.php');

/**
 * Handles purging of {@link ATTACHMENT}s.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class PURGE_ATTACHMENT_FORM extends PURGE_OBJECT_FORM
{
  /**
   * @param FOLDER $app Deleting content from this folder.
   * @param string $set_name
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TEXT_FIELD ();
    $field->id = 'type';
    $field->caption = 'Type';
    $field->visible = false;
    $this->add_field ($field);
    
    $field = $this->field_at ('remove_resources');
    $field->visible = true;
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