<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
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
 * Handles display and validation for {@link PERMISSIONS}.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 * @abstract
 */
abstract class PERMISSIONS_FORM extends FORM
{
  /**
   * @var string
   */
  public $name = 'permissions_form';
  
  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function grant_all_permissions (form)
  {
    set_all_controls_of_type (form, 'checkbox', 1);
  }

  function grant_no_permissions (form)
  {
    set_all_controls_of_type (form, 'checkbox', 0);
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $formatter = $this->app->make_permissions_formatter ();

    $renderer->set_width ('');
    $renderer->start ();
    $this->_draw_permission_controls ($renderer, $formatter);
    $renderer->finish ();
  }
  
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_buttons ($renderer)
  {
    $buttons [] = $renderer->javascript_button_as_html ('Grant All', 'grant_all_permissions (this.form)', '{icons}buttons/select');
    $buttons [] = $renderer->javascript_button_as_html ('Grant None', 'grant_no_permissions (this.form)', '{icons}buttons/close');
    $buttons [] = $renderer->submit_button_as_html ();
    $renderer->start_button_row ('');
    $renderer->draw_buttons_in_row ($buttons);
    $renderer->finish_row ();
    $renderer->draw_separator ();
  }

  /**
   * @param FORM_RENDERER $renderer
   * @param PERMISSIONS_FORMATTER $formatter
   * @access private
   * @abstract
   */
  protected abstract function _draw_permission_controls ($renderer, $formatter);

  /**
   * Draw the permission with icon and title.
   * Adds the icon to the title. This is done when drawn so that the icon
   * calculation is not done if the form is only being submitted.
   * @param PRIVILEGE_MAP $map Information about the privilege.
   * @param PERMISSIONS_FORMATTER $formatter Use this to get formatting information.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_permission ($map, $formatter, $renderer)
  {
    $id = $map->id ();
    $field = $this->field_at ($id);
    $field->caption = $formatter->icon_for ($map) . ' ' . $formatter->title_for ($map);
    echo $renderer->check_box_as_HTML ($id);
    echo "<div style=\"height: .2em\"></div>\n";
  }
}

?>