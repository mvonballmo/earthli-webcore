<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
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
require_once ('webcore/forms/folder_permissions_form.php');

/**
 * Create {@link PERMISSIONS} for a {@link USER} in a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class FOLDER_GROUP_PERMISSIONS_CREATE_FORM extends FOLDER_PERMISSIONS_FORM
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
   * @param GROUP_QUERY $group_query Retrieve groups with this query.
   */
  public function __construct ($folder, $group_query)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;
    $this->_group_query = $group_query;

    $field = new TEXT_FIELD ();
    $field->id = 'group_name';
    $field->title = 'Group';
    $field->required = true;
    $field->min_length = 1;
    $field->max_length = 50;
    $this->add_field ($field);
  }
  
  /**
   * Called after fields are validated.
   * @param PERMISSIONS $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->num_errors ('group_name'))
    {
      // the name validated OK (specifically, it is not empty)
      
      $this->_group = $this->_group_query->object_with_field ('title', $this->value_for ('group_name'));

      if (! $this->_group)
      {
        $this->record_error ('group_name', "Please choose a valid group.");
      }

      if (! $this->num_errors ('group_name'))
      {
        $security = $this->_folder->security_definition ();
        $group = $security->group_permissions_at_id ($this->_group->id);
        if (isset ($group))
        {
          $this->record_error ('group_name', "That group already has permissions in this folder.");
        }
      }
    }
  }

  /**
   * Store the form's values to this set of permissions.
   * @param PERMISSIONS $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->ref_id = $this->_group->id;
    $obj->kind = Privilege_kind_group;

    parent::commit ($obj);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('id', read_var ('id'));

    $selected_group_id = read_var ('selected_group_id', 0);
    if ($selected_group_id)
    {
      $group = $this->_group_query->object_at_id ($selected_group_id);
      $this->set_value ('group_name', $group->title);
    }
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
  field.attach (<?php echo $js_form . '.group_name'; ?>);
  field.object_id = <?php echo $this->_folder->id; ?>;
  field.width = 400;
  field.height = 300;
  field.page_name = 'browse_for_folder_permissions_group.php';
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
    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '20em';

    $renderer->draw_text_line_row ('group_name', $options);

    $buttons [] = $renderer->javascript_button_as_HTML ('Browse...', 'field.show_picker()', '{icons}buttons/browse');
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->draw_separator ();

    parent::_draw_permission_controls ($renderer, $formatter);
  }
}
?>