<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.4.0
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
require_once ('webcore/forms/storable_form.php');

/**
 * Form that stores {@link UNIQUE_OBJECT}s.
 * Descendent forms either stored unique object's or work with objects that use the id
 * from a unique object.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.5.0
 * @abstract
 */
class UNIQUE_OBJECT_FORM extends STORABLE_FORM
{
  /**
   * @param CONTEXT &$context.
   */
  function UNIQUE_OBJECT_FORM (&$context)
  {
    STORABLE_FORM::STORABLE_FORM ($context);

    $field = new INTEGER_FIELD ();
    $field->id = 'id';
    $field->title = 'ID';
    $field->min_value = 1;
    $field->visible = FALSE;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param UNIQUE_OBJECT &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('id', $obj->id);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('id', read_var ('id'));
  }
}

?>