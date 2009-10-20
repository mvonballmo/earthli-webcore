<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Form that stores {@link THEME}s.
 * @package webcore
 * @subpackage forms
 * @version 3.2.0
 * @since 2.5.0
 */
class THEME_FORM extends RENDERABLE_FORM
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

    $field = new URI_FIELD ();
    $field->id = 'main_CSS_file_name';
    $field->title = 'Skin CSS';
    $field->required = true;
    $field->min_length = 1;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'font_name_CSS_file_name';
    $field->title = 'Font CSS';
    $field->required = false;
    $field->min_length = 1;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new URI_FIELD ();
    $field->id = 'font_size_CSS_file_name';
    $field->title = 'Size CSS';
    $field->required = false;
    $field->min_length = 1;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'icon_set';
    $field->title = 'Icon set';
    $field->required = false;
    $field->min_length = 1;
    $field->max_length = 100;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'icon_extension';
    $field->title = 'Extension';
    $field->description = 'Default extension for icons and logos.';
    $field->required = false;
    $field->min_length = 1;
    $field->max_length = 5;
    $this->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'renderer_class_name';
    $field->title = 'Renderer';
    $field->description = 'Name of the PHP class that will render pages.';
    $field->required = false;
    $field->min_length = 1;
    $field->max_length = 100;
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
    $this->set_value ('main_CSS_file_name', $obj->main_CSS_file_name);
    $this->set_value ('font_name_CSS_file_name', $obj->font_name_CSS_file_name);
    $this->set_value ('font_size_CSS_file_name', $obj->font_size_CSS_file_name);
    $this->set_value ('icon_set', $obj->icon_set);
    $this->set_value ('icon_extension', $obj->icon_extension);
    $this->set_value ('renderer_class_name', $obj->renderer_class_name);
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
    $obj->main_CSS_file_name = $this->value_for ('main_CSS_file_name');
    $obj->font_name_CSS_file_name = $this->value_for ('font_name_CSS_file_name');
    $obj->font_size_CSS_file_name = $this->value_for ('font_size_CSS_file_name');
    $obj->icon_set = $this->value_for ('icon_set');
    $obj->icon_extension = $this->value_for ('icon_extension');
    $obj->renderer_class_name = $this->value_for ('renderer_class_name');
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
    $renderer->draw_text_row (' ', 'Each theme must define a skin file and can also specify font and sizing files.', 'notes');

    $renderer->draw_separator ();
    $renderer->draw_text_line_row ('main_CSS_file_name');
    $renderer->draw_text_line_row ('font_name_CSS_file_name');
    $renderer->draw_text_line_row ('font_size_CSS_file_name');

    $renderer->draw_separator ();
    $renderer->draw_text_row (' ', 'A theme can override which icon set is used and specify the default extension to apply.', 'notes');

    $renderer->draw_separator ();
    $renderer->draw_text_line_row ('icon_set');
    $renderer->draw_text_line_row ('icon_extension');

    $renderer->draw_separator ();
    $renderer->draw_text_line_row ('renderer_class_name');

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->finish ();
  }
}

?>