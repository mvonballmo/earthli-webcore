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
require_once ('webcore/forms/send_mail_form.php');

/**
 * Send the contents of an exception handler in an email.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */
class SUBMIT_EXCEPTION_FORM extends SEND_MAIL_FORM
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'include_browser_info';
    $field->title = 'Browser';
    $field->description = 'Include information about your browser and operating system.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'include_page_data';
    $field->title = 'Data';
    $field->description = 'Include page, form and cookie data. Excludes passwords, but may include other personal information.';
    $this->add_field ($field);

    $this->set_required ('sender_name', false);
    $this->set_required ('sender_email', false);

    $field = $this->field_at ('sender_email');
    $field->description = 'Optional, but lets us follow up if we have any questions.';
    
    $field = $this->field_at ('message');
    $field->description = 'Briefly describe what you were doing when the error occurred (very useful).';
  }

  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->load_with_defaults ();

    $this->set_value ('send_to', $this->context->mail_options->webmaster_address);
    $this->set_value ('include_browser_info', true);
    $this->set_value ('include_page_data', true);

    $class_name = read_var ('class_name');
    $routine_name = read_var ('routine_name');

    if ($routine_name)
    {
      if ($class_name)
      {
        $this->set_value ('subject', "Exception in method [$class_name.$routine_name]");
      }
      else
      {
        $this->set_value ('subject', "Exception in function [$routine_name]");
      }
    }
    else
    {
      $this->set_value ('subject', 'Exception at global scope');
    }
  }

  /**
   * @param object $obj Get renderer for this object.
   * @return EXCEPTION_MAIL_RENDERER
   * @access private
   */
  protected function _make_obj_renderer ($obj)
  {
    $class_name = $this->context->final_class_name ('EXCEPTION_MAIL_RENDERER', 'webcore/mail/exception_mail_renderer.php');
    return new $class_name ($this->context);
  }

  /**
   * @return MAIL_OBJECT_RENDERER_OPTIONS
   * @access private
   */
  protected function _make_renderer_options ()
  {
    $class_name = $this->context->final_class_name ('EXCEPTION_RENDERER_OPTIONS', 'webcore/gui/exception_renderer.php');
    $Result = new $class_name ();
    $Result->include_page_data = $this->value_for ('include_page_data');
    $Result->include_browser_info = $this->value_for ('include_browser_info');
    $Result->subject = $this->value_for ('subject');
    $Result->preferred_text_length = $this->excerpt_size ();
    return $Result;
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_hidden_controls ($renderer)
  {
    parent::_draw_hidden_controls ($renderer);

    $params = $this->_object->as_array ();

    foreach ($params as $name => $value)
    {
      $renderer->draw_hidden_value ($name, $value);
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
    $renderer->draw_check_box_row ('include_browser_info');
    $renderer->draw_check_box_row ('include_page_data');
  }
}