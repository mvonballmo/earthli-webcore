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
require_once ('webcore/forms/form.php');

/**
 * Adds a {@link USER} to a security {@link GROUP}.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
 */
class ADD_USER_TO_GROUP_FORM extends ID_BASED_FORM
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
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new TITLE_FIELD ();
    $field->id = 'name';
    $field->caption = 'User Name';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * Called after fields are validated.
   * @param GROUP $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if ($this->num_errors ('name') == 0)
    {
      $name = $this->value_for ('name');

      $user_query = $this->app->user_query ();
      $this->_user = $user_query->object_at_name ($name);

      if (! $this->_user)
      {
        $this->record_error ('name', 'Please choose a valid user name.');
      }

      if ($this->num_errors ('name') == 0)
      {
        $user_query = $obj->user_query ();
        $user = $user_query->object_at_name ($name);

        if ($user)
        {
          $this->record_error ('name', "[$name] is already in this group.");
        }
      }
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('name', read_var ('name'));
  }

  /**
   * Add the selected user to the group.
   * @param GROUP $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->add_user ($this->_user);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $user_query = $this->app->user_query ();
    $group_query = $this->app->group_query ();
    /** @var GROUP $group */
    $group = $group_query->object_at_id ($this->value_for ('id'));

    $group_user_query = $group->user_query ();
    $ids = $group_user_query->indexed_objects ();
    if (sizeof ($ids))
    {
      $user_query->restrict_by_op ('usr.id', array_keys ($ids), Operator_not_in);
    }

    if (read_var ('show_anon'))
    {
      $user_query->set_kind (Privilege_kind_anonymous);
    }
    else
    {
      $user_query->set_kind (Privilege_kind_registered);
    }

    /** @var USER[] $items */
    $items = $user_query->objects();

    $renderer->draw_text_line_with_named_object_chooser_row('name', $items);

    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }
}