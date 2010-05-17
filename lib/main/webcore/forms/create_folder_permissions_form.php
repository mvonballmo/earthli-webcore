<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/forms/form.php');
require_once ('webcore/sys/security.php');

/**
 * Create or delete {@link PERMISSIONS} for a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class CREATE_FOLDER_PERMISSIONS_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'copy_mode';
    $field->title = '';
    $field->add_value (Security_copy_none);
    $field->add_value (Security_copy_current);
    $field->add_value (Security_create_admin);
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this folder's permissions.
   * @param FOLDER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $security = $obj->security_definition ();
    if ($security->inherited ())
    {
      $security->copy_and_store ($this->value_for ('copy_mode'));
    }
    else
    {
      $security->purge ();
    }
  }

  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('copy_mode', Security_copy_current);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    if (! $this->_object->defines_security ())
    {
      $renderer->start ();
      $renderer->draw_text_row ('', 'Are you sure you want to create new permissions for ' . $this->_object->title_as_link () . '?');
      $renderer->draw_separator ();
      
      $parent_folder = $this->_object->parent_folder ();
      $permissions_folder = $parent_folder->permissions_folder ();
      
      $props = $renderer->make_list_properties ();
      $props->add_item ('Do not create any default permissions*', Security_copy_none);
      $props->add_item ('Copy current permissions from ' . $permissions_folder->title_as_link (), Security_copy_current);
      $props->add_item ('Grant all permissions for user ' . $this->login->title_as_link (), Security_create_admin);
      $renderer->draw_radio_group_row ('copy_mode', $props);
      $renderer->draw_separator ();

      $buttons [] = $renderer->button_as_HTML ('No', 'view_folder_permissions.php?id=' . $this->_object->id);
      $buttons [] = $renderer->submit_button_as_HTML ();
      $renderer->draw_buttons_in_row ($buttons);
      $renderer->draw_separator ();

      $permissions = $this->login->permissions ();      
      if ($permissions->value_for (Privilege_set_folder, Privilege_view) != Privilege_always_granted)
      {
        $renderer->draw_text_row ('', '<div class="caution">' . $this->app->resolve_icon_as_html ('{icons}/indicators/warning', 'Warning', '16px') . ' *In this case, you <span class="field">will not</span> be able to see this folder.</div>', 'notes');
      }
      else
      {
        $renderer->draw_text_row ('', '*Your user will still be allowed to see this folder.', 'notes');
      }

      $renderer->finish ();
    }
    else
    {
      $renderer->start ();

      $renderer->draw_text_row ('', 'Are you sure you want to remove permissions for ' . $this->_object->title_as_link () . '?*');
      $renderer->draw_separator ();

      $buttons [] = $renderer->button_as_HTML ('No', 'view_folder_permissions.php?id=' . $this->_object->id);
      $buttons [] = $renderer->submit_button_as_HTML ();
      $renderer->draw_buttons_in_row ($buttons);
      $renderer->draw_separator ();

      $renderer->draw_text_row ('', '*Doing so will revert all permissions to those used by the parent folder.', 'notes');

      $renderer->finish ();
    }
  }
}
?>