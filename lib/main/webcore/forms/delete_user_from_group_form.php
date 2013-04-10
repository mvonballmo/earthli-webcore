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
 * Confirm and remove the specified {@link USER} from the {@link GROUP}.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */
class DELETE_USER_FROM_GROUP_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Yes';

  /**
   * @param USER $user Delete this user.
   */
  public function __construct ($user)
  {
    parent::__construct ($user->app);

    $this->_user = $user;

    $field = new TITLE_FIELD ();
    $field->id = 'name';
    $field->title = 'User Name';
    $field->required = true;
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Remove the selected user from the given group.
   * @param GROUP $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->remove_user ($this->_user);
  }

  /**
   * Load form fields from this object.
   * @param object $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('name', $this->_user->title);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->width = '65%';
    $renderer->start ();

    $renderer->start_row ();
?>
    <p>Are you sure you want to remove <?php echo $this->_user->title_as_link (); ?>
      from <?php echo $this->_object->title_as_link (); ?>?</p>
<?php
    $renderer->finish_row ();

    $buttons [] = $renderer->button_as_HTML ('No', $this->_object->home_page ());
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);

    $renderer->finish ();
  }
}

?>