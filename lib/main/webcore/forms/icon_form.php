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
require_once ('webcore/forms/renderable_form.php');

/**
 * Form that stores {@link ICON}s.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */
class ICON_FORM extends RENDERABLE_FORM
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'title';
    $field->title = 'Title';
    $field->required = true;
    $this->add_field ($field);

    $field = new TITLE_FIELD ();
    $field->id = 'category';
    $field->title = 'Category';
    $field->required = false;
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'url';
    $field->title = 'URL';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param UNIQUE_OBJECT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('title', $obj->title);
    $this->set_value ('category', $obj->category);
    $this->set_value ('url', $obj->url);
  }

  /**
   * Store the form's values to this object.
   * @param STORABLE $obj
   * @access private
   * @abstract
   */
  protected function _store_to_object ($obj)
  {
    $obj->title = $this->value_for ('title');
    $obj->category = $this->value_for ('category');
    $obj->url = $this->value_for ('url');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_text_line_row ('category');
    $renderer->draw_text_line_row ('url');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}

?>