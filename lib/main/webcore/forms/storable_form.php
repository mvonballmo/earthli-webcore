<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
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

/** */
require_once ('webcore/forms/previewable_form.php');

/**
 * Load the form from a cloned object.
 */
define ('Form_load_action_clone', 'clone');

/**
 * Creates/updates {@link STORABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 * @abstract
 */
abstract class STORABLE_FORM extends PREVIEWABLE_FORM
{
  /**
   * Is a clone operation loaded?
   * @return boolean
   */
  public function cloning ()
  {
    return isset ($this->_load_action) && ($this->_load_action == Form_load_action_clone);
  }

  /**
   * Execute the form on a cloned object.
   * This will commit the form if it has been {@link submitted()}.
   * @param object $obj Object being copied.
   */
  public function process_clone ($obj)
  {
    $this->_process ($obj, Form_load_action_clone);
  }

  /**
   * Load initial properties from the object, but store as a new object.
   * @param STORABLE $obj
   */
  public function load_from_clone ($obj)
  {
    $this->load_from_object ($obj);
  }

  /**
   * Execute the form.
   * The form has been validated and can be executed.
   * @param STORABLE $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->store ();
  }

  /**
   * Validate the form and apply data to the object.
   * @param object $obj
   * @access private
   */
  protected function _prepare_for_commit ($obj)
  {
    $this->_store_to_object ($obj);
  }

  /**
   * Load the form according to the given action.
   * @param STORABLE $obj
   * @param string $load_action Can be {@link Form_load_action_default}, {@link Form_load_action_object} or {@link Form_load_action_clone}.
   * @access private
   */
  protected function _process_load_action ($obj, $load_action)
  {
    if (strcmp ($this->button, Form_default_button_title) == 0)
    {
      switch ($load_action)
      {
      case Form_load_action_default:
        $this->button_icon = '{icons}indicators/created';
        $this->button = 'Create';
        break;
      case Form_load_action_object:
        $this->button_icon = '{icons}buttons/save';
        $this->button = 'Save';
        break;
      case Form_load_action_clone:
        $this->button = 'Create';
        $this->button_icon = '{icons}buttons/clone';
        break;
      }
    }

    if ($load_action == Form_load_action_clone)
    {
      $this->load_from_clone ($obj);
    }
    else
    {
      parent::_process_load_action ($obj, $load_action);
    }
  }
}

?>