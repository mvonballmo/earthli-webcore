<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
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
 * Changes a {@link USER}'s password.
 * Does not require the current password.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.2.1
 */
class PASSWORD_FORM extends FORM
{
  /**
   * @var string
   */
  public $button = 'Change';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/password';
  
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TEXT_FIELD ();
    $field->id = 'name';
    $field->caption = 'Name';
    $field->required = true;
    $field->visible = false;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'password1';
    $field->caption = 'Password';
    $field->required = true;
    $field->min_length = $this->app->user_options->minimum_password_length;
    $field->max_length = 20;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'password2';
    $field->caption = 'Confirm Password';
    $field->required = true;
    $field->min_length = $this->app->user_options->minimum_password_length;
    $field->max_length = 20;
    $this->add_field ($field);
    
    $field = new BOOLEAN_FIELD ();
    $field->id = 'remember';
    $field->caption = 'Remember me';
    $field->description = 'Store your user information on this computer.';
    $this->add_field ($field);
  }

  /**
   * Called after fields are validated.
   * @param USER $obj This parameter is ignored.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->num_errors ('password1') && ! $this->num_errors ('password2'))
    {
      $password1 = $this->value_for ('password1');
      $password2 = $this->value_for ('password2');
      if (strcasecmp ($password1, $password2))
      {
        $this->record_error ('password2', "Please make sure the passwords are the same.");
      }
    }
  }

  /**
   * Store the form's values as this user's password.
   * @param USER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $history_item = $obj->new_history_item ();
    $obj->set_password ($this->value_as_text ('password1'));
    $obj->store_if_different ($history_item);
    $obj->load_permissions ();
    if ($this->login->equals ($obj))
    {
      $this->app->log_in ($obj, $this->value_for ('remember'));
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_hidden_controls ($renderer)
  {
    $this->set_value ('name', read_var ('name'));
    parent::_draw_hidden_controls ($renderer);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->set_width ('12em');

    $renderer->start ();
    $renderer->draw_password_row ('password1');
    $renderer->draw_password_row ('password2');
    if ($this->login->equals ($this->_object))
    {
      $renderer->draw_check_box_row ('remember');
    }
    $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}