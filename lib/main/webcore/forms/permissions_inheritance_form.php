<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
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
require_once ('webcore/forms/form.php');

/**
 * Switch inheritance for {@link FOLDER} security settings.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class PERMISSIONS_INHERITANCE_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $name = 'permissions_form';

  /**
   * @var string
   */
  public $action = 'create_folder_permissions.php';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'defined';
    $field->caption = 'Defined';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * @param FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('defined', $obj->defines_security ());
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $folder_query = $this->login->folder_query ();
    $folder = $this->_object;
    $parent = $folder->parent_folder ();

    if (! $this->value_for ('defined'))
    {
      $permissions_folder = $folder_query->object_at_id ($folder->permissions_id);
    }
    else
    {
      $permissions_folder = $folder_query->object_at_id ($parent->permissions_id);
    }

    if ($this->login->is_allowed (Privilege_set_folder, Privilege_secure, $permissions_folder))
    {
      $t = $permissions_folder->title_formatter ();
      $t->set_name ($this->env->url (Url_part_file_name));
      $title = $permissions_folder->title_as_link ($t);
    }
    else
    {
      $title = $permissions_folder->title_as_html ();
    }
      
    if ($folder->defines_security ())
    {
      $renderer->draw_text_row ('', 'Permissions for this folder are defined below.');
      $renderer->draw_separator ();
      $this->button_icon = '{icons}buttons/restore';
      $this->button = 'Revert to inherited...';
      $renderer->draw_submit_button_row ();
    }
    else
    {
      $renderer->draw_text_row ('', 'Permissions are inherited from ' . $title . '.');
      $renderer->draw_separator ();
      $this->button_icon = '{icons}buttons/create';
      $this->button = 'Define permissions...';
      $renderer->draw_submit_button_row ();
    }

    $renderer->finish ();
  }
}
?>