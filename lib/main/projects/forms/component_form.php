<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Form that stores {@link ICON}s.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 * @abstract
 */
class COMPONENT_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param APPLICATION $folder
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new URI_FIELD ();
    $field->id = 'icon_url';
    $field->caption = 'Icon URL';
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param UNIQUE_OBJECT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $icon_url = read_var ('icon_url');
    if ($icon_url)
    {
      $this->set_value ('icon_url', $icon_url);
    }
    else
    {
      $this->set_value ('icon_url', $obj->icon_url);
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $icon_url = read_var ('icon_url');
    if ($icon_url)
    {
      $this->set_value ('icon_url', $icon_url);
    }
  }

  /**
   * Store the form's values to this object.
   * @param STORABLE $obj
   * @access private
   * @abstract
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);
    $obj->icon_url = $this->value_for ('icon_url');
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
    $this->_draw_icon_browser_script_for ('icon_url');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');

    $renderer->draw_icon_browser_row ('icon_url');
    $renderer->draw_check_box_row ('is_visible');
    $renderer->draw_text_box_row ('description');
    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, false);
    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
}

?>