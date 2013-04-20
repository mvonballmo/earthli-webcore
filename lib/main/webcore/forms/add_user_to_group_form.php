<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
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
 * Adds a {@link USER} to a security {@link GROUP}.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
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
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

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
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
    $js_form = $this->js_form_name ();
?>
  var field = new OBJECT_VALUE_FIELD ();
  field.attach (<?php echo $js_form . '.name'; ?>);
  field.object_id = <?php echo $this->value_for ('id'); ?>;
  field.width = 400;
  field.height = 600;
  field.page_name = 'browse_group_user.php';
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->set_width ('20em');

    $renderer->start ();
    $renderer->draw_text_line_row ('name');

    $buttons [] = $renderer->javascript_button_as_HTML ('Browse...', 'field.show_picker()', '{icons}buttons/browse');
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }
}
?>