<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
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
require_once ('webcore/forms/permissions_form.php');

/**
 * Updates all {@link USER} permissions.
 * Presents content, folder, group and user {@link PERMISSIONS}.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */
class USER_PERMISSIONS_FORM extends PERMISSIONS_FORM
{
  /**
   * @var string
   */
  public $button = 'Save';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/save';
  
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TITLE_FIELD ();
    $field->id = 'name';
    $field->caption = 'Title';
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'use_defaults';
    $field->caption = 'Use Defaults';
    $this->add_field ($field);

    $formatter = $this->app->make_permissions_formatter ();
    $this->content_groups = $formatter->content_privilege_groups ();
    $this->global_groups = $formatter->global_privilege_groups ();

    foreach ($this->content_groups as $group)
    {
      foreach ($group->maps as $map)
      {
        $field = new INTEGER_FIELD ();
        $field->id = $map->id ();
        $field->min_value = Privilege_always_denied;
        $field->max_value = Privilege_controlled_by_content;
        $this->add_privilege_field ($field);
      }
    }

    foreach ($this->global_groups as $group)
    {
      foreach ($group->maps as $map)
      {
        $field = new BOOLEAN_FIELD ();
        $field->id = $map->id ();
        $this->add_privilege_field ($field);
      }
    }
  }

  /**
   * Add a field that is represents a permission.
   * These fields are handled specially so that they can be enabled/disabled
   * easily when loading the form.
   * @param FIELD $field
   * @access private
   */
  public function add_privilege_field ($field)
  {
    $this->add_field ($field);
    $this->_privilege_fields [] = $field;
  }

  /**
   * Load initial properties from this user.
   * @param USER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('name', $obj->title);

    $permissions = $obj->permissions ();

    $this->set_value ('use_defaults', ! $permissions->exists ());

    if (! $permissions->exists ())
    {
      foreach ($this->_privilege_fields as &$field)
      {
        $field->enabled = false;
      }
    }

    foreach ($this->content_groups as $group)
    {
      foreach ($group->maps as $map)
      {
        $this->set_value ($map->id (), $permissions->value_for ($map->set_name, $map->type));
      }
    }

    foreach ($this->global_groups as $group)
    {
      foreach ($group->maps as $map)
      {
        $this->set_value ($map->id (), $permissions->value_for ($map->set_name, $map->type));
      }
    }
  }

  /**
   * Store the form's values as this user's permissions.
   * @param USER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $permissions = $obj->permissions ();

    if ($this->value_for ('use_defaults'))
    {
      if ($permissions->exists ())
      {
        $permissions->delete ();
      }
    }
    else
    {
      foreach ($this->content_groups as $group)
      {
        foreach ($group->maps as $map)
        {
          $map->store_to_object ($permissions, $this->value_for ($map->id ()));
        }
      }

      foreach ($this->global_groups as $group)
      {
        foreach ($group->maps as $map)
        {
          $map->store_to_object ($permissions, $this->value_for ($map->id ()));
        }
      }

      $permissions->store ();
    }
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
?>

function grant_all_permissions (form)
{
  set_all_controls_of_type (form, 'checkbox', 1);
  set_all_controls_of_type (form, 'select-one', <?php echo Privilege_always_granted; ?>);
}

function grant_no_permissions (form)
{
  set_all_controls_of_type (form, 'checkbox', 0);
  set_all_controls_of_type (form, 'select-one', <?php echo Privilege_always_denied; ?>);
}

function update_controls ()
{
  var form = <?php echo $this->js_form_name (); ?>;
  var def_control = form.use_defaults;

  if (def_control)
  {
    var use_defaults = is_selected (def_control, 0);

    <?php
    foreach ($this->_privilege_fields as $field)
    {
      echo "form.$field->id.disabled = ! use_defaults;\n";
    }
    ?>
  }
}

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
    $renderer->draw_text_row ('', 'The settings on the left can override content permissions, either <em>Granting</em> or <em>Denying</em> them. <em>[Folder]</em> uses the permissions defined in the folder.', 'notes');
    $renderer->draw_separator ();
    
    if ($this->visible ('use_defaults'))
    {
      $props = $renderer->make_list_properties ();
      $props->on_click_script = 'update_controls ()';
      if ($this->_object->is_anonymous ())
      {
        $props->add_item ('Use default permissions for anonymous users.', 1);
      }
      else
      {
        $props->add_item ('Use default permissions for registered users.', 1);
      }
      $props->add_item ('Use the permissions below.', 0);

      $renderer->start_row ();
      echo $renderer->radio_group_as_HTML ('use_defaults', $props);
      $renderer->finish_row ();
      $renderer->draw_separator ();
    }

    $this->_draw_buttons ($renderer);

    $props = $renderer->make_list_properties ();
    $props->add_item ('[Folder]', Privilege_controlled_by_content);
    $props->add_item ('Granted', Privilege_always_granted);
    $props->add_item ('Denied', Privilege_always_denied);

    $renderer->start_column ();

      foreach ($this->content_groups as $group)
      {
        $renderer->start_row ($group->title);
        foreach ($group->maps as $map)
        {
          $this->_draw_tri_permission ($map, $formatter, $renderer, $props);
        }
        $renderer->finish_row ();
        $renderer->draw_separator ();
      }

    $renderer->start_column (); 

      foreach ($this->global_groups as $group)
      {
        $renderer->start_row ($group->title);
        foreach ($group->maps as $map)
        {
          $this->_draw_permission ($map, $formatter, $renderer, $props);
        }
        $renderer->finish_row ();
        $renderer->draw_separator ();
      }

    $renderer->finish_column ();

    $this->_draw_buttons ($renderer);
  }

  /**
   * Draw the permission with icon and title.
   * Adds the icon to the title. This is done when drawn so that the icon
   * calculation is not done if the form is only being submitted.
   * @param PRIVILEGE_MAP $map Information about the privilege.
   * @param PERMISSIONS_FORMATTER $formatter Use this to get formatting information.
   * @param FORM_RENDERER $renderer
   * @param FORM_LIST_PROPERTIES $props
   * @access private
   */
  protected function _draw_tri_permission ($map, $formatter, $renderer, $props)
  {
    $id = $map->id ();
    $field = $this->field_at ($id);
    $field->caption = $formatter->icon_for ($map) . ' ' . $formatter->title_for ($map);
    echo '<div style="margin-bottom: .4em">';
    echo $renderer->drop_down_as_HTML ($id, $props);
    echo ' ' . $field->caption . "</div>\n";
  }

  /**
   * Holds a list of the fields that are represent permissions.
   * @var FIELD[]
   * @access private
   */
  protected $_privilege_fields;
}

?>