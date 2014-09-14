<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
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
require_once ('webcore/forms/folder_permissions_form.php');

/**
 * Create {@link PERMISSIONS} for a {@link USER} in a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */
class FOLDER_USER_PERMISSIONS_CREATE_FORM extends FOLDER_PERMISSIONS_FORM
{
  /**
   * @var string
   */
  public $button = 'Add';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/add';

  /**
   * @param FOLDER $folder Folder for which permissions are defined.
   * @param USER_QUERY $user_query Retrieve user with this query.
   */
  public function __construct ($folder, $user_query)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;
    $this->_user_query = $user_query;

    $field = new TITLE_FIELD ();
    $field->id = 'title';
    $field->caption = 'User Name';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * Called after fields are validated.
   * @param FOLDER_PERMISSIONS $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->num_errors ('title'))
    {
      // the name validated OK (specifically, it is not empty)
      
      $this->_user = $this->_user_query->object_with_field ('title', $this->value_for ('title'));

      if (! $this->_user)
      {
        $this->record_error ('title', "Please choose a valid user.");
      }

      if (! $this->num_errors ('title'))
      {
        $security = $this->_folder->security_definition ();
        $user = $security->user_permissions_at_id ($this->_user->id);
        if (isset ($user))
        {
          $this->record_error ('title', "That user already has permissions in this folder.");
        }
      }
    }
  }

  /**
   * Store the form's values to this set of permissions.
   * @param FOLDER_PERMISSIONS $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->ref_id = $this->_user->id;
    $obj->kind = Privilege_kind_user;

    parent::commit ($obj);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('id', read_var ('id'));
    $this->set_value ('title', read_var ('name'));
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
    $js_form = $this->js_form_name ();
?>
  var field = new OBJECT_VALUE_FIELD ();
  field.attach (<?php echo $js_form . '.title'; ?>);
  field.object_id = <?php echo $this->_folder->id; ?>;
  field.width = 400;
  field.height = 600;
  field.page_name = 'browse_for_folder_permissions_user.php';
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @param PERMISSIONS_FORMATTER $formatter
   * @access private
   * @abstract
   */
  protected function _draw_permission_controls ($renderer, $formatter)
  {
    $renderer->draw_text_line_with_browse_button_row ('title', 'field.show_picker()');

    parent::_draw_permission_controls ($renderer, $formatter);
  }
}
?>