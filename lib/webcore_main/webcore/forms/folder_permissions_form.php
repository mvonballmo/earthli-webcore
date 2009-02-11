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
require_once ('webcore/forms/permissions_form.php');

/**
 * Handles display and validation for {@link FOLDER} permissions.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 * @abstract
 */
class FOLDER_PERMISSIONS_FORM extends PERMISSIONS_FORM
{
  /**
   * @var string
   */
  var $button = 'Save';
  /**
   * @var string
   */
  var $button_icon = '{icons}buttons/save';

  /**
   * @param APPLICATION &$app Main application.
   */
  function FOLDER_PERMISSIONS_FORM (&$app)
  {
    PERMISSIONS_FORM::PERMISSIONS_FORM ($app);

    $field = new INTEGER_FIELD ();
    $field->id = 'id';
    $field->title = 'ID';
    $field->min_value = 1;
    $field->visible = FALSE;
    $this->add_field ($field);

    $formatter = $this->app->make_permissions_formatter ();
    $this->groups = $formatter->content_privilege_groups ();

    foreach ($this->groups as $group)
    {
      foreach ($group->maps as $map)
      {
        $field = new BOOLEAN_FIELD ();
        $field->id = $map->id ();
        $field->sticky = TRUE;
        $this->add_field ($field);
      }
    }
  }

  /**
   * Store the form's values to this set of permissions.
   * @param FOLDER_PERMISSIONS &$obj
   * @access private
   */
  function commit (&$obj)
  {
    foreach ($this->groups as $group)
    {
      foreach ($group->maps as $map)
        $map->store_to_object ($obj, $this->value_for ($map->id ()));
    }

    $obj->store ();
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();

    foreach ($this->groups as $group)
    {
      foreach ($group->maps as $map)
        $this->load_from_client ($map->id (), FALSE);
    }
  }

  /**
   * Load initial properties from these permissions.
   * @param FOLDER_PERMISSIONS &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('id', $obj->folder_id);

    foreach ($this->groups as $group)
    {
      foreach ($group->maps as $map)
        $this->set_value ($map->id (), $obj->is_allowed ($map->set_name, $map->type));
    }
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @param PERMISSIONS_FORMATTER &$formatter
   * @access private
   * @abstract
   */
  function _draw_permission_controls (&$renderer, &$formatter)
  {
    $this->_draw_buttons ($renderer);
    foreach ($this->groups as $group)
    {
      $renderer->start_row ($group->title);
      foreach ($group->maps as $map)
        $this->_draw_permission ($map, $formatter, $renderer);
      $renderer->finish_row ();
      $renderer->draw_separator ();
    }
    $this->_draw_buttons ($renderer);
  }
}

?>