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
require_once ('webcore/forms/form.php');

/**
 * Log in or out of a registered {@link USER} account.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class LOG_IN_FORM extends FORM
{
  /**
   * @var string
   */
  public $name = 'log_in_form';

  /**
   * @var string
   */
  public $button = 'Log in';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/login';

  /**
   * @var string
   * @access private
   */
  public $submitted_flag = 'login_submitted';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TEXT_FIELD ();
    $field->id = 'name';
    $field->title = 'Name';
    $field->required = true;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'password';
    $field->title = 'Password';
    $field->required = true;
    $field->min_length = 1;
    $field->max_length = 20;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'remember';
    $field->title = 'Remember me';
    $field->description = 'Store your user information on this computer and avoid logging in every time.';
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'last_page';
    $field->title = 'Last page';
    $field->visible = false;
    $this->add_field ($field);

    if ($this->login->is_anonymous ())
    {
      $this->set_initial_focus ('name');
    }
  }

  /**
   * Called after fields are validated.
   * @param object $obj This parameter is ignored.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if (! $this->num_errors ('name') && $this->value_for ('name'))
    {
      $user_query = $this->app->user_query ();
      $user_query->include_permissions (true);
      $this->_user = $user_query->object_at_name ($this->value_for ('name'));

      if (! $this->_user)
      {
        $this->record_error ('password', 'Please provide a valid login.');
      }
      else
      {
        if (! $this->num_errors ('password'))
        {
          if (! $this->_user->password_matches ($this->value_for ('password')))
          {
            $this->record_error ('password', 'Please provide a valid login.');
          }
        }
      }
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    $this->set_value ('last_page', urlencode (read_var ('last_page')));
  }

  /**
   * Log in the selected user, if possible.
   * @param object $obj This parameter is ignored.
   * @access private
   */
  public function commit ($obj)
  {
    if (! $this->_user->is_anonymous ())
    {
      $this->app->log_in ($this->_user, $this->value_for ('remember'));
    }
    else
    {
      $this->app->log_out ();
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->show_required_mark = false;
    $renderer->set_width ('15em');

    $renderer->start ();

    $renderer->draw_text_line_row ('name');
    $renderer->draw_password_row ('password');

    $renderer->start_row (' ');
    echo $renderer->check_box_as_HTML ('remember');
    $renderer->finish_row ();

    $renderer->draw_submit_button_row ();

    if ($this->app->login->is_anonymous ())
    {
      $anon = $this->app->anon_user ();
      if ($anon->is_allowed (Privilege_set_user, Privilege_create))
      {
        $renderer->draw_separator ();
        $renderer->start_row ();
?>
<div class="notes">Don't have an account? <a href="<?php echo $this->app->page_names->user_create; ?>">Register...</a></div>
<?php
        $renderer->finish_row ();
      }
    }

    $renderer->finish ();
  }
}

?>