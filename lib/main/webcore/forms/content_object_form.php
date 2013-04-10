<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
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
require_once ('webcore/forms/auditable_form.php');

/**
 * Base form for {@link CONTENT_OBJECT} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */
class CONTENT_OBJECT_FORM extends AUDITABLE_FORM
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'title';
    $field->title = 'Title';
    $field->required = true;
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'description';
    $field->title = 'Description';
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this object.
   * @param CONTENT_OBJECT $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);
    
    $obj->title = $this->value_as_text ('title');
    $obj->description = $this->value_as_text ('description');
  }

  /**
   * Load initial properties from this object.
   * @param CONTENT_OBJECT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('title', $obj->title);
    $this->set_value ('description', $obj->description);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer, false);
    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set;

  /**
   * Folder containing the object being edited/created.
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}

?>