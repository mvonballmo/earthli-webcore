<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
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
require_once ('webcore/forms/previewable_form.php');

/**
 * Basic mailing form.
 * Handles setting up the {@link MAIL_PROVIDER} and {@link MAIL_RENDERER}s for descendant classes.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 */
abstract class SEND_MAIL_FORM extends PREVIEWABLE_FORM
{
  /**
   * Enable previewing for these forms.
   * @var boolean
   */
  public $preview_enabled = true;
  
  /**
   * @var string
   */
  public $button = 'Send';
  
  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/send';

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'subject';
    $field->title = 'Subject';
    $field->required = true;
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'message';
    $field->title = 'Message';
    $field->max_length = 5000;
    $this->add_field ($field);

    $field = new EMAIL_FIELD ();
    $field->id = 'sender_email';
    $field->title = 'Sender email';
    $field->required = true;
    $this->add_field ($field);

    $field = new TITLE_FIELD ();
    $field->id = 'sender_name';
    $field->title = 'Sender name';
    $field->required = true;
    $this->add_field ($field);

    $field = new EMAIL_FIELD ();
    $field->id = 'send_to';
    $field->title = 'Send to email';
    $field->required = true;
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'send_as_html';
    $field->title = 'Format';
    $this->add_field ($field);
  }

  /**
   * Get a mail renderer for this object.
   * @return MAIL_OBJECT_RENDERER
   */
  public function mail_renderer ($obj)
  {
    return $this->_make_obj_renderer ($obj);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('send_as_html', $this->context->mail_options->send_as_html);

    if (isset ($this->login))
    {
      if (! $this->login->is_anonymous ())
      {
        $this->set_value ('sender_name', $this->login->real_name ());
        $this->set_value ('sender_email', $this->login->email);
      }
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
  }

  /**
   * Return true to use integrated captcha verification.
   * @return boolean
   */
  protected function _captcha_enabled ()
  {
    return ! isset ($this->login) || $this->login->is_anonymous ();
  }
  
  /**
   * Send the given object in an email.
   * Takes care of setting the mail format, setting up the recipient list and
   * rendering the object.
   * @param object $obj
   * @access private
   */
  public function commit ($obj)
  {
    $provider = $this->context->make_mail_provider ();
    $this->send_mail ($provider, $this->make_message ($obj));
    $provider->logs->close_all ();
  }

  /**
   * Return a mail representing this form.
   * @param object $obj
   * @return MAIL_MESSAGE
   * @access private
   */
  public function make_message ($obj)
  {
    $class_name = $this->context->final_class_name ('MAIL_MESSAGE', 'webcore/mail/mail_message.php');
    /** @var $Result MAIL_MESSAGE */
    $Result = new $class_name ($this->context);
    $Result->send_as_html = $this->value_for ('send_as_html');

    $sender_name = $this->value_for ('sender_name');
    $sender_email = $this->value_for ('sender_email');

    if (! $sender_name)
    {
      $sender_name = 'Not specified';
    }
    if (! $sender_email)
    {
      $sender_email = $this->context->mail_options->webmaster_address;
    }

    $Result->set_sender ($sender_name, $sender_email);

    $class_name = $this->context->final_class_name ('THEMED_MAIL_BODY_RENDERER', 'webcore/mail/themed_mail_body_renderer.php');
    /** @var $mail_renderer THEMED_MAIL_BODY_RENDERER */
    $mail_renderer = new $class_name ($this->context);

    $class_name = $this->context->final_class_name ('SEND_MAIL_FORM_RENDERER', 'webcore/mail/send_mail_form_renderer.php');
    /** @var $mail_obj_renderer SEND_MAIL_FORM_RENDERER */
    $mail_obj_renderer = new $class_name ($this->context);
    $mail_renderer->add ($this, $mail_obj_renderer);

    /** @var $options MAIL_OBJECT_RENDERER_OPTIONS */
    $options = $this->_make_renderer_options ();

    $obj_renderer = $this->mail_renderer ($obj);
    if (! empty ($obj_renderer))
    {
      $mail_renderer->add ($obj, $obj_renderer);
      $subject = $obj_renderer->subject ($obj, $options);
    }
    
    if (empty ($subject))
    {
      $subject = $mail_obj_renderer->subject ($this, $options);
    }

    if ($Result->send_as_html)
    {
      $Result->set_content ($subject, $mail_renderer->as_html ($options));
    }
    else
    {
      $Result->set_content ($subject, $mail_renderer->as_text ($options));
    }

    return $Result;
  }

  /**
   * Send a message using the given provider
   * @param MAIL_PROVIDER $provider
   * @param MAIL_MESSAGE $msg
   * @access private
   */
  public function send_mail ($provider, $msg)
  {
    $msg->set_send_to ($this->value_for ('send_to'));
    $msg->send ($provider);
  }

  /**
   * @return integer
   * @access private
   */
  public function excerpt_size () { return 0; }

  /**
   * @param object $obj Get renderer for this object.
   * @return MAIL_OBJECT_RENDERER
   * @access private
   * @abstract
   */
  protected abstract function _make_obj_renderer ($obj);

  /**
   * Return a preview for the given object.
   * @param STORABLE $obj
   * @return FORM_PREVIEW_SETTINGS
   * @access private
   */
  protected function _make_preview_settings ($obj)
  {
    $Result = new SEND_MAIL_FORM_PREVIEW_SETTINGS ($this->context);
    $Result->obj_renderer = $this->_make_obj_renderer ($obj);
    $Result->options = $this->_make_renderer_options ();
    $Result->show_as_html = $this->value_for ('send_as_html'); 
    $Result->form = $this;
    return $Result;
  }

  /**
   * @return MAIL_OBJECT_RENDERER_OPTIONS
   * @access private
   */
  protected function _make_renderer_options ()
  {
    $class_name = $this->context->final_class_name ('MAIL_OBJECT_RENDERER_OPTIONS', 'webcore/mail/mail_object_renderer.php');
    $Result = new $class_name ();
    $Result->preferred_text_length = $this->excerpt_size ();
    $Result->show_interactive = false;
    return $Result;
  }

  /**
   * Title for a previewed object.
   * @param object $obj
   * @return string
   */
  protected function _preview_title ($obj)
  {
    $munger = $this->context->html_title_formatter ();
    return 'Preview of ' . $munger->transform ($this->value_for ('subject'));
  }

  /**
   * Display additional email options.
   * Allow descendants to use the standard email form rendering, while still adding new mailing options.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    if ($this->visible ('send_as_html'))
    {
      $props = $renderer->make_list_properties ();
      $props->items_per_row = 2;
      $props->add_item ('HTML', 1);
      $props->add_item ('Plain text', 0);

      $renderer->draw_radio_group_row ('send_as_html', $props);
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->set_width ('25em');
    $renderer->default_control_height = '6em';

    $renderer->start ();
    $renderer->draw_text_line_row ('sender_name');
    $renderer->draw_text_line_row ('sender_email');

    $renderer->draw_separator ();
    
    $renderer->draw_text_line_row ('subject');
    $renderer->draw_text_box_row ('message');

    $renderer->draw_text_line_row ('send_to');

    $this->_draw_options ($renderer);
    $renderer->draw_separator ();

    $this->_draw_captcha_controls ($renderer);    

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->finish ();
  }
}

/**
 * Represents an object to preview in a form.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.5.0
 */
class SEND_MAIL_FORM_PREVIEW_SETTINGS extends FORM_PREVIEW_SETTINGS
{
  /**
   * Use these options with the {@link $obj_renderer}.
   * @var MAIL_OBJECT_RENDERER_OPTIONS
   */
  public $options;

  /**
   * Renders the {@link $object}.
   * @var MAIL_OBJECT_RENDERER
   */
  public $obj_renderer;

  /**
   * Renders HTML if <code>True</code>, otherwise plain text.
   * @var boolean
   */
  public $show_as_html;
  
  /**
   * Render the preview for this object.
   */
  protected function _display ()
  {
    $class_name = $this->context->final_class_name ('SEND_MAIL_FORM_RENDERER', 'webcore/mail/send_mail_form_renderer.php');
    $send_mail_renderer = new $class_name ($this->context);

    if ($this->show_as_html)
    {
      echo $send_mail_renderer->html_body ($this->form, $this->options);
      if (! empty ($this->obj_renderer))
      {
        echo $this->obj_renderer->html_body ($this->object, $this->options);
      }
    }
    else
    {
      echo '<pre style="white-space: -o-pre-wrap">';
      echo htmlspecialchars ($send_mail_renderer->text_body ($this->form, $this->options));
      if (! empty ($this->obj_renderer))
      {
        echo htmlspecialchars ($this->obj_renderer->text_body ($this->object, $this->options));
      }
      echo '</pre>';
    }
  }
}

?>