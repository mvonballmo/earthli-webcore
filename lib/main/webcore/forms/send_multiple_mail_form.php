<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.2.1
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
require_once ('webcore/forms/send_mail_form.php');

/**
 * Sends an object to one or more emails.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.2.1
 */
abstract class SEND_MULTIPLE_MAIL_FORM extends SEND_MAIL_FORM
{
  /**
   * @var bool
   * @access private
   */
  public $show_send_to = false;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new TEXT_FIELD ();
    $field->id = 'recipients';
    $field->title = 'Recipients';
    $field->description = 'Type each email on its own line.';
    $field->required = true;
    $field->tag_validator_type = Tag_validator_none;
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'email_type';
    $field->title = 'Email Options';
    $field->description = '*May time out if many emails are specified.';
    $field->add_value ('single_mail');
    $field->add_value ('multiple_mail');
    $this->add_field ($field);

    $field = $this->field_at ('send_to');
    $field->required = false;
  }

  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('email_type', 'single_mail');
  }

  /**
   * Called after fields are validated.
   * Anonymous users are limited to sending 'max_anonymous_recipients' emails at a time.
   * Registered users are limited to sending 'max_registered_recipients' emails at a time.
   * @see APPLICATION::$mail_options
   * @param object $obj This parameter is ignored.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    // strip the '\r' for Windows systems, then split the string at '\n'
    $this->recipient_list = str_replace ("\r", '', $this->value_for ('recipients'));
    $this->recipient_list = split ("\n", $this->recipient_list);

    $opts = $this->app->mail_options;

    if (($this->login->is_anonymous () && (sizeof ($this->recipient_list) > $opts->max_anonymous_recipients)) || 
      (! $this->login->is_anonymous () && (sizeof ($this->recipient_list) > $opts->max_registered_recipients)))
    {
      $this->record_error ('recipients', "Guests are only allowed to send mail to at most {$opts->max_anonymous_recipients} users." .
                           " <br>Registered users are allowed {$opts->max_anonymous_recipients}, so log in if you have an account.");
    }
  }

  /**
   * Send a message to the chosen recipients.
   * @param MAIL_PROVIDER $provider
   * @param MAIL_MESSAGE $msg
   * @access private
   */
  public function send_mail ($provider, $msg)
  {
    if ($this->value_for ('email_type') == 'single_mail')
    {
      $msg->set_send_to ($this->recipient_list);
      $msg->send ($provider);
    }
    else
    {
      foreach ($this->recipient_list as $email)
      {
        $msg->set_send_to ($email);
        $msg->send ($provider);
      }
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

    $props = $renderer->make_list_properties ();
    $props->add_item ('Send one email to all recipients.', 'single_mail');
    $props->add_item ('Send separate email to each recipient.*', 'multiple_mail');

    $renderer->draw_radio_group_row ('email_type', $props);
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('recipients');
  }
}

?>