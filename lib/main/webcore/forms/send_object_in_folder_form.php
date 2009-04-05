<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
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
require_once ('webcore/forms/send_multiple_mail_form.php');

/**
 * Sends an {@link OBJECT_IN_FOLDER} via email to multiple addresses.
 * Used to implement the 'send_entry' template pages.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEND_OBJECT_IN_FOLDER_FORM extends SEND_MULTIPLE_MAIL_FORM
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function SEND_OBJECT_IN_FOLDER_FORM ($app)
  {
    SEND_MULTIPLE_MAIL_FORM::SEND_MULTIPLE_MAIL_FORM ($app);

    $field = new INTEGER_FIELD ();
    $field->id = 'id';
    $field->title = 'ID';
    $field->min_value = 1;
    $field->visible = false;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'excerpt_type';
    $field->title = 'Text Options';
    $field->min_value = 1;
    $field->max_value = 2;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'excerpt_size';
    $field->title = 'Excerpt Size';
    $field->min_value = 100;
    $field->max_value = 1000;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param OBJECT_IN_FOLDER $obj
   */
  public function load_from_object ($obj)
  {
    $folder = $obj->parent_folder ();
    $this->set_value ('id', $obj->id);
    $this->set_value ('subject', "$folder->title: $obj->title");
    $this->load_with_defaults ();
  }

  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('excerpt_type', 2);
    $this->set_value ('excerpt_size', $this->app->mail_options->excerpt_length);
  }

  /**
   * How much of the object's descriptions does the user wish to send?
   * @return integer
   */
  public function excerpt_size ()
  {
    switch ($this->value_for ('excerpt_type'))
    {
    case 1:
      return 0;
    case 2:
      return $this->value_for ('excerpt_size');
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($this->value_for ('excerpt_type'));
    }
  }

  /**
   * Display additional email options.
   * Allow descendants to use the standard email form rendering, while still adding new mailing options.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    parent::_draw_options ($renderer);

    $renderer->draw_separator ();

    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '4em';

    $props = $renderer->make_list_properties ();
    $props->add_item ('Include all text', 1);
    $props->add_item ('Include ' . $renderer->text_line_as_HTML ('excerpt_size', $options) . ' character excerpt', 2);

    $renderer->draw_radio_group_row ('excerpt_type', $props);
    $renderer->draw_error_row ('excerpt_size');
  }

  /**
   * @param OBJECT_IN_FOLDER $obj The object to render.
   * @return CONTENT_OBJECT_MAIL_RENDERER
   * @access private
   */
  protected function _make_obj_renderer ($obj)
  {
    $class_name = $this->context->final_class_name ('CONTENT_OBJECT_MAIL_RENDERER', 'webcore/mail/content_object_mail_renderer.php');
    return new $class_name ($this->context);
  }
}

?>