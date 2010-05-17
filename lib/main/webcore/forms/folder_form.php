<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Update or create a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class FOLDER_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param FOLDER $folder Edit this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new URI_FIELD ();
    $field->id = 'icon_url';
    $field->title = 'Icon';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'summary';
    $field->title = 'Summary';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'is_organizational';
    $field->title = 'Organizer';
    $field->description = 'Used for structure; cannot add new content';
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this folder.
   * @param FOLDER $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->organizational = $this->value_for ('is_organizational');
    $obj->summary = $this->value_as_text ('summary');
    $obj->icon_url = $this->value_as_text ('icon_url');

    parent::_store_to_object ($obj);
  }

  /**
   * Load initial properties from this folder.
   * @param FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('summary', $obj->summary);
    $this->set_value ('is_organizational', $obj->is_organizational ());
    $this->set_enabled ('is_organizational', ! $obj->is_root ());

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
    $renderer->draw_check_box_row ('is_organizational');
    $renderer->draw_check_box_row ('is_visible');
    $renderer->draw_text_box_row ('summary', $renderer->default_control_width, '4em');
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